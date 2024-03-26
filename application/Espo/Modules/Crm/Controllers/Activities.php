<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Modules\Crm\Tools\Activities\Service as Service;

class Activities
{
    public function __construct(
        private Service $service
    ) {}

    
    public function postActionRemovePopupNotification(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        $id = $data->id;

        $this->service->removeReminder($id);

        return true;
    }
}
