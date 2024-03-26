<?php


namespace Espo\Core\Authentication\Oidc;

use Espo\Core\Authentication\Jwt\Token\Payload;
use Espo\Entities\User;

interface UserProvider
{
    public function get(Payload $payload): ?User;
}
