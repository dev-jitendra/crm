<?php

namespace Laminas\Ldap\Node\Schema;

use Laminas\Ldap;
use Laminas\Ldap\Converter;
use Laminas\Ldap\Node;

use function array_key_exists;
use function array_pop;
use function array_shift;
use function count;
use function in_array;
use function is_array;
use function ksort;
use function preg_match;
use function preg_match_all;
use function strtolower;
use function trim;

use const SORT_STRING;


class OpenLdap extends Node\Schema
{
    
    protected $attributeTypes;

    
    protected $objectClasses;

    
    protected $ldapSyntaxes;

    
    protected $matchingRules;

    
    protected $matchingRuleUse;

    
    protected function parseSchema(Ldap\Dn $dn, Ldap\Ldap $ldap)
    {
        parent::parseSchema($dn, $ldap);
        $this->loadAttributeTypes();
        $this->loadLdapSyntaxes();
        $this->loadMatchingRules();
        $this->loadMatchingRuleUse();
        $this->loadObjectClasses();
        return $this;
    }

    
    public function getAttributeTypes()
    {
        return $this->attributeTypes;
    }

    
    public function getObjectClasses()
    {
        return $this->objectClasses;
    }

    
    public function getLdapSyntaxes()
    {
        return $this->ldapSyntaxes;
    }

    
    public function getMatchingRules()
    {
        return $this->matchingRules;
    }

    
    public function getMatchingRuleUse()
    {
        return $this->matchingRuleUse;
    }

    
    protected function loadAttributeTypes()
    {
        $this->attributeTypes = [];
        foreach ($this->getAttribute('attributeTypes') as $value) {
            $val                                   = $this->parseAttributeType($value);
            $val                                   = new AttributeType\OpenLdap($val);
            $this->attributeTypes[$val->getName()] = $val;
        }
        foreach ($this->attributeTypes as $val) {
            if (! empty($val->sup) && count($val->sup) > 0) {
                $this->resolveInheritance($val, $this->attributeTypes);
            }
            foreach ($val->aliases as $alias) {
                $this->attributeTypes[$alias] = $val;
            }
        }
        ksort($this->attributeTypes, SORT_STRING);
    }

    
    protected function parseAttributeType($value)
    {
        $attributeType = [
            'oid'                  => null,
            'name'                 => null,
            'desc'                 => null,
            'obsolete'             => false,
            'sup'                  => null,
            'equality'             => null,
            'ordering'             => null,
            'substr'               => null,
            'syntax'               => null,
            'max-length'           => null,
            'single-value'         => false,
            'collective'           => false,
            'no-user-modification' => false,
            'usage'                => 'userApplications',
            '_string'              => $value,
            '_parents'             => [],
        ];

        $tokens               = $this->tokenizeString($value);
        $attributeType['oid'] = array_shift($tokens); 
        $this->parseLdapSchemaSyntax($attributeType, $tokens);

        if (array_key_exists('syntax', $attributeType)) {
            
            if (preg_match('/^(.+){(\d+)}$/', $attributeType['syntax'], $matches)) {
                $attributeType['syntax']     = $matches[1];
                $attributeType['max-length'] = $matches[2];
            }
        }

        $this->ensureNameAttribute($attributeType);

        return $attributeType;
    }

    
    protected function loadObjectClasses()
    {
        $this->objectClasses = [];
        foreach ($this->getAttribute('objectClasses') as $value) {
            $val                                  = $this->parseObjectClass($value);
            $val                                  = new ObjectClass\OpenLdap($val);
            $this->objectClasses[$val->getName()] = $val;
        }
        foreach ($this->objectClasses as $val) {
            if (count($val->sup) > 0) {
                $this->resolveInheritance($val, $this->objectClasses);
            }
            foreach ($val->aliases as $alias) {
                $this->objectClasses[$alias] = $val;
            }
        }
        ksort($this->objectClasses, SORT_STRING);
    }

    
    protected function parseObjectClass($value)
    {
        $objectClass = [
            'oid'        => null,
            'name'       => null,
            'desc'       => null,
            'obsolete'   => false,
            'sup'        => [],
            'abstract'   => false,
            'structural' => false,
            'auxiliary'  => false,
            'must'       => [],
            'may'        => [],
            '_string'    => $value,
            '_parents'   => [],
        ];

        $tokens             = $this->tokenizeString($value);
        $objectClass['oid'] = array_shift($tokens); 
        $this->parseLdapSchemaSyntax($objectClass, $tokens);

        $this->ensureNameAttribute($objectClass);

        return $objectClass;
    }

    
    protected function resolveInheritance(AbstractItem $node, array $repository)
    {
        $data    = $node->getData();
        $parents = $data['sup'];
        if ($parents === null || ! is_array($parents) || count($parents) < 1) {
            return;
        }
        foreach ($parents as $parent) {
            if (! array_key_exists($parent, $repository)) {
                continue;
            }
            if (! array_key_exists('_parents', $data) || ! is_array($data['_parents'])) {
                $data['_parents'] = [];
            }
            $data['_parents'][] = $repository[$parent];
        }
        $node->setData($data);
    }

    
    protected function loadLdapSyntaxes()
    {
        $this->ldapSyntaxes = [];
        foreach ($this->getAttribute('ldapSyntaxes') as $value) {
            $val                             = $this->parseLdapSyntax($value);
            $this->ldapSyntaxes[$val['oid']] = $val;
        }
        ksort($this->ldapSyntaxes, SORT_STRING);
    }

    
    protected function parseLdapSyntax($value)
    {
        $ldapSyntax = [
            'oid'     => null,
            'desc'    => null,
            '_string' => $value,
        ];

        $tokens            = $this->tokenizeString($value);
        $ldapSyntax['oid'] = array_shift($tokens); 
        $this->parseLdapSchemaSyntax($ldapSyntax, $tokens);

        return $ldapSyntax;
    }

    
    protected function loadMatchingRules()
    {
        $this->matchingRules = [];
        foreach ($this->getAttribute('matchingRules') as $value) {
            $val                               = $this->parseMatchingRule($value);
            $this->matchingRules[$val['name']] = $val;
        }
        ksort($this->matchingRules, SORT_STRING);
    }

    
    protected function parseMatchingRule($value)
    {
        $matchingRule = [
            'oid'      => null,
            'name'     => null,
            'desc'     => null,
            'obsolete' => false,
            'syntax'   => null,
            '_string'  => $value,
        ];

        $tokens              = $this->tokenizeString($value);
        $matchingRule['oid'] = array_shift($tokens); 
        $this->parseLdapSchemaSyntax($matchingRule, $tokens);

        $this->ensureNameAttribute($matchingRule);

        return $matchingRule;
    }

    
    protected function loadMatchingRuleUse()
    {
        $this->matchingRuleUse = [];
        foreach ($this->getAttribute('matchingRuleUse') as $value) {
            $val                                 = $this->parseMatchingRuleUse($value);
            $this->matchingRuleUse[$val['name']] = $val;
        }
        ksort($this->matchingRuleUse, SORT_STRING);
    }

    
    protected function parseMatchingRuleUse($value)
    {
        $matchingRuleUse = [
            'oid'      => null,
            'name'     => null,
            'desc'     => null,
            'obsolete' => false,
            'applies'  => [],
            '_string'  => $value,
        ];

        $tokens                 = $this->tokenizeString($value);
        $matchingRuleUse['oid'] = array_shift($tokens); 
        $this->parseLdapSchemaSyntax($matchingRuleUse, $tokens);

        $this->ensureNameAttribute($matchingRuleUse);

        return $matchingRuleUse;
    }

    
    protected function ensureNameAttribute(array &$data)
    {
        if (! array_key_exists('name', $data) || empty($data['name'])) {
            
            $data['name'] = $data['oid'];
        }
        if (is_array($data['name'])) {
            
            $aliases         = $data['name'];
            $data['name']    = array_shift($aliases);
            $data['aliases'] = $aliases;
        } else {
            $data['aliases'] = [];
        }
    }

    
    protected function parseLdapSchemaSyntax(array &$data, array $tokens)
    {
        
        $noValue = [
            'single-value',
            'obsolete',
            'collective',
            'no-user-modification',
            'abstract',
            'structural',
            'auxiliary',
        ];
        
        $multiValue = ['must', 'may', 'sup'];

        while (count($tokens) > 0) {
            $token = strtolower(array_shift($tokens));
            if (in_array($token, $noValue)) {
                $data[$token] = true; 
            } else {
                $data[$token] = array_shift($tokens);
                
                if ($data[$token] === '(') {
                    
                    
                    $data[$token] = [];

                    $tmp = array_shift($tokens);
                    while ($tmp) {
                        if ($tmp === ')') {
                            break;
                        }
                        if ($tmp !== '$') {
                            $data[$token][] = Converter\Converter::fromLdap($tmp);
                        }
                        $tmp = array_shift($tokens);
                    }
                } else {
                    $data[$token] = Converter\Converter::fromLdap($data[$token]);
                }
                
                if (in_array($token, $multiValue) && ! is_array($data[$token])) {
                    $data[$token] = [$data[$token]];
                }
            }
        }
    }

    
    protected function tokenizeString($value)
    {
        $tokens  = [];
        $matches = [];
        
        $pattern = "/\\s* (?:([()]) | ([^'\\s()]+) | '((?:[^']+|'[^\\s)])*)') \\s*/x";
        preg_match_all($pattern, $value, $matches);
        $cMatches = count($matches[0]);
        $cPattern = count($matches);
        for ($i = 0; $i < $cMatches; $i++) { 
            for ($j = 1; $j < $cPattern; $j++) { 
                $tok = trim($matches[$j][$i]);
                if (! empty($tok)) { 
                    $tokens[$i] = $tok; 
                }
            }
        }
        if ($tokens[0] === '(') {
            array_shift($tokens);
        }
        if ($tokens[count($tokens) - 1] === ')') {
            array_pop($tokens);
        }

        return $tokens;
    }
}
