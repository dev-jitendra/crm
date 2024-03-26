<?php


namespace Espo\Core\Portal;

use Espo\Core\Container\Exceptions\NotSettableException;
use Espo\Entities\Portal as PortalEntity;
use Espo\Core\Portal\Utils\Config;
use Espo\Core\Container as BaseContainer;

use Psr\Container\NotFoundExceptionInterface;

use LogicException;

class Container extends BaseContainer
{
    private const ID_PORTAL = 'portal';
    private const ID_CONFIG = 'config';
    private const ID_ACL_MANAGER = 'aclManager';

    private bool $portalIsSet = false;

    
    public function setPortal(PortalEntity $portal): void
    {
        if ($this->portalIsSet) {
            throw new NotSettableException("Can't set portal second time.");
        }

        $this->portalIsSet = true;

        $this->setForced(self::ID_PORTAL, $portal);

        $data = [];

        foreach ($portal->getSettingsAttributeList() as $attribute) {
            $data[$attribute] = $portal->get($attribute);
        }

        try {
            
            $config = $this->get(self::ID_CONFIG);
            $config->setPortalParameters($data);

            
            $aclManager = $this->get(self::ID_ACL_MANAGER);
        }
        catch (NotFoundExceptionInterface) {
            throw new LogicException();
        }

        $aclManager->setPortal($portal);
    }
}
