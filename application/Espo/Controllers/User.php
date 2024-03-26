<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Api\Request;
use Espo\Core\Controllers\Record;
use Espo\Core\Select\SearchParams;
use Espo\Core\Select\Where\Item as WhereItem;

class User extends Record
{
    public function postActionCreateLink(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        return parent::postActionCreateLink($request);
    }

    public function deleteActionRemoveLink(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        return parent::deleteActionRemoveLink($request);
    }

    protected function fetchSearchParamsFromRequest(Request $request): SearchParams
    {
        $searchParams = parent::fetchSearchParamsFromRequest($request);

        $userType = $request->getQueryParam('userType');

        if (!$userType) {
            return $searchParams;
        }

        return $searchParams->withWhereAdded(
            WhereItem::fromRaw([
                'type' => 'isOfType',
                'attribute' => 'id',
                'value' => $userType,
            ])
        );
    }
}
