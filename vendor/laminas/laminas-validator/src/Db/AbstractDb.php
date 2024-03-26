<?php

namespace Laminas\Validator\Db;

use Laminas\Db\Adapter\Adapter as DbAdapter;
use Laminas\Db\Adapter\AdapterAwareInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\TableIdentifier;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Validator\AbstractValidator;
use Laminas\Validator\Exception;
use Laminas\Validator\Exception\InvalidArgumentException;
use Laminas\Validator\Exception\RuntimeException;
use Traversable;

use function array_key_exists;
use function array_shift;
use function func_get_args;
use function func_num_args;
use function is_array;


abstract class AbstractDb extends AbstractValidator implements AdapterAwareInterface
{
    use AdapterAwareTrait;

    
    public const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    public const ERROR_RECORD_FOUND    = 'recordFound';

    
    protected $messageTemplates = [
        self::ERROR_NO_RECORD_FOUND => 'No record matching the input was found',
        self::ERROR_RECORD_FOUND    => 'A record matching the input was found',
    ];

    
    protected $select;

    
    protected $schema;

    
    protected $table = '';

    
    protected $field = '';

    
    protected $exclude;

    
    public function __construct($options = null)
    {
        parent::__construct($options);

        if ($options instanceof Select) {
            $this->setSelect($options);
            return;
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (func_num_args() > 1) {
            $options       = func_get_args();
            $firstArgument = array_shift($options);
            if (is_array($firstArgument)) {
                $temp = ArrayUtils::iteratorToArray($firstArgument);
            } else {
                $temp['table'] = $firstArgument;
            }

            $temp['field'] = array_shift($options);

            if (! empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (! empty($options)) {
                $temp['adapter'] = array_shift($options);
            }

            $options = $temp;
        }

        if (! array_key_exists('table', $options) && ! array_key_exists('schema', $options)) {
            throw new Exception\InvalidArgumentException('Table or Schema option missing!');
        }

        if (! array_key_exists('field', $options)) {
            throw new Exception\InvalidArgumentException('Field option missing!');
        }

        if (array_key_exists('adapter', $options)) {
            $this->setAdapter($options['adapter']);
        }

        if (array_key_exists('exclude', $options)) {
            $this->setExclude($options['exclude']);
        }

        $this->setField($options['field']);
        if (array_key_exists('table', $options)) {
            $this->setTable($options['table']);
        }

        if (array_key_exists('schema', $options)) {
            $this->setSchema($options['schema']);
        }
    }

    
    public function getAdapter()
    {
        return $this->adapter;
    }

    
    public function setAdapter(DbAdapter $adapter)
    {
        return $this->setDbAdapter($adapter);
    }

    
    public function getExclude()
    {
        return $this->exclude;
    }

    
    public function setExclude($exclude)
    {
        $this->exclude = $exclude;
        $this->select  = null;
        return $this;
    }

    
    public function getField()
    {
        return $this->field;
    }

    
    public function setField($field)
    {
        $this->field  = (string) $field;
        $this->select = null;
        return $this;
    }

    
    public function getTable()
    {
        return $this->table;
    }

    
    public function setTable($table)
    {
        $this->table  = (string) $table;
        $this->select = null;
        return $this;
    }

    
    public function getSchema()
    {
        return $this->schema;
    }

    
    public function setSchema($schema)
    {
        $this->schema = $schema;
        $this->select = null;
        return $this;
    }

    
    public function setSelect(Select $select)
    {
        $this->select = $select;
        return $this;
    }

    
    public function getSelect()
    {
        if ($this->select instanceof Select) {
            return $this->select;
        }

        
        $select          = new Select();
        $tableIdentifier = new TableIdentifier($this->table, $this->schema);
        $select->from($tableIdentifier)->columns([$this->field]);
        $select->where->equalTo($this->field, null);

        if ($this->exclude !== null) {
            if (is_array($this->exclude)) {
                $select->where->notEqualTo(
                    $this->exclude['field'],
                    $this->exclude['value']
                );
            } else {
                $select->where($this->exclude);
            }
        }

        $this->select = $select;

        return $this->select;
    }

    
    protected function query($value)
    {
        $sql                  = new Sql($this->getAdapter());
        $select               = $this->getSelect();
        $statement            = $sql->prepareStatementForSqlObject($select);
        $parameters           = $statement->getParameterContainer();
        $parameters['where1'] = $value;
        $result               = $statement->execute();

        return $result->current();
    }
}
