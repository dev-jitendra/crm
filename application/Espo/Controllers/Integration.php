<?php


namespace Espo\Controllers;

use Espo\Services\Integration as Service;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Api\Request;

use Espo\Entities\User;

use stdClass;

class Integration
{
    private $service;

    private $user;

    public function __construct(Service $service, User $user)
    {
        $this->service = $service;
        $this->user = $user;

        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }
    }

    public function getActionRead(Request $request): stdClass
    {
        
        $id = $request->getRouteParam('id');

        $entity = $this->service->read($id);

        return $entity->getValueMap();
    }

    public function putActionUpdate(Request $request): stdClass
    {
        
        $id = $request->getRouteParam('id');
        $data = $request->getParsedBody();

        $entity = $this->service->update($id, $data);

        return $entity->getValueMap();
    }
}
