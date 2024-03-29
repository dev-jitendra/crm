<?php

namespace Doctrine\DBAL\Driver\PDO;

use Doctrine\DBAL\Driver\Exception as ExceptionInterface;
use Doctrine\DBAL\Driver\Exception\UnknownParameterType;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;
use Doctrine\Deprecations\Deprecation;
use PDO;
use PDOException;
use PDOStatement;

use function array_slice;
use function func_get_args;
use function func_num_args;

final class Statement implements StatementInterface
{
    private const PARAM_TYPE_MAP = [
        ParameterType::NULL => PDO::PARAM_NULL,
        ParameterType::INTEGER => PDO::PARAM_INT,
        ParameterType::STRING => PDO::PARAM_STR,
        ParameterType::ASCII => PDO::PARAM_STR,
        ParameterType::BINARY => PDO::PARAM_LOB,
        ParameterType::LARGE_OBJECT => PDO::PARAM_LOB,
        ParameterType::BOOLEAN => PDO::PARAM_BOOL,
    ];

    private PDOStatement $stmt;

    
    public function __construct(PDOStatement $stmt)
    {
        $this->stmt = $stmt;
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

        $type = $this->convertParamType($type);

        try {
            return $this->stmt->bindValue($param, $value, $type);
        } catch (PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    
    public function bindParam(
        $param,
        &$variable,
        $type = ParameterType::STRING,
        $length = null,
        $driverOptions = null
    ): bool {
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

        if (func_num_args() > 4) {
            Deprecation::triggerIfCalledFromOutside(
                'doctrine/dbal',
                'https:
                'The $driverOptions argument of Statement::bindParam() is deprecated.',
            );
        }

        $type = $this->convertParamType($type);

        try {
            return $this->stmt->bindParam(
                $param,
                $variable,
                $type,
                $length ?? 0,
                ...array_slice(func_get_args(), 4),
            );
        } catch (PDOException $exception) {
            throw Exception::new($exception);
        }
    }

    
    public function execute($params = null): ResultInterface
    {
        if ($params !== null) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Passing $params to Statement::execute() is deprecated. Bind parameters using'
                    . ' Statement::bindParam() or Statement::bindValue() instead.',
            );
        }

        try {
            $this->stmt->execute($params);
        } catch (PDOException $exception) {
            throw Exception::new($exception);
        }

        return new Result($this->stmt);
    }

    
    private function convertParamType(int $type): int
    {
        if (! isset(self::PARAM_TYPE_MAP[$type])) {
            throw UnknownParameterType::new($type);
        }

        return self::PARAM_TYPE_MAP[$type];
    }
}
