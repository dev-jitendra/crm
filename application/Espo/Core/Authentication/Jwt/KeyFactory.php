<?php


namespace Espo\Core\Authentication\Jwt;

use Espo\Core\Authentication\Jwt\Exceptions\UnsupportedKey;
use stdClass;

interface KeyFactory
{
    
    public function create(stdClass $raw): Key;
}
