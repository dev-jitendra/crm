<?php


require_once('../../../bootstrap.php');

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\Api;

(new Application())->run(Api::class);
