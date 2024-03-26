<?php


namespace Espo\Classes\Select\Email;

use Espo\Core\Exceptions\Error;
use Espo\Core\Select\Text\Filter;
use Espo\Core\Select\Text\Filter\Data;
use Espo\Core\Select\Text\DefaultFilter;
use Espo\Core\Select\Text\ConfigProvider;

use Espo\ORM\EntityManager;
use Espo\ORM\Query\SelectBuilder as QueryBuilder;
use Espo\ORM\Query\Part\Where\OrGroup;
use Espo\ORM\Query\Part\Where\Comparison as Cmp;
use Espo\ORM\Query\Part\Expression as Expr;

use Espo\Entities\EmailAddress;

class TextFilter implements Filter
{
    public function __construct(
        private DefaultFilter $defaultFilter,
        private ConfigProvider $config,
        private EntityManager $entityManager
    ) {}

    
    public function apply(QueryBuilder $queryBuilder, Data $data): void
    {
        $filter = $data->getFilter();
        $ftWhereItem = $data->getFullTextSearchWhereItem();

        if (
            mb_strlen($filter) < $this->config->getMinLengthForContentSearch() ||
            !str_contains($filter, '@') ||
            $data->forceFullTextSearch()
        ) {
            $this->defaultFilter->apply($queryBuilder, $data);

            return;
        }

        $emailAddressId = $this->getEmailAddressIdByValue($filter);

        $orGroupBuilder = OrGroup::createBuilder();

        if ($ftWhereItem) {
            $orGroupBuilder->add($ftWhereItem);
        }

        if (!$emailAddressId) {
            $orGroupBuilder->add(
                Cmp::equal(Expr::column('id'), null)
            );

            $queryBuilder->where($orGroupBuilder->build());

            return;
        }

        $this->leftJoinEmailAddress($queryBuilder);

        $orGroupBuilder
            ->add(
                Cmp::equal(
                    Expr::column('fromEmailAddressId'),
                    $emailAddressId
                )
            )
            ->add(
                Cmp::equal(
                    Expr::column('emailEmailAddress.emailAddressId'),
                    $emailAddressId
                )
            );

        $queryBuilder->where($orGroupBuilder->build());
    }

    private function leftJoinEmailAddress(QueryBuilder $queryBuilder): void
    {
        if ($queryBuilder->hasLeftJoinAlias('emailEmailAddress')) {
            return;
        }

        $queryBuilder->distinct();
        $queryBuilder->leftJoin(
            'EmailEmailAddress',
            'emailEmailAddress',
            [
                'emailId:' => 'id',
                'deleted' => false,
            ]
        );
    }

    private function getEmailAddressIdByValue(string $value): ?string
    {
        $emailAddress = $this->entityManager
            ->getRDBRepository(EmailAddress::ENTITY_TYPE)
            ->select('id')
            ->where([
                'lower' => strtolower($value),
            ])
            ->findOne();

        if (!$emailAddress) {
            return null;
        }

        return $emailAddress->getId();
    }
}
