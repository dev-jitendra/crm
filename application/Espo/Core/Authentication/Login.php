<?php


namespace Espo\Core\Authentication;

use Espo\Core\Api\Request;
use Espo\Core\Authentication\Result;
use Espo\Core\Authentication\Login\Data;


interface Login
{
    
    public function login(Data $data, Request $request): Result;
}
