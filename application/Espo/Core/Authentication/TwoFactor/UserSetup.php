<?php


namespace Espo\Core\Authentication\TwoFactor;

use Espo\Core\Authentication\TwoFactor\Exceptions\NotConfigured;
use Espo\Core\Exceptions\BadRequest;
use Espo\Entities\User;

use stdClass;


interface UserSetup
{
    
    public function getData(User $user): stdClass;

    
    public function verifyData(User $user, stdClass $payloadData): bool;
}
