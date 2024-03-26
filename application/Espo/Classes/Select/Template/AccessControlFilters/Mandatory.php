<?php


namespace Espo\Classes\Select\Template\AccessControlFilters;

use Espo\ORM\Defs;
use Espo\ORM\Query\SelectBuilder;
use Espo\Core\Acl\Exceptions\NotImplemented;
use Espo\Core\AclManager;
use Espo\Core\Select\AccessControl\Filter;

use Espo\Entities\User;

class Mandatory implements Filter
{
    public function __construct(
        private User $user,
        private Defs $defs,
        private AclManager $aclManager
    ) {}

    public function apply(SelectBuilder $queryBuilder): void
    {
        if ($this->user->isAdmin()) {
            return;
        }

        $forbiddenEntityTypeList = [];

        foreach ($this->defs->getEntityTypeList() as $entityType) {
            try {
                if (!$this->aclManager->checkScope($this->user, $entityType)) {
                    $forbiddenEntityTypeList[] = $entityType;
                }
            }
            catch (NotImplemented $e) {}
        }

        if (empty($forbiddenEntityTypeList)) {
            return;
        }

        $queryBuilder->where([
            'entityType!=' => $forbiddenEntityTypeList,
        ]);
    }
}
