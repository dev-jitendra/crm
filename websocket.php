<?php


include "bootstrap.php";

use Espo\Core\Application;
use Espo\Core\ApplicationRunners\WebSocket;

(new Application())->run(WebSocket::class);
