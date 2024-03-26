<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Tools\Dashboard\Service;

use Espo\Core\Api\Request;
use Espo\Core\Controllers\Record;

class DashboardTemplate extends Record
{
    protected function checkAccess(): bool
    {
        return $this->user->isAdmin();
    }

    
    public function postActionDeployToUsers(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        if (empty($data->userIdList)) {
            throw new BadRequest();
        }

        $this->getDashboardTemplateService()->deployTemplateToUsers(
            $data->id,
            $data->userIdList,
            !empty($data->append)
        );

        return true;
    }

    
    public function postActionDeployToTeam(Request $request): bool
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        if (empty($data->teamId)) {
            throw new BadRequest();
        }

        $this->getDashboardTemplateService()->deployTemplateToTeam(
            $data->id,
            $data->teamId,
            !empty($data->append)
        );

        return true;
    }

    private function getDashboardTemplateService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
