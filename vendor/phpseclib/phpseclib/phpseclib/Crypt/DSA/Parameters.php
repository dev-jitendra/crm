<?php



namespace phpseclib3\Crypt\DSA;

use phpseclib3\Crypt\DSA;


final class Parameters extends DSA
{
    
    public function toString($type = 'PKCS1', array $options = [])
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');

        return $type::saveParameters($this->p, $this->q, $this->g, $options);
    }
}
