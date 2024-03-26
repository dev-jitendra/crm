<?php


namespace Espo\Core\Authentication\TwoFactor\Sms;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Config;
use Espo\Core\Sms\SmsSender;
use Espo\Core\Sms\SmsFactory;
use Espo\Core\Utils\Language;
use Espo\Core\Field\DateTime;
use Espo\ORM\EntityManager;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\Entities\User;
use Espo\Entities\Sms;
use Espo\Entities\TwoFactorCode;
use Espo\Entities\UserData;
use Espo\Repositories\UserData as UserDataRepository;

use RuntimeException;

use const STR_PAD_LEFT;

class Util
{
    private const METHOD = SmsLogin::NAME;

    
    private const CODE_LIFETIME_PERIOD = '10 minutes';
    
    private const CODE_ATTEMPTS_COUNT = 5;
    
    private const CODE_LENGTH = 6;
    
    private const CODE_LIMIT = 5;
    
    private const CODE_LIMIT_PERIOD = '20 minutes';

    public function __construct(
        private EntityManager $entityManager,
        private Config $config,
        private SmsSender $smsSender,
        private Language $language,
        private SmsFactory $smsFactory
    ) {}

    
    public function storePhoneNumber(User $user, string $phoneNumber): void
    {
        $this->checkPhoneNumberIsUsers($user, $phoneNumber);

        $userData = $this->getUserDataRepository()->getByUserId($user->getId());

        if (!$userData) {
            throw new RuntimeException();
        }

        $userData->set('auth2FASmsPhoneNumber', $phoneNumber);

        $this->entityManager->saveEntity($userData);
    }

    public function verifyCode(User $user, string $code): bool
    {
        $codeEntity = $this->findCodeEntity($user);

        if (!$codeEntity) {
            return false;
        }

        if ($codeEntity->getAttemptsLeft() <= 1) {
            $this->decrementAttemptsLeft($codeEntity);
            $this->inactivateExistingCodeRecords($user);

            return false;
        }

        if ($codeEntity->getCode() !== $code) {
            $this->decrementAttemptsLeft($codeEntity);

            return false;
        }

        if (!$this->isCodeValidByLifetime($codeEntity)) {
            $this->inactivateExistingCodeRecords($user);

            return false;
        }

        $this->inactivateExistingCodeRecords($user);

        return true;
    }

    
    public function sendCode(User $user, ?string $phoneNumber = null): void
    {
        if ($phoneNumber === null) {
            $phoneNumber = $this->getPhoneNumber($user);
        }

        $this->checkPhoneNumberIsUsers($user, $phoneNumber);
        $this->checkCodeLimit($user);

        $code = $this->generateCode();

        $this->inactivateExistingCodeRecords($user);
        $this->createCodeRecord($user, $code);

        $sms = $this->createSms($code, $phoneNumber);

        $this->smsSender->send($sms);
    }

    private function isCodeValidByLifetime(TwoFactorCode $codeEntity): bool
    {
        $period = $this->config->get('auth2FASmsCodeLifetimePeriod') ?? self::CODE_LIFETIME_PERIOD;

        $validUntil = $codeEntity->getCreatedAt()->modify($period);

        if (DateTime::createNow()->diff($validUntil)->invert) {
            return false;
        }

        return true;
    }

    private function findCodeEntity(User $user): ?TwoFactorCode
    {
        
        return $this->entityManager
            ->getRDBRepository(TwoFactorCode::ENTITY_TYPE)
            ->where([
                'method' => self::METHOD,
                'userId' => $user->getId(),
                'isActive' => true,
            ])
            ->findOne();
    }

    
    private function getPhoneNumber(User $user): string
    {
        $userData = $this->getUserDataRepository()->getByUserId($user->getId());

        if (!$userData) {
            throw new RuntimeException("UserData not found.");
        }

        $phoneNumber = $userData->get('auth2FASmsPhoneNumber');

        if ($phoneNumber) {
            return $phoneNumber;
        }

        if ($user->getPhoneNumberGroup()->getCount() === 0) {
            throw new Forbidden("User does not have phone number.");
        }

        
        return $user->getPhoneNumberGroup()->getPrimaryNumber();
    }

    
    private function checkPhoneNumberIsUsers(User $user, string $phoneNumber): void
    {
        $userNumberList = array_map(
            function (string $item) {
                return strtolower($item);
            },
            $user->getPhoneNumberGroup()->getNumberList()
        );

        if (!in_array(strtolower($phoneNumber), $userNumberList)) {
            throw new Forbidden("Phone number is not one of user's.");
        }
    }

    
    private function checkCodeLimit(User $user): void
    {
        $limit = $this->config->get('auth2FASmsCodeLimit') ?? self::CODE_LIMIT;
        $period = $this->config->get('auth2FASmsCodeLimitPeriod') ?? self::CODE_LIMIT_PERIOD;

        $from = DateTime::createNow()
            ->modify('-' . $period)
            ->toString();

        $count = $this->entityManager
            ->getRDBRepository(TwoFactorCode::ENTITY_TYPE)
            ->where(
                Cond::and(
                    Cond::equal(Cond::column('method'), self::METHOD),
                    Cond::equal(Cond::column('userId'), $user->getId()),
                    Cond::greaterOrEqual(Cond::column('createdAt'), $from),
                    Cond::lessOrEqual(Cond::column('attemptsLeft'), 0),
                )
            )
            ->count();

        if ($count >= $limit) {
            throw new Forbidden("Max code count exceeded.");
        }
    }

    private function generateCode(): string
    {
        $codeLength = $this->config->get('auth2FASmsCodeLength') ?? self::CODE_LENGTH;

        $max = pow(10, $codeLength) - 1;

        
        return str_pad(
            (string) random_int(0, $max),
            $codeLength,
            '0',
            STR_PAD_LEFT
        );
    }

    private function createSms(string $code, string $phoneNumber): Sms
    {
        $fromNumber = $this->config->get('outboundSmsFromNumber');

        $bodyTpl = $this->language->translateLabel('yourAuthenticationCode', 'messages', 'User');

        $body = str_replace('{code}', $code, $bodyTpl);

        $sms = $this->smsFactory->create();

        $sms->setFromNumber($fromNumber);
        $sms->setBody($body);
        $sms->addToNumber($phoneNumber);

        return $sms;
    }

    private function inactivateExistingCodeRecords(User $user): void
    {
        $query = $this->entityManager
            ->getQueryBuilder()
            ->update()
            ->in(TwoFactorCode::ENTITY_TYPE)
            ->where([
                'userId' => $user->getId(),
                'method' => self::METHOD,
            ])
            ->set([
                'isActive' => false,
            ])
            ->build();

        $this->entityManager
            ->getQueryExecutor()
            ->execute($query);
    }

    private function createCodeRecord(User $user, string $code): void
    {
        $this->entityManager->createEntity(TwoFactorCode::ENTITY_TYPE, [
            'code' => $code,
            'userId' => $user->getId(),
            'method' => self::METHOD,
            'attemptsLeft' => $this->getCodeAttemptsCount(),
        ]);
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        return $this->entityManager->getRepository(UserData::ENTITY_TYPE);
    }

    private function decrementAttemptsLeft(TwoFactorCode $codeEntity): void
    {
        $codeEntity->decrementAttemptsLeft();

        $this->entityManager->saveEntity($codeEntity);
    }

    private function getCodeAttemptsCount(): int
    {
        return $this->config->get('auth2FASmsCodeAttemptsCount') ?? self::CODE_ATTEMPTS_COUNT;
    }
}
