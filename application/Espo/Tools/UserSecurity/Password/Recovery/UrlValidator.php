<?php


namespace Espo\Tools\UserSecurity\Password\Recovery;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Utils\Config;
use Espo\Entities\Portal;
use Espo\ORM\EntityManager;

class UrlValidator
{
    public function __construct(
        private Config $config,
        private EntityManager $entityManager
    ) {}

    
    public function validate(string $url): void
    {
        $siteUrl = rtrim($this->config->get('siteUrl') ?? '', '/');

        if (str_starts_with($url, $siteUrl)) {
            return;
        }

        
        $portals = $this->entityManager
            ->getRDBRepositoryByClass(Portal::class)
            ->find();

        foreach ($portals as $portal) {
            $siteUrl = rtrim($portal->getUrl() ?? '', '/');

            if (str_starts_with($url, $siteUrl)) {
                return;
            }
        }

        throw new Forbidden("URL does not match Site URL.");
    }
}
