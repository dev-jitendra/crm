<?php


namespace Espo\Modules\Crm\Controllers;

use Espo\Core\Controllers\Record;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Api\Request;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Core\Exceptions\NotFound;
use Espo\Modules\Crm\Tools\Document\Service;
use Espo\Tools\Attachment\FieldData;
use stdClass;

class Document extends Record
{

    
    public function postActionGetAttachmentList(Request $request): array
    {
        $data = $request->getParsedBody();

        $id = $data->id ?? null;
        $field = $data->field ?? null;
        $parentType = $data->parentType ?? null;
        $relatedType = $data->relatedType ?? null;

        if (!$id || !$field) {
            throw new BadRequest("No `id` or `field`.");
        }

        try {
            $fieldData = new FieldData(
                $field,
                $parentType,
                $relatedType
            );
        }
        catch (Error $e) {
            throw new BadRequest($e->getMessage());
        }

        $attachment = $this->getDocumentService()->copyAttachment($id, $fieldData);

        return [$attachment->getValueMap()];
    }

    private function getDocumentService(): Service
    {
        return $this->injectableFactory->create(Service::class);
    }
}
