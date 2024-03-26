<?php


namespace Espo\Core\Utils\Resource;

use Espo\Core\Utils\File\Unifier;
use Espo\Core\Utils\File\UnifierObj;
use Espo\Core\Utils\Resource\Reader\Params;

use stdClass;


class Reader
{
    public function __construct(
        private Unifier $unifier,
        private UnifierObj $unifierObj
    ) {}

    
    public function read(string $path, Params $params): stdClass
    {
        
        return $this->unifierObj->unify($path, $params->noCustom(), $params->getForceAppendPathList());
    }

    
    public function readAsArray(string $path, Params $params): array
    {
        
        return $this->unifier->unify($path, $params->noCustom(), $params->getForceAppendPathList());
    }
}
