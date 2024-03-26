<?php


namespace Espo\Core\Rebuild\Actions;

use Espo\Core\Rebuild\RebuildAction;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Util;


class GenerateInstanceId implements RebuildAction
{
    public function __construct(
        private Config $config,
        private Config\ConfigWriter $configWriter
    ) {}

    public function process(): void
    {
        if ($this->config->get('instanceId')) {
            return;
        }

        $id = Util::generateUuid4();

        $this->configWriter->set('instanceId', $id);
        $this->configWriter->save();
    }
}
