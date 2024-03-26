<?php


namespace Espo\Modules\Crm\Classes\Select\KnowledgeBaseArticle\PrimaryFilters;

use Espo\Core\Select\Primary\Filter;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\ORM\Query\SelectBuilder;

class Published implements Filter
{
    public function apply(SelectBuilder $queryBuilder): void
    {
        $queryBuilder->where([
            'status' => KnowledgeBaseArticle::STATUS_PUBLISHED,
        ]);
    }
}
