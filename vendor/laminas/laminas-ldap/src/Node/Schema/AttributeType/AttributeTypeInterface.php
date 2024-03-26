<?php

namespace Laminas\Ldap\Node\Schema\AttributeType;


interface AttributeTypeInterface
{
    
    public function getName();

    
    public function getOid();

    
    public function getSyntax();

    
    public function getMaxLength();

    
    public function isSingleValued();

    
    public function getDescription();
}
