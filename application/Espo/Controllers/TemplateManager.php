<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\Metadata;
use Espo\Core\Utils\TemplateFileManager;
use Espo\Core\ApplicationState;
use Espo\Core\Api\Request;

use stdClass;


class TemplateManager
{
    
    public function __construct(
        private Metadata $metadata,
        private TemplateFileManager $templateFileManager,
        private ApplicationState $applicationState,
        private Config $config
    ) {

        if (!$this->applicationState->isAdmin()) {
            throw new Forbidden();
        }
    }

    
    public function getActionGetTemplate(Request $request): stdClass
    {
        $name = $request->getQueryParam('name');

        if (empty($name)) {
            throw new BadRequest();
        }

        $scope = $request->getQueryParam('scope');

        $module = $this->metadata->get(['app', 'templates', $name, 'module']);
        $hasSubject = !$this->metadata->get(['app', 'templates', $name, 'noSubject']);

        $templateFileManager = $this->templateFileManager;

        $returnData = (object) [];

        $returnData->body = $templateFileManager->getTemplate($name, 'body', $scope, $module);

        if ($hasSubject) {
            $returnData->subject = $templateFileManager->getTemplate($name, 'subject', $scope, $module);
        }

        return $returnData;
    }

    
    public function postActionSaveTemplate(Request $request): bool
    {
        $data = $request->getParsedBody();

        $scope = null;

        if (empty($data->name)) {
            
            throw new BadRequest();
        }

        if (
            $data->name === 'passwordChangeLink' &&
            $this->config->get('restrictedMode') &&
            !$this->applicationState->getUser()->isSuperAdmin()
        ) {
            throw new Forbidden();
        }

        if (!empty($data->scope)) {
            $scope = $data->scope;
        }

        $templateFileManager = $this->templateFileManager;

        if (isset($data->subject)) {
            $templateFileManager->saveTemplate($data->name, 'subject', $data->subject, $scope);
        }

        if (isset($data->body)) {
            $templateFileManager->saveTemplate($data->name, 'body', $data->body, $scope);
        }

        return true;
    }

    
    public function postActionResetTemplate(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        $scope = null;

        if (empty($data->name)) {
            throw new BadRequest();
        }

        if (!empty($data->scope)) {
            $scope = $data->scope;
        }

        $module = $this->metadata->get(['app', 'templates', $data->name, 'module']);
        $hasSubject = !$this->metadata->get(['app', 'templates', $data->name, 'noSubject']);

        $templateFileManager = $this->templateFileManager;

        if ($hasSubject) {
            $templateFileManager->resetTemplate($data->name, 'subject', $scope);
        }

        $templateFileManager->resetTemplate($data->name, 'body', $scope);

        $returnData = (object) [];

        $returnData->body = $templateFileManager->getTemplate($data->name, 'body', $scope, $module);

        if ($hasSubject) {
            $returnData->subject = $templateFileManager->getTemplate($data->name, 'subject', $scope, $module);
        }

        return $returnData;
    }
}
