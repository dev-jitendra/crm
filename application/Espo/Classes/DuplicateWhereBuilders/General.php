<?php


namespace Espo\Classes\DuplicateWhereBuilders;

use Espo\Core\Duplicate\WhereBuilder;
use Espo\Core\Field\EmailAddressGroup;
use Espo\Core\Field\PhoneNumberGroup;
use Espo\Core\ORM\Entity as CoreEntity;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\ORM\Defs;
use Espo\ORM\Entity;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\Part\Where\OrGroup;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\WhereItem;
use Espo\ORM\Type\AttributeType;


class General implements WhereBuilder
{
    public function __construct(
        private Metadata $metadata,
        private Defs $ormDefs,
        private Config $config
    ) {}

    
    public function build(Entity $entity): ?WhereItem
    {
        
        $fieldList = $this->metadata->get(['scopes', $entity->getEntityType(), 'duplicateCheckFieldList']) ?? [];

        $orBuilder = OrGroup::createBuilder();

        $toCheck = false;

        foreach ($fieldList as $field) {
            $toCheckItem = $this->applyField($field, $entity, $orBuilder);

            if ($toCheckItem) {
                $toCheck = true;
            }
        }

        if (!$toCheck) {
            return null;
        }

        return $orBuilder->build();
    }

    private function applyField(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $type = $this->ormDefs
            ->getEntity($entity->getEntityType())
            ->tryGetField($field)
            ?->getType();

        if ($type === 'personName') {
            return $this->applyFieldPersonName($field, $entity, $orBuilder);
        }

        if ($type === 'email') {
            return $this->applyFieldEmail($field, $entity, $orBuilder);
        }

        if ($type === 'phone') {
            return $this->applyFieldPhone($field, $entity, $orBuilder);
        }

        if ($entity->getAttributeType($field) === AttributeType::VARCHAR) {
            return $this->applyFieldVarchar($field, $entity, $orBuilder);
        }

        return false;
    }

    private function applyFieldPersonName(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $first = 'first' . ucfirst($field);
        $last = 'last' . ucfirst($field);

        if (!$entity->get($first) && !$entity->get($last)) {
            return false;
        }

        $orBuilder->add(
            Cond::and(
                Cond::equal(
                    Cond::column($first),
                    $entity->get($first)
                ),
                Cond::equal(
                    Cond::column($last),
                    $entity->get($last)
                )
            )
        );

        return true;
    }

    private function applyFieldEmail(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $toCheck = false;

        if (
            ($entity->get($field) || $entity->get($field . 'Data')) &&
            (
                $entity->isNew() ||
                $entity->isAttributeChanged($field) ||
                $entity->isAttributeChanged($field . 'Data')
            )
        ) {
            foreach ($this->getEmailAddressList($entity) as $emailAddress) {
                $orBuilder->add(
                    Cond::equal(
                        Cond::column($field),
                        $emailAddress
                    )
                );

                $toCheck = true;
            }
        }

        return $toCheck;
    }

    private function applyFieldPhone(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        $toCheck = false;

        $isNumeric = $this->config->get('phoneNumberNumericSearch');

        $column = $isNumeric ?
            $field . 'Numeric' :
            $field;

        if (
            ($entity->get($field) || $entity->get($field . 'Data')) &&
            (
                $entity->isNew() ||
                $entity->isAttributeChanged($field) ||
                $entity->isAttributeChanged($field . 'Data')
            )
        ) {
            foreach ($this->getPhoneNumberList($entity) as $number) {
                if ($isNumeric) {
                    $number = preg_replace('/[^0-9]/', '', $number);
                }

                $orBuilder->add(
                    Cond::equal(
                        Cond::column($column),
                        $number
                    )
                );

                $toCheck = true;
            }
        }

        return $toCheck;
    }

    private function applyFieldVarchar(
        string $field,
        CoreEntity $entity,
        OrGroupBuilder $orBuilder
    ): bool {

        if (!$entity->get($field)) {
            return false;
        }

        $orBuilder->add(
            Cond::equal(
                Cond::column($field),
                $entity->get($field)
            ),
        );

        return true;
    }

    
    private function getEmailAddressList(CoreEntity $entity): array
    {
        if ($entity->get('emailAddressData')) {
            
            $eaGroup = $entity->getValueObject('emailAddress');

            return $eaGroup->getAddressList();
        }

        if ($entity->get('emailAddress')) {
            return [
                $entity->get('emailAddress')
            ];
        }

        return [];
    }

    
    private function getPhoneNumberList(CoreEntity $entity): array
    {
        if ($entity->get('phoneNumberData')) {
            
            $eaGroup = $entity->getValueObject('phoneNumber');

            return $eaGroup->getNumberList();
        }

        if ($entity->get('phoneNumber')) {
            return [$entity->get('phoneNumber')];
        }

        return [];
    }
}
