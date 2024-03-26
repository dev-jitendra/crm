<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\InjectableFactory;
use Espo\Tools\Layout\CustomLayoutService;
use Espo\Tools\Layout\LayoutDefs;
use Espo\Tools\Layout\Service as Service;
use Espo\Entities\User;
use stdClass;

class Layout
{
    public function __construct(
        private User $user,
        private Service $service,
        private InjectableFactory $injectableFactory
    ) {}

    
    public function getActionRead(Request $request)
    {
        $params = $request->getRouteParams();

        $scope = $params['scope'] ?? null;
        $name = $params['name'] ?? null;

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        return $this->service->getForFrontend($scope, $name);
    }

    
    public function putActionUpdate(Request $request)
    {
        $params = $request->getRouteParams();

        $data = json_decode($request->getBodyContents() ?? 'null');

        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $scope = $params['scope'] ?? null;
        $name = $params['name'] ?? null;
        $setId = $params['setId'] ?? null;

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        return $this->service->update($scope, $name, $setId, $data);
    }

    
    public function postActionResetToDefault(Request $request)
    {
        $data = $request->getParsedBody();

        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        if (empty($data->scope) || empty($data->name)) {
            throw new BadRequest();
        }

        return $this->service->resetToDefault($data->scope, $data->name, $data->setId ?? null);
    }

    
    public function getActionGetOriginal(Request $request)
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $scope = $request->getQueryParam('scope');
        $name = $request->getQueryParam('name');
        $setId = $request->getQueryParam('setId');

        if (!$scope || !$name) {
            throw new BadRequest("No `scope` or `name` parameter.");
        }

        return $this->service->getOriginal($scope, $name, $setId);
    }

    
    public function postActionCreate(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $body = $request->getParsedBody();

        $scope = $body->scope ?? null;
        $name = $body->name ?? null;
        $type = $body->type ?? null;
        $label = $body->label ?? null;

        if (
            !is_string($scope) ||
            !is_string($name) ||
            !is_string($type) ||
            !is_string($label) ||
            !$scope ||
            !$name ||
            !$type ||
            !$label
        ) {
            throw new BadRequest();
        }

        $defs = new LayoutDefs($scope, $name, $type, $label);

        $service = $this->injectableFactory->create(CustomLayoutService::class);

        $service->create($defs);

        return true;
    }

    
    public function postActionDelete(Request $request): bool
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        $body = $request->getParsedBody();

        $scope = $body->scope ?? null;
        $name = $body->name ?? null;

        if (
            !is_string($scope) ||
            !is_string($name) ||
            !$scope ||
            !$name
        ) {
            throw new BadRequest();
        }

        $service = $this->injectableFactory->create(CustomLayoutService::class);

        $service->delete($scope, $name);

        return true;
    }
}
