<?php


include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Preload;

(new Application())->run(Preload::class);
