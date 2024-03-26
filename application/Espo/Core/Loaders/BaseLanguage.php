<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Language as LanguageService;

class BaseLanguage implements Loader
{
    public function __construct(private InjectableFactory $injectableFactory, protected Config $config)
    {}

    public function load(): LanguageService
    {
        return $this->injectableFactory->createWith(LanguageService::class, [
            'language' => $this->getLanguage(),
            'useCache' => $this->config->get('useCache') ?? false,
        ]);
    }

    protected function getLanguage(): string
    {
        return 'en_US';
    }
}
