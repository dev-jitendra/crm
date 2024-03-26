<?php


namespace Espo\Tools\UserSecurity;

use Espo\Core\Authentication\TwoFactor\Exceptions\NotConfigured;
use Espo\Core\Exceptions\Error\Body;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Utils\Log;
use Espo\ORM\EntityManager;
use Espo\Entities\User;
use Espo\Entities\UserData;
use Espo\Repositories\UserData as UserDataRepository;
use Espo\Core\Api\RequestNull;
use Espo\Core\Authentication\Login\Data as LoginData;
use Espo\Core\Authentication\LoginFactory;
use Espo\Core\Authentication\TwoFactor\UserSetupFactory as TwoFactorUserSetupFactory;
use Espo\Core\Utils\Config;

use stdClass;

class Service
{
    public function __construct(
        private EntityManager $entityManager,
        private User $user,
        private Config $config,
        private LoginFactory $authLoginFactory,
        private TwoFactorUserSetupFactory $twoFactorUserSetupFactory,
        private Log $log
    ) {}

    
    public function read(string $id): stdClass
    {
        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $id);

        if (!$user) {
            throw new NotFound();
        }

        $allow =
            $user->isAdmin() ||
            $user->isRegular() ||
            $user->isPortal() && $this->config->get('auth2FAInPortal');

        if (!$allow) {
            throw new Forbidden();
        }

        $userData = $this->getUserDataRepository()->getByUserId($id);

        if (!$userData) {
            throw new NotFound();
        }

        return (object) [
            'auth2FA' => $userData->get('auth2FA'),
            'auth2FAMethod' => $userData->get('auth2FAMethod'),
        ];
    }

    
    public function getTwoFactorUserSetupData(string $id, stdClass $data): stdClass
    {
        if (
            !$this->user->isAdmin() &&
            $id !== $this->user->getId()
        ) {
            throw new Forbidden();
        }

        $isReset = $data->reset ?? false;

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $id);

        if (!$user) {
            throw new NotFound();
        }

        $allow =
            $this->config->get('auth2FA') &&
            (
                $user->isAdmin() ||
                $user->isRegular() ||
                $user->isPortal() && $this->config->get('auth2FAInPortal')
            );

        if (!$allow) {
            throw new Forbidden();
        }

        $password = $data->password ?? null;

        if (!$password) {
            throw new Forbidden('Passport required.');
        }

        if (!$this->user->isAdmin()) {
            $this->checkPassword($id, $password);
        }

        if ($this->user->isAdmin()) {
            $this->checkPassword($this->user->getId(), $password);
        }

        $auth2FAMethod = $data->auth2FAMethod ?? null;

        if (!$auth2FAMethod) {
            throw new BadRequest();
        }

        try {
            $clientData = $this->twoFactorUserSetupFactory
                ->create($auth2FAMethod)
                ->getData($user);
        }
        catch (NotConfigured $e) {
            $this->log->error($e->getMessage());

            throw Forbidden::createWithBody(
                "2FA method '$auth2FAMethod' is not fully configured.",
                Body::create()->withMessageTranslation('2faMethodNotConfigured', 'User')
            );
        }

        if ($isReset) {
            $userData = $this->getUserDataRepository()->getByUserId($id);

            if (!$userData) {
                throw new NotFound();
            }

            $userData->set('auth2FA', false);
            
            $userData->set('auth2FAMethod', null);

            $this->entityManager->saveEntity($userData);
        }

        return $clientData;
    }

    
    public function update(string $id, stdClass $data): stdClass
    {
        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        
        $user = $this->entityManager->getEntityById(User::ENTITY_TYPE, $id);

        if (!$user) {
            throw new NotFound();
        }

        $allow =
            $user->isAdmin() ||
            $user->isRegular() ||
            $user->isPortal() && $this->config->get('auth2FAInPortal');

        if (!$allow) {
            throw new Forbidden();
        }

        $userData = $this->getUserDataRepository()->getByUserId($id);

        if (!$userData) {
            throw new NotFound();
        }

        $password = $data->password ?? null;

        if (!$password) {
            throw new Forbidden('Password required.');
        }

        if (!$this->user->isAdmin() || $this->user->getId() === $id) {
            $this->checkPassword($id, $password);
        }

        if (property_exists($data, 'auth2FA')) {
            $userData->set('auth2FA', $data->auth2FA);
        }

        if (property_exists($data, 'auth2FAMethod')) {
            $userData->set('auth2FAMethod', $data->auth2FAMethod);
        }

        if (!$userData->get('auth2FA')) {
            
            $userData->set('auth2FAMethod', null);
        }

        if ($userData->get('auth2FA') && $userData->isAttributeChanged('auth2FA')) {
            if (!$this->config->get('auth2FA')) {
                throw new Forbidden('2FA is not enabled.');
            }
        }

        if (
            $userData->get('auth2FA') &&
            $userData->get('auth2FAMethod') &&
            ($userData->isAttributeChanged('auth2FA') || $userData->isAttributeChanged('auth2FAMethod')) &&
            (
                !$user->isPortal() ||
                $this->config->get('auth2FAInPortal')
            )
        ) {
            $auth2FAMethod = $userData->get('auth2FAMethod');

            if (!in_array($auth2FAMethod, $this->config->get('auth2FAMethodList', []))) {
                throw new Forbidden('Not allowed 2FA auth method.');
            }

            $verifyResult = $this->twoFactorUserSetupFactory
                ->create($auth2FAMethod)
                ->verifyData($user, $data);

            if (!$verifyResult) {
                throw new Forbidden('Not verified.');
            }
        }

        $this->entityManager->saveEntity($userData);

        return (object) [
            'auth2FA' => $userData->get('auth2FA'),
            'auth2FAMethod' => $userData->get('auth2FAMethod'),
        ];
    }

    
    private function checkPassword(string $id, string $password): void
    {
        $user = $this->entityManager
            ->getRDBRepository(User::ENTITY_TYPE)
            ->where([
                'id' => $id,
            ])
            ->findOne();

        if (!$user) {
            throw new Forbidden('User is not found.');
        }

        $loginData = LoginData::createBuilder()
            ->setUsername($user->get('userName'))
            ->setPassword($password)
            ->build();

        $login = $this->authLoginFactory->createDefault();

        $result = $login->login($loginData, new RequestNull());

        if ($result->isFail()) {
            throw new Forbidden('Password is incorrect.');
        }
    }

    private function getUserDataRepository(): UserDataRepository
    {
        
        return $this->entityManager->getRepository(UserData::ENTITY_TYPE);
    }
}
