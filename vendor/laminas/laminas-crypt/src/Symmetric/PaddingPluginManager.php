<?php

namespace Laminas\Crypt\Symmetric;

use Psr\Container\ContainerInterface;

use function array_key_exists;
use function sprintf;


class PaddingPluginManager implements ContainerInterface
{
    
    private $paddings = [
        'pkcs7'     => Padding\Pkcs7::class,
        'nopadding' => Padding\NoPadding::class,
        'null'      => Padding\NoPadding::class,
    ];

    
    public function has($id)
    {
        return array_key_exists($id, $this->paddings);
    }

    
    public function get($id)
    {
        if (! $this->has($id)) {
            throw new Exception\NotFoundException(sprintf(
                "The padding adapter %s does not exist",
                $id
            ));
        }
        $class = $this->paddings[$id];
        return new $class();
    }
}
