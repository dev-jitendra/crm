<?php

namespace Laminas\Ldap\Node\Schema\AttributeType;

use Laminas\Ldap\Node\Schema;

use function count;


class OpenLdap extends Schema\AbstractItem implements AttributeTypeInterface
{
    
    public function getName()
    {
        return $this->name;
    }

    
    public function getOid()
    {
        return $this->oid;
    }

    
    public function getSyntax()
    {
        if ($this->syntax === null) {
            $parent = $this->getParent();
            if ($parent === null) {
                return;
            } else {
                return $parent->getSyntax();
            }
        }

        return $this->syntax;
    }

    
    public function getMaxLength()
    {
        $maxLength = $this->{'max-length'};
        if ($maxLength === null) {
            $parent = $this->getParent();
            if ($parent === null) {
                return;
            } else {
                return $parent->getMaxLength();
            }
        }

        return (int) $maxLength;
    }

    
    public function isSingleValued()
    {
        return $this->{'single-value'};
    }

    
    public function getDescription()
    {
        return $this->desc;
    }

    
    public function getParent()
    {
        if (count($this->_parents) === 1) {
            return $this->_parents[0];
        }

        return null;
    }
}
