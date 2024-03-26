<?php


include "../../bootstrap.php";

use Espo\Core\Application;
use Espo\Core\Application\Runner\Params;
use Espo\Core\ApplicationRunners\EntryPoint;

$app = new Application();

$app->run(
    EntryPoint::class,
    Params::create()->with('entryPoint', 'oauthCallback')
);
