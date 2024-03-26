<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Api\Request;
use Espo\Core\Utils\Metadata;
use Espo\Tools\App\SettingsService as Service;
use Espo\Entities\User;

use stdClass;

class Settings
{
    private Service $service;
    private User $user;

    public function __construct(
        Service $service,
        User $user
    ) {
        $this->service = $service;
        $this->user = $user;
    }

    public function getActionRead(): stdClass
    {
        return $this->getConfigData();
    }

    
    public function putActionUpdate(Request $request): stdClass
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $data = $request->getParsedBody();

        $this->service->setConfigData($data);

        return $this->getConfigData();
    }

    private function getConfigData(): stdClass
    {
        $data = $this->service->getConfigData();
        $metadataData = $this->service->getMetadataConfigData();

        foreach (get_object_vars($metadataData) as $key => $value) {
            $data->$key = $value;
        }

        return $data;
    }
}
