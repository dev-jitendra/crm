<?php


include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\ClearCache;

(new Application())->run(ClearCache::class);
