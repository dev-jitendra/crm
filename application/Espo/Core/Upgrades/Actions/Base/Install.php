<?php


namespace Espo\Core\Upgrades\Actions\Base;

use Espo\Core\Exceptions\Error;
use Espo\Core\Utils\Util;

class Install extends \Espo\Core\Upgrades\Actions\Base
{
    
    public function run($data)
    {
        $processId = $data['id'];

        $this->getLog()->debug('Installation process ['.$processId.']: start run.');

        $this->stepInit($data);

        $this->stepCopyBefore($data);

        if ($this->getCopyFilesPath('before')) {
            $this->stepRebuild($data);
        }

        $this->stepBeforeInstallScript($data);

        if ($this->getScriptPath('before')) {
            $this->stepRebuild($data);
        }

        $this->stepCopy($data);
        $this->stepRebuild($data);

        $this->stepCopyAfter($data);

        if ($this->getCopyFilesPath('after')) {
            $this->stepRebuild($data);
        }

        $this->stepAfterInstallScript($data);
        if ($this->getScriptPath('after')) {
            $this->stepRebuild($data);
        }

        $this->stepFinalize($data);

        $this->getLog()->debug('Installation process ['.$processId.']: end run.');
    }

    
    protected function initPackage(array $data)
    {
        $processId = $data['id'];

        if (empty($processId)) {
            throw new Error('Installation package ID was not specified.');
        }

        $this->setProcessId($processId);

        if (isset($data['parentProcessId'])) {
            $this->setParentProcessId($data['parentProcessId']);
        }

        
        $packagePath = $this->getPackagePath();
        if (!file_exists($packagePath)) {
            $this->unzipArchive();
            $this->isAcceptable();
        }
    }

    
    public function stepInit(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "init" step.');

        if (!$this->systemRebuild()) {
            $this->throwErrorAndRemovePackage('Rebuild is failed. Fix all errors before upgrade.');
        }

        $this->initialize();
        $this->checkIsWritable();
        $this->enableMaintenanceMode();
        $this->beforeRunAction();
        $this->backupExistingFiles();

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "init" step.');
    }

    
    public function stepCopyBefore(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "copyBefore" step.');

        if (!$this->copyFiles('before')) {
            $this->throwErrorAndRemovePackage('Cannot copy beforeInstall files.');
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "copyBefore" step.');
    }

    
    public function stepBeforeInstallScript(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "beforeInstallScript" step.');

        if (!isset($data['skipBeforeScript']) || !$data['skipBeforeScript']) {
            $this->runScript('before');
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "beforeInstallScript" step.');
    }

    
    public function stepCopy(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "copy" step.');

        
        if (!$this->deleteFiles('delete', true)) {
            $this->throwErrorAndRemovePackage('Cannot delete files.');
        }

        
        if (!$this->copyFiles()) {
            $this->throwErrorAndRemovePackage('Cannot copy files.');
        }

        if (!$this->deleteFiles('vendor')) {
            $this->throwErrorAndRemovePackage('Cannot delete vendor files.');
        }

        if (!$this->copyFiles('vendor')) {
            $this->throwErrorAndRemovePackage('Cannot copy vendor files.');
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "copy" step.');
    }

    
    public function stepRebuild(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "rebuild" step.');

        if (!isset($data['skipSystemRebuild']) || !$data['skipSystemRebuild']) {
            if (!$this->systemRebuild()) {
                $this->throwErrorAndRemovePackage('Error occurred while EspoCRM rebuild. Please see the log for more detail.');
            }
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "rebuild" step.');
    }

    
    public function stepCopyAfter(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "copyAfter" step.');

        
        if (!$this->copyFiles('after')) {
            $this->throwErrorAndRemovePackage('Cannot copy afterInstall files.');
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "copyAfter" step.');
    }

    
    public function stepAfterInstallScript(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "afterInstallScript" step.');

        
        if (!isset($data['skipAfterScript']) || !$data['skipAfterScript']) {
            $this->runScript('after');
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "afterInstallScript" step.');
    }

    
    public function stepFinalize(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "finalize" step.');

        $this->disableMaintenanceMode();
        $this->afterRunAction();
        $this->finalize();

        
        $this->deletePackageFiles();

        if ($this->getManifestParam('skipBackup')) {

            $path = Util::concatPath($this->getPath('backupPath'), self::FILES);

            $this->getFileManager()->removeInDir($path);
        }

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "finalize" step.');
    }

    
    public function stepRevert(array $data)
    {
        $this->initPackage($data);

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: Start "revert" step.');

        $this->restoreFiles();

        $this->getLog()->info('Installation process ['. $this->getProcessId() .']: End "revert" step.');
    }

    
    protected function restoreFiles()
    {
        $this->getLog()->info('Installer: Restore previous files.');

        $backupPath = $this->getPath('backupPath');
        $backupFilePath = Util::concatPath($backupPath, self::FILES);

        if (!file_exists($backupFilePath)) {
            return true;
        }

        $backupFileList = $this->getRestoreFileList();
        $copyFileList = $this->getCopyFileList();
        $deleteFileList = array_diff($copyFileList, $backupFileList);

        $res = $this->copy($backupFilePath, '', true);

        if (!empty($deleteFileList)) {
            $res &= $this->getFileManager()->remove($deleteFileList, null, true);
        }

        if ($res) {
            $this->getFileManager()->removeInDir($backupPath, true);
        }

        return (bool) $res;
    }

    
    public function throwErrorAndRemovePackage($errorMessage = '', $deletePackage = true, $systemRebuild = true)
    {
        $this->restoreFiles();

        parent::throwErrorAndRemovePackage($errorMessage, $deletePackage, $systemRebuild);
    }
}
