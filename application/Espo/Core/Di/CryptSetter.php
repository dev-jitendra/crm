<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Crypt;

trait CryptSetter
{
    
    protected $crypt;

    public function setCrypt(Crypt $crypt): void
    {
        $this->crypt = $crypt;
    }
}
