<?php

namespace Laminas\Ldap\Node;

use Laminas\Ldap;
use Laminas\Ldap\Node;


class Collection extends Ldap\Collection
{
    
    protected function createEntry(array $data)
    {
        $node = Ldap\Node::fromArray($data, true);
        $node->attachLDAP($this->iterator->getLDAP());
        return $node;
    }

    
    public function key()
    {
        return $this->iterator->key();
    }
}
