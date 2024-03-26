<?php


namespace Espo\Core\Console\Commands;

use Espo\Core\Application;
use Espo\Core\Console\Command;
use Espo\Core\Console\Command\Params;
use Espo\Core\Console\IO;
use Espo\Core\Upgrades\UpgradeManager;

use Exception;

class UpgradeStep implements Command
{
    public function __construct() {}

    public function run(Params $params, IO $io): void
    {
        $options = $params->getOptions();

        if (empty($options['step'])) {
            echo "Step is not specified.\n";

            return;
        }

        if (empty($options['id'])) {
            echo "Upgrade ID is not specified.\n";

            return;
        }

        $stepName = $options['step'];
        $upgradeId = $options['id'];

        $result = $this->runUpgradeStep($stepName, ['id' => $upgradeId]);

        if (!$result) {
            echo "false";

            return;
        }

        echo "true";
    }

    
    private function runUpgradeStep(string $stepName, array $params): bool
    {
        $app = new Application();

        $app->setupSystemUser();

        $upgradeManager = new UpgradeManager($app->getContainer());

        try {
            $result = $upgradeManager->runInstallStep($stepName, $params);
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }

        return $result;
    }
}
