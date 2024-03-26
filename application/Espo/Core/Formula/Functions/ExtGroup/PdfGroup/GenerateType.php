<?php


namespace Espo\Core\Formula\Functions\ExtGroup\PdfGroup;

use Espo\Core\Field\LinkParent;
use Espo\Entities\Attachment;
use Espo\Entities\Template;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Functions\BaseFunction;
use Espo\Core\Utils\Util;
use Espo\Tools\Pdf\Params;
use Espo\Core\Di;

use Espo\Tools\Pdf\Service;
use Exception;

class GenerateType extends BaseFunction implements
    Di\EntityManagerAware,
    Di\InjectableFactoryAware,
    Di\FileStorageManagerAware
{
    use Di\EntityManagerSetter;
    use Di\InjectableFactorySetter;
    use Di\FileStorageManagerSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 3) {
            $this->throwTooFewArguments(3);
        }

        $args = $this->evaluate($args);

        $entityType = $args[0];
        $id = $args[1];
        $templateId = $args[2];
        $fileName = $args[3];

        if (!$entityType || !is_string($entityType)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!$id || !is_string($id)) {
            $this->throwBadArgumentType(2, 'string');
        }

        if (!$templateId || !is_string($templateId)) {
            $this->throwBadArgumentType(3, 'string');
        }

        if ($fileName && !is_string($fileName)) {
            $this->throwBadArgumentType(4, 'string');
        }

        $em = $this->entityManager;

        try {
            $entity = $em->getEntity($entityType, $id);
        }
        catch (Exception $e) {
            $this->log("Message: " . $e->getMessage() . ".");

            throw new Error();
        }

        if (!$entity) {
            $this->log("Record {$entityType} {$id} does not exist.");

            throw new Error();
        }

        
        $template = $em->getEntityById(Template::ENTITY_TYPE, $templateId);

        if (!$template) {
            $this->log("Template {$templateId} does not exist.");

            throw new Error();
        }

        if ($fileName) {
            if (substr($fileName, -4) !== '.pdf') {
                $fileName .= '.pdf';
            }
        } else {
            $fileName = Util::sanitizeFileName($entity->get('name')) . '.pdf';
        }

        $params = Params::create()->withAcl(false);

        try {
            $service = $this->injectableFactory->create(Service::class);

            $contents = $service->generate(
                $entity->getEntityType(),
                $entity->getId(),
                $template->getId(),
                $params
            );
        }
        catch (Exception $e) {
            $this->log("Error while generating. Message: " . $e->getMessage() . ".", 'error');

            throw new Error();
        }

        
        $attachment = $em->getNewEntity(Attachment::ENTITY_TYPE);

        $attachment
            ->setName($fileName)
            ->setType('application/pdf')
            ->setSize($contents->getStream()->getSize())
            ->setRelated(LinkParent::create($entityType, $id))
            ->setRole(Attachment::ROLE_ATTACHMENT);

        $em->saveEntity($attachment);

        $this->fileStorageManager->putStream($attachment, $contents->getStream());

        return $attachment->getId();
    }
}
