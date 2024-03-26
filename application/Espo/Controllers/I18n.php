<?php


namespace Espo\Controllers;

use Espo\Tools\App\LanguageService as Service;

use Espo\Core\Api\Request;

class I18n
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    
    public function getActionRead(Request $request): array
    {
        $default = $request->getQueryParam('default') === 'true';

        return $this->service->getDataForFrontend($default);
    }
}
