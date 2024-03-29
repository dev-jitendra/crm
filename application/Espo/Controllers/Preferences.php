<?php


namespace Espo\Controllers;

use Espo\Core\Api\Request;
use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Tools\App\PreferencesService as Service;

use stdClass;

class Preferences
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    
    public function getActionRead(Request $request): stdClass
    {
        $userId = $request->getRouteParam('id');

        if (!$userId) {
            throw new BadRequest();
        }

        return $this->service->read($userId)->getValueMap();
    }

    
    public function deleteActionDelete(Request $request): stdClass
    {
        $userId = $request->getRouteParam('id');

        if (!$userId) {
            throw new BadRequest();
        }

        $this->service->resetToDefaults($userId);

        return $this->service
            ->read($userId)
            ->getValueMap();
    }

    
    public function putActionUpdate(Request $request): stdClass
    {
        $userId = $request->getRouteParam('id');

        if (!$userId) {
            throw new BadRequest();
        }

        $data = $request->getParsedBody();

        return $this->service
            ->update($userId, $data)
            ->getValueMap();
    }

    
    public function postActionResetDashboard(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        $userId = $data->id ?? null;

        if (!$userId) {
            throw new BadRequest();
        }

        return $this->service->resetDashboard($userId);
    }
}
