<?php


include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Daemon;

(new Application())->run(Daemon::class);
