<?php


namespace Espo\Core\Authentication\Hook;

use Espo\Core\Authentication\AuthenticationData;
use Espo\Core\Api\Request;
use Espo\Core\Authentication\Result;


interface OnResult
{
    public function process(Result $result, AuthenticationData $data, Request $request): void;
}
