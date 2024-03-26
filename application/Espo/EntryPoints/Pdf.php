<?php


namespace Espo\EntryPoints;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\EntryPoint\EntryPoint;
use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\ORM\EntityManager;
use Espo\Core\Utils\Util;
use Espo\Entities\Template;
use Espo\Tools\Pdf\Service;

class Pdf implements EntryPoint
{
    private EntityManager $entityManager;
    private Service $service;

    public function __construct(EntityManager $entityManager, Service $service)
    {
        $this->entityManager = $entityManager;
        $this->service = $service;
    }

    public function run(Request $request, Response $response): void
    {
        $entityId = $request->getQueryParam('entityId');
        $entityType = $request->getQueryParam('entityType');
        $templateId = $request->getQueryParam('templateId');

        if (!$entityId || !$entityType || !$templateId) {
            throw new BadRequest();
        }

        $entity = $this->entityManager->getEntityById($entityType, $entityId);
        
        $template = $this->entityManager->getEntityById(Template::ENTITY_TYPE, $templateId);

        if (!$entity || !$template) {
            throw new NotFound();
        }

        $contents = $this->service->generate($entityType, $entityId, $templateId);

        $fileName = Util::sanitizeFileName($entity->get('name') ?? 'unnamed');

        $fileName = $fileName . '.pdf';

        $response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Cache-Control', 'private, must-revalidate, post-check=0, pre-check=0, max-age=1')
            ->setHeader('Pragma', 'public')
            ->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT')
            ->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($fileName) . '"');

        if (!$request->getServerParam('HTTP_ACCEPT_ENCODING')) {
            $response->setHeader('Content-Length', (string) $contents->getStream()->getSize());
        }

        $response->writeBody($contents->getStream());
    }
}
