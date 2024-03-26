<?php


namespace Espo\Core\Htmlizer;

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\DateTime\DateTimeFactory;
use Espo\Core\AclManager;

use Espo\Entities\User;


class HtmlizerFactory
{
    private $injectableFactory;

    private $dateTimeFactory;

    private $aclManager;

    public function __construct(
        InjectableFactory $injectableFactory,
        DateTimeFactory $dateTimeFactory,
        AclManager $aclManager
    ) {
        $this->injectableFactory = $injectableFactory;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->aclManager = $aclManager;
    }

    public function create(bool $skipAcl = false, ?string $timeZone = null): Htmlizer
    {
        $with = [];

        if ($skipAcl) {
            $with['acl'] = null;
        }

        if ($timeZone) {
            $with['dateTime'] = $this->dateTimeFactory->createWithTimeZone($timeZone);
        }

        return $this->injectableFactory->createWith(Htmlizer::class, $with);
    }

    public function createNoAcl(): Htmlizer
    {
        return $this->create(true);
    }

    public function createForUser(User $user, ?CreateForUserParams $params = null): Htmlizer
    {
        if (!$params) {
            $params = new CreateForUserParams();
            $params->useUserTimezone = true;
            $params->applyAcl = true;
        }

        $deps = [];

        if ($params->useUserTimezone) {
            $deps['dateTime'] = $this->dateTimeFactory->createWithUserTimeZone($user);
        }

        if ($params->applyAcl) {
            $deps['acl'] = $this->aclManager->createUserAcl($user);
        }

        return $this->injectableFactory->createWith(Htmlizer::class, $deps);
    }
}
