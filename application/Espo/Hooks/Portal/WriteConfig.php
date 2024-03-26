<?php


namespace Espo\Hooks\Portal;

use Espo\ORM\Entity;
use Espo\Entities\Portal;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Config\ConfigWriter;

class WriteConfig
{
    public function __construct(private Config $config, private ConfigWriter $configWriter)
    {}

    
    public function afterSave(Entity $entity): void
    {
        if (!$entity->has('isDefault')) {
            return;
        }

        if ($entity->get('isDefault')) {
            $defaultPortalId = $this->config->get('defaultPortalId');

            if ($defaultPortalId === $entity->getId()) {
                return;
            }

            $this->configWriter->set('defaultPortalId', $entity->getId());

            $this->configWriter->save();
        }

        if ($entity->isAttributeChanged('isDefault') && $entity->getFetched('isDefault')) {
            $this->configWriter->set('defaultPortalId', null);

            $this->configWriter->save();
        }
    }
}
