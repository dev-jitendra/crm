<?php


namespace Espo\Repositories;

use Espo\Entities\Portal as PortalEntity;

use Espo\Core\Repositories\Database;

use Espo\Core\Di;


class Portal extends Database implements

    Di\ConfigAware
{
    use Di\ConfigSetter;

    public function loadUrlField(PortalEntity $entity): void
    {
        if ($entity->get('customUrl')) {
            $entity->set('url', $entity->get('customUrl'));
        }

        $siteUrl = $this->config->get('siteUrl');
        $siteUrl = rtrim($siteUrl , '/') . '/';

        $url = $siteUrl . 'portal/';

        if ($entity->getId() === $this->config->get('defaultPortalId')) {
            $entity->set('isDefault', true);
            $entity->setFetched('isDefault', true);
        }
        else {
            if ($entity->get('customId')) {
                $url .= $entity->get('customId') . '/';
            } else {
                $url .= $entity->getId() . '/';
            }

            $entity->set('isDefault', false);
            $entity->setFetched('isDefault', false);
        }

        if (!$entity->get('customUrl')) {
            $entity->set('url', $url);
        }
    }
}
