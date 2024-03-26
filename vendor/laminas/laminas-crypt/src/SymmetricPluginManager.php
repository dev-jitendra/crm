<?php

namespace Laminas\Crypt;

use Psr\Container\ContainerInterface;

use function array_key_exists;
use function sprintf;


class SymmetricPluginManager implements ContainerInterface
{
    
    protected $symmetric = [
        'mcrypt'  => Symmetric\Mcrypt::class,
        'openssl' => Symmetric\Openssl::class,
    ];

    
    public function has($id)
    {
        return array_key_exists($id, $this->symmetric);
    }

    
    public function get($id)
    {
        if (! $this->has($id)) {
            throw new Exception\NotFoundException(sprintf(
                'The symmetric adapter %s does not exist',
                $id
            ));
        }
        $class = $this->symmetric[$id];
        return new $class();
    }
}
