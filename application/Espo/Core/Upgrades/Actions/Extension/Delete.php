<?php


namespace Espo\Core\Upgrades\Actions\Extension;
use Espo\Core\Exceptions\Error;

class Delete extends \Espo\Core\Upgrades\Actions\Base\Delete
{
    
    protected $extensionEntity;

    
    protected function getExtensionEntity()
    {
        if (!isset($this->extensionEntity)) {
            $processId = $this->getProcessId();
            $this->extensionEntity = $this->getEntityManager()->getEntity('Extension', $processId);
            if (!isset($this->extensionEntity)) {
                throw new Error('Extension Entity not found.');
            }
        }

        return $this->extensionEntity;
    }

    
    protected function afterRunAction()
    {
        
        $extensionEntity = $this->getExtensionEntity();

        $this->getEntityManager()->removeEntity($extensionEntity);
    }
}
