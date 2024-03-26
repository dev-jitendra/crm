<?php




if (!str_starts_with(php_sapi_name(), 'cli')) {
    exit;
}

include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Rebuild;
use Espo\Core\Upgrades\ExtensionManager;

$arg = isset($_SERVER['argv'][1]) ? trim($_SERVER['argv'][1]) : '';

if (empty($arg)) {
    die("Extension package file is not specified.\n");
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

$upgradeManager = new ExtensionManager($app->getContainer());

echo "Starting installation process...\n";

try {
    $fileData = file_get_contents($arg);
    $fileData = 'data:application/zip;base64,' . base64_encode($fileData);

    $upgradeId = $upgradeManager->upload($fileData);
    $upgradeManager->install(array('id' => $upgradeId));
}
catch (\Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}

try {
    (new Application())->run(Rebuild::class);
}
catch (\Exception $e) {}

echo "Extension installation is complete.\n";
