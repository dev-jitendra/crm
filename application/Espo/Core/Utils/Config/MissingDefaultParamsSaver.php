<?php


namespace Espo\Core\Utils\Config;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\File\Manager as FileManager;

use RuntimeException;

class MissingDefaultParamsSaver
{
    private string $defaultConfigPath = 'application/Espo/Resources/defaults/config.php';

    public function __construct(
        private Config $config,
        private ConfigWriter $configWriter,
        private FileManager $fileManager
    ) {}

    public function process(): void
    {
        $data = $this->fileManager->getPhpSafeContents($this->defaultConfigPath);

        if (!is_array($data)) {
            throw new RuntimeException();
        }

        

        $newData = [];

        foreach ($data as $param => $value) {
            if ($this->config->has($param)) {
                continue;
            }

            $newData[$param] = $value;
        }

        if (!count($newData)) {
            return;
        }

        $this->configWriter->setMultiple($newData);
        $this->configWriter->save();
    }
}
