<?php


namespace Espo\Core\Di;

use Espo\Core\Utils\Language;

trait LanguageSetter
{
    
    protected $language;

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }
}
