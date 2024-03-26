<?php


namespace Espo\Hooks\Integration;

use Espo\ORM\Entity;

use Espo\Entities\Integration;

use Espo\Core\Utils\Config\ConfigWriter;

class GoogleMaps
{
    private ConfigWriter $configWriter;

    public function __construct(ConfigWriter $configWriter)
    {
        $this->configWriter = $configWriter;
    }

    
    public function afterSave(Entity $entity): void
    {
        if ($entity->getId() !== 'GoogleMaps') {
            return;
        }

        if (!$entity->get('enabled') || !$entity->get('apiKey')) {
            $this->configWriter->set('googleMapsApiKey', null);

            $this->configWriter->save();

            return;
        }

        $this->configWriter->set('googleMapsApiKey', $entity->get('apiKey'));

        $this->configWriter->save();
    }
}
