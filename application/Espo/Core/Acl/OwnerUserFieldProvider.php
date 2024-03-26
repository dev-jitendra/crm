<?php


namespace Espo\Core\Acl;

use Espo\Core\Utils\Metadata;

use Espo\ORM\Defs;

class OwnerUserFieldProvider
{
    protected const FIELD_ASSIGNED_USERS = 'assignedUsers';

    protected const FIELD_ASSIGNED_USER = 'assignedUser';

    protected const FIELD_CREATED_BY = 'createdBy';

    private $ormDefs;

    private $metadata;

    public function __construct(Defs $ormDefs, Metadata $metadata)
    {
        $this->ormDefs = $ormDefs;
        $this->metadata = $metadata;
    }

    
    public function get(string $entityType): ?string
    {
        $value = $this->metadata->get(['aclDefs', $entityType, 'readOwnerUserField']);

        if ($value) {
            return $value;
        }

        $defs = $this->ormDefs->getEntity($entityType);

        if (
            $defs->hasField(self::FIELD_ASSIGNED_USERS) &&
            $defs->getField(self::FIELD_ASSIGNED_USERS)->getType() === 'linkMultiple' &&
            $defs->hasRelation(self::FIELD_ASSIGNED_USERS) &&
            $defs->getRelation(self::FIELD_ASSIGNED_USERS)->getForeignEntityType() === 'User'
        ) {
            return self::FIELD_ASSIGNED_USERS;
        }

        if (
            $defs->hasField(self::FIELD_ASSIGNED_USER) &&
            $defs->getField(self::FIELD_ASSIGNED_USER)->getType() === 'link' &&
            $defs->hasRelation(self::FIELD_ASSIGNED_USER) &&
            $defs->getRelation(self::FIELD_ASSIGNED_USER)->getForeignEntityType() === 'User'
        ) {
            return self::FIELD_ASSIGNED_USER;
        }

        if (
            $defs->hasField(self::FIELD_CREATED_BY) &&
            $defs->getField(self::FIELD_CREATED_BY)->getType() === 'link' &&
            $defs->hasRelation(self::FIELD_CREATED_BY) &&
            $defs->getRelation(self::FIELD_CREATED_BY)->getForeignEntityType() === 'User'
        ) {
            return self::FIELD_CREATED_BY;
        }

        return null;
    }
}
