<?php



namespace phpseclib3\Crypt\DH;

use phpseclib3\Crypt\Common;
use phpseclib3\Crypt\DH;


final class PublicKey extends DH
{
    use Common\Traits\Fingerprint;

    
    public function toString($type, array $options = [])
    {
        $type = self::validatePlugin('Keys', $type, 'savePublicKey');

        return $type::savePublicKey($this->prime, $this->base, $this->publicKey, $options);
    }

    
    public function toBigInteger()
    {
        return $this->publicKey;
    }
}
