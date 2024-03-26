<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Acl;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;

use Espo\Tools\DataPrivacy\Erasor;

class DataPrivacy
{
    public function __construct(private Erasor $erasor, private Acl $acl)
    {
        if ($this->acl->getPermissionLevel('dataPrivacyPermission') === Acl\Table::LEVEL_NO) {
            throw new Forbidden();
        }
    }

    public function postActionErase(Request $request, Response $response): void
    {
        $data = $request->getParsedBody();

        if (
            empty($data->entityType) ||
            empty($data->id) ||
            empty($data->fieldList) ||
            !is_array($data->fieldList)
        ) {
            throw new BadRequest();
        }

        $this->erasor->erase($data->entityType, $data->id, $data->fieldList);

        $response->writeBody('true');
    }
}
