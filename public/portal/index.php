<?php


include "../../bootstrap.php";

use Espo\Core\Application;
use Espo\Core\Application\Runner\Params;
use Espo\Core\ApplicationRunners\EntryPoint;
use Espo\Core\ApplicationRunners\PortalClient;
use Espo\Core\Portal\Utils\Url;

$app = new Application();

if (!$app->isInstalled()) {
    exit;
}

$basePath = null;

if (Url::detectIsInPortalDir()) {
    $basePath = '../';

    if (Url::detectIsInPortalWithId()) {
        $basePath = '../../';
    }

    $app->setClientBasePath($basePath);
}

if (filter_has_var(INPUT_GET, 'entryPoint')) {
    $app->run(EntryPoint::class);

    exit;
}

$app->run(
    PortalClient::class,
    Params::create()->with('basePath', $basePath)
);
