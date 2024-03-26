<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Controllers\RecordBase;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Tools\EmailFolder\Service;
use stdClass;

class EmailFolder extends RecordBase
{
    
    public function postActionMoveUp(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $this->getEmailFolderService()->moveUp($data->id);

        return true;
    }

    
    public function postActionMoveDown(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $this->getEmailFolderService()->moveDown($data->id);

        return true;
    }

    
    public function getActionListAll(Request $request): stdClass
    {
        $userId = $request->getQueryParam('userId');

        $list = $this->getEmailFolderService()->listAll($userId);

        return (object) ['list' => $list];
    }

    private function getEmailFolderService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
