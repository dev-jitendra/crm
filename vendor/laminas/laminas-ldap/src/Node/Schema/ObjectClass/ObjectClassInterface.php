<?php

namespace Laminas\Ldap\Node\Schema\ObjectClass;


interface ObjectClassInterface
{
    
    public function getName();

    
    public function getOid();

    
    public function getMustContain();

    
    public function getMayContain();

    
    public function getDescription();

    
    public function getType();

    
    public function getParentClasses();
}
