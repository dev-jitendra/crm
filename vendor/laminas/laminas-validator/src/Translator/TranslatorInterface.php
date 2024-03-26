<?php

namespace Laminas\Validator\Translator;

interface TranslatorInterface
{
    
    public function translate($message, $textDomain = 'default', $locale = null);
}
