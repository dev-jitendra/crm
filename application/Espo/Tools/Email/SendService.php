<?php


namespace Espo\Tools\Email;

use Espo\Core\Acl;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\ErrorSilent;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\FieldValidation\FieldValidationManager;
use Espo\Core\InjectableFactory;
use Espo\Core\Mail\Account\Account;
use Espo\Core\Mail\Account\GroupAccount\Account as GroupAccount;
use Espo\Core\Mail\Account\GroupAccount\AccountFactory as GroupAccountFactory;
use Espo\Core\Mail\Account\GroupAccount\Service as GroupAccountService;
use Espo\Core\Mail\Account\PersonalAccount\Account as PersonalAccount;
use Espo\Core\Mail\Account\PersonalAccount\AccountFactory as PersonalAccountFactory;
use Espo\Core\Mail\Account\PersonalAccount\Service as PersonalAccountService;
use Espo\Core\Mail\Account\SendingAccountProvider;
use Espo\Core\Mail\EmailSender;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\Mail\SenderParams;
use Espo\Core\Mail\Smtp\HandlerProcessor;
use Espo\Core\Mail\SmtpParams;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Json;
use Espo\Core\Utils\Log;
use Espo\Entities\Email;
use Espo\Entities\EmailAccount;
use Espo\Entities\EmailAddress;
use Espo\Entities\InboundEmail;
use Espo\Entities\User;
use Espo\Entities\UserData;
use Espo\Modules\Crm\Entities\CaseObj;
use Espo\ORM\Collection;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\Repositories\UserData as UserDataRepository;
use Espo\Tools\Stream\Service as StreamService;
use Exception;
use Laminas\Mail\Message;
use LogicException;
use Throwable;

use const FILTER_VALIDATE_EMAIL;


class SendService
{
    
    private array $notAllowedStatusList = [
        Email::STATUS_ARCHIVED,
        Email::STATUS_SENT,
        Email::STATUS_BEING_IMPORTED,
    ];

