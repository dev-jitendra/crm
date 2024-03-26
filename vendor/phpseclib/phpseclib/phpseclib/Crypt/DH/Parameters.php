<?php



namespace phpseclib3\Crypt\DH;

use phpseclib3\Crypt\DH;


final class Parameters extends DH
{
    
    public function toString($type = 'PKCS1', array $options = [])
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');

        return $type::saveParameters($this->prime, $this->base, $options);
    }
}
