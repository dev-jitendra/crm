<?php


include "../bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Client;
use Espo\Core\ApplicationRunners\EntryPoint;

$app = new Application();

if (filter_has_var(INPUT_GET, 'entryPoint')) {
    $app->run(EntryPoint::class);

    exit;
}

$app->run(Client::class);
