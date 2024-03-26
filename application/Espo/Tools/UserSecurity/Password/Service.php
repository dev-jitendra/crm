<?php


namespace Espo\Tools\UserSecurity\Password;

use Espo\Core\Authentication\Logins\Espo;
use Espo\Core\Authentication\Util\MethodProvider as AuthenticationMethodProvider;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\FieldValidation\FieldValidationManager;
use Espo\Core\Mail\EmailSender;
use Espo\Core\Mail\Exceptions\SendingError;
use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\PasswordHash;
use Espo\Entities\User;
use Espo\ORM\EntityManager;

class Service
{
    public function __construct(
        private User $user,
        private ServiceContainer $serviceContainer,
        private EmailSender $emailSender,
        private Config $config,
        private Generator $generator,
        private Sender $sender,
        private PasswordHash $passwordHash,
        private EntityManager $entityManager,
        private RecoveryService $recovery,
        private FieldValidationManager $fieldValidationManager,
        private Checker $checker,
        private AuthenticationMethodProvider $authenticationMethodProvider
    ) {}

    
    public function createAndSendPasswordRecovery(string $id): void
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $id);

        if (!$user) {
            throw new NotFound();
        }

        if (!$user->isActive()) {
            throw new Forbidden("User is not active.");
        }

        if (
            !$user->isRegular() &&
            !$user->isAdmin() &&
            !$user->isPortal()
        ) {
            throw new Forbidden();
        }

        $this->recovery->createAndSendRequestForExistingUser($user);
    }

    
    public function changePasswordByRecovery(string $requestId, string $password): ?string
    {
        $request = $this->recovery->getRequest($requestId);

        $this->changePassword($request->getUserId(), $password);
        $this->recovery->removeRequest($requestId);

        return $request->getUrl();
    }

    
    public function changePasswordWithCheck(string $userId, string $password, string $currentPassword): void
    {
        $this->changePasswordInternal($userId, $password, true, $currentPassword);
    }

    
    private function changePassword(string $userId, string $password): void
    {
        $this->changePasswordInternal($userId, $password);
    }

    
    private function changePasswordInternal(
        string $userId,
        string $password,
        bool $checkCurrentPassword = false,
        ?string $currentPassword = null
    ): void {

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $userId);

        if (!$user) {
            throw new NotFound();
        }

        if (
            $user->isSuperAdmin() &&
            !$this->user->isSuperAdmin()
        ) {
            throw new Forbidden();
        }

        $authenticationMethod = $this->authenticationMethodProvider->get();

        if (!$user->isAdmin() && $authenticationMethod !== Espo::NAME) {
            throw new Forbidden("Authentication method is not `Espo`.");
        }

        if (empty($password)) {
            throw new Error("Password can't be empty.");
        }

        if ($checkCurrentPassword) {
            $u = $this->entityManager
                ->getRDBRepository(User::ENTITY_TYPE)
                ->where([
                    'id' => $user->getId(),
                    'password' => $this->passwordHash->hash($currentPassword ?? ''),
                ])
                ->findOne();

            if (!$u) {
                throw new Forbidden("Wrong password.");
            }
        }

        if (!$this->checker->checkStrength($password)) {
            throw new Forbidden("Password is weak.");
        }

        $validLength = $this->fieldValidationManager->check(
            $user,
            'password',
            'maxLength',
            (object) ['password' => $password]
        );

        if (!$validLength) {
            throw new Forbidden("Password exceeds max length.");
        }

        $user->set('password', $this->passwordHash->hash($password));

        $this->entityManager->saveEntity($user);
    }

    
    public function sendAccessInfoForNewUser(User $user): void
    {
        $emailAddress = $user->getEmailAddress();

        if ($emailAddress === null) {
            throw new Error("Can't send access info for user '{$user->getId()}' w/o email address.");
        }

        if (!$this->isSmtpConfigured()) {
            throw new Error("Can't send access info. SMTP is not configured.");
        }

        $stubPassword = $this->generator->generate();

        $this->savePasswordSilent($user, $stubPassword);

        $request = $this->recovery->createRequestForNewUser($user);

        $this->sender->sendAccessInfo($user, $request);
    }

    
    public function generateAndSendNewPasswordForUser(string $id): void
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        
        $user = $this->serviceContainer
            ->get(User::ENTITY_TYPE)
            ->getEntity($id);

        if (!$user) {
            throw new NotFound();
        }

        if ($user->isApi()) {
            throw new Forbidden();
        }

        if ($user->isSuperAdmin()) {
            throw new Forbidden();
        }

        if ($user->isSystem()) {
            throw new Forbidden();
        }

        if (!$user->getEmailAddress()) {
            throw new Forbidden("Generate new password: Can't process because user doesn't have email address.");
        }

        if (!$this->isSmtpConfigured()) {
            throw new Forbidden("Generate new password: Can't process because SMTP is not configured.");
        }

        $password = $this->generator->generate();

        try {
            $this->sender->sendPassword($user, $password);
        }
        catch (SendingError) {
            throw new Error("Email sending error.");
        }

        $this->savePassword($user, $password);
    }

    private function savePassword(User $user, string $password): void
    {
        $user->set('password', $this->passwordHash->hash($password));

        $this->entityManager->saveEntity($user);
    }

    private function savePasswordSilent(User $user, string $password): void
    {
        $user->set('password', $this->passwordHash->hash($password));

        $this->entityManager->saveEntity($user, [SaveOption::SILENT => true]);
    }

    private function isSmtpConfigured(): bool
    {
        return
            $this->emailSender->hasSystemSmtp() ||
            $this->config->get('internalSmtpServer');
    }
}
