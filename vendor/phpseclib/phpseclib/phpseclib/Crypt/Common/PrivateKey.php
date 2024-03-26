<?php



namespace phpseclib3\Crypt\Common;


interface PrivateKey
{
    public function sign($message);
    
    public function getPublicKey();
    public function toString($type, array $options = []);

    
    public function withPassword($password = false);
}
