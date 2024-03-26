<?php


namespace Espo\Tools\UserSecurity\TwoFactor;

use Espo\Core\Authentication\TwoFactor\Sms\SmsLogin;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Utils\Config;
use Espo\Core\Authentication\TwoFactor\Sms\Util;
use Espo\ORM\EntityManager;
use Espo\Entities\User;

class SmsService
{
    public function __construct(
        private Util $util,
        private User $user,
        private EntityManager $entityManager,
        private Config $config
    ) {}

    
    public function sendCode(string $userId, string $phoneNumber): void
    {
        if (!$this->user->isAdmin() && $userId !== $this->user->getId()) {
            throw new Forbidden();
        }

        $this->checkAllowed();

        
        $user = $this->entityManager->getEntity(User::ENTITY_TYPE, $userId);

        if (!$user) {
            throw new NotFound();
        }

        $this->util->sendCode($user, $phoneNumber);
        $this->util->storePhoneNumber($user, $phoneNumber);
    }

    
    private function checkAllowed(): void
    {
        if (!$this->config->get('auth2FA')) {
            throw new Forbidden("2FA is not enabled.");
        }

        if ($this->user->isPortal() && !$this->config->get('auth2FAInPortal')) {
            throw new Forbidden("2FA is not enabled in portals.");
        }

        $methodList = $this->config->get('auth2FAMethodList') ?? [];

        if (!in_array(SmsLogin::NAME, $methodList)) {
            throw new Forbidden("Sms 2FA is not allowed.");
        }
    }
}
