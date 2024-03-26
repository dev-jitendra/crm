<?php



namespace phpseclib3\Crypt\Common;


interface PublicKey
{
    public function verify($message, $signature);
    
    public function toString($type, array $options = []);
    public function getFingerprint($algorithm);
}
