<?php

namespace Doctrine\DBAL\Driver;

use Doctrine\DBAL\ParameterType;


interface Statement
{
    
    public function bindValue($param, $value, $type = ParameterType::STRING);

    
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null);

    
    public function execute($params = null): Result;
}
