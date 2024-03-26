<?php


namespace Espo\Services;

use Espo\Entities\EmailAccount as EmailAccountEntity;
use Espo\Entities\EmailFilter as EmailFilterEntity;
use Espo\Entities\InboundEmail as InboundEmailEntity;
use Espo\Entities\User as UserEntity;
use Espo\ORM\Entity;

use Espo\Core\Exceptions\Forbidden;
use stdClass;


class EmailFilter extends Record
{
    
    protected function beforeCreateEntity(Entity $entity, $data)
    {
        parent::beforeCreateEntity($entity, $data);

        
        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden();
        }

        $this->controlEntityValues($entity);
    }

    
    protected function beforeUpdateEntity(Entity $entity, $data)
    {
        parent::beforeUpdateEntity($entity, $data);

        $this->controlEntityValues($entity);
    }

    
    private function controlEntityValues(EmailFilterEntity $entity): void
    {
        if ($entity->isGlobal()) {
            $entity->set('parentId', null);
            $entity->set('parentType', null);

            if ($entity->getAction() !== EmailFilterEntity::ACTION_SKIP) {
                throw new Forbidden("Not allowed `action`.");
            }
        }

        if ($entity->getParentType() && !$entity->getParentId()) {
            throw new Forbidden("Not allowed `parentId` value.");
        }

        if (
            $entity->getParentType() === UserEntity::ENTITY_TYPE &&
            !in_array(
                $entity->getAction(),
                [
                    EmailFilterEntity::ACTION_NONE,
                    EmailFilterEntity::ACTION_SKIP,
                    EmailFilterEntity::ACTION_MOVE_TO_FOLDER,
                ]
            )
        ) {
            throw new Forbidden("Not allowed `action`.");
        }

        if (
            $entity->getParentType() === InboundEmailEntity::ENTITY_TYPE &&
            !in_array(
                $entity->getAction(),
                [
                    EmailFilterEntity::ACTION_SKIP,
                    EmailFilterEntity::ACTION_MOVE_TO_GROUP_FOLDER,
                ]
            )
        ) {
            throw new Forbidden("Not allowed `action`.");
        }

        if (
            $entity->getParentType() === EmailAccountEntity::ENTITY_TYPE &&
            $entity->getAction() !== EmailFilterEntity::ACTION_SKIP
        ) {
            throw new Forbidden("Not allowed `action`.");
        }

        if ($entity->getAction() !== EmailFilterEntity::ACTION_MOVE_TO_FOLDER) {
            $entity->set('emailFolderId', null);
        }

        if ($entity->getAction() !== EmailFilterEntity::ACTION_MOVE_TO_GROUP_FOLDER) {
            $entity->set('groupEmailFolderId', null);
        }
    }

    public function filterUpdateInput(stdClass $data): void
    {
        parent::filterUpdateInput($data);

        unset($data->isGlobal);
        unset($data->parentId);
        unset($data->parentType);
    }
}
