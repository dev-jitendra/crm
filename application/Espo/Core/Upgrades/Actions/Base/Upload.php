<?php


namespace Espo\Core\Upgrades\Actions\Base;

use Espo\Core\Exceptions\Error;

class Upload extends \Espo\Core\Upgrades\Actions\Base
{
    
    public function run($data)
    {
        $processId = $this->createProcessId();

        $this->getLog()->debug('Installation process ['.$processId.']: start upload the package.');

        $this->initialize();
        $this->beforeRunAction();

        $packageArchivePath = $this->getPackagePath(true);

        $contents = null;

        if (!empty($data)) {
            list($prefix, $contents) = explode(',', $data);

            $contents = base64_decode($contents);
        }

        $res = $this->getFileManager()->putContents($packageArchivePath, $contents);

        if ($res === false) {
            throw new Error('Could not upload the package.');
        }

        $this->unzipArchive();
        $this->isAcceptable();
        $this->afterRunAction();
        $this->finalize();

        $this->getLog()->debug('Installation process ['.$processId.']: end upload the package.');

        return $processId;
    }
}
