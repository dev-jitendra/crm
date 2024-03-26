<?php

namespace Doctrine\DBAL\Driver\SQLSrv;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Driver\Result as ResultInterface;
use Doctrine\DBAL\Driver\SQLSrv\Exception\Error;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;
use Doctrine\Deprecations\Deprecation;

use function assert;
use function func_num_args;
use function is_int;
use function sqlsrv_execute;
use function SQLSRV_PHPTYPE_STREAM;
use function SQLSRV_PHPTYPE_STRING;
use function sqlsrv_prepare;
use function SQLSRV_SQLTYPE_VARBINARY;
use function stripos;

use const SQLSRV_ENC_BINARY;
use const SQLSRV_ENC_CHAR;
use const SQLSRV_PARAM_IN;

final class Statement implements StatementInterface
{
    
    private $conn;

    
    private string $sql;

    
    private $stmt;

    
    private array $variables = [];

    
    private array $types = [];

    
    private const LAST_INSERT_ID_SQL = ';SELECT SCOPE_IDENTITY() AS LastInsertId;';

    
    public function __construct($conn, $sql)
    {
        $this->conn = $conn;
        $this->sql  = $sql;

        if (stripos($sql, 'INSERT INTO ') !== 0) {
            return;
        }

        $this->sql .= self::LAST_INSERT_ID_SQL;
    }

    
    public function bindValue($param, $value, $type = ParameterType::STRING): bool
    {
        assert(is_int($param));

        if (func_num_args() < 3) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Not passing $type to Statement::bindValue() is deprecated.'
                    . ' Pass the type corresponding to the parameter being bound.',
            );
        }

        $this->variables[$param] = $value;
        $this->types[$param]     = $type;

        return true;
    }

    
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null): bool
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            '%s is deprecated. Use bindValue() instead.',
            __METHOD__,
        );

        assert(is_int($param));

        if (func_num_args() < 3) {
            Deprecation::trigger(
                'doctrine/dbal',
                'https:
                'Not passing $type to Statement::bindParam() is deprecated.'
                    . ' Pass the type corresponding to the parameter being bound.',
            );
        }

        $this->variables[$param] =& $variable;
        $this->types[$param]     = $type;

        
        $this->stmt = null;

        return true;
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

            foreach ($params as $key => $val) {
                if (is_int($key)) {
                    $this->bindValue($key + 1, $val, ParameterType::STRING);
                } else {
                    $this->bindValue($key, $val, ParameterType::STRING);
                }
            }
        }

        $this->stmt ??= $this->prepare();

        if (! sqlsrv_execute($this->stmt)) {
            throw Error::new();
        }

        return new Result($this->stmt);
    }

    
    private function prepare()
    {
        $params = [];

        foreach ($this->variables as $column => &$variable) {
            switch ($this->types[$column]) {
                case ParameterType::LARGE_OBJECT:
                    $params[$column - 1] = [
                        &$variable,
                        SQLSRV_PARAM_IN,
                        SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY),
                        SQLSRV_SQLTYPE_VARBINARY('max'),
                    ];
                    break;

                case ParameterType::BINARY:
                    $params[$column - 1] = [
                        &$variable,
                        SQLSRV_PARAM_IN,
                        SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_BINARY),
                    ];
                    break;

                case ParameterType::ASCII:
                    $params[$column - 1] = [
                        &$variable,
                        SQLSRV_PARAM_IN,
                        SQLSRV_PHPTYPE_STRING(SQLSRV_ENC_CHAR),
                    ];
                    break;

                default:
                    $params[$column - 1] =& $variable;
                    break;
            }
        }

        $stmt = sqlsrv_prepare($this->conn, $this->sql, $params);

        if ($stmt === false) {
            throw Error::new();
        }

        return $stmt;
    }
}
