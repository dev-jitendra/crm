<?php


namespace Espo\EntryPoints;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\EntryPoint\Traits\NoAuth;


class OauthCallback implements EntryPoint
{
    use NoAuth;

    public function run(Request $request, Response $response): void
    {
        echo "If this window is not closed automatically, it's probable that the URL you use to access ".
            "EspoCRM doesn't match the URL specified at Administration > Settings > Site URL.";
    }
}
