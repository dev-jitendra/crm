<?php


namespace Espo\Modules\Crm\Tools\KnowledgeBase;

use Espo\Core\Acl;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Record\ServiceContainer;
use Espo\Core\Select\SearchParams;
use Espo\Core\Select\SelectBuilderFactory;
use Espo\Core\Select\Where\Item as WhereItem;
use Espo\Entities\Attachment;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\ORM\EntityManager;
use Espo\Repositories\Attachment as AttachmentRepository;
use Espo\Tools\Attachment\AccessChecker as AttachmentAccessChecker;
use Espo\Tools\Attachment\FieldData;

class Service
{
    private EntityManager $entityManager;
    private AttachmentAccessChecker $attachmentAccessChecker;
    private ServiceContainer $serviceContainer;
    private SelectBuilderFactory $selectBuilderFactory;
    private Acl $acl;

    public function __construct(
        EntityManager $entityManager,
        AttachmentAccessChecker $attachmentAccessChecker,
        ServiceContainer $serviceContainer,
        SelectBuilderFactory $selectBuilderFactory,
        Acl $acl
    ) {
        $this->entityManager = $entityManager;
        $this->attachmentAccessChecker = $attachmentAccessChecker;
        $this->serviceContainer = $serviceContainer;
        $this->selectBuilderFactory = $selectBuilderFactory;
        $this->acl = $acl;
    }

    
    public function copyAttachments(string $id, FieldData $fieldData): array
    {
        
        $entity = $this->serviceContainer
            ->get(KnowledgeBaseArticle::ENTITY_TYPE)
            ->getEntity($id);

        if (!$entity) {
            throw new NotFound();
        }

        $this->attachmentAccessChecker->check($fieldData);

        $list = [];

        foreach ($entity->getAttachmentIdList() as $attachmentId) {
            $attachment = $this->copyAttachment($attachmentId, $fieldData);

            if ($attachment) {
                $list[] = $attachment;
            }
        }

        return $list;
    }

    private function copyAttachment(string $attachmentId, FieldData $fieldData): ?Attachment
    {
        
        $attachment = $this->entityManager
            ->getRDBRepositoryByClass(Attachment::class)
            ->getById($attachmentId);

        if (!$attachment) {
            return null;
        }

        $copied = $this->getAttachmentRepository()->getCopiedAttachment($attachment);

        $copied->set('parentType', $fieldData->getParentType());
        $copied->set('relatedType', $fieldData->getRelatedType());
        $copied->setTargetField($fieldData->getField());
        $copied->setRole(Attachment::ROLE_ATTACHMENT);

        $this->getAttachmentRepository()->save($copied);

        return $copied;
    }

    private function getAttachmentRepository(): AttachmentRepository
    {
        
        return $this->entityManager->getRepositoryByClass(Attachment::class);
    }

    
    public function moveUp(string $id, ?WhereItem $where = null): void
    {
        
        $entity = $this->entityManager->getEntityById(KnowledgeBaseArticle::ENTITY_TYPE, $id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden();
        }

        $currentIndex = $entity->getOrder();

        if (!is_int($currentIndex)) {
            throw new Error();
        }

        $params = SearchParams::create();

        if ($where) {
            $params = $params->withWhere($where);
        }

        $query = $this->selectBuilderFactory
            ->create()
            ->from(KnowledgeBaseArticle::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->withSearchParams($params)
            ->buildQueryBuilder()
            ->where([
                'order<' => $currentIndex,
            ])
            ->order([
                ['order', 'DESC'],
            ])
            ->build();

        
        $previousEntity = $this->entityManager
            ->getRDBRepositoryByClass(KnowledgeBaseArticle::class)
            ->clone($query)
            ->findOne();

        if (!$previousEntity) {
            return;
        }

        $entity->set('order', $previousEntity->getOrder());

        $previousEntity->set('order', $currentIndex);

        $this->entityManager->saveEntity($entity);
        $this->entityManager->saveEntity($previousEntity);
    }

    
    public function moveDown(string $id, ?WhereItem $where = null): void
    {
        
        $entity = $this->entityManager->getEntityById(KnowledgeBaseArticle::ENTITY_TYPE, $id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden();
        }

        $currentIndex = $entity->getOrder();

        if (!is_int($currentIndex)) {
            throw new Error();
        }

        $params = SearchParams::create();

        if ($where) {
            $params = $params->withWhere($where);
        }

        $query = $this->selectBuilderFactory
            ->create()
            ->from(KnowledgeBaseArticle::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->withSearchParams($params)
            ->buildQueryBuilder()
            ->where([
                'order>' => $currentIndex,
            ])
            ->order([
                ['order', 'ASC'],
            ])
            ->build();

        
        $nextEntity = $this->entityManager
            ->getRDBRepositoryByClass(KnowledgeBaseArticle::class)
            ->clone($query)
            ->findOne();

        if (!$nextEntity) {
            return;
        }

        $entity->set('order', $nextEntity->getOrder());

        $nextEntity->set('order', $currentIndex);

        $this->entityManager->saveEntity($entity);
        $this->entityManager->saveEntity($nextEntity);
    }

    
    public function moveToTop(string $id, ?WhereItem $where = null): void
    {
        
        $entity = $this->entityManager->getEntityById(KnowledgeBaseArticle::ENTITY_TYPE, $id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden();
        }

        $currentIndex = $entity->getOrder();

        if (!is_int($currentIndex)) {
            throw new Error();
        }

        $params = SearchParams::create();

        if ($where) {
            $params = $params->withWhere($where);
        }

        $query = $this->selectBuilderFactory
            ->create()
            ->from(KnowledgeBaseArticle::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->withSearchParams($params)
            ->buildQueryBuilder()
            ->where([
                'order<' => $currentIndex,
            ])
            ->order([
                ['order', 'ASC'],
            ])
            ->build();

        
        $previousEntity = $this->entityManager
            ->getRDBRepositoryByClass(KnowledgeBaseArticle::class)
            ->clone($query)
            ->findOne();

        if (!$previousEntity) {
            return;
        }

        $entity->set('order', $previousEntity->getOrder() - 1);

        $this->entityManager->saveEntity($entity);
    }

    
    public function moveToBottom(string $id, ?WhereItem $where = null): void
    {
        
        $entity = $this->entityManager->getEntityById(KnowledgeBaseArticle::ENTITY_TYPE, $id);

        if (!$entity) {
            throw new NotFound();
        }

        if (!$this->acl->checkEntityEdit($entity)) {
            throw new Forbidden();
        }

        $currentIndex = $entity->getOrder();

        if (!is_int($currentIndex)) {
            throw new Error();
        }

        $params = SearchParams::create();

        if ($where) {
            $params = $params->withWhere($where);
        }

        $query = $this->selectBuilderFactory
            ->create()
            ->from(KnowledgeBaseArticle::ENTITY_TYPE)
            ->withStrictAccessControl()
            ->withSearchParams($params)
            ->buildQueryBuilder()
            ->where([
                'order>' => $currentIndex,
            ])
            ->order([
                ['order', 'DESC'],
            ])
            ->build();

        
        $nextEntity = $this->entityManager
            ->getRDBRepositoryByClass(KnowledgeBaseArticle::class)
            ->clone($query)
            ->findOne();

        if (!$nextEntity) {
            return;
        }

        $entity->set('order', $nextEntity->getOrder() + 1);

        $this->entityManager->saveEntity($entity);
    }
}
