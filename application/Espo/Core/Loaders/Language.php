<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Language as LanguageService;

use Espo\Entities\Preferences;

class Language implements Loader
{
    public function __construct(
        private InjectableFactory $injectableFactory,
        private Config $config,
        private Preferences $preferences
    ) {}

    public function load(): LanguageService
    {
        return $this->injectableFactory->createWith(LanguageService::class, [
            'language' => LanguageService::detectLanguage($this->config, $this->preferences),
            'useCache' => $this->config->get('useCache') ?? false,
        ]);
    }
}
