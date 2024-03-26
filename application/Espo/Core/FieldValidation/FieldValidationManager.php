<?php


namespace Espo\Core\FieldValidation;

use Espo\ORM\Entity;

use Espo\Core\FieldValidation\Exceptions\ValidationError;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\FieldUtil;

use LogicException;
use stdClass;
use ReflectionClass;


class FieldValidationManager
{
    
    private array $checkerCache = [];
    
    private array $validatorCache = [];

    private CheckerFactory $checkerFactory;

    public function __construct(
        private Metadata $metadata,
        private FieldUtil $fieldUtil,
        CheckerFactory $factory,
        private ValidatorFactory $validatorFactory
    ) {
        $this->checkerFactory = $factory;
    }

    
    public function process(Entity $entity, ?stdClass $data = null, ?FieldValidationParams $params = null): void
    {
        $this->processInternal($entity, $data, $params, true);
    }

    
    public function processAll(Entity $entity, ?stdClass $data = null, ?FieldValidationParams $params = null): array
    {
        try {
            return $this->processInternal($entity, $data, $params, false);
        }
        catch (ValidationError) {
            throw new LogicException();
        }
    }

    
    private function processInternal(
        Entity $entity,
        ?stdClass $data,
        ?FieldValidationParams $params,
        bool $throw
    ): array {

        $dataIsSet = $data !== null;

        $data ??= (object) [];
        $params ??= new FieldValidationParams();

        $fieldList = array_filter(
            $this->fieldUtil->getEntityTypeFieldList($entity->getEntityType()),
            fn ($field) => !in_array($field, $params->getSkipFieldList())
        );

        $failureList = [];

        foreach ($fieldList as $field) {
            if (
                !$entity->isNew() &&
                $dataIsSet &&
                !$this->isFieldSetInData($entity->getEntityType(), $field, $data)
            ) {
                continue;
            }

            $itemFailureList = $this->processField($entity, $field, $params, $data, $throw);

            $failureList = array_merge($failureList, $itemFailureList);
        }

        return $failureList;
    }

    
    private function getMandatoryValidationList(string $entityType, string $field): array
    {
        
        $fieldType = $this->fieldUtil->getEntityTypeFieldParam($entityType, $field, 'type');

        return
            $this->metadata->get(['entityDefs', $entityType, 'fields', $field, 'mandatoryValidationList']) ??
            $this->metadata->get(['fields', $fieldType ?? '', 'mandatoryValidationList']) ?? [];
    }

    
    private function getValidationList(string $entityType, string $field): array
    {
        
        $fieldType = $this->fieldUtil->getEntityTypeFieldParam($entityType, $field, 'type');

        return
            $this->metadata->get(['entityDefs', $entityType, 'fields', $field, 'validationList']) ??
            $this->metadata->get(['fields', $fieldType ?? '', 'validationList']) ?? [];
    }

    
    public function check(Entity $entity, string $field, string $type, ?stdClass $data = null): bool
    {
        $data ??= (object) [];
        $entityType = $entity->getEntityType();

        $result = $this->processValidator($entity, $field, $type, new Data($data));

        if (!$result) {
            return false;
        }

        $validationValue = $this->fieldUtil->getEntityTypeFieldParam($entityType, $field, $type);
        $isMandatory = in_array($type, $this->getMandatoryValidationList($entityType, $field));

        $skip = !$isMandatory && (is_null($validationValue) || $validationValue === false);

        if ($skip) {
            return true;
        }

        $result1 = $this->processFieldCheck($entityType, $type, $entity, $field, $validationValue);

        if (!$result1) {
            return false;
        }

        $result2 = $this->processFieldRawCheck($entityType, $type, $data, $field, $validationValue);

        if (!$result2) {
            return false;
        }

        return true;
    }

