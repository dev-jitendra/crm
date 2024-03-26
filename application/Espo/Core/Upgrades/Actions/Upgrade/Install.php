<?php


namespace Espo\Core\Upgrades\Actions\Upgrade;

class Install extends \Espo\Core\Upgrades\Actions\Base\Install
{
    
    public function stepBeforeUpgradeScript(array $data)
    {
        
        return $this->stepBeforeInstallScript($data);
    }

    
    public function stepAfterUpgradeScript(array $data)
    {
        
        return $this->stepAfterInstallScript($data);
    }

    
    protected function finalize()
    {
        $manifest = $this->getManifest();

        $configWriter = $this->createConfigWriter();

        $configWriter->set('version', $manifest['version']);

        $configWriter->save();
    }

    
    protected function deletePackageFiles()
    {
        $res = parent::deletePackageFiles();

        $res &= $this->deletePackageArchive();

        

        return $res;
    }
}
