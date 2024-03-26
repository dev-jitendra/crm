<?php


require_once('../../../../bootstrap.php');

use Espo\Core\Portal\Application;
use Espo\Core\Portal\ApplicationRunners\Api;
use Espo\Core\Portal\Utils\Url;

$portalId = Url::detectPortalIdForApi();

if ($portalId === null || $portalId === '') {
    echo "No portal ID";

    exit;
}

(new Application($portalId))->run(Api::class);