    public function __construct(
        private User $user,
        private EntityManager $entityManager,
        private FieldValidationManager $fieldValidationManager,
        private EmailSender $emailSender,
        private StreamService $streamService,
        private Config $config,
        private Log $log,
        private InjectableFactory $injectableFactory,
        private Acl $acl,
        private SendingAccountProvider $accountProvider,
        private PersonalAccountService $personalAccountService,
        private GroupAccountService $groupAccountService,
        private HandlerProcessor $handlerProcessor,
        private PersonalAccountFactory $personalAccountFactory,
        private GroupAccountFactory $groupAccountFactory
    ) {}

    
    public function send(Email $entity, ?User $user = null): void
    {
        if (in_array($entity->getStatus(), $this->notAllowedStatusList)) {
            throw new Error("Can't send email with status `{$entity->getStatus()}`.");
        }

        if (!$this->fieldValidationManager->check($entity, 'to', 'required')) {
            $entity->setStatus(Email::STATUS_DRAFT);

            $this->entityManager->saveEntity($entity, [SaveOption::SILENT => true]);

            throw new BadRequest("Empty To address.");
        }

        $systemIsShared = $this->config->get('outboundEmailIsShared');
        $systemFromName = $this->config->get('outboundEmailFromName');
        $systemFromAddress = $this->config->get('outboundEmailFromAddress');

        $emailSender = $this->emailSender->create();

        $userAddressList = [];

        if ($user) {
            
            $emailAddressCollection = $this->entityManager
                ->getRDBRepositoryByClass(User::class)
                ->getRelation($user, 'emailAddresses')
                ->find();

            foreach ($emailAddressCollection as $ea) {
                $userAddressList[] = $ea->getLower();
            }
        }

        $originalFromAddress = $entity->getFromAddress();

        if (!$originalFromAddress) {
            throw new Error("Email sending: Can't send with empty 'from' address.");
        }

        $fromAddress = strtolower($originalFromAddress);

        $isUserAddress = in_array($fromAddress, $userAddressList);
        $isSystemAddress = $fromAddress === strtolower($systemFromAddress);

        $smtpParams = null;
        $personalAccount = null;
        $groupAccount = null;
        $params = SenderParams::create();

        if ($user && $isUserAddress) {
            [$smtpParams, $personalAccount] = $this->getPersonalAccount($user, $originalFromAddress);
        }

        if ($user && $smtpParams) {
            
            $smtpParams = $this->applyUserHandler($user, $smtpParams, $fromAddress);

            $emailSender->withSmtpParams($smtpParams);
        }

        if (!$smtpParams) {
            [$smtpParams, $groupAccount] = $this->getGroupAccount($user, $originalFromAddress);

            if ($smtpParams) {
                $emailSender->withSmtpParams($smtpParams);
            }
        }

        if (!$smtpParams && $isSystemAddress) {
            $params = $params->withFromName($systemFromName);
        }

        
        if (!$smtpParams && !$systemIsShared) {
            if ($isSystemAddress) {
                throw new NoSmtp("Can not use system SMTP. System SMTP is not shared.");
            }

            throw new NoSmtp("No SMTP params for $fromAddress.");
        }

        if (
            !$smtpParams &&
            $user &&
            in_array($fromAddress, $userAddressList)
        ) {
            $params = $params->withFromName($user->getName());
        }

        $parent = null;
        $parentId = $entity->getParentId();
        $parentType = $entity->getParentType();

        if ($parentType && $parentId) {
            $parent = $this->entityManager->getEntityById($parentType, $parentId);

            $params = $this->applyParent($parent, $params);
        }

        $this->validateEmailAddresses($entity);

        $message = new Message();

        $repliedMessageId = $this->getRepliedEmailMessageId($entity);

        if ($repliedMessageId) {
            $message->getHeaders()->addHeaderLine('In-Reply-To', $repliedMessageId);
            $message->getHeaders()->addHeaderLine('References', $repliedMessageId);
        }

        try {
            $emailSender
                ->withParams($params)
                ->withMessage($message)
                ->send($entity);
        }
        catch (Exception $e) {
            $entity->setStatus(Email::STATUS_DRAFT);

            $this->entityManager->saveEntity($entity, [SaveOption::SILENT => true]);

            $this->log->error("Email sending:" . $e->getMessage() . "; " . $e->getCode());

            $errorData = [
                'id' => $entity->getId(),
                'message' => $e->getMessage(),
            ];

            throw ErrorSilent::createWithBody('sendingFail', Json::encode($errorData));
        }

        if ($groupAccount) {
            $groupAccountId = $groupAccount->getId();

            if ($groupAccountId) {
                $entity->addLinkMultipleId('inboundEmails', $groupAccountId);
            }
        }

        if ($personalAccount) {
            $personalAccountId = $personalAccount->getId();

            if ($personalAccountId) {
                $entity->addLinkMultipleId('emailAccounts', $personalAccountId);
            }
        }

        $this->entityManager->saveEntity($entity, ['isJustSent' => true]);

        $this->store($message, $groupAccount, $personalAccount);

        if ($parent) {
            $this->streamService->noteEmailSent($parent, $entity);
        }
    }

    private function applyParent(?Entity $parent, SenderParams $params): SenderParams
    {
        
        if ($parent instanceof CaseObj) {
            $inboundEmailId = $parent->getInboundEmailId();

            if (!$inboundEmailId) {
                return $params;
            }

            
            $inboundEmail = $this->entityManager
                ->getRDBRepositoryByClass(InboundEmail::class)
                ->getById($inboundEmailId);

            if (!$inboundEmail || !$inboundEmail->getReplyToAddress()) {
                return $params;
            }

            $params = $params->withReplyToAddress($inboundEmail->getReplyToAddress());
        }

        return $params;
    }

