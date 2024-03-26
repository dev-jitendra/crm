<?php


namespace Espo\Core\Action;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;

interface Action
{
    
    public function process(Params $params, Data $data): void;
}
