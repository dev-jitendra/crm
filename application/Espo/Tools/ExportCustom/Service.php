<?php


namespace Espo\Tools\ExportCustom;

use Espo\Core\Utils\Config\ConfigWriter;

class Service
{
    public function __construct(
        private ConfigWriter $configWriter
    ) {}

    public function storeToConfig(Params $params): void
    {
        $this->configWriter->set('customExportManifest', (object) [
            'name' => $params->getName(),
            'version' => $params->getVersion(),
            'description' => $params->getDescription(),
            'author' => $params->getAuthor(),
            'module' => $params->getModule(),
        ]);

        $this->configWriter->save();
    }
}
