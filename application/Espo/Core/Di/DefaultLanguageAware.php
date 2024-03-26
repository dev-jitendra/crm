<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Language;

interface DefaultLanguageAware
{
    public function setDefaultLanguage(Language $defaultLanguage): void;
}
