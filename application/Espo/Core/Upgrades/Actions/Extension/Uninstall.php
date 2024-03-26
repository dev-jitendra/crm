<?php


namespace Espo\Core\Upgrades\Actions\Extension;

use Espo\Core\Exceptions\Error;

use Throwable;

class Uninstall extends \Espo\Core\Upgrades\Actions\Base\Uninstall
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
        $extensionEntity->set('isInstalled', false);

        try {
            $this->getEntityManager()->saveEntity($extensionEntity);
        }
        catch (Throwable $e) {
            $this->getLog()->error(
                'Error saving Extension entity. The error occurred by existing Hook, more details: ' .
                $e->getMessage() .' at '. $e->getFile() . ':' . $e->getLine()
            );

            $this->throwErrorAndRemovePackage('Error saving Extension entity. Check logs for details.', false);
        }
    }

    
    protected function getRestoreFileList()
    {
        if (!isset($this->data['restoreFileList'])) {
            $extensionEntity = $this->getExtensionEntity();
            $this->data['restoreFileList'] = $extensionEntity->get('fileList');
        }

        return $this->data['restoreFileList'];
    }
}
