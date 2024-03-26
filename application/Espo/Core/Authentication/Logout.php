<?php


namespace Espo\Core\Authentication;

use Espo\Core\Authentication\AuthToken\AuthToken;
use Espo\Core\Authentication\Logout\Params;
use Espo\Core\Authentication\Logout\Result as Result;


interface Logout
{
    public function logout(AuthToken $authToken, Params $params): Result;
}
