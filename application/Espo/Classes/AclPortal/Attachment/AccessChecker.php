<?php


namespace Espo\Classes\AclPortal\Attachment;

use Espo\Entities\Attachment;
use Espo\Entities\Note;
use Espo\Entities\Settings;
use Espo\Entities\User;
use Espo\ORM\Entity;
use Espo\Core\Acl\AccessEntityCREDChecker;
use Espo\Core\Acl\ScopeData;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Portal\Acl\DefaultAccessChecker;
use Espo\Core\Portal\Acl\Traits\DefaultAccessCheckerDependency;
use Espo\Core\Portal\AclManager;


class AccessChecker implements AccessEntityCREDChecker
{
    use DefaultAccessCheckerDependency;

    private DefaultAccessChecker $defaultAccessChecker;
    private AclManager $aclManager;
    private EntityManager $entityManager;

    public function __construct(
        DefaultAccessChecker $defaultAccessChecker,
        AclManager $aclManager,
        EntityManager $entityManager
    ) {
        $this->defaultAccessChecker = $defaultAccessChecker;
        $this->aclManager = $aclManager;
        $this->entityManager = $entityManager;
    }

    public function checkEntityRead(User $user, Entity $entity, ScopeData $data): bool
    {
        

        if ($entity->get('parentType') === Settings::ENTITY_TYPE) {
            
            return true;
        }

        $parent = null;

        $parentType = $entity->get('parentType');
        $parentId = $entity->get('parentId');

        $relatedType = $entity->get('relatedType');
        $relatedId = $entity->get('relatedId');

        if ($parentId && $parentType) {
            $parent = $this->entityManager->getEntityById($parentType, $parentId);
        }
        else if ($relatedId && $relatedType) {
            $parent = $this->entityManager->getEntityById($relatedType, $relatedId);
        }

        if (!$parent) {
            if ($entity->get('createdById') === $user->getId()) {
                return true;
            }

            return false;
        }

        if ($parent->getEntityType() === Note::ENTITY_TYPE) {
            
            $result = $this->checkEntityReadNoteParent($user, $parent);

            if ($result !== null) {
                return $result;
            }
        }
        else if ($this->aclManager->checkEntity($user, $parent)) {
            if (
                $entity->getTargetField() &&
                in_array(
                    $entity->getTargetField(),
                    $this->aclManager->getScopeForbiddenFieldList($user, $parent->getEntityType())
                )
            ) {
                return false;
            }

            return true;
        }

        if ($this->defaultAccessChecker->checkEntityRead($user, $entity, $data)) {
            return true;
        }

        return false;
    }

    private function checkEntityReadNoteParent(User $user, Note $note): ?bool
    {
        if ($note->isInternal()) {
            return false;
        }

        if ($note->getTargetType() === Note::TARGET_PORTALS) {
            $intersect = array_intersect(
                $note->getLinkMultipleIdList('portals'),
                $user->getLinkMultipleIdList('portals')
            );

            if (count($intersect)) {
                return true;
            }

            return false;
        }

        if ($note->getTargetType() === Note::TARGET_USERS) {
            $isRelated = $this->entityManager
                ->getRDBRepository(Note::ENTITY_TYPE)
                ->getRelation($note, 'users')
                ->isRelated($user);

            if ($isRelated) {
                return true;
            }

            return false;
        }

        if (!$note->getParentId() || !$note->getParentType()) {
            return null;
        }

        $parent = $this->entityManager->getEntity($note->getParentType(), $note->getParentId());

        if ($parent && $this->aclManager->checkEntity($user, $parent)) {
            return true;
        }

        return null;
    }
}
