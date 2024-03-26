<?php


namespace Espo\Tools\Currency\Conversion;

use Espo\Core\Acl;
use Espo\Core\Acl\Table;
use Espo\Core\Currency\Converter;
use Espo\Core\Currency\Rates;
use Espo\Core\Field\Currency;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;
use LogicException;


class DefaultEntityConverter implements EntityConverter
{
    public function __construct(
        private Converter $converter,
        private EntityManager $entityManager,
        private Metadata $metadata,
        private Acl $acl
    ) {}

    
    public function convert(Entity $entity, string $targetCurrency, Rates $rates): void
    {
        $entityDefs = $this->entityManager
            ->getDefs()
            ->getEntity($entity->getEntityType());

        foreach ($this->getFieldList($entity->getEntityType()) as $field) {
            $disabled = $entityDefs->getField($field)->getParam('conversionDisabled');

            if ($disabled) {
                continue;
            }

            $value = $entity->getValueObject($field);

            if (!$value) {
                continue;
            }

            if (!$value instanceof Currency) {
                throw new LogicException();
            }

            if ($targetCurrency === $value->getCode()) {
                continue;
            }

            $convertedValue = $this->converter->convertWithRates($value, $targetCurrency, $rates);

            $entity->setValueObject($field, $convertedValue);
        }
    }

    
    private function getFieldList(string $entityType): array
    {
        $resultList = [];

        
        $requiredFieldList = $this->metadata->get(['scopes', $entityType, 'currencyConversionAccessRequiredFieldList']);

        $allFields = $requiredFieldList !== null;

        $fieldDefsList = $this->entityManager
            ->getDefs()
            ->getEntity($entityType)
            ->getFieldList();

        foreach ($fieldDefsList as $fieldDefs) {
            $field = $fieldDefs->getName();
            $type = $fieldDefs->getType();

            if ($type !== 'currency') {
                continue;
            }

            if (
                !$allFields &&
                !$this->acl->checkField($entityType, $field, Table::ACTION_EDIT)
            ) {
                continue;
            }

            $resultList[] = $field;
        }

        return $resultList;
    }
}
