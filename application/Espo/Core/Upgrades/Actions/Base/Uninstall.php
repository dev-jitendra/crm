<?php


namespace Espo\Core\Upgrades\Actions\Base;

use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Util;
use Espo\Core\Utils\Json;

class Uninstall extends \Espo\Core\Upgrades\Actions\Base
{
    
    public function run($data)
    {
        $processId = $data['id'];

        $this->getLog()->debug('Uninstallation process ['.$processId.']: start run.');

        if (empty($processId)) {
            throw new Error('Uninstallation package ID was not specified.');
        }

        $this->setProcessId($processId);

        if (isset($data['parentProcessId'])) {
            $this->setParentProcessId($data['parentProcessId']);
        }

        $this->initialize();

        $this->checkIsWritable();

        $this->enableMaintenanceMode();

        $this->beforeRunAction();

        
        if (!isset($data['skipBeforeScript']) || !$data['skipBeforeScript']) {
            $this->runScript('beforeUninstall');
        }

        $backupPath = $this->getPath('backupPath');
        if (file_exists($backupPath)) {
            
            if (!$this->copyFiles()) {
                $this->throwErrorAndRemovePackage('Cannot copy files.');
            }
        }

        
        if (!$this->deleteFiles('delete', true)) {
            $this->throwErrorAndRemovePackage('Permission denied to delete files.');
        }

        $this->disableMaintenanceMode();

        if (!isset($data['skipSystemRebuild']) || !$data['skipSystemRebuild']) {
            if (!$this->systemRebuild()) {
                $this->throwErrorAndRemovePackage(
                    'Error occurred while EspoCRM rebuild. Please see the log for more detail.'
                );
            }
        }

        
        if (!isset($data['skipAfterScript']) || !$data['skipAfterScript']) {
            $this->runScript('afterUninstall');
        }

        $this->afterRunAction();

        
        $this->deletePackageFiles();

        $this->finalize();

        $this->getLog()->debug('Uninstallation process ['.$processId.']: end run.');

        $this->clearCache();
    }

    
    protected function restoreFiles()
    {
        $packagePath = $this->getPath('packagePath');

        $manifestPath = Util::concatPath($packagePath, $this->manifestName);

        if (!file_exists($manifestPath)) {
            $this->unzipArchive($packagePath);
        }

        $fileDirs = $this->getFileDirs($packagePath);

        $res = true;

        foreach ($fileDirs as $filesPath) {
            if (file_exists($filesPath)) {
                $res = $this->copy($filesPath, '', true);
            }
        }

        $manifestJson = $this->getFileManager()->getContents($manifestPath);
        $manifest = Json::decode($manifestJson, true);

        if (!empty($manifest['delete'])) {
            $res &= $this->getFileManager()->remove($manifest['delete'], null, true);
        }

        $res &= $this->getFileManager()->removeInDir($packagePath, true);

        return $res;
    }

    
    protected function copyFiles($type = null, $dest = '')
    {
        $backupPath = $this->getPath('backupPath');

        $source = Util::concatPath($backupPath, self::FILES);

        $res = $this->copy($source, $dest, true);

        return $res;
    }

    
    protected function getPackagePath($isPackage = false)
    {
        if ($isPackage) {
            return $this->getPath('packagePath', $isPackage);
        }

        return $this->getPath('backupPath');
    }

    
    protected function deletePackageFiles()
    {
        $backupPath = $this->getPath('backupPath');
        $res = $this->getFileManager()->removeInDir($backupPath, true);

        return $res;
    }

    
    public function throwErrorAndRemovePackage($errorMessage = '', $deletePackage = true, $systemRebuild = true)
    {
        $this->restoreFiles();

        parent::throwErrorAndRemovePackage($errorMessage, false, $systemRebuild);
    }

    
    protected function getCopyFileList()
    {
        if (!isset($this->data['fileList'])) {
            $backupPath = $this->getPath('backupPath');
            $filesPath = Util::concatPath($backupPath, self::FILES);

            $this->data['fileList'] = $this->getFileManager()->getFileList($filesPath, true, '', true, true);
        }

        return $this->data['fileList'];
    }

    
    protected function getRestoreFileList()
    {
        if (!isset($this->data['restoreFileList'])) {
            $packagePath = $this->getPackagePath();
            $filesPath = Util::concatPath($packagePath, self::FILES);

            if (!file_exists($filesPath)) {
                $this->unzipArchive($packagePath);
            }

            $this->data['restoreFileList'] = $this->getFileManager()->getFileList($filesPath, true, '', true, true);
        }

        return $this->data['restoreFileList'];
    }

    
    protected function getDeleteList($type = 'delete')
    {
        if ($type == 'delete') {
            $packageFileList = $this->getRestoreFileList();
            $backupFileList = $this->getCopyFileList();

            return array_diff($packageFileList, $backupFileList);
        }

        return [];
    }
}
