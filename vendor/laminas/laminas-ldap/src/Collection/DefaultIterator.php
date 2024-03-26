<?php

namespace Laminas\Ldap\Collection;

use Countable;
use Iterator;
use Laminas\Ldap;
use Laminas\Ldap\ErrorHandler;
use Laminas\Ldap\Exception;
use Laminas\Ldap\Exception\LdapException;
use Laminas\Ldap\Handler;
use LDAP\Result;
use LDAP\ResultEntry;
use ReturnTypeWillChange;

use function array_change_key_case;
use function call_user_func;
use function current;
use function function_exists;
use function is_array;
use function is_callable;
use function is_string;
use function ksort;
use function method_exists;
use function next;
use function reset;
use function strtolower;
use function strtoupper;
use function usort;

use const CASE_LOWER;
use const SORT_LOCALE_STRING;


class DefaultIterator implements Iterator, Countable
{
    public const ATTRIBUTE_TO_LOWER = 1;
    public const ATTRIBUTE_TO_UPPER = 2;
    public const ATTRIBUTE_NATIVE   = 3;

    
    protected $ldap;

    
    protected $resultId;

    
    protected $current;

    
    protected $itemCount = -1;

    
    protected $attributeNameTreatment = self::ATTRIBUTE_TO_LOWER;

    
    protected $entries = [];

    
    protected $sortFunction;

    
    public function __construct(Ldap\Ldap $ldap, $resultId)
    {
        $this->setSortFunction('strnatcasecmp');
        $this->ldap     = $ldap;
        $this->resultId = $resultId;

        $resource = $ldap->getResource();
        ErrorHandler::start();
        $this->itemCount = ldap_count_entries($resource, $resultId);
        ErrorHandler::stop();
        if ($this->itemCount === false) {
            throw new Exception\LdapException($this->ldap, 'counting entries');
        }

        $identifier = ldap_first_entry(
            $ldap->getResource(),
            $resultId
        );

        while (false !== $identifier) {
            $this->entries[] = [
                'resource'  => $identifier,
                'sortValue' => '',
            ];

            $identifier = ldap_next_entry(
                $ldap->getResource(),
                $identifier
            );
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    
    public function close()
    {
        $isClosed = false;
        if (Handler::isResultHandle($this->resultId)) {
            ErrorHandler::start();
            $isClosed = ldap_free_result($this->resultId);
            ErrorHandler::stop();

            $this->resultId = null;
            $this->current  = null;
        }
        return $isClosed;
    }

    
    public function getLDAP()
    {
        return $this->ldap;
    }

    
    public function setAttributeNameTreatment($attributeNameTreatment)
    {
        if (is_callable($attributeNameTreatment)) {
            if (is_string($attributeNameTreatment) && ! function_exists($attributeNameTreatment)) {
                $this->attributeNameTreatment = self::ATTRIBUTE_TO_LOWER;
            } elseif (
                is_array($attributeNameTreatment)
                && ! method_exists($attributeNameTreatment[0], $attributeNameTreatment[1])
            ) {
                $this->attributeNameTreatment = self::ATTRIBUTE_TO_LOWER;
            } else {
                $this->attributeNameTreatment = $attributeNameTreatment;
            }
        } else {
            $attributeNameTreatment = (int) $attributeNameTreatment;
            switch ($attributeNameTreatment) {
                case self::ATTRIBUTE_TO_LOWER:
                case self::ATTRIBUTE_TO_UPPER:
                case self::ATTRIBUTE_NATIVE:
                    $this->attributeNameTreatment = $attributeNameTreatment;
                    break;
                default:
                    $this->attributeNameTreatment = self::ATTRIBUTE_TO_LOWER;
                    break;
            }
        }

        return $this;
    }

    
    public function getAttributeNameTreatment()
    {
        return $this->attributeNameTreatment;
    }

    
    #[ReturnTypeWillChange]
    public function count()
    {
        return $this->itemCount;
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        if (! Handler::isResultEntryHandle($this->current)) {
            $this->rewind();
        }
        if (! Handler::isResultEntryHandle($this->current)) {
            return null;
        }

        $entry = ['dn' => $this->key()];

        $resource = $this->ldap->getResource();
        ErrorHandler::start();
        $name = ldap_first_attribute($resource, $this->current);
        ErrorHandler::stop();

        while ($name) {
            ErrorHandler::start();
            $data = ldap_get_values_len($resource, $this->current, $name);
            ErrorHandler::stop();

            if (! $data) {
                $data = [];
            }

            if (isset($data['count'])) {
                unset($data['count']);
            }

            switch ($this->attributeNameTreatment) {
                case self::ATTRIBUTE_TO_LOWER:
                    $attrName = strtolower($name);
                    break;
                case self::ATTRIBUTE_TO_UPPER:
                    $attrName = strtoupper($name);
                    break;
                case self::ATTRIBUTE_NATIVE:
                    $attrName = $name;
                    break;
                default:
                    $attrName = call_user_func($this->attributeNameTreatment, $name);
                    break;
            }
            $entry[$attrName] = $data;

            ErrorHandler::start();
            $name = ldap_next_attribute($resource, $this->current);
            ErrorHandler::stop();
        }
        ksort($entry, SORT_LOCALE_STRING);
        return $entry;
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        if (! Handler::isResultEntryHandle($this->current)) {
            $this->rewind();
        }

        if (! Handler::isResultEntryHandle($this->current)) {
            return null;
        }

        $resource = $this->ldap->getResource();
        ErrorHandler::start();
        $currentDn = ldap_get_dn($resource, $this->current);
        ErrorHandler::stop();

        if ($currentDn === false) {
            throw new Exception\LdapException($this->ldap, 'getting dn');
        }

        return $currentDn;
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        next($this->entries);
        $nextEntry     = current($this->entries);
        $this->current = $nextEntry['resource'] ?? null;
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->entries);
        $nextEntry     = current($this->entries);
        $this->current = $nextEntry['resource'] ?? null;
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        return Handler::isResultEntryHandle($this->current);
    }

    
    public function setSortFunction(callable $sortFunction)
    {
        $this->sortFunction = $sortFunction;

        return $this;
    }

    
    public function sort($sortAttribute)
    {
        foreach ($this->entries as $key => $entry) {
            $attributes = ldap_get_attributes(
                $this->ldap->getResource(),
                $entry['resource']
            );

            $attributes = array_change_key_case($attributes, CASE_LOWER);

            if (isset($attributes[$sortAttribute][0])) {
                $sortValue                        = (string) $attributes[$sortAttribute][0];
                $this->entries[$key]['sortValue'] = $sortValue;
            }
        }

        $sortFunction = $this->sortFunction;
        $sorted       = usort(
            $this->entries,
            static fn($a, $b) =>
                $sortFunction($a['sortValue'], $b['sortValue'])
        );

        if (! $sorted) {
            throw new Exception\LdapException($this->ldap, 'sorting result-set');
        }
    }
}
