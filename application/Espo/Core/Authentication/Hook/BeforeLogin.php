<?php


namespace Espo\Core\Authentication\Hook;

use Espo\Core\Authentication\AuthenticationData;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ServiceUnavailable;


interface BeforeLogin
{
    public function process(AuthenticationData $data, Request $request): void;
}
