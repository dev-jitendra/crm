<?php


include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Rebuild;

(new Application())->run(Rebuild::class);
