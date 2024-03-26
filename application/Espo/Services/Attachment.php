<?php


namespace Espo\Services;

use Espo\ORM\Entity;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\Error;

use Espo\Entities\Attachment as AttachmentEntity;

use Espo\Tools\Attachment\AccessChecker;
use Espo\Tools\Attachment\Checker;

use Espo\Tools\Attachment\DetailsObtainer;
use Espo\Tools\Attachment\FieldData;
use stdClass;


class Attachment extends Record
{
    
    protected $notFilteringAttributeList = ['contents'];

    protected function afterCreateEntity(Entity $entity, $data)
    {
        if (!empty($data->file)) {
            $entity->clear('contents');
        }
    }

    public function filterUpdateInput(stdClass $data): void
    {
        parent::filterUpdateInput($data);

        unset($data->parentId);
        unset($data->parentType);
        unset($data->relatedId);
        unset($data->relatedType);
        unset($data->isBeingUploaded);
        unset($data->storage);
    }

    
    public function filterCreateInput(stdClass $data): void
    {
        parent::filterCreateInput($data);

        unset($data->parentId);
        unset($data->relatedId);

        $isBeingUploaded = (bool) ($data->isBeingUploaded ?? false);

        $contents = '';

        if (!$isBeingUploaded) {
            if (!property_exists($data, 'file')) {
                throw new BadRequest("No file contents.");
            }

            if (!is_string($data->file)) {
                throw new BadRequest("Non-string file contents.");
            }

            $arr = explode(',', $data->file);

            if (count($arr) > 1) {
                $contents = $arr[1];
            }

            $contents = base64_decode($contents);
        }

        $data->contents = $contents;

        $relatedEntityType = null;

        if (isset($data->parentType)) {
            $relatedEntityType = $data->parentType;

            unset($data->relatedType);
        }
        else if (isset($data->relatedType)) {
            $relatedEntityType = $data->relatedType;
        }

        $field = $data->field ?? null;
        $role = $data->role ?? AttachmentEntity::ROLE_ATTACHMENT;

        if (!$relatedEntityType || !$field) {
            throw new BadRequest("No `field` and `parentType`.");
        }

        $fieldData = new FieldData(
            $field,
            $data->parentType ?? null,
            $data->relatedType ?? null
        );

        $this->getAccessChecker()->check($fieldData, $role);

        $size = mb_strlen($contents, '8bit');

        $dummy = $this->entityManager->getRepositoryByClass(AttachmentEntity::class)->getNew();

        $dummy->set([
            'parentType' => $data->parentType ?? null,
            'relatedType' => $data->relatedType ?? null,
            'field' => $data->field ?? null,
            'role' => $role,
        ]);

        $maxSize = $this->getDetailsObtainer()->getUploadMaxSize($dummy);

        if ($maxSize && $size > $maxSize * 1024 * 1024) {
            throw new Error("File size should not exceed {$maxSize} Mb.");
        }
    }

    
    protected function beforeCreateEntity(Entity $entity, $data)
    {
        $storage = $entity->getStorage();

        $availableStorageList = $this->config->get('attachmentAvailableStorageList') ?? [];

        if (
            $storage &&
            (
                !in_array($storage, $availableStorageList) ||
                !$this->metadata->get(['app', 'fileStorage', 'implementationClassNameMap', $storage])
            )
        ) {
            $entity->clear('storage');
        }

        if (!$entity->getRole()) {
            $entity->set('role', AttachmentEntity::ROLE_ATTACHMENT);
        }

        $size = $entity->getSize();

        $maxSize = $this->getDetailsObtainer()->getUploadMaxSize($entity);

        
        if ($size && $size > $maxSize) {
            throw new Forbidden("Attachment size exceeds `attachmentUploadMaxSize`.");
        }

        $this->getChecker()->checkType($entity);
    }

    private function getChecker(): Checker
    {
        return $this->injectableFactory->create(Checker::class);
    }

    private function getDetailsObtainer(): DetailsObtainer
    {
        return $this->injectableFactory->create(DetailsObtainer::class);
    }

    private function getAccessChecker(): AccessChecker
    {
        return $this->injectableFactory->create(AccessChecker::class);
    }
}
