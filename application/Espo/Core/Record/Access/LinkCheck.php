<?php


namespace Espo\Core\Record\Access;

use Espo\Core\Acl;
use Espo\Core\Acl\LinkChecker;
use Espo\Core\Acl\LinkChecker\LinkCheckerFactory;
use Espo\Core\Acl\Table as AclTable;
use Espo\Core\Exceptions\Error\Body as ErrorBody;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\ForbiddenSilent;
use Espo\Core\InjectableFactory;
use Espo\Core\ORM\Type\FieldType;
use Espo\Core\Utils\Metadata;
use Espo\Entities\User;
use Espo\Modules\Crm\Entities\Account;
use Espo\Modules\Crm\Entities\Contact;
use Espo\ORM\Defs;
use Espo\ORM\Defs\EntityDefs;
use Espo\ORM\Defs\FieldDefs;
use Espo\ORM\Defs\RelationDefs;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use Espo\ORM\Type\RelationType;


class LinkCheck
{
    
    private $linkCheckerCache = [];

    
    private array $oneFieldTypeList = [
        FieldType::LINK,
        FieldType::LINK_ONE,
        FieldType::FILE,
        FieldType::IMAGE,
    ];

    
    private array $manyFieldTypeList = [
        FieldType::LINK_MULTIPLE,
        FieldType::ATTACHMENT_MULTIPLE,
    ];

    
    public function __construct(
        private Defs $ormDefs,
        private EntityManager $entityManager,
        private Acl $acl,
        private Metadata $metadata,
        private InjectableFactory $injectableFactory,
        private User $user,
        private array $noEditAccessRequiredLinkList = [],
        private bool $noEditAccessRequiredForLink = false
    ) {}

    
    public function processFields(Entity $entity): void
    {
        $this->processLinkMultipleFields($entity);
        $this->processLinkFields($entity);
    }

    
    private function processLinkMultipleFields(Entity $entity): void
    {
        $entityType = $entity->getEntityType();

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entityType);

        $typeList = [
            Entity::HAS_MANY,
            Entity::MANY_MANY,
            Entity::HAS_CHILDREN,
        ];

