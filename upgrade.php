<?php




if (!str_starts_with(php_sapi_name(), 'cli')) {
    exit;
}

include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Rebuild;
use Espo\Core\Upgrades\UpgradeManager;

$arg = isset($_SERVER['argv'][1]) ? trim($_SERVER['argv'][1]) : '';

if ($arg == 'version' || $arg == '-v') {
    $app = new Application();

    die("Current version is " . $app->getContainer()->get('config')->get('version') . ".\n");
}

if (empty($arg)) {
    die("Upgrade package file is not specified.\n");
}

if (!file_exists($arg)) {
    die("Package file does not exist.\n");
}

$pathInfo = pathinfo($arg);

if (!isset($pathInfo['extension']) || $pathInfo['extension'] !== 'zip' || !is_file($arg)) {
    die("Unsupported package.\n");
}

$app = new Application();

$app->setupSystemUser();

$config = $app->getContainer()->get('config');
$entityManager = $app->getContainer()->get('entityManager');

$upgradeManager = new UpgradeManager($app->getContainer());

echo "Current version is " . $config->get('version') . "\n";
echo "Starting upgrade process...\n";

try {
    $fileData = file_get_contents($arg);
    $fileData = 'data:application/zip;base64,' . base64_encode($fileData);

    $upgradeId = $upgradeManager->upload($fileData);

    $upgradeManager->install(['id' => $upgradeId]);
}
catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

try {
    (new Application())->run(Rebuild::class);
}
catch (\Exception $e) {}

echo "Upgrade is complete. Current version is " . $config->get('version') . ". \n";
