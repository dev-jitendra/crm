<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Mail\Exceptions\NoSmtp;
use Espo\Modules\Crm\Entities\MassEmail as MassEmailEntity;
use Espo\Modules\Crm\Tools\MassEmail\Service;

use Espo\Core\Acl\Table;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Forbidden;

use stdClass;

class MassEmail extends Record
{
    
    public function postActionSendTest(Request $request): bool
    {
        $id = $request->getParsedBody()->id ?? null;
        $targetList = $request->getParsedBody()->targetList ?? null;

        if (!$id || !is_array($targetList)) {
            throw new BadRequest();
        }

        $this->getMassEmailService()->processTest($id, $targetList);

        return true;
    }

    
    public function getActionSmtpAccountDataList(): array
    {
        if (
            !$this->acl->checkScope(MassEmailEntity::ENTITY_TYPE, Table::ACTION_CREATE) &&
            !$this->acl->checkScope(MassEmailEntity::ENTITY_TYPE, Table::ACTION_EDIT)
        ) {
            throw new Forbidden();
        }

        return $this->getMassEmailService()->getSmtpAccountDataList();
    }

    private function getMassEmailService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
