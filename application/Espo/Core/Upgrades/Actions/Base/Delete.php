<?php


namespace Espo\Core\Upgrades\Actions\Base;

use Espo\Core\Exceptions\Error;

class Delete extends \Espo\Core\Upgrades\Actions\Base
{
    
    public function run($data)
    {
        $processId = $data['id'];

        $this->getLog()->debug('Delete package process ['.$processId.']: start run.');

        if (empty($processId)) {
            throw new Error('Delete package package ID was not specified.');
        }

        $this->initialize();

        $this->setProcessId($processId);

        if (isset($data['parentProcessId'])) {
            $this->setParentProcessId($data['parentProcessId']);
        }

        $this->beforeRunAction();

        
        $this->deletePackage();

        $this->afterRunAction();

        $this->finalize();

        $this->getLog()->debug('Delete package process ['.$processId.']: end run.');
    }

    
    protected function deletePackage()
    {
        $packageArchivePath = $this->getPackagePath(true);

        $res = $this->getFileManager()->removeFile($packageArchivePath);

        return $res;
    }
}
