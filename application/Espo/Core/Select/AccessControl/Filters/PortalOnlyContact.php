<?php


namespace Espo\Core\Select\AccessControl\Filters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Core\Select\Helpers\FieldHelper;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;

class PortalOnlyContact implements Filter
{
    public function __construct(
        private User $user,
        private FieldHelper $fieldHelper
    ) {}

    public function apply(QueryBuilder $queryBuilder): void
    {
        $orGroup = [];

        $contactId = $this->user->get('contactId');

        if ($contactId) {
            if ($this->fieldHelper->hasContactField()) {
                $orGroup['contactId'] = $contactId;
            }

            if ($this->fieldHelper->hasContactsRelation()) {
                $queryBuilder
                    ->leftJoin('contacts', 'contactsAccess')
                    ->distinct();

                $orGroup['contactsAccess.id'] = $contactId;
            }

            if ($this->fieldHelper->hasParentField()) {
                $orGroup[] = [
                    'parentType' => Contact::ENTITY_TYPE,
                    'parentId' => $contactId,
                ];
            }
        }

        if ($this->fieldHelper->hasCreatedByField()) {
            $orGroup['createdById'] = $this->user->getId();
        }

        if (empty($orGroup)) {
            $queryBuilder->where(['id' => null]);

            return;
        }

        $queryBuilder->where(['OR' => $orGroup]);
    }
}
