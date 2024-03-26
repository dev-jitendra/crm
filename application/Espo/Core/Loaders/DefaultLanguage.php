<?php


namespace Espo\Core\Loaders;

use Espo\Core\Utils\Language as LanguageService;

class DefaultLanguage extends BaseLanguage
{
    protected function getLanguage(): string
    {
        return LanguageService::detectLanguage($this->config) ?? parent::getLanguage();
    }
}