    private function processValidator(Entity $entity, string $field, string $type, Data $data): bool
    {
        $validator = $this->getValidator($entity->getEntityType(), $field, $type);

        if (!$validator) {
            return true;
        }

        $failure = $validator->validate($entity, $field, $data);

        if ($failure) {
            return false;
        }

        return true;
    }

    
    private function getValidator(string $entityType, string $field, string $type): ?Validator
    {
        $key = $entityType . '_' . $field . '_' . $type;

        if (array_key_exists($key, $this->validatorCache)) {
            return $this->validatorCache[$key];
        }

        if (!$this->validatorFactory->isCreatable($entityType, $field, $type)) {
            $this->validatorCache[$key] = null;

            return null;
        }

        $validator = $this->validatorFactory->create($entityType, $field, $type);

        $this->validatorCache[$key] = $validator;

        return $validator;
    }

    
    private function processField(
        Entity $entity,
        string $field,
        FieldValidationParams $params,
        stdClass $data,
        bool $throw
    ): array {

        $validationList = $this->getAllValidationList($entity->getEntityType(), $field, $params);

        foreach ($validationList as $type) {
            $result = $this->check($entity, $field, $type, $data);

            if ($result) {
                continue;
            }

            $failure = new Failure($entity->getEntityType(), $field, $type);

            if ($throw) {
                throw ValidationError::create($failure);
            }

            return [$failure];
        }

        $failure = $this->checkAdditional($entity, $field, new Data($data));

        if (!$failure) {
            return [];
        }

        if ($throw) {
            throw ValidationError::create($failure);
        }

        return [$failure];
    }

    
    private function getAllValidationList(string $entityType, string $field, FieldValidationParams $params): array
    {
        $validationList = array_unique(array_merge(
            $this->getValidationList($entityType, $field),
            $this->getMandatoryValidationList($entityType, $field)
        ));

        
        $suppressList = $this->metadata->get("entityDefs.$entityType.fields.$field.suppressValidationList") ?? [];

        $validationList = array_filter(
            $validationList,
            fn ($type) => !in_array($type, $suppressList)
        );

        $validationList = array_filter(
            $validationList,
            fn ($type) => !in_array($field, $params->getTypeSkipFieldList($type))
        );

        return array_values($validationList);
    }

    
    private function processFieldCheck(
        string $entityType,
        string $type,
        Entity $entity,
        string $field,
        $validationValue
    ): bool {

        $checker = $this->getFieldTypeChecker($entityType, $field);

        if (!$checker) {
            return true;
        }

        $methodName = 'check' . ucfirst($type);

        if (!method_exists($checker, $methodName)) {
            return true;
        }

        return $checker->$methodName($entity, $field, $validationValue);
    }

    
    private function processFieldRawCheck(
        string $entityType,
        string $type,
        stdClass $data,
        string $field,
        $validationValue
    ): bool {

        $checker = $this->getFieldTypeChecker($entityType, $field);

        if (!$checker) {
            return true;
        }

        $methodName = 'rawCheck' . ucfirst($type);

        if (!method_exists($checker, $methodName)) {
            return true;
        }

        return $checker->$methodName($data, $field, $validationValue);
    }

    private function getFieldTypeChecker(string $entityType, string $field): ?object
    {
        $key = $entityType . '_' . $field;

        if (!array_key_exists($key, $this->checkerCache)) {
            $this->loadFieldTypeChecker($entityType, $field);
        }

        return $this->checkerCache[$key];
    }

    private function loadFieldTypeChecker(string $entityType, string $field): void
    {
        $key = $entityType . '_' . $field;

        if (!$this->checkerFactory->isCreatable($entityType, $field)) {
            $this->checkerCache[$key] = null;

            return;
        }

        $this->checkerCache[$key] = $this->checkerFactory->create($entityType, $field);
    }

    private function isFieldSetInData(string $entityType, string $field, stdClass $data): bool
    {
        $attributeList = $this->fieldUtil->getActualAttributeList($entityType, $field);

        $isSet = false;

        foreach ($attributeList as $attribute) {
            if (property_exists($data, $attribute)) {
                $isSet = true;

                break;
            }
        }

        return $isSet;
    }

    private function checkAdditional(Entity $entity, string $field, Data $data): ?Failure
    {
        $validatorList = $this->validatorFactory->createAdditionalList($entity->getEntityType(), $field);

        foreach ($validatorList as $validator) {
            $itemFailure = $validator->validate($entity, $field, $data);

            if (!$itemFailure) {
                continue;
            }

            $type = lcfirst((new ReflectionClass($validator))->getShortName());

            return new Failure($entity->getEntityType(), $field, $type);
        }

        return null;
    }
}
