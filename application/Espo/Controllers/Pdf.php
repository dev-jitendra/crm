<?php


namespace Espo\Controllers;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;

use Espo\Core\Acl;
use Espo\Core\Api\Request;

use Espo\Core\Exceptions\NotFound;
use Espo\Entities\Template as TemplateEntity;
use Espo\Tools\Pdf\MassService;

use stdClass;

class Pdf
{
    private MassService $service;
    private Acl $acl;

    public function __construct(MassService $service, Acl $acl)
    {
        $this->service = $service;
        $this->acl = $acl;
    }

    
    public function postActionMassPrint(Request $request): stdClass
    {
        $data = $request->getParsedBody();

        if (empty($data->idList) || !is_array($data->idList)) {
            throw new BadRequest();
        }

        if (empty($data->entityType)) {
            throw new BadRequest();
        }

        if (empty($data->templateId)) {
            throw new BadRequest();
        }

        if (!$this->acl->checkScope(TemplateEntity::ENTITY_TYPE)) {
            throw new Forbidden();
        }

        if (!$this->acl->checkScope($data->entityType)) {
            throw new Forbidden();
        }

        $id = $this->service->generate($data->entityType, $data->idList, $data->templateId);

        return (object) [
            'id' => $id,
        ];
    }
}
