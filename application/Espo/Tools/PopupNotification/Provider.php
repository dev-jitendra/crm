<?php


namespace Espo\Tools\PopupNotification;

use Espo\Entities\User;


interface Provider
{
    
    public function get(User $user): array;
}
