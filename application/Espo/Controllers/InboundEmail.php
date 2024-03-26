<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Error;
use Espo\Core\Mail\Account\GroupAccount\Service;
use Espo\Core\Mail\Account\Storage\Params as StorageParams;

use Espo\Core\Controllers\Record;
use Espo\Core\Api\Request;

class InboundEmail extends Record
{
    protected function checkAccess(): bool
    {
        return $this->getUser()->isAdmin();
    }

    
    public function postActionGetFolders(Request $request): array
    {
        $data = $request->getParsedBody();

        $params = StorageParams::createBuilder()
            ->setHost($data->host ?? null)
            ->setPort($data->port ?? null)
            ->setSecurity($data->security ?? null)
            ->setUsername($data->username ?? null)
            ->setPassword($data->password ?? null)
            ->setId($data->id ?? null)
            ->build();

        return $this->getInboundEmailService()->getFolderList($params);
    }

    
    public function postActionTestConnection(Request $request): bool
    {
        $data = $request->getParsedBody();

        $params = StorageParams::createBuilder()
            ->setHost($data->host ?? null)
            ->setPort($data->port ?? null)
            ->setSecurity($data->security ?? null)
            ->setUsername($data->username ?? null)
            ->setPassword($data->password ?? null)
            ->setId($data->id ?? null)
            ->build();

        $this->getInboundEmailService()->testConnection($params);

        return true;
    }

    private function getInboundEmailService(): Service
    {
        
        return $this->injectableFactory->create(Service::class);
    }
}
