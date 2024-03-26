<?php


namespace Espo\Entities;

use Espo\Core\Field\Link;
use Espo\Repositories\Portal as PortalRepository;

class Portal extends \Espo\Core\ORM\Entity
{
    public const ENTITY_TYPE = 'Portal';

    
    protected $settingsAttributeList = [
        'companyLogoId',
        'tabList',
        'quickCreateList',
        'dashboardLayout',
        'dashletsOptions',
        'theme',
        'themeParams',
        'language',
        'timeZone',
        'dateFormat',
        'timeFormat',
        'weekStart',
        'defaultCurrency',
    ];

    
    public function getSettingsAttributeList(): array
    {
        return $this->settingsAttributeList;
    }

    public function getUrl(): ?string
    {
        if (!$this->has('url') && $this->entityManager) {
            
            $repository = $this->entityManager->getRDBRepositoryByClass(Portal::class);

            $repository->loadUrlField($this);
        }

        return $this->get('url');
    }

    public function getAuthenticationProvider(): ?Link
    {
        
        return $this->getValueObject('authenticationProvider');
    }

    public function getLayoutSet(): ?Link
    {
        
        return $this->getValueObject('layoutSet');
    }
}
