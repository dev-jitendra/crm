<?php

namespace Laminas\Ldap\Node\Schema\ObjectClass;

use Laminas\Ldap\Node\Schema;

use function array_diff;
use function array_merge;
use function array_unique;
use function sort;

use const SORT_STRING;


class OpenLdap extends Schema\AbstractItem implements ObjectClassInterface
{
    
    protected $inheritedMust;

    
    protected $inheritedMay;

    
    public function getName()
    {
        return $this->name;
    }

    
    public function getOid()
    {
        return $this->oid;
    }

    
    public function getMustContain()
    {
        if ($this->inheritedMust === null) {
            $this->resolveInheritance();
        }
        return $this->inheritedMust;
    }

    
    public function getMayContain()
    {
        if ($this->inheritedMay === null) {
            $this->resolveInheritance();
        }
        return $this->inheritedMay;
    }

    
    protected function resolveInheritance()
    {
        $must = $this->must;
        $may  = $this->may;
        foreach ($this->getParents() as $p) {
            $must = array_merge($must, $p->getMustContain());
            $may  = array_merge($may, $p->getMayContain());
        }
        $must = array_unique($must);
        $may  = array_unique($may);
        $may  = array_diff($may, $must);
        sort($must, SORT_STRING);
        sort($may, SORT_STRING);
        $this->inheritedMust = $must;
        $this->inheritedMay  = $may;
    }

    
    public function getDescription()
    {
        return $this->desc;
    }

    
    public function getType()
    {
        if ($this->structural) {
            return Schema::OBJECTCLASS_TYPE_STRUCTURAL;
        } elseif ($this->abstract) {
            return Schema::OBJECTCLASS_TYPE_ABSTRACT;
        } elseif ($this->auxiliary) {
            return Schema::OBJECTCLASS_TYPE_AUXILIARY;
        }

        return Schema::OBJECTCLASS_TYPE_UNKNOWN;
    }

    
    public function getParentClasses()
    {
        return $this->sup;
    }

    
    public function getParents()
    {
        return $this->_parents;
    }
}
