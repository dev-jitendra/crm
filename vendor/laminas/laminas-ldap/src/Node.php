<?php

namespace Laminas\Ldap;

use ArrayAccess;
use Iterator;
use Laminas\EventManager\EventManager;
use Laminas\Ldap\Node\Collection;
use RecursiveIterator;
use ReturnTypeWillChange;

use function array_key_exists;
use function array_merge;
use function class_exists;
use function count;
use function in_array;
use function is_array;
use function is_string;
use function strtolower;


class Node extends Node\AbstractNode implements Iterator, RecursiveIterator
{
    
    protected $newDn;

    
    protected $originalData;

    
    protected $new;

    
    protected $delete;

    
    protected $ldap;

    
    protected $children;

    
    private bool $iteratorRewind = false;

    
    protected $events;

    
    protected function __construct(Dn $dn, array $data, $fromDataSource, ?Ldap $ldap = null)
    {
        parent::__construct($dn, $data, $fromDataSource);
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        } else {
            $this->detachLdap();
        }
    }

    
    public function __sleep()
    {
        return [
            'dn',
            'currentData',
            'newDn',
            'originalData',
            'new',
            'delete',
            'children',
        ];
    }

    
    public function __wakeup()
    {
        $this->detachLdap();
    }

    
    public function getLdap()
    {
        if ($this->ldap === null) {
            throw new Exception\LdapException(
                null,
                'No LDAP connection specified.',
                Exception\LdapException::LDAP_OTHER
            );
        }

        return $this->ldap;
    }

    
    public function attachLdap(Ldap $ldap)
    {
        if (! Dn::isChildOf($this->_getDn(), $ldap->getBaseDn())) {
            throw new Exception\LdapException(
                null,
                'LDAP connection is not responsible for given node.',
                Exception\LdapException::LDAP_OTHER
            );
        }

        if ($ldap !== $this->ldap) {
            $this->ldap = $ldap;
            if (is_array($this->children)) {
                foreach ($this->children as $child) {
                    $child->attachLdap($ldap);
                }
            }
        }

        return $this;
    }

    
    public function detachLdap()
    {
        $this->ldap = null;
        if (is_array($this->children)) {
            foreach ($this->children as $child) {
                $child->detachLdap();
            }
        }

        return $this;
    }

    
    public function isAttached()
    {
        return $this->ldap !== null;
    }

    
    protected function triggerEvent($event, $argv = [])
    {
        $events = $this->getEventManager();
        if (! $events) {
            return;
        }
        $events->trigger($event, $this, $argv);
    }

    
    protected function loadData(array $data, $fromDataSource)
    {
        parent::loadData($data, $fromDataSource);
        if ($fromDataSource === true) {
            $this->originalData = $data;
        } else {
            $this->originalData = [];
        }
        $this->children = null;
        $this->markAsNew($fromDataSource !== true);
        $this->markAsToBeDeleted(false);
    }

    
    public static function create($dn, array $objectClass = [])
    {
        if (is_string($dn) || is_array($dn)) {
            $dn = Dn::factory($dn);
        } elseif ($dn instanceof Dn) {
            $dn = clone $dn;
        } else {
            throw new Exception\LdapException(null, '$dn is of a wrong data type.');
        }
        $new = new static($dn, [], false, null);
        $new->ensureRdnAttributeValues();
        $new->setAttribute('objectClass', $objectClass);

        return $new;
    }

    
    public static function fromLdap($dn, Ldap $ldap)
    {
        if (is_string($dn) || is_array($dn)) {
            $dn = Dn::factory($dn);
        } elseif ($dn instanceof Dn) {
            $dn = clone $dn;
        } else {
            throw new Exception\LdapException(null, '$dn is of a wrong data type.');
        }
        $data = $ldap->getEntry($dn, ['*', '+'], true);
        if ($data === null) {
            return;
        }
        return new static($dn, $data, true, $ldap);
    }

    
    public static function fromArray(array $data, $fromDataSource = false)
    {
        if (! array_key_exists('dn', $data)) {
            throw new Exception\LdapException(null, '\'dn\' key is missing in array.');
        }
        if (is_string($data['dn']) || is_array($data['dn'])) {
            $dn = Dn::factory($data['dn']);
        } elseif ($data['dn'] instanceof Dn) {
            $dn = clone $data['dn'];
        } else {
            throw new Exception\LdapException(null, '\'dn\' key is of a wrong data type.');
        }
        $fromDataSource = $fromDataSource === true;
        $new            = new static($dn, $data, $fromDataSource, null);
        $new->ensureRdnAttributeValues();

        return $new;
    }

    
    protected function ensureRdnAttributeValues($overwrite = false)
    {
        foreach ($this->getRdnArray() as $key => $value) {
            if (! array_key_exists($key, $this->currentData) || $overwrite) {
                Attribute::setAttribute($this->currentData, $key, $value, false);
            } elseif (! in_array($value, $this->currentData[$key])) {
                Attribute::setAttribute($this->currentData, $key, $value, true);
            }
        }
    }

    
    protected function markAsNew($new)
    {
        $this->new = (bool) $new;
    }

    
    public function isNew()
    {
        return $this->new;
    }

    
    protected function markAsToBeDeleted($delete)
    {
        $this->delete = (bool) $delete;
    }

    
    public function willBeDeleted()
    {
        return $this->delete;
    }

    
    public function delete()
    {
        $this->markAsToBeDeleted(true);

        return $this;
    }

    
    public function willBeMoved()
    {
        if ($this->isNew() || $this->willBeDeleted()) {
            return false;
        } elseif ($this->newDn !== null) {
            
            return $this->dn != $this->newDn; 
        }

        return false;
    }

    
    public function update(?Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();
        if (! $ldap instanceof Ldap) {
            throw new Exception\LdapException(null, 'No LDAP connection available');
        }

        if ($this->willBeDeleted()) {
            if ($ldap->exists($this->dn)) {
                $this->triggerEvent('pre-delete');
                $ldap->delete($this->dn);
                $this->triggerEvent('post-delete');
            }
            return $this;
        }

        if ($this->isNew()) {
            $this->triggerEvent('pre-add');
            $data = $this->getData();
            $ldap->add($this->_getDn(), $data);
            $this->loadData($data, true);
            $this->triggerEvent('post-add');

            return $this;
        }

        $changedData = $this->getChangedData();
        if ($this->willBeMoved()) {
            $this->triggerEvent('pre-rename');
            $recursive = $this->hasChildren();
            $ldap->rename($this->dn, $this->newDn, $recursive, false);
            foreach ($this->newDn->getRdn() as $key => $value) {
                if (array_key_exists($key, $changedData)) {
                    unset($changedData[$key]);
                }
            }
            $this->dn    = $this->newDn;
            $this->newDn = null;
            $this->triggerEvent('post-rename');
        }
        if (count($changedData) > 0) {
            $this->triggerEvent('pre-update');
            $ldap->update($this->_getDn(), $changedData);
            $this->triggerEvent('post-update');
        }
        $this->originalData = $this->currentData;

        return $this;
    }

    
    
    protected function _getDn()
    {
        
        return $this->newDn ?? parent::_getDn();
    }

    
    public function getCurrentDn()
    {
        return clone parent::_getDn();
    }

    
    public function setDn($newDn)
    {
        if ($newDn instanceof Dn) {
            $this->newDn = clone $newDn;
        } else {
            $this->newDn = Dn::factory($newDn);
        }
        $this->ensureRdnAttributeValues(true);

        return $this;
    }

    
    public function move($newDn)
    {
        return $this->setDn($newDn);
    }

    
    public function rename($newDn)
    {
        return $this->setDn($newDn);
    }

    
    public function setObjectClass($value)
    {
        $this->setAttribute('objectClass', $value);

        return $this;
    }

    
    public function appendObjectClass($value)
    {
        $this->appendToAttribute('objectClass', $value);

        return $this;
    }

    
    public function toLdif(array $options = [])
    {
        $attributes = array_merge(['dn' => $this->getDnString()], $this->getData(false));

        return Ldif\Encoder::encode($attributes, $options);
    }

    
    public function getChangedData()
    {
        $changed = [];
        foreach ($this->currentData as $key => $value) {
            if (! array_key_exists($key, $this->originalData) && ! empty($value)) {
                $changed[$key] = $value;
            } elseif ($this->originalData[$key] !== $this->currentData[$key]) {
                $changed[$key] = $value;
            }
        }

        return $changed;
    }

    
    public function getChanges()
    {
        $changes = [
            'add'     => [],
            'delete'  => [],
            'replace' => [],
        ];
        foreach ($this->currentData as $key => $value) {
            if (! array_key_exists($key, $this->originalData) && ! empty($value)) {
                $changes['add'][$key] = $value;
            } elseif (count($this->originalData[$key]) === 0 && ! empty($value)) {
                $changes['add'][$key] = $value;
            } elseif ($this->originalData[$key] !== $this->currentData[$key]) {
                if (empty($value)) {
                    $changes['delete'][$key] = $value;
                } else {
                    $changes['replace'][$key] = $value;
                }
            }
        }

        return $changes;
    }

    
    public function setAttribute($name, $value)
    {
        $this->_setAttribute($name, $value, false);
        return $this;
    }

    
    public function appendToAttribute($name, $value)
    {
        $this->_setAttribute($name, $value, true);

        return $this;
    }

    
    
    protected function _setAttribute($name, $value, $append)
    {
        
        $this->assertChangeableAttribute($name);
        Attribute::setAttribute($this->currentData, $name, $value, $append);
    }

    
    public function setDateTimeAttribute($name, $value, $utc = false)
    {
        $this->_setDateTimeAttribute($name, $value, $utc, false);
        return $this;
    }

    
    public function appendToDateTimeAttribute($name, $value, $utc = false)
    {
        $this->_setDateTimeAttribute($name, $value, $utc, true);

        return $this;
    }

    
    
    protected function _setDateTimeAttribute($name, $value, $utc, $append)
    {
        
        $this->assertChangeableAttribute($name);
        Attribute::setDateTimeAttribute($this->currentData, $name, $value, $utc, $append);
    }

    
    public function setPasswordAttribute(
        $password,
        $hashType = Attribute::PASSWORD_HASH_MD5,
        $attribName = 'userPassword'
    ) {
        $this->assertChangeableAttribute($attribName);
        Attribute::setPassword($this->currentData, $password, $hashType, $attribName);

        return $this;
    }

    
    public function deleteAttribute($name)
    {
        if ($this->existsAttribute($name, true)) {
            $this->_setAttribute($name, null, false);
        }

        return $this;
    }

    
    public function removeDuplicatesFromAttribute($attribName)
    {
        Attribute::removeDuplicatesFromAttribute($this->currentData, $attribName);
    }

    
    public function removeFromAttribute($attribName, $value)
    {
        Attribute::removeFromAttribute($this->currentData, $attribName, $value);
    }

    
    protected function assertChangeableAttribute($name)
    {
        $name = strtolower($name);
        $rdn  = $this->getRdnArray(Dn::ATTR_CASEFOLD_LOWER);

        if ($name === 'dn') {
            throw new Exception\LdapException(null, 'DN cannot be changed.');
        }

        if (array_key_exists($name, $rdn)) {
            throw new Exception\LdapException(null, 'Cannot change attribute because it\'s part of the RDN');
        }

        if (in_array($name, static::$systemAttributes)) {
            throw new Exception\LdapException(null, 'Cannot change attribute because it\'s read-only');
        }

        return true;
    }

    
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    
    public function __unset($name)
    {
        $this->deleteAttribute($name);
    }

    
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    
    public function offsetUnset($offset)
    {
        $this->deleteAttribute($offset);
    }

    
    public function exists(?Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();

        return $ldap->exists($this->_getDn());
    }

    
    public function reload(?Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap = $this->getLdap();
        parent::reload($ldap);

        return $this;
    }

    
    public function searchSubtree($filter, $scope = Ldap::SEARCH_SCOPE_SUB, $sort = null)
    {
        return $this->getLdap()->search(
            $filter,
            $this->_getDn(),
            $scope,
            ['*', '+'],
            $sort,
            Collection::class
        );
    }

    
    public function countSubtree($filter, $scope = Ldap::SEARCH_SCOPE_SUB)
    {
        return $this->getLdap()->count($filter, $this->_getDn(), $scope);
    }

    
    public function countChildren()
    {
        return $this->countSubtree('(objectClass=*)', Ldap::SEARCH_SCOPE_ONE);
    }

    
    public function searchChildren($filter, $sort = null)
    {
        return $this->searchSubtree($filter, Ldap::SEARCH_SCOPE_ONE, $sort);
    }

    
    #[ReturnTypeWillChange]
    public function hasChildren()
    {
        if (! is_array($this->children)) {
            if ($this->isAttached()) {
                return $this->countChildren() > 0;
            }
            return false;
        }
        return count($this->children) > 0;
    }

    
    #[ReturnTypeWillChange]
    public function getChildren()
    {
        if (! is_array($this->children)) {
            $this->children = [];
            if ($this->isAttached()) {
                $children = $this->searchChildren('(objectClass=*)', null);
                foreach ($children as $child) {
                    $this->children[$child->getRdnString(Dn::ATTR_CASEFOLD_LOWER)] = $child;
                }
            }
        }

        return new Node\ChildrenIterator($this->children);
    }

    
    public function getParent(?Ldap $ldap = null)
    {
        if ($ldap !== null) {
            $this->attachLdap($ldap);
        }
        $ldap     = $this->getLdap();
        $parentDn = $this->_getDn()->getParentDn(1);

        return static::fromLdap($parentDn, $ldap);
    }

    
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this;
    }

    
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->getRdnString();
    }

    
    #[ReturnTypeWillChange]
    public function next()
    {
        $this->iteratorRewind = false;
    }

    
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->iteratorRewind = true;
    }

    
    #[ReturnTypeWillChange]
    public function valid()
    {
        return $this->iteratorRewind;
    }

    
    private function getEventManager()
    {
        if ($this->events) {
            return $this->events;
        }

        if (! class_exists(EventManager::class)) {
            return;
        }

        $this->events = new EventManager();
        $this->events->setIdentifiers([self::class]);
        return $this->events;
    }
}
