<?php



namespace phpseclib3\Crypt\DH;

use phpseclib3\Crypt\Common;
use phpseclib3\Crypt\DH;


final class PrivateKey extends DH
{
    use Common\Traits\PasswordProtected;

    
    protected $privateKey;

    
    protected $publicKey;

    
    public function getPublicKey()
    {
        $type = self::validatePlugin('Keys', 'PKCS8', 'savePublicKey');

        if (!isset($this->publicKey)) {
            $this->publicKey = $this->base->powMod($this->privateKey, $this->prime);
        }

        $key = $type::savePublicKey($this->prime, $this->base, $this->publicKey);

        return DH::loadFormat('PKCS8', $key);
    }

    
    public function toString($type, array $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePrivateKey');

        if (!isset($this->publicKey)) {
            $this->publicKey = $this->base->powMod($this->privateKey, $this->prime);
        }

        return $type::savePrivateKey($this->prime, $this->base, $this->privateKey, $this->publicKey, $this->password, $options);
    }
}
