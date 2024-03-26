<?php

namespace Doctrine\DBAL\Driver\Middleware;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;
use Doctrine\Deprecations\Deprecation;

use function func_num_args;

abstract class AbstractStatementMiddleware implements Statement
{
    private Statement $wrappedStatement;

    public function __construct(Statement $wrappedStatement)
    {
        $this->wrappedStatement = $wrappedStatement;
    }

    
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        if (func_num_args() < 3) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Not passing $type to Statement::bindValue() is deprecated.'
                    . ' Pass the type corresponding to the parameter being bound.',
            );
        }

        return $this->wrappedStatement->bindValue($param, $value, $type);
    }

    
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated. Use bindValue() instead.',
            __METHOD__,
        );

        if (func_num_args() < 3) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Not passing $type to Statement::bindParam() is deprecated.'
                    . ' Pass the type corresponding to the parameter being bound.',
            );
        }

        return $this->wrappedStatement->bindParam($param, $variable, $type, $length);
    }

    
    public function execute($params = null): Result
    {
        return $this->wrappedStatement->execute($params);
    }
}
