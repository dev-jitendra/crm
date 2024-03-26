<?php


namespace Espo\Core\Repositories;

use Espo\Core\ORM\Repository\Option\SaveOption;
use Espo\Core\Utils\SystemUser;
use Espo\ORM\BaseEntity;
use Espo\ORM\Entity;
use Espo\ORM\Repository\RDBRepository;
use Espo\Core\ORM\EntityFactory;
use Espo\Core\ORM\EntityManager;
use Espo\Core\ORM\Repository\HookMediator;
use Espo\Core\ApplicationState;
use Espo\Core\HookManager;
use Espo\Core\Utils\DateTime as DateTimeUtil;
use Espo\Core\Utils\Id\RecordIdGenerator;
use Espo\Core\Utils\Metadata;


class Database extends RDBRepository
{
    private const ATTR_ID = 'id';
    private const ATTR_CREATED_BY_ID = 'createdById';
    private const ATTR_MODIFIED_BY_ID = 'modifiedById';
    private const ATTR_MODIFIED_BY_NAME = 'modifiedByName';
    private const ATTR_CREATED_AT = 'createdAt';
    private const ATTR_MODIFIED_AT = 'modifiedAt';

    
    protected $hooksDisabled = false;

    
    private $restoreData = null;
    
    protected $metadata;
    
    protected $hookManager;
    
    protected $applicationState;
    
    protected $recordIdGenerator;

