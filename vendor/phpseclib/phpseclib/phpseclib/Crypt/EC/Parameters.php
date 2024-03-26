<?php



namespace phpseclib3\Crypt\EC;

use phpseclib3\Crypt\EC;


final class Parameters extends EC
{
    
    public function toString($type = 'PKCS1', array $options = [])
    {
        $type = self::validatePlugin('Keys', 'PKCS1', 'saveParameters');

        return $type::saveParameters($this->curve, $options);
    }
}
