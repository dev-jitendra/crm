<?php


namespace Espo\Core\MassAction;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;

interface MassAction
{
    
    public function process(Params $params, Data $data): Result;
}
