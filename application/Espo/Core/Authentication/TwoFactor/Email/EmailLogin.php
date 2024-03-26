<?php


namespace Espo\Core\Authentication\TwoFactor\Email;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Utils\Log;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Entities\UserData;
use Espo\Repositories\UserData as UserDataRepository;
use Espo\Core\Authentication\TwoFactor\Login;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Result\Data as ResultData;
use Espo\Core\Authentication\Result\FailReason;
use Espo\Core\Api\Request;

use RuntimeException;

class EmailLogin implements Login
{
    public const NAME = 'Email';

    public function __construct(
        private EntityManager $entityManager,
        private Util $util,
        private Log $log
    ) {}

    public function login(Result $result, Request $request): Result
    {
        $code = $request->getHeader('Espo-Authorization-Code');

        $user = $result->getUser();

        if (!$user) {
            throw new RuntimeException("No user.");
        }

        if (!$code) {
            try {
                $this->util->sendCode($user);
            }
            catch (Forbidden|SendingError $e) {
                $this->log->error("Could not send 2FA code for user {$user->getUserName()}. " . $e->getMessage());

                return Result::fail(FailReason::ERROR);
            }

            return Result::secondStepRequired($user, $this->getResultData());
        }

        if ($this->verifyCode($user, $code)) {
            return $result;
        }

        return Result::fail(FailReason::CODE_NOT_VERIFIED);
    }

    private function getResultData(): ResultData
    {
        return ResultData::createWithMessage('enterCodeSentInEmail');
    }

    private function verifyCode(User $user, string $code): bool
    {
        $userData = $this->getUserDataRepository()->getByUserId($user->getId());

        if (!$userData) {
            return false;
        }

        if (!$userData->get('auth2FA')) {
            return false;
        }

        if ($userData->get('auth2FAMethod') !== self::NAME) {
            return false;
        }

        return $this->util->verifyCode($user, $code);
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        return $this->entityManager->getRepository(UserData::ENTITY_TYPE);
    }
}
