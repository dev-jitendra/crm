<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Api\Request;

use Espo\Core\Exceptions\NotFound;
use Espo\Tools\UserSecurity\TwoFactor\SmsService as Service;

use Espo\Entities\User;

class TwoFactorSms
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

    
    public function postActionSendCode(Request $request): bool
    {
        $data = $request->getParsedBody();

        $id = $data->id ?? null;
        $phoneNumber = $data->phoneNumber ?? null;

        if (!$id) {
            throw new BadRequest("No 'id'.");
        }

        if (!$phoneNumber) {
            throw new BadRequest("No 'phoneNumber'.");
        }

        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        $this->service->sendCode($id, $phoneNumber);

        return true;
    }
}
