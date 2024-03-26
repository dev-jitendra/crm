<?php


namespace Espo\Services;

use Espo\Core\Exceptions\Forbidden;
use Espo\Tools\EmailTemplate\Processor;
use Espo\Tools\EmailTemplate\Params;
use Espo\Tools\EmailTemplate\Data;
use Espo\Entities\EmailTemplate as EmailTemplateEntity;
use Espo\Core\Exceptions\NotFound;
use Espo\Core\Di;


class EmailTemplate extends Record implements

    Di\FieldUtilAware
{
    use Di\FieldUtilSetter;

    
    public function parseTemplate(
        EmailTemplateEntity $emailTemplate,
        array $params = [],
        bool $copyAttachments = false,
        bool $skipAcl = false
    ): array {

        $paramsInternal = Params::create()
            ->withApplyAcl(!$skipAcl)
            ->withCopyAttachments($copyAttachments);

        $data = Data::create()
            ->withEmailAddress($params['emailAddress'] ?? null)
            ->withEntityHash($params['entityHash'] ?? [])
            ->withParent($params['parent'] ?? null)
            ->withParentId($params['parentId'] ?? null)
            ->withParentType($params['parentType'] ?? null)
            ->withRelatedId($params['relatedId'] ?? null)
            ->withRelatedType($params['relatedType'] ?? null)
            ->withUser($this->user);

        $result = $this->createProcessor()->process($emailTemplate, $paramsInternal, $data);

        
        return get_object_vars($result->getValueMap());
    }

    
    public function parse(string $id, array $params = [], bool $copyAttachments = false): array
    {
        
        $emailTemplate = $this->getEntity($id);

        if (empty($emailTemplate)) {
            throw new NotFound();
        }

        return $this->parseTemplate($emailTemplate, $params, $copyAttachments);
    }

    private function createProcessor(): Processor
    {
        return $this->injectableFactory->create(Processor::class);
    }
}