    private function store(Message $message, ?Account $groupAccount, ?Account $personalAccount): void
    {
        if ($groupAccount instanceof GroupAccount && $groupAccount->storeSentEmails()) {
            $id = $groupAccount->getId() ?? null;

            if (!$id) {
                throw new LogicException();
            }

            try {
                $this->groupAccountService->storeSentMessage($id, $message);
            }
            catch (Exception $e) {
                $this->log->error(
                    "Email sending: Could not store sent email (Group Email Account {$groupAccount->getId()}): " .
                    $e->getMessage() . "."
                );
            }
        }

        if ($personalAccount instanceof PersonalAccount && $personalAccount->storeSentEmails()) {
            $id = $personalAccount->getId() ?? null;

            if (!$id) {
                throw new LogicException();
            }

            try {
                $this->personalAccountService->storeSentMessage($id, $message);
            }
            catch (Exception $e) {
                $this->log->error(
                    "Email sending: Could not store sent email (Email Account {$personalAccount->getId()}): " .
                    $e->getMessage() . "."
                );
            }
        }
    }

    
    private function getPersonalAccount(User $user, string $emailAddress): array
    {
        $personalAccount = $this->accountProvider->getPersonal($user, $emailAddress);

        if (!$personalAccount) {
            return [null, null];
        }

        if (!$personalAccount->isAvailableForSending()) {
            return [null, null];
        }

        $smtpParams = $personalAccount->getSmtpParams();

        if (!$smtpParams) {
            return [null, null];
        }

        $smtpParams = $smtpParams->withFromName($user->getName());

        return [$smtpParams, $personalAccount];
    }

    
    private function getGroupAccount(?User $user, string $emailAddress): array
    {
        $groupAccount = $user ?
            $this->accountProvider->getShared($user, $emailAddress) :
            $this->accountProvider->getGroup($emailAddress);

        if (!$groupAccount) {
            return [null, null];
        }

        $smtpParams = $groupAccount->getSmtpParams();

        if (!$smtpParams) {
            return [null, null];
        }

        return [$smtpParams, $groupAccount];
    }

    private function applyUserHandler(User $user, SmtpParams $smtpParams, string $emailAddress): SmtpParams
    {
        $raw = $smtpParams->toArray();

        $applied = $this->applyUserHandlerInternal($user->getId(), $emailAddress, $raw);

        if ($applied) {
            return SmtpParams::fromArray($raw);
        }

        return $smtpParams;
    }

    
    public function sendTestEmail(SmtpParams $params, TestSendData $data): void
    {
        $emailAddress = $data->getEmailAddress();
        $userId = $data->getUserId();
        $type = $data->getType();
        $id = $data->getId();

        if ($params->getPassword() === null) {
            $params = $params->withPassword(
                $this->obtainSendTestEmailPassword($type, $id)
            );
        }

        $fromAddress = $params->getFromAddress();

        if (
            $userId &&
            $userId !== $this->user->getId() &&
            !$this->user->isAdmin()
        ) {
            throw new Forbidden();
        }

        
        $user = $userId ?
            $this->entityManager->getRDBRepositoryByClass(User::class)->getById($userId) :
            null;

        if ($userId && !$user) {
            throw new NotFound("User not found.");
        }

        
        $email = $this->entityManager->getNewEntity(Email::ENTITY_TYPE);

        $email
            ->setSubject('EspoCRM: Test Email')
            ->setIsHtml(false)
            ->addToAddress($emailAddress);

        $handlerClassName = null;

        if ($type === 'emailAccount' && $id) {
            
            $emailAccount = $this->entityManager->getEntityById(EmailAccount::ENTITY_TYPE, $id);

            $handlerClassName = $emailAccount?->getSmtpHandlerClassName();
        }

        if ($type === 'inboundEmail' && $id) {
            
            $inboundEmail = $this->entityManager->getEntityById(InboundEmail::ENTITY_TYPE, $id);

            if ($inboundEmail) {
                $handlerClassName = $inboundEmail->getSmtpHandlerClassName();
            }
        }

        if ($handlerClassName && $id) {
            $params = $this->handlerProcessor->handle($handlerClassName, $params, $id);
        }

        if ($user && $fromAddress) {
            $params = $this->applyUserHandler($user, $params, $fromAddress);
        }

        $emailSender = $this->emailSender;

        try {
            $emailSender
                ->withSmtpParams($params)
                ->send($email);
        }
        catch (Exception $e) {
            $this->log->warning("Email sending:" . $e->getMessage() . "; " . $e->getCode());

            $errorData = ['message' => $e->getMessage()];

            throw ErrorSilent::createWithBody('sendingFail', Json::encode($errorData));
        }
    }

    
    public function validateEmailAddresses(Email $entity): void
    {
        $from = $entity->getFromAddress();

        if ($from) {
            if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
                throw new Error('From email address is not valid.');
            }
        }

