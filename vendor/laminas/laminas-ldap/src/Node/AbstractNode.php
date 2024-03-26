<?php

namespace Laminas\Ldap\Node;

use ArrayAccess;
use Countable;
use Laminas\Ldap;
use Laminas\Ldap\Dn;
use Laminas\Ldap\Exception;
use Laminas\Ldap\Exception\BadMethodCallException;
use Laminas\Ldap\Exception\LdapException;
use ReturnTypeWillChange;

use function array_key_exists;
use function array_merge;
use function count;
use function in_array;
use function json_encode;
use function ksort;
use function strtolower;

use const SORT_STRING;


abstract class AbstractNode implements ArrayAccess, Countable
{
    
    protected static $systemAttributes = [
        'createtimestamp',
        'creatorsname',
        'entrycsn',
        'entrydn',
        'entryuuid',
        'hassubordinates',
        'modifiersname',
        'modifytimestamp',
        'structuralobjectclass',
        'subschemasubentry',
        'distinguishedname',
        'instancetype',
        'name',
        'objectcategory',
        'objectguid',
        'usnchanged',
        'usncreated',
        'whenchanged',
        'whencreated',
    ];

    
    protected $dn;

    
    protected $currentData;

    
    protected function __construct(Ldap\Dn $dn, array $data, $fromDataSource)
    {
        $this->dn = $dn;
        $this->loadData($data, $fromDataSource);
    }

    
    protected function loadData(array $data, $fromDataSource)
    {
        if (array_key_exists('dn', $data)) {
            unset($data['dn']);
        }
        ksort($data, SORT_STRING);
        $this->currentData = $data;
    }

    
    public function reload(?Ldap\Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $data = $ldap->getEntry($this->_getDn(), ['*', '+'], true);
            $this->loadData($data, true);
        }

        return $this;
    }

    
    
    protected function _getDn()
    {
        
        return $this->dn;
    }

    
    public function getDn()
    {
        return clone $this->_getDn();
    }

    
    public function getDnString($caseFold = null)
    {
        return $this->_getDn()->toString($caseFold);
    }

    
    public function getDnArray($caseFold = null)
    {
        return $this->_getDn()->toArray($caseFold);
    }

    
    public function getRdnString($caseFold = null)
    {
        return $this->_getDn()->getRdnString($caseFold);
    }

    
    public function getRdnArray($caseFold = null)
    {
        return $this->_getDn()->getRdn($caseFold);
    }

    
    public function getObjectClass()
    {
        return $this->getAttribute('objectClass', null);
    }

    
    public function getAttributes($includeSystemAttributes = true)
    {
        $data = [];
        foreach ($this->getData($includeSystemAttributes) as $name => $value) {
            $data[$name] = $this->getAttribute($name, null);
        }
        return $data;
    }

    
    public function toString()
    {
        return $this->getDnString();
    }

    
    public function __toString()
    {
        return $this->toString();
    }

    
    public function toArray($includeSystemAttributes = true)
    {
        $attributes = $this->getAttributes($includeSystemAttributes);
        return array_merge(['dn' => $this->getDnString()], $attributes);
    }

    
    public function toJson($includeSystemAttributes = true)
    {
        return json_encode($this->toArray($includeSystemAttributes));
    }

    
    public function getData($includeSystemAttributes = true)
    {
        if ($includeSystemAttributes === false) {
            $data = [];
            foreach ($this->currentData as $key => $value) {
                if (! in_array($key, static::$systemAttributes)) {
                    $data[$key] = $value;
                }
            }
            return $data;
        }

        return $this->currentData;
    }

    
    public function existsAttribute($name, $emptyExists = false)
    {
        $name = strtolower($name);
        if (isset($this->currentData[$name])) {
            if ($emptyExists) {
                return true;
            }

            return count($this->currentData[$name]) > 0;
        }

        return false;
    }

    
    public function attributeHasValue($attribName, $value)
    {
        return Ldap\Attribute::attributeHasValue($this->currentData, $attribName, $value);
    }

    
    public function getAttribute($name, $index = null)
    {
        if ($name === 'dn') {
            return $this->getDnString();
        }

        return Ldap\Attribute::getAttribute($this->currentData, $name, $index);
    }

    
    public function getDateTimeAttribute($name, $index = null)
    {
        return Ldap\Attribute::getDateTimeAttribute($this->currentData, $name, $index);
    }

    
    public function __set($name, $value)
    {
        throw new Exception\BadMethodCallException();
    }

    
    public function __get($name)
    {
        return $this->getAttribute($name, null);
    }

    
    public function __unset($name)
    {
        throw new Exception\BadMethodCallException();
    }

    
    public function __isset($name)
    {
        return $this->existsAttribute($name, false);
    }

    
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        throw new Exception\BadMethodCallException();
    }

    
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset, null);
    }

    
    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        throw new Exception\BadMethodCallException();
    }

    
    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return $this->existsAttribute($offset, false);
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->currentData);
    }
}
