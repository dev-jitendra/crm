<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Api\Request;
use Espo\Core\Utils\Metadata as MetadataUtil;
use Espo\Entities\User as UserEntity;
use Espo\Tools\App\MetadataService as Service;

use stdClass;

class Metadata
{
    private Service $service;
    private MetadataUtil $metadata;
    private UserEntity $user;

    public function __construct(
        Service $service,
        MetadataUtil $metadata,
        UserEntity $user
    ) {
        $this->service = $service;
        $this->metadata = $metadata;
        $this->user = $user;
    }

    public function getActionRead(): stdClass
    {
        return $this->service->getDataForFrontend();
    }

    
    public function getActionGet(Request $request)
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $key = $request->getQueryParam('key');

        return $this->metadata->get($key, false);
    }
}
