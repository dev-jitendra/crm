<?php


namespace Espo\Core\Select\Text;

use Espo\Core\Exceptions\Error;
use Espo\Core\Select\Text\Filter\Data;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\ORM\Query\Part\Where\OrGroup;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\Where\Comparison as Cmp;
use Espo\ORM\Query\Part\Expression as Expr;

use Espo\ORM\Entity;

class DefaultFilter implements Filter
{
    public function __construct(
        private string $entityType,
        private MetadataProvider $metadataProvider,
        private ConfigProvider $config
    ) {}

    
    public function apply(QueryBuilder $queryBuilder, Data $data): void
    {
        $orGroupBuilder = OrGroup::createBuilder();

        foreach ($data->getAttributeList() as $attribute) {
            $this->applyAttribute($queryBuilder, $orGroupBuilder, $attribute, $data);
        }

        if ($data->getFullTextSearchWhereItem()) {
            $orGroupBuilder->add(
                $data->getFullTextSearchWhereItem()
            );
        }

        $orGroup = $orGroupBuilder->build();

        if ($orGroup->getItemCount() === 0) {
            $queryBuilder->where(['id' => null]);

            return;
        }

        $queryBuilder->where($orGroup);
    }

    
    private function applyAttribute(
        QueryBuilder $queryBuilder,
        OrGroupBuilder $orGroupBuilder,
        string $attribute,
        Data $data
    ): void {

        $filter = $data->getFilter();
        $skipWildcards = $data->skipWildcards();

        $attributeType = $this->getAttributeTypeAndApplyJoin($queryBuilder, $attribute);

        if ($attributeType === Entity::INT) {
            if (is_numeric($filter)) {
                $orGroupBuilder->add(
                    Cmp::equal(
                        Expr::column($attribute),
                        intval($filter)
                    )
                );
            }

            return;
        }

        if (
            !str_contains($attribute, '.') &&
            $this->metadataProvider->getFieldType($this->entityType, $attribute) === 'email' &&
            str_contains($filter, ' ')
        ) {
            return;
        }

        if (
            !str_contains($attribute, '.') &&
            $this->metadataProvider->getFieldType($this->entityType, $attribute) === 'phone'
        ) {
            if (!preg_match("#[0-9()\-+% ]+$#", $filter)) {
                return;
            }

            if ($this->config->usePhoneNumberNumericSearch()) {
                $attribute = $attribute . 'Numeric';

                $filter = preg_replace('/[^0-9%]/', '', $filter);
            }

            if (!$filter) {
                return;
            }
        }

        $expression = $filter;

        if (!$skipWildcards) {
            $expression = $this->checkWhetherToUseContains($attribute, $filter, $attributeType) ?
                '%' . $filter . '%' :
                $filter . '%';
        }

        $expression = addslashes($expression);

        $orGroupBuilder->add(
            Cmp::like(
                Expr::column($attribute),
                $expression
            )
        );
    }

    
    private function getAttributeTypeAndApplyJoin(QueryBuilder $queryBuilder, string $attribute): string
    {
        if (str_contains($attribute, '.')) {
            list($link, $foreignField) = explode('.', $attribute);

            $foreignEntityType = $this->metadataProvider->getRelationEntityType($this->entityType, $link);

            if (!$foreignEntityType) {
                throw new Error("Bad relation in text filter field '{$attribute}'.");
            }

            if ($this->metadataProvider->getRelationType($this->entityType, $link) === Entity::HAS_MANY) {
                $queryBuilder->distinct();
            }

            $queryBuilder->leftJoin($link);

            return $this->metadataProvider->getAttributeType($foreignEntityType, $foreignField);
        }

        $attributeType = $this->metadataProvider->getAttributeType($this->entityType, $attribute);

        if ($attributeType === Entity::FOREIGN) {
            $link = $this->metadataProvider->getAttributeRelationParam($this->entityType, $attribute);

            if ($link) {
                $queryBuilder->leftJoin($link);
            }
        }

        return $attributeType;
    }

    private function checkWhetherToUseContains(string $attribute, string $filter, string $attributeType): bool
    {
        if (mb_strlen($filter) < $this->config->getMinLengthForContentSearch()) {
            return false;
        }

        if ($attributeType === Entity::TEXT) {
            return true;
        }

        if (
            in_array(
                $attribute,
                $this->metadataProvider->getUseContainsAttributeList($this->entityType)
            )
        ) {
            return true;
        }

        if (
            $attributeType === Entity::VARCHAR &&
            $this->config->useContainsForVarchar()
        ) {
            return true;
        }

        return false;
    }
}
