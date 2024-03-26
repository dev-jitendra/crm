<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata as MetadataService;

class Metadata implements Loader
{
    public function __construct(private InjectableFactory $injectableFactory, private Config $config)
    {}

    public function load(): MetadataService
    {
        return $this->injectableFactory->createWith(MetadataService::class, [
            'useCache' => $this->config->get('useCache') ?? false,
        ]);
    }
}