    public function __construct(
        string $entityType,
        EntityManager $entityManager,
        EntityFactory $entityFactory,
        Metadata $metadata,
        HookManager $hookManager,
        ApplicationState $applicationState,
        RecordIdGenerator $recordIdGenerator,
        private SystemUser $systemUser
    ) {
        $this->metadata = $metadata;
        $this->hookManager = $hookManager;
        $this->applicationState = $applicationState;
        $this->recordIdGenerator = $recordIdGenerator;

        $hookMediator = null;

        if (!$this->hooksDisabled) {
            $hookMediator = new HookMediator($hookManager);
        }

        parent::__construct($entityType, $entityManager, $entityFactory, $hookMediator);
    }

    
    protected function getMetadata() 
    {
        return $this->metadata;
    }

    
    public function handleSelectParams(&$params) 
    {}

    
    public function save(Entity $entity, array $options = []): void
    {
        if (
            $entity->isNew() &&
            !$entity->has(self::ATTR_ID) &&
            !$this->getAttributeParam($entity, self::ATTR_ID, 'autoincrement')
        ) {
            $entity->set(self::ATTR_ID, $this->recordIdGenerator->generate());
        }

        if (empty($options[SaveOption::SKIP_ALL])) {
            $this->processCreatedAndModifiedFieldsSave($entity, $options);
        }

        $this->restoreData = [];

        parent::save($entity, $options);
    }

    
    protected function beforeRemove(Entity $entity, array $options = [])
    {
        parent::beforeRemove($entity, $options);

        if (!$this->hooksDisabled && empty($options[SaveOption::SKIP_HOOKS])) {
            $this->hookManager->process($this->entityType, 'beforeRemove', $entity, $options);
        }

        $nowString = DateTimeUtil::getSystemNowString();

        if ($entity->hasAttribute(self::ATTR_MODIFIED_AT)) {
            $entity->set(self::ATTR_MODIFIED_AT, $nowString);
        }

        if ($entity->hasAttribute(self::ATTR_MODIFIED_BY_ID)) {
            $modifiedById = $options[SaveOption::MODIFIED_BY_ID] ?? null;

            if ($modifiedById === SystemUser::NAME) {
                
                $modifiedById = $this->systemUser->getId();
            }

            if (!$modifiedById && $this->applicationState->hasUser()) {
                $modifiedById = $this->applicationState->getUser()->getId();
            }

            if ($modifiedById) {
                $entity->set(self::ATTR_MODIFIED_BY_ID, $modifiedById);
            }
        }
    }

    
    protected function afterRemove(Entity $entity, array $options = [])
    {
        parent::afterRemove($entity, $options);

        if (!$this->hooksDisabled && empty($options[SaveOption::SKIP_HOOKS])) {
            $this->hookManager->process($this->entityType, 'afterRemove', $entity, $options);
        }
    }

    
    protected function afterMassRelate(Entity $entity, $relationName, array $params = [], array $options = [])
    {
        if ($this->hooksDisabled || !empty($options[SaveOption::SKIP_HOOKS])) {
            return;
        }

        $hookData = [
            'relationName' => $relationName,
            'relationParams' => $params,
        ];

        $this->hookManager->process(
            $this->entityType,
            'afterMassRelate',
            $entity,
            $options,
            $hookData
        );
    }

    
    protected function afterRelate(Entity $entity, $relationName, $foreign, $data = null, array $options = [])
    {
        parent::afterRelate($entity, $relationName, $foreign, $data, $options);

        if ($this->hooksDisabled || !empty($options[SaveOption::SKIP_HOOKS])) {
            return;
        }

        if (is_string($foreign)) {
            $foreignId = $foreign;

            $foreignEntityType = $this->getRelationParam($entity, $relationName, 'entity');

            if ($foreignEntityType) {
                $foreign = $this->entityManager->getNewEntity($foreignEntityType);

                $foreign->set(self::ATTR_ID, $foreignId);
                $foreign->setAsFetched();
            }
        }

        if ($foreign instanceof Entity) {
            if (is_object($data)) {
                $data = (array) $data;
            }

            $this->hookMediator->afterRelate($entity, $relationName, $foreign, $data, $options);
        }
    }

    
    protected function afterUnrelate(Entity $entity, $relationName, $foreign, array $options = [])
    {
        parent::afterUnrelate($entity, $relationName, $foreign, $options);

        if ($this->hooksDisabled || !empty($options[SaveOption::SKIP_HOOKS])) {
            return;
        }

        if (is_string($foreign)) {
            $foreignId = $foreign;

            $foreignEntityType = $this->getRelationParam($entity, $relationName, 'entity');

            if ($foreignEntityType) {
                $foreign = $this->entityManager->getNewEntity($foreignEntityType);

                $foreign->set(self::ATTR_ID, $foreignId);
                $foreign->setAsFetched();
            }
        }

        if ($foreign instanceof Entity) {
            $this->hookMediator->afterUnrelate($entity, $relationName, $foreign, $options);
        }
    }

    
    protected function beforeSave(Entity $entity, array $options = [])
    {
        parent::beforeSave($entity, $options);

        if (!$this->hooksDisabled && empty($options[SaveOption::SKIP_HOOKS])) {
            $this->hookManager->process($this->entityType, 'beforeSave', $entity, $options);
        }
    }

    
    protected function afterSave(Entity $entity, array $options = [])
    {
        if (!empty($this->restoreData)) {
            $entity->set($this->restoreData);

            $this->restoreData = null;
        }

        parent::afterSave($entity, $options);

        if (!$this->hooksDisabled && empty($options[SaveOption::SKIP_HOOKS])) {
            $this->hookManager->process($this->entityType, 'afterSave', $entity, $options);
        }
    }

    
    private function processCreatedAndModifiedFieldsSave(Entity $entity, array $options): void
    {
        if ($entity->isNew()) {
            $this->processCreatedAndModifiedFieldsSaveNew($entity, $options);

            return;
        }

        $nowString = DateTimeUtil::getSystemNowString();

        if (
            !empty($options[SaveOption::SILENT]) ||
            !empty($options[SaveOption::SKIP_MODIFIED_BY])
        ) {
            return;
        }

        $isChanged = false;

        foreach ($entity->getAttributeList() as $attribute) {
            if ($entity->isAttributeChanged($attribute)) {
                $isChanged = true;

                break;
            }
        }

        if (!$isChanged && empty($options[SaveOption::MODIFIED_BY_ID])) {
            return;
        }

        if ($entity->hasAttribute(self::ATTR_MODIFIED_AT)) {
            $entity->set(self::ATTR_MODIFIED_AT, $nowString);
        }

        if ($entity->hasAttribute(self::ATTR_MODIFIED_BY_ID)) {
            $modifiedById = $options[SaveOption::MODIFIED_BY_ID] ?? null;

            if ($modifiedById === SystemUser::NAME) {
                
                $modifiedById = $this->systemUser->getId();
            }

            if ($modifiedById) {
                $entity->set(self::ATTR_MODIFIED_BY_ID, $modifiedById);
            }
            else if ($this->applicationState->hasUser()) {
                $user = $this->applicationState->getUser();

                $entity->set(self::ATTR_MODIFIED_BY_ID, $user->getId());
                $entity->set(self::ATTR_MODIFIED_BY_NAME, $user->getName());
            }
        }
    }

    
    private function processCreatedAndModifiedFieldsSaveNew(Entity $entity, array $options): void
    {
        $nowString = DateTimeUtil::getSystemNowString();

        if (
            $entity->hasAttribute(self::ATTR_CREATED_AT) &&
            (empty($options[SaveOption::IMPORT]) || !$entity->has(self::ATTR_CREATED_AT))
        ) {
            $entity->set(self::ATTR_CREATED_AT, $nowString);
        }

        if ($entity->hasAttribute(self::ATTR_MODIFIED_AT)) {
            $entity->set(self::ATTR_MODIFIED_AT, $nowString);
        }

        if ($entity->hasAttribute(self::ATTR_CREATED_BY_ID)) {
            $createdById = $options[SaveOption::CREATED_BY_ID] ?? null;

            if ($createdById) {
                if ($createdById === SystemUser::NAME) {
                    
                    $createdById = $this->systemUser->getId();
                }

                $entity->set(self::ATTR_CREATED_BY_ID, $createdById);
            }
            else if (
                empty($options[SaveOption::SKIP_CREATED_BY]) &&
                (empty($options[SaveOption::IMPORT]) || !$entity->has(self::ATTR_CREATED_BY_ID)) &&
                $this->applicationState->hasUser()
            ) {
                $entity->set(self::ATTR_CREATED_BY_ID, $this->applicationState->getUser()->getId());
            }
        }
    }

    
    private function getAttributeParam(Entity $entity, string $attribute, string $param)
    {
        if ($entity instanceof BaseEntity) {
            return $entity->getAttributeParam($attribute, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entityDefs->hasAttribute($attribute)) {
            return null;
        }

        return $entityDefs->getAttribute($attribute)->getParam($param);
    }

    
    private function getRelationParam(Entity $entity, string $relation, string $param)
    {
        if ($entity instanceof BaseEntity) {
            return $entity->getRelationParam($relation, $param);
        }

        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        if (!$entityDefs->hasRelation($relation)) {
            return null;
        }

        return $entityDefs->getRelation($relation)->getParam($param);
    }
}
