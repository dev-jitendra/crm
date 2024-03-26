<?php


namespace Espo\Core\Upgrades\Actions\Extension;

use Espo\Core\Upgrades\ExtensionManager;
use Espo\Core\Utils\Util;
use Espo\Core\Exceptions\Error;

use Throwable;

class Install extends \Espo\Core\Upgrades\Actions\Base\Install
{
    
    protected $extensionEntity = null;

    
    protected function beforeRunAction()
    {
        $this->findExtension();

        if (!$this->isNew()) {
            $this->scriptParams['isUpgrade'] = true;

            $this->compareVersion();
            $this->uninstallExtension();
        }
    }

    
    protected function afterRunAction()
    {
        if (!$this->isNew()) {
            $this->deleteExtension();
        }

        $this->storeExtension();
    }

    
    protected function backupExistingFiles()
    {
        parent::backupExistingFiles();

        $backupPath = $this->getPath('backupPath');

        
        $packagePath = $this->getPackagePath();

        $source = Util::concatPath($packagePath, self::SCRIPTS);
        $destination = Util::concatPath($backupPath, self::SCRIPTS);

        return $this->copy($source, $destination, true);
    }

    
    protected function isNew()
    {
        $extensionEntity = $this->getExtensionEntity();

        if (isset($extensionEntity)) {
            $id = $extensionEntity->get('id');
        }

        return isset($id) ? false : true;
    }

    
    protected function getExtensionId()
    {
        $extensionEntity = $this->getExtensionEntity();

        if (isset($extensionEntity)) {
            $extensionEntityId = $extensionEntity->get('id');
        }

        if (!isset($extensionEntityId)) {
            return $this->getProcessId();
        }

        return $extensionEntityId;
    }

    
    protected function getExtensionEntity()
    {
        return $this->extensionEntity;
    }

    
    protected function findExtension()
    {
        $manifest = $this->getManifest();

        $this->extensionEntity = $this->getEntityManager()
            ->getRDBRepository('Extension')
            ->where([
                'name' => $manifest['name'],
                'isInstalled' => true,
            ])
            ->findOne();

        return $this->extensionEntity;
    }

    
    protected function storeExtension()
    {
        $entityManager = $this->getEntityManager();

        $extensionEntity = $entityManager->getEntity('Extension', $this->getProcessId());

        if (!isset($extensionEntity)) {
            $extensionEntity = $entityManager->getNewEntity('Extension');
        }

        $manifest = $this->getManifest();
        $fileList = $this->getCopyFileList();

        $data = [
            'id' => $this->getProcessId(),
            'name' => trim($manifest['name']),
            'isInstalled' => true,
            'version' => $manifest['version'],
            'fileList' => $fileList,
            'description' => $manifest['description'],
        ];

        if (!empty($manifest['checkVersionUrl'])) {
            $data['checkVersionUrl'] = $manifest['checkVersionUrl'];
        }

        $extensionEntity->set($data);

        try {
            $entityManager->saveEntity($extensionEntity);
        }
        catch (Throwable $e) {
            $this->getLog()
                ->error(
                    'Error saving Extension entity. The error occurred by existing Hook, more details: ' .
                    $e->getMessage() .' at '. $e->getFile() . ':' . $e->getLine()
                );

            $this->throwErrorAndRemovePackage('Error saving Extension entity. Check logs for details.', false);
        }
    }

    
    protected function compareVersion()
    {
        $manifest = $this->getManifest();
        $extensionEntity = $this->getExtensionEntity();

        if (isset($extensionEntity)) {
            $comparedVersion = version_compare($manifest['version'], $extensionEntity->get('version'), '>=');
            if ($comparedVersion <= 0) {
                $this->throwErrorAndRemovePackage('You cannot install an older version of this extension.');
            }
        }
    }

    
    protected function uninstallExtension()
    {
        $extensionEntity = $this->getExtensionEntity();

        if (!$extensionEntity) {
            throw new Error("Can't unistall not existing extension.");
        }

        $this->executeAction(ExtensionManager::UNINSTALL, [
            'id' => $extensionEntity->get('id'),
            'skipSystemRebuild' => true,
            'skipAfterScript' => true,
            'parentProcessId' => $this->getProcessId(),
        ]);
    }

    
    protected function deleteExtension()
    {
        $extensionEntity = $this->getExtensionEntity();

        if (!$extensionEntity) {
            throw new Error("Can't delete not existing extension.");
        }

        $this->executeAction(ExtensionManager::DELETE, [
            'id' => $extensionEntity->get('id'),
            'parentProcessId' => $this->getProcessId(),
        ]);
    }

    
    protected function checkDependencies($dependencyList)
    {
        return $this->getHelper()->checkDependencies($dependencyList);
    }
}
