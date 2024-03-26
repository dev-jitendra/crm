<?php


namespace Espo\Core\Formula;

use Espo\Core\Formula\Exceptions\Error;


interface Func
{
    
    public function process(EvaluatedArgumentList $arguments): mixed;
}
