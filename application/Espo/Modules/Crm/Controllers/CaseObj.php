<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Acl\Table;
use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Modules\Crm\Entities\CaseObj as CaseEntity;
use Espo\Modules\Crm\Tools\Case\Service;
use stdClass;

class CaseObj extends Record
{
    protected $name = CaseEntity::ENTITY_TYPE;

    
    public function getActionEmailAddressList(Request $request): array
    {
        $id = $request->getQueryParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        if (!$this->acl->checkScope(CaseEntity::ENTITY_TYPE, Table::ACTION_READ)) {
            throw new Forbidden();
        }

        $result = $this->injectableFactory
            ->create(Service::class)
            ->getEmailAddressList($id);

        return array_map(
            fn ($item) => $item->getValueMap(),
            $result
        );
    }
}
