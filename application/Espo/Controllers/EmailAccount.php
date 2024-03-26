<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Mail\Account\PersonalAccount\Service;
use Espo\Core\Mail\Account\Storage\Params as StorageParams;

use Espo\Core\Controllers\Record;
use Espo\Core\Api\Request;

class EmailAccount extends Record
{
    protected function checkAccess(): bool
    {
        return $this->acl->check('EmailAccountScope');
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
            ->setEmailAddress($data->emailAddress ?? null)
            ->setUserId($data->userId ?? null)
            ->build();

        return $this->getEmailAccountService()->getFolderList($params);
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
            ->setEmailAddress($data->emailAddress ?? null)
            ->setUserId($data->userId ?? null)
            ->build();

        $this->getEmailAccountService()->testConnection($params);

        return true;
    }

    private function getEmailAccountService(): Service
    {
        
        return $this->injectableFactory->create(Service::class);
    }
}
