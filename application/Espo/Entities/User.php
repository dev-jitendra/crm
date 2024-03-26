<?php


namespace Espo\Entities;

use Espo\Core\Entities\Person;

use Espo\Core\Field\Link;
use Espo\Core\Field\LinkMultiple;
use Espo\Modules\Crm\Entities\Contact;
use RuntimeException;

class User extends Person
{
    public const ENTITY_TYPE = 'User';

    public const ATTRIBUTE_TYPE = 'type';
    public const ATTRIBUTE_IS_ACTIVE = 'isActive';

    public const LINK_ACCOUNTS = 'accounts';
    public const LINK_CONTACT = 'contact';
    public const LINK_PORTALS = 'portals';
    public const LINK_TEAMS = 'teams';
    public const LINK_DEFAULT_TEAM = 'defaultTeam';
    public const LINK_ROLES = 'roles';
    public const LINK_PORTAL_ROLES = 'portalRoles';

    public const TYPE_PORTAL = 'portal';
    public const TYPE_ADMIN = 'admin';
    public const TYPE_SYSTEM = 'system';
    public const TYPE_REGULAR = 'regular';
    public const TYPE_API = 'api';
    public const TYPE_SUPER_ADMIN = 'super-admin';

    public function isActive(): bool
    {
        return (bool) $this->get('isActive');
    }

    
    public function isPortalUser(): bool
    {
        return $this->isPortal();
    }

    public function getType(): ?string
    {
        return $this->get('type');
    }

    
    public function isRegular(): bool
    {
        return $this->getType() === self::TYPE_REGULAR ||
            ($this->has('type') && !$this->getType());
    }

    
    public function isAdmin(): bool
    {
        return $this->getType() === self::TYPE_ADMIN ||
            $this->isSystem() ||
            $this->isSuperAdmin();
    }

    
    public function isPortal(): bool
    {
        return $this->getType() === self::TYPE_PORTAL;
    }

    
    public function isApi(): bool
    {
        return $this->getType() === self::TYPE_API;
    }

    
    public function isSystem(): bool
    {
        return $this->getType() === self::TYPE_SYSTEM;
    }

    
    public function isSuperAdmin(): bool
    {
        return $this->getType() === self::TYPE_SUPER_ADMIN;
    }

    public function getRoles(): LinkMultiple
    {
        
        return $this->getValueObject('roles');
    }

    public function getDefaultTeam(): ?Link
    {
        
        return $this->getValueObject('defaultTeam');
    }

    public function getWorkingTimeCalendar(): ?Link
    {
        
        return $this->getValueObject('workingTimeCalendar');
    }

    public function getLayoutSet(): ?Link
    {
        
        return $this->getValueObject('layoutSet');
    }

    public function getTeams(): LinkMultiple
    {
        
        return $this->getValueObject('teams');
    }

    
    public function getTeamIdList(): array
    {
        
        return $this->getLinkMultipleIdList('teams');
    }

    public function setDefaultTeam(?Link $defaultTeam): self
    {
        $this->setValueObject('defaultTeam', $defaultTeam);

        return $this;
    }

    public function setTeams(LinkMultiple $teams): self
    {
        $this->setValueObject('teams', $teams);

        return $this;
    }

    public function getPortals(): LinkMultiple
    {
        
        return $this->getValueObject('portals');
    }

    public function setPortals(LinkMultiple $portals): self
    {
        $this->setValueObject('portals', $portals);

        return $this;
    }

    public function setRoles(LinkMultiple $roles): self
    {
        $this->setValueObject('roles', $roles);

        return $this;
    }

    public function loadAccountField(): void
    {
        if (!$this->entityManager) {
            throw new RuntimeException("No entity manager");
        }

        if ($this->get('contactId')) {
            $contact = $this->entityManager->getEntityById(Contact::ENTITY_TYPE, $this->get('contactId'));

            if ($contact && $contact->get('accountId')) {
                $this->set('accountId', $contact->get('accountId'));
                $this->set('accountName', $contact->get('accountName'));
            }
        }
    }

    public function setTitle(?string $title): self
    {
        $this->set('title', $title);

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->get('title');
    }

    public function getUserName(): ?string
    {
        return $this->get('userName');
    }

    public function getAuthMethod(): ?string
    {
        return $this->get('authMethod');
    }

    public function getContactId(): ?string
    {
        return $this->get('contactId');
    }

    public function getContact(): ?Link
    {
        
        $value = $this->getValueObject('contact');

        return $value;
    }

    
    public function getPortalId(): ?string
    {
        return $this->get('portalId');
    }

    public function getAccounts(): LinkMultiple
    {
        
        $value = $this->getValueObject('accounts');

        return $value;
    }

    
    protected function _getName()
    {
        if (!$this->hasInContainer('name') || !$this->getFromContainer('name')) {
            if ($this->get('userName')) {
                return $this->get('userName');
            }
        }

        return $this->getFromContainer('name');
    }

    
    protected function _hasName()
    {
        if ($this->hasInContainer('name')) {
            return true;
        }

        if ($this->has('userName')) {
            return true;
        }

        return false;
    }
}