        foreach ($entityDefs->getRelationList() as $relationDefs) {
            $name = $relationDefs->getName();

            if (!in_array($relationDefs->getType(), $typeList)) {
                continue;
            }

            $attribute = $name . 'Ids';

            if (
                !$entityDefs->hasAttribute($attribute) ||
                !$entity->isAttributeChanged($attribute)
            ) {
                continue;
            }

            
            $ids = $entity->get($attribute) ?? [];
            
            $oldIds = $entity->getFetched($attribute) ?? [];

            $ids = array_values(array_diff($ids, $oldIds));
            $removedIds = array_values(array_diff($oldIds, $ids));

            if ($ids === [] && $removedIds === []) {
                continue;
            }

            $this->processCheckLinkWithoutField($entityDefs, $name, $this->manyFieldTypeList);

            if ($ids === []) {
                continue;
            }

            foreach ($ids as $id) {
                $this->processLinkedRecordsCheckItem($entity, $relationDefs, $id);
            }
        }
    }

    
    private function processCheckLinkWithoutField(EntityDefs $entityDefs, string $name, array $fieldTypes): void
    {
        $hasField =
            $entityDefs->hasField($name) &&
            in_array($entityDefs->getField($name)->getType(), $fieldTypes);

        if ($hasField) {
            return;
        }

        $forbiddenLinkList = $this->acl->getScopeForbiddenLinkList($entityDefs->getName(), AclTable::ACTION_EDIT);

        if (!in_array($name, $forbiddenLinkList)) {
            return;
        }

        throw ForbiddenSilent::createWithBody(
            "No access to link $name.",
            ErrorBody::create()
                ->withMessageTranslation('cannotRelateForbiddenLink', null, ['link' => $name])
                ->encode()
        );
    }

    
    private function processLinkedRecordsCheckItem(
        Entity $entity,
        RelationDefs $defs,
        string $id,
        bool $isOne = false
    ): void {

        $entityType = $entity->getEntityType();
        $link = $defs->getName();

        if ($this->getParam($entityType, $link, 'linkCheckDisabled')) {
            return;
        }

        $foreignEntityType = null;

        if ($defs->getType() === RelationType::BELONGS_TO_PARENT) {
            $foreignEntityType = $entity->get($link . 'Type');
        }

        if (!$foreignEntityType && !$defs->hasForeignEntityType()) {
            return;
        }

        $foreignEntityType ??= $defs->getForeignEntityType();

        $foreignEntity = $this->entityManager->getEntityById($foreignEntityType, $id);

        if (!$foreignEntity) {
            throw ForbiddenSilent::createWithBody(
                "Can't relate with non-existing record. entity type: $entityType, link: $link.",
                ErrorBody::create()
                    ->withMessageTranslation(
                        'cannotRelateNonExisting', null, ['foreignEntityType' => $foreignEntityType])
                    ->encode()
            );
        }

        $toSkip = $this->linkForeignAccessCheck($isOne, $entityType, $link, $foreignEntity);

        if ($toSkip) {
            return;
        }

        $this->linkEntityAccessCheck($entity, $foreignEntity, $link);
    }

    
    private function linkForeignAccessCheck(
        bool $isOne,
        string $entityType,
        string $link,
        Entity $foreignEntity
    ): bool {

        if ($isOne) {
            return $this->linkForeignAccessCheckOne($entityType, $link, $foreignEntity);
        }

        return $this->linkForeignAccessCheckMany($entityType, $link, $foreignEntity, true);
    }

    private function getParam(string $entityType, string $link, string $param): mixed
    {
        return $this->metadata->get(['recordDefs', $entityType, 'relationships', $link, $param]);
    }

    
    public function processLink(Entity $entity, string $link): void
    {
        $entityType = $entity->getEntityType();

        
        $action = $this->getParam($entityType, $link, 'linkRequiredAccess');

        if (!$action) {
            $action = $this->noEditAccessRequiredForLink ?
                AclTable::ACTION_READ :
                AclTable::ACTION_EDIT;
        }

        if (!$this->acl->check($entity, $action)) {
            throw ForbiddenSilent::createWithBody(
                "No record access for link operation ($entityType:$link).",
                ErrorBody::create()
                    ->withMessageTranslation('noAccessToRecord', null, ['action' => $action])
                    ->encode()
            );
        }
    }

    
    public function processUnlink(Entity $entity, string $link): void
    {
        $this->processLink($entity, $link);
    }

    
    public function processLinkForeign(Entity $entity, string $link, Entity $foreignEntity): void
    {
        $toSkip = $this->linkForeignAccessCheckMany($entity->getEntityType(), $link, $foreignEntity);

        if ($toSkip) {
            return;
        }

        $this->linkEntityAccessCheck($entity, $foreignEntity, $link);
    }

    
    public function processUnlinkForeign(Entity $entity, string $link, Entity $foreignEntity): void
    {
        $this->processLinkForeign($entity, $link, $foreignEntity);
        $this->processUnlinkForeignRequired($entity, $link, $foreignEntity);
    }

    
    private function linkForeignAccessCheckMany(
        string $entityType,
        string $link,
        Entity $foreignEntity,
        bool $fromUpdate = false
    ): bool {

        $action = in_array($link, $this->noEditAccessRequiredLinkList) ?
            AclTable::ACTION_READ :
            null;

        if (!$action) {
            
            $action = $this->getParam($entityType, $link, 'linkRequiredForeignAccess') ?? AclTable::ACTION_EDIT;
        }

        if ($this->getParam($entityType, $link, 'linkForeignAccessCheckDisabled')) {
            return true;
        }

        $fieldDefs = $fromUpdate ?
            $this->entityManager
                ->getDefs()
                ->getEntity($entityType)
                ->tryGetField($link) :
            null;

        if (
            $fromUpdate &&
            $fieldDefs &&
            in_array($fieldDefs->getType(), $this->manyFieldTypeList)
        ) {
            $action = AclTable::ACTION_READ;

            if ($this->checkInDefaults($fieldDefs, $link, $foreignEntity)) {
                return true;
            }
        }

        if (
            $action === AclTable::ACTION_READ &&
            $this->checkIsAllowedForPortal($foreignEntity)
        ) {
            return true;
        }

        if ($this->acl->check($foreignEntity, $action)) {
            return false;
        }

        if ($this->getLinkChecker($entityType, $link)) {
            return false;
        }

        $body = ErrorBody::create();

        $body = $fromUpdate ?
            $body->withMessageTranslation('cannotRelateForbidden', null, [
                'foreignEntityType' => $foreignEntity->getEntityType(),
                'action' => $action,
            ]) :
            $body->withMessageTranslation('noAccessToForeignRecord', null, ['action' => $action]);

        throw ForbiddenSilent::createWithBody(
            "No foreign record access for link operation ($entityType:$link).",
            $body->encode()
        );
    }

    public function checkIsAllowedForPortal(Entity $foreignEntity): bool
    {
        if (!$this->user->isPortal()) {
            return false;
        }

        if (
            $foreignEntity->getEntityType() === Account::ENTITY_TYPE &&
            $this->user->getAccounts()->hasId($foreignEntity->getId())
        ) {
            return true;
        }

        if (
            $foreignEntity->getEntityType() === Contact::ENTITY_TYPE &&
            $this->user->getContactId() === $foreignEntity->getId()
        ) {
            return true;
        }

        return false;
    }

    
    private function linkEntityAccessCheck(Entity $entity, Entity $foreignEntity, string $link): void
    {
        $entityType = $entity->getEntityType();

        $checker = $this->getLinkChecker($entityType, $link);

        if (!$checker) {
            return;
        }

        if ($checker->check($this->user, $entity, $foreignEntity)) {
            return;
        }

        throw ForbiddenSilent::createWithBody(
            "No access for link operation ($entityType:$link).",
            ErrorBody::create()
                ->withMessageTranslation('noLinkAccess', null, [
                    'foreignEntityType' => $foreignEntity->getEntityType(),
                    'link' => $link,
                ])
        );
    }

    
    private function getLinkChecker(string $entityType, string $link): ?LinkChecker
    {
        $key = $entityType . '_' . $link;

        if (array_key_exists($key, $this->linkCheckerCache)) {
            return $this->linkCheckerCache[$key];
        }

        $factory = $this->injectableFactory->create(LinkCheckerFactory::class);

        if (!$factory->isCreatable($entityType, $link)) {
            return null;
        }

        $checker = $factory->create($entityType, $link);

        $this->linkCheckerCache[$link] = $checker;

        return $checker;
    }

    
    private function processUnlinkForeignRequired(Entity $entity, string $link, Entity $foreignEntity): void
    {
        $relationDefs = $this->ormDefs
            ->getEntity($entity->getEntityType())
            ->tryGetRelation($link);

        if (!$relationDefs) {
            return;
        }

        if (
            !$relationDefs->hasForeignEntityType() ||
            !$relationDefs->hasForeignRelationName()
        ) {
            return;
        }

        $foreignLink = $relationDefs->getForeignRelationName();

        $foreignRelationDefs = $this->ormDefs
            ->getEntity($foreignEntity->getEntityType())
            ->tryGetRelation($foreignLink);

        if (!$foreignRelationDefs) {
            return;
        }

        if (
            !in_array($foreignRelationDefs->getType(), [
                RelationType::BELONGS_TO,
                RelationType::HAS_ONE,
                RelationType::BELONGS_TO_PARENT,
            ])
        ) {
            return;
        }

        $foreignFieldDefs = $this->ormDefs
            ->getEntity($foreignEntity->getEntityType())
            ->tryGetField($foreignLink);

        if (!$foreignFieldDefs) {
            return;
        }

        if (!$foreignFieldDefs->getParam('required')) {
            return;
        }

        throw ForbiddenSilent::createWithBody(
            "Can't unlink required field ({$foreignEntity->getEntityType()}:$foreignLink}).",
            ErrorBody::create()
                ->withMessageTranslation('cannotUnrelateRequiredLink')
                ->encode()
        );
    }

    
    private function processLinkFields(Entity $entity): void
    {
        $entityType = $entity->getEntityType();

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entityType);

        $typeList = [
            Entity::BELONGS_TO,
            Entity::BELONGS_TO_PARENT,
            Entity::HAS_ONE,
        ];

        foreach ($entityDefs->getRelationList() as $relationDefs) {
            $name = $relationDefs->getName();
            $attribute = $name . 'Id';

            if (
                !in_array($relationDefs->getType(), $typeList) ||
                !$entityDefs->hasAttribute($attribute) ||
                !$entity->isAttributeChanged($attribute) ||
                $entity->get($attribute) === null
            ) {
                continue;
            }

            $this->processCheckLinkWithoutField($entityDefs, $name, $this->oneFieldTypeList);

            $id = $entity->get($attribute);

            $this->processLinkedRecordsCheckItem($entity, $relationDefs, $id, true);
        }
    }

    
    private function linkForeignAccessCheckOne(string $entityType, string $link, Entity $foreignEntity): bool
    {
        if ($this->getParam($entityType, $link, 'linkForeignAccessCheckDisabled')) {
            return true;
        }

        $fieldDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entityType)
            ->tryGetField($link);

        if (
            $fieldDefs &&
            in_array($fieldDefs->getType(), $this->oneFieldTypeList)
        ) {
            if ($this->checkIsDefault($fieldDefs, $link, $foreignEntity)) {
                return true;
            }
        }

        if ($this->checkIsAllowedForPortal($foreignEntity)) {
            return true;
        }

        if ($this->acl->check($foreignEntity, AclTable::ACTION_READ)) {
            return false;
        }

        if ($this->getLinkChecker($entityType, $link)) {
            return false;
        }

        throw ForbiddenSilent::createWithBody(
            "No foreign record access for link operation ($entityType:$link).",
            ErrorBody::create()
                ->withMessageTranslation('cannotRelateForbidden', null, [
                    'foreignEntityType' => $foreignEntity->getEntityType(),
                    'action' => AclTable::ACTION_READ,
                ])
                ->encode()
        );
    }

    private function checkInDefaults(FieldDefs $fieldDefs, string $link, Entity $foreignEntity): bool
    {
        
        $defaults = $this->getDefault($fieldDefs,  $link . 'Ids') ?? [];

        return in_array($foreignEntity->getId(), $defaults);
    }

    private function checkIsDefault(FieldDefs $fieldDefs, string $link, Entity $foreignEntity): bool
    {
        return $foreignEntity->getId() === $this->getDefault($fieldDefs, $link . 'Id');
    }
    private function getDefault(FieldDefs $fieldDefs, string $attribute): mixed
    {
        $defaultAttributes = (object) ($fieldDefs->getParam('defaultAttributes') ?? []);

        return $defaultAttributes->$attribute ?? null;
    }
}
