<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\RecordBase;
use Espo\Core\Upgrades\ExtensionManager;

use stdClass;

class Extension extends RecordBase
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }

    
    public function postActionUpload(Request $request): stdClass
    {
        if ($this->config->get('restrictedMode') && !$this->user->isSuperAdmin()) {
            throw new Forbidden();
        }

        if ($this->config->get('adminUpgradeDisabled')) {
            throw new Forbidden("Disabled with 'adminUpgradeDisabled' parameter.");
        }

        $body = $request->getBodyContents();

        if ($body === null) {
            throw new BadRequest();
        }

        $manager = new ExtensionManager($this->getContainer());

        $id = $manager->upload($body);

        $manifest = $manager->getManifest();

        return (object) [
            'id' => $id,
            'version' => $manifest['version'],
            'name' => $manifest['name'],
            'description' => $manifest['description'],
        ];
    }

    
    public function postActionInstall(Request $request): bool
    {
        $data = $request->getParsedBody();

        if ($this->config->get('restrictedMode') && !$this->user->isSuperAdmin()) {
            throw new Forbidden();
        }

        $manager = new ExtensionManager($this->getContainer());

        $manager->install(get_object_vars($data));

        return true;
    }

    
    public function postActionUninstall(Request $request): bool
    {
        $data = $request->getParsedBody();

        if ($this->config->get('restrictedMode') && !$this->user->isSuperAdmin()) {
            throw new Forbidden();
        }

        $manager = new ExtensionManager($this->getContainer());

        $manager->uninstall(get_object_vars($data));

        return true;
    }

    
    public function deleteActionDelete(Request $request, Response $response): bool
    {
        $params = $request->getRouteParams();

        if ($this->config->get('restrictedMode') && !$this->user->isSuperAdmin()) {
            throw new Forbidden();
        }

        $manager = new ExtensionManager($this->getContainer());

        $manager->delete($params);

        return true;
    }

    public function postActionCreate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
    }

    public function putActionUpdate(Request $request, Response $response): stdClass
    {
        throw new Forbidden();
    }
}
