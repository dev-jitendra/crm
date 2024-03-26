<?php


namespace Espo\Tools\MassUpdate;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\FieldProcessing\LinkMultiple\ListLoader as LinkMultipleLoader;
use Espo\Core\FieldProcessing\Loader\Params as LoaderParams;
use Espo\Core\MassAction\QueryBuilder;
use Espo\Core\MassAction\Params;
use Espo\Core\MassAction\Result;
use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Record\Access\LinkCheck;
use Espo\Core\Record\ActionHistory\Action as RecordAction;
use Espo\Core\Record\ServiceFactory;
use Espo\Core\Record\Service;
use Espo\Core\Utils\FieldUtil;
use Espo\Core\Exceptions\Forbidden;
use Espo\ORM\EntityManager;
use Espo\ORM\Entity;
use Espo\Repositories\Attachment as AttachmentRepository;
use Espo\Entities\User;
use Espo\Entities\Attachment;

use Exception;
use RuntimeException;
use stdClass;

class Processor
{
    private const PERMISSION = 'massUpdatePermission';

    public function __construct(
        private ValueMapPreparator $valueMapPreparator,
        private QueryBuilder $queryBuilder,
        private Acl $acl,
        private ServiceFactory $serviceFactory,
        private EntityManager $entityManager,
        private FieldUtil $fieldUtil,
        private User $user,
        private LinkCheck $linkCheck,
        private LinkMultipleLoader $linkMultipleLoader
    ) {}

    
    public function process(Params $params, Data $data): Result
    {
        $entityType = $params->getEntityType();

        if (!$this->acl->check($entityType, Table::ACTION_EDIT)) {
            throw new Forbidden("No edit access for '{$entityType}'.");
        }

        if ($this->acl->getPermissionLevel(self::PERMISSION) !== Table::LEVEL_YES) {
            throw new Forbidden("No mass-update permission.");
        }

        $service = $this->serviceFactory->create($entityType);

        $filteredData = $this->filterData($data, $service);

        if ($filteredData->getAttributeList() === []) {
            return new Result(0, []);
        }

        $copyFieldList = $this->detectFieldToCopyList($entityType, $filteredData);

        $query = $this->queryBuilder->build($params);

        $collection = $this->entityManager
            ->getRDBRepository($entityType)
            ->clone($query)
            ->sth()
            ->find();

        $ids = [];
        $count = 0;

        foreach ($collection as $i => $entity) {
            $itemResult = $this->processEntity($entity, $filteredData, $i, $copyFieldList, $service);

            if (!$itemResult) {
                continue;
            }

            $ids[] = $entity->getId();
            $count++;
        }

        return new Result($count, $ids);
    }

    
    private function filterData(Data $data, Service $service): Data
    {
        $filteredData = $data;

        $values = $data->getValues();

        $service->filterUpdateInput($values);
        $service->sanitizeInput($values);

        foreach ($data->getAttributeList() as $attribute) {
            if (!property_exists($values, $attribute)) {
                $filteredData = $filteredData->without($attribute);

                continue;
            }

            $action = $filteredData->getAction($attribute) ?? Action::UPDATE;
            $value = $values->$attribute;

            $filteredData = $filteredData->with($attribute, $value, $action);
        }

        return $filteredData;
    }

    
    private function processEntity(Entity $entity, Data $data, int $i, array $fieldToCopyList, Service $service): bool
    {
        if (!$this->acl->check($entity, Table::ACTION_EDIT)) {
            return false;
        }

        $values = $this->prepareItemValueMap($entity, $data, $i, $fieldToCopyList);

        
        $this->linkMultipleLoader->process(
            $entity,
            LoaderParams::create()
                ->withSelect($data->getAttributeList())
        );

        $entity->set($values);

        try {
            $service->processValidation($entity, $values);
        }
        catch (Exception) {
            return false;
        }

        if (!$service->checkAssignment($entity)) {
            return false;
        }

        try {
            $this->linkCheck->processFields($entity);
        }
        catch (Forbidden) {
            return false;
        }

        $this->entityManager->saveEntity($entity, [
            'massUpdate' => true,
            'skipStreamNotesAcl' => true,
            'modifiedById' => $this->user->getId(),
        ]);

        $service->processActionHistoryRecord(RecordAction::UPDATE, $entity);

        return true;
    }

    
    private function prepareItemValueMap(Entity $entity, Data $data, int $i, array $copyFieldList): stdClass
    {
        $dataModified = $this->copy($entity->getEntityType(), $data, $i, $copyFieldList);

        return $this->valueMapPreparator->prepare($entity, $dataModified);
    }

    
    private function copy(string $entityType, Data $data, int $i, array $copyFieldList): Data
    {
        if (!count($copyFieldList)) {
            return $data;
        }

        if ($i === 0) {
            return $data;
        }

        foreach ($copyFieldList as $field) {
            $type = $this->fieldUtil->getEntityTypeFieldParam($entityType, $field, 'type');

            if ($type === 'file' || $type === 'image') {
                $data = $this->copyFileField($field, $data);

                continue;
            }

             if ($type === 'attachmentMultiple') {
                $data = $this->copyAttachmentMultipleField($field, $data);

                continue;
            }
        }

        return $data;
    }

    private function copyFileField(string $field, Data $data): Data
    {
        $attribute = $field . 'Id';

        $id = $data->getValue($attribute);

        if (!$id) {
            return $data;
        }

        $attachment = $this->entityManager->getEntityById(Attachment::ENTITY_TYPE, $id);

        if (!$attachment) {
            return $data->with($attribute, null);
        }

        
        $attachmentRepository = $this->entityManager->getRepository(Attachment::ENTITY_TYPE);

        $copiedAttachment = $attachmentRepository->getCopiedAttachment($attachment);

        return $data->with($attribute, $copiedAttachment->getId());
    }

    private function copyAttachmentMultipleField(string $field, Data $data): Data
    {
        $attribute = $field . 'Ids';

        $ids = $data->getValue($attribute) ?? [];

        if (!is_array($ids)) {
            throw new RuntimeException("Bad link-multiple-ids value.");
        }

        if (!count($ids)) {
            return $data;
        }

        
        $attachmentRepository = $this->entityManager->getRepository(Attachment::ENTITY_TYPE);

        $copiedIds = [];

        foreach ($ids as $id) {
            $attachment = $this->entityManager->getEntityById(Attachment::ENTITY_TYPE, $id);

            if (!$attachment) {
                continue;
            }

            $copiedIds[] = $attachmentRepository
                ->getCopiedAttachment($attachment)
                ->getId();
        }

        return $data->with($attribute, $copiedIds);
    }

    
    private function detectFieldToCopyList(string $entityType, Data $data): array
    {
        $resultFieldList = [];

        $fieldList = array_merge(
            $this->fieldUtil->getFieldByTypeList($entityType, 'file'),
            $this->fieldUtil->getFieldByTypeList($entityType, 'image'),
            $this->fieldUtil->getFieldByTypeList($entityType, 'attachmentMultiple')
        );

        foreach ($fieldList as $field) {
            $actualAttributeList = $this->fieldUtil->getActualAttributeList($entityType, $field);

            $met = false;

            foreach ($actualAttributeList as $attribute) {
                if ($data->getValue($attribute)) {
                    $met = true;
                }
            }

            if ($met) {
                $resultFieldList[] = $field;
            }
        }

        return $resultFieldList;
    }
}
