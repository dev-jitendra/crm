<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Language;

interface LanguageAware
{
    public function setLanguage(Language $language): void;
}
