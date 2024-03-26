<?php


namespace Espo\Core\Portal;

use Espo\Entities\Portal;
use Espo\Core\InjectableFactory;

use LogicException;


class AclManagerContainer
{
    
    private $data = [];

    public function __construct(private InjectableFactory $injectableFactory)
    {}

    public function get(Portal $portal): AclManager
    {
        if (!$portal->hasId()) {
            throw new LogicException("AclManagerContainer: portal should have ID.");
        }

        $id = $portal->getId();

        if (!isset($this->data[$id])) {
            $aclManager = $this->injectableFactory->create(AclManager::class);
            $aclManager->setPortal($portal);

            $this->data[$id] = $aclManager;
        }

        return $this->data[$id];
    }
}