        foreach ($entity->getToAddressList() as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                throw new Error('To email address is not valid.');
            }
        }

        foreach ($entity->getCcAddressList() as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                throw new Error('CC email address is not valid.');
            }
        }

        foreach ($entity->getBccAddressList() as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                throw new Error('BCC email address is not valid.');
            }
        }
    }

    
    public function getUserSmtpParams(string $userId): ?SmtpParams
    {
        
        $user = $this->entityManager->getRDBRepositoryByClass(User::class)->getById($userId);

        if (!$user) {
            return null;
        }

        $address = $user->getEmailAddress();

        if (!$address) {
            return null;
        }

        $account = $this->accountProvider->getPersonal($user, $address);

        if (!$account) {
            return null;
        }

        $smtpParams = $account->getSmtpParams();

        if (!$smtpParams) {
            return null;
        }

        
        $smtpParams = $this->applyUserHandler($user, $smtpParams, strtolower($address));

        return $smtpParams
            ->withFromName($user->getName())
            ->withFromAddress($address);
    }

    private function getRepliedEmailMessageId(Email $email): ?string
    {
        $repliedLink = $email->getReplied();

        if (!$repliedLink) {
            return null;
        }

        
        $replied = $this->entityManager
            ->getRDBRepositoryByClass(Email::class)
            ->select(['messageId'])
            ->where(['id' => $repliedLink->getId()])
            ->findOne();

        return $replied?->getMessageId();
    }

    
    private function applyUserHandlerInternal(string $userId, string $emailAddress, array &$params): bool
    {
        $userData = $this->getUserDataRepository()->getByUserId($userId);

        if (!$userData) {
            return false;
        }

        $smtpHandlers = $userData->get('smtpHandlers') ?? (object) [];

        if (!is_object($smtpHandlers)) {
            return false;
        }

        if (!isset($smtpHandlers->$emailAddress)) {
            return false;
        }

        
        $handlerClassName = $smtpHandlers->$emailAddress;

        try {
            $handler = $this->injectableFactory->create($handlerClassName);
        }
        catch (Throwable $e) {
            $this->log->error(
                "Email sending: Could not create Smtp Handler for $emailAddress. Error: " .
                $e->getMessage() . "."
            );

            return false;
        }

        if (method_exists($handler, 'applyParams')) {
            $handler->applyParams($userId, $emailAddress, $params);

            return true;
        }

        return false;
    }

    
    private function obtainSendTestEmailPassword(?string $type, ?string $id): ?string
    {
        if ($type === 'emailAccount') {
            if (!$this->acl->checkScope(EmailAccount::ENTITY_TYPE)) {
                throw new Forbidden();
            }

            if (!$id) {
                return null;
            }

            $personalAccount = $this->personalAccountFactory->create($id);

            if (
                !$this->user->isAdmin() &&
                $personalAccount->getUser()->getId() !== $id
            ) {
                throw new Forbidden();
            }

            $smtpParams = $personalAccount->getSmtpParams();

            return $smtpParams?->getPassword();
        }

        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        if ($type === 'inboundEmail') {
            if (!$id) {
                return null;
            }

            $smtpParams = $this->groupAccountFactory
                ->create($id)
                ->getSmtpParams();

            return $smtpParams?->getPassword();
        }

        return $this->config->get('smtpPassword');
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        return $this->entityManager->getRepository(UserData::ENTITY_TYPE);
    }
}
