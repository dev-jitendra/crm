<?php


include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Cron;

(new Application())->run(Cron::class);
