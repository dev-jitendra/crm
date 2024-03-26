<?php


namespace Espo\Services;

use Espo\Core\Exceptions\NotFound;
use Espo\ORM\Entity;

use Espo\Core\Exceptions\Error;
use Espo\Core\Exceptions\Forbidden;
use Espo\Tools\Pdf\Data;
use Espo\Tools\Pdf\Params;
use Espo\Tools\Pdf\Service;
use Espo\Entities\Template;


class Pdf
{
    private Service $service;

    public function __construct(
        Service $service
    ) {
        $this->service = $service;
    }

    
    public function generate(Entity $entity, Template $template, ?Params $params = null, ?Data $data = null): string
    {
        $additionalData = null;

        if ($data) {
            $additionalData = get_object_vars($data->getAdditionalTemplateData());
        }

        return $this->buildFromTemplate($entity, $template, false, $additionalData);
    }

    
    public function buildFromTemplate(
        Entity $entity,
        Template $template,
        bool $displayInline = false,
        ?array $additionalData = null
    ): string {

        $data = Data::create()
            ->withAdditionalTemplateData(
                (object) ($additionalData ?? [])
            );

        $contents = $this->service->generate(
            $entity->getEntityType(),
            $entity->getId(),
            $template->getId(),
            null,
            $data
        );

        return $contents->getString();
    }
}
