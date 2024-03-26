<?php


namespace Espo\Tools\Attachment\Api;

use Espo\Core\Api\Action as ActionAlias;
use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Tools\Attachment\Service;


class GetFile implements ActionAlias
{
    public function __construct(private Service $service) {}

    public function process(Request $request): Response
    {
        $id = $request->getRouteParam('id');

        if (!$id) {
            throw new BadRequest();
        }

        $fileData = $this->service->getFileData($id);

        $response = ResponseComposer::empty()
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileData->getName() . '"')
            ->setHeader('Content-Length', (string) $fileData->getSize())
            ->setBody($fileData->getStream());

        if ($fileData->getType()) {
            $response->setHeader('Content-Type', $fileData->getType());
        }

        return $response;
    }
}
