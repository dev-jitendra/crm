<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\ForbiddenSilent;

use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\Error;

use Espo\Tools\LeadCapture\Service;
use Espo\Tools\LeadCapture\CaptureService as CaptureService;
use stdClass;

class LeadCapture extends Record
{
    
    public function postActionLeadCapture(Request $request, Response $response): bool
    {
        $data = $request->getParsedBody();
        $apiKey = $request->getRouteParam('apiKey');

        if (!$apiKey) {
            throw new BadRequest('No API key provided.');
        }

        $allowOrigin = $this->config->get('leadCaptureAllowOrigin', '*');

        $response->setHeader('Access-Control-Allow-Origin', $allowOrigin);

        $this->getCaptureService()->capture($apiKey, $data);

        return true;
    }

    
    public function optionsActionLeadCapture(Request $request, Response $response): bool
    {
        $apiKey = $request->getRouteParam('apiKey');

        if (!$apiKey) {
            throw new BadRequest('No API key provided.');
        }

        if (!$this->getLeadCaptureService()->isApiKeyValid($apiKey)) {
            throw new NotFound();
        }

        $allowOrigin = $this->config->get('leadCaptureAllowOrigin', '*');

        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Accept');
        $response->setHeader('Access-Control-Allow-Origin', $allowOrigin);
        $response->setHeader('Access-Control-Allow-Methods', 'POST');

        return true;
    }

    
    public function postActionGenerateNewApiKey(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        if (empty($data->id)) {
            throw new BadRequest();
        }

        return $this->getLeadCaptureService()
            ->generateNewApiKeyForEntity($data->id)
            ->getValueMap();
    }

    
    public function getActionSmtpAccountDataList(): array
    {
        if (!$this->user->isAdmin()) {
            throw new Forbidden();
        }

        return $this->getLeadCaptureService()->getSmtpAccountDataList();
    }

    private function getCaptureService(): CaptureService
    {
        return $this->injectableFactory->create(CaptureService::class);
    }

    private function getLeadCaptureService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
