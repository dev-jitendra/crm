<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Api\Request;

use Espo\Core\Exceptions\NotFound;
use Espo\Tools\UserSecurity\TwoFactor\EmailService as Service;

use Espo\Entities\User;

class TwoFactorEmail
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
        $emailAddress = $data->emailAddress ?? null;

        if (!$id) {
            throw new BadRequest("No 'id'.");
        }

        if (!$emailAddress) {
            throw new BadRequest("No 'emailAddress'.");
        }

        if (!$this->user->isAdmin() && $id !== $this->user->getId()) {
            throw new Forbidden();
        }

        $this->service->sendCode($id, $emailAddress);

        return true;
    }
}
