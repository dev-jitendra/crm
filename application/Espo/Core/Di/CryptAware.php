<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Crypt;

interface CryptAware
{
    public function setCrypt(Crypt $crypt): void;
}
