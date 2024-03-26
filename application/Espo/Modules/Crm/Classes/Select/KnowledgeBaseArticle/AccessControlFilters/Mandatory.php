<?php


namespace Espo\Modules\Crm\Classes\Select\KnowledgeBaseArticle\AccessControlFilters;

use Espo\Core\Select\AccessControl\Filter;
use Espo\Modules\Crm\Entities\KnowledgeBaseArticle;
use Espo\ORM\Query\SelectBuilder;

use Espo\Entities\User;

class Mandatory implements Filter
{
    public function __construct(private User $user)
    {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if (!$this->user->isPortal()) {
            return;
        }

        $queryBuilder
            ->where([
                'status' => KnowledgeBaseArticle::STATUS_PUBLISHED,
            ])
            ->distinct()
            ->leftJoin('portals', 'portalsAccess')
            ->where([
                'portalsAccess.id' => $this->user->getPortalId(),
            ]);
    }
}
