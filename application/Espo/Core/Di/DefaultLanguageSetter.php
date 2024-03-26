<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Language;

trait DefaultLanguageSetter
{
    
    protected $defaultLanguage;

    public function setDefaultLanguage(Language $defaultLanguage): void
    {
        $this->defaultLanguage = $defaultLanguage;
    }
}
