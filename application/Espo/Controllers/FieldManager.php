<?php


namespace Espo\Controllers;

use Espo\Entities\User;
use Espo\Tools\FieldManager\FieldManager as FieldManagerTool;
use Espo\Core\Api\Request;
use Espo\Core\DataManager;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Conflict;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;

class FieldManager
{
    
    public function __construct(
        private User $user,
        private DataManager $dataManager,
        private FieldManagerTool $fieldManagerTool
    ) {
        $this->checkControllerAccess();
    }

    
    protected function checkControllerAccess(): void
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }
    }

    
    public function getActionRead(Request $request): array
    {
        $scope = $request->getRouteParam('scope');
        $name = $request->getRouteParam('name');

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        return $this->fieldManagerTool->read($scope, $name);
    }

    
    public function postActionCreate(Request $request): array
    {
        $data = $request->getParsedBody();

        $scope = $request->getRouteParam('scope');
        $name = $data->name ?? null;

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        $fieldManagerTool = $this->fieldManagerTool;

        $fieldManagerTool->create($scope, $name, get_object_vars($data));

        try {
            $this->dataManager->rebuild([$scope]);
        }
        catch (Error $e) {
            $fieldManagerTool->delete($scope, $data->name);

            throw new Error($e->getMessage());
        }

        return $fieldManagerTool->read($scope, $data->name);
    }

    
    public function patchActionUpdate(Request $request): array
    {
        return $this->putActionUpdate($request);
    }

    
    public function putActionUpdate(Request $request): array
    {
        $data = $request->getParsedBody();

        $scope = $request->getRouteParam('scope');
        $name = $request->getRouteParam('name');

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        $fieldManagerTool = $this->fieldManagerTool;

        $fieldManagerTool->update($scope, $name, get_object_vars($data));

        if ($fieldManagerTool->isChanged()) {
            $this->dataManager->rebuild([$scope]);
        } else {
            $this->dataManager->clearCache();
        }

        return $fieldManagerTool->read($scope, $name);
    }

    
    public function deleteActionDelete(Request $request): bool
    {
        $scope = $request->getRouteParam('scope');
        $name = $request->getRouteParam('name');

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        $result = $this->fieldManagerTool->delete($scope, $name);

        $this->dataManager->clearCache();
        $this->dataManager->rebuildMetadata();

        return $result;
    }

    
    public function postActionResetToDefault(Request $request): bool
    {
        $data = $request->getParsedBody();

        $scope = $data->scope ?? null;
        $name = $data->name ?? null;

        if (!$scope || !$name) {
            throw new BadRequest();
        }

        if (!is_string($scope) || !is_string($name)) {
            throw new BadRequest();
        }

        $this->fieldManagerTool->resetToDefault($scope, $name);

        $this->dataManager->rebuild([$scope]);

        return true;
    }
}
