<?php


namespace Espo\Core\Utils\Language;

use Espo\Core\Container;
use Espo\Core\Utils\Language;

class LanguageProxy
{
    public function __construct(
        private Container $container
    ) {}

    
    public function translateLabel(string $label, string $category = 'labels', string $scope = 'Global'): string
    {
        return $this->container
            ->getByClass(Language::class)
            ->translateLabel($label, $category, $scope);
    }
}
