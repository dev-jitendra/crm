<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Controllers\Record;
use Espo\Core\Api\Request;
use Espo\Tools\EmailFolder\GroupFolderService as Service;

class GroupEmailFolder extends Record
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

    private function getEmailFolderService(): Service
    {
        
        return $this->injectableFactory->create(Service::class);
    }
}
