<?php


namespace Espo\Core\Authentication\TwoFactor;

use Espo\Core\Authentication\Result;
use Espo\Core\Api\Request;


interface Login
{
    public function login(Result $result, Request $request): Result;
}
