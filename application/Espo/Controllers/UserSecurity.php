<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Api\Request;

use Espo\Core\Exceptions\NotFound;
use Espo\Tools\UserSecurity\Service as Service;

use Espo\Entities\User;

use stdClass;

class UserSecurity
{
    private Service $service;
    private User $user;

    
    public function __construct(Service $service, User $user)
    {
        $this->service = $service;
        $this->user = $user;

        if (
            !$this->user->isAdmin() &&
            !$this->user->isRegular() &&
            !$this->user->isPortal()
        ) {
            throw new Forbidden();
        }
    }

    
    public function getActionRead(Request $request): stdClass
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        return $this->service->read($id);
    }

    
    public function postActionGetTwoFactorUserSetupData(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        $id = $data->id ?? null;

        if (!$id) {
            throw new BadRequest("No 'id'.");
        }

        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        return $this->service->getTwoFactorUserSetupData($id, $data);
    }

    
    public function putActionUpdate(Request $request): stdClass
    {
        $id = $request->getRouteParam('id');

        $data = $request->getParsedBody();

        if (!$id) {
            throw new BadRequest();
        }

        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        return $this->service->update($id, $data);
    }
}
