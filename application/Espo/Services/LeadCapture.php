<?php


namespace Espo\Services;

use Espo\Entities\LeadCapture as LeadCaptureEntity;
use Espo\Modules\Crm\Entities\Lead;
use Espo\ORM\Entity;
use Espo\Tools\LeadCapture\Service as LeadCaptureService;
use Espo\Core\Utils\Util;


class LeadCapture extends Record
{
    
    protected $readOnlyAttributeList = ['apiKey'];

    
    public function prepareEntityForOutput(Entity $entity)
    {
        parent::prepareEntityForOutput($entity);

        $entity->set('exampleRequestMethod', 'POST');

        $entity->set('exampleRequestHeaders', [
            'Content-Type: application/json',
        ]);

        $apiKey = $entity->getApiKey();

        if ($apiKey) {
            $requestUrl = $this->config->getSiteUrl() . '/api/v1/LeadCapture/' . $apiKey;

            $entity->set('exampleRequestUrl', $requestUrl);
        }

        $fieldUtil = $this->fieldUtil;

        $requestPayload = "```{\n";

        $attributeList = [];

        $attributeIgnoreList = [
            'emailAddressIsOptedOut',
            'phoneNumberIsOptedOut',
            'emailAddressIsInvalid',
            'phoneNumberIsInvalid',
            'emailAddressData',
            'phoneNumberData',
        ];

        foreach ($entity->getFieldList() as $field) {
            foreach ($fieldUtil->getActualAttributeList(Lead::ENTITY_TYPE, $field) as $attribute) {
                if (!in_array($attribute, $attributeIgnoreList)) {
                    $attributeList[] = $attribute;
                }
            }
        }

        $seed = $this->entityManager->getNewEntity(Lead::ENTITY_TYPE);

        foreach ($attributeList as $i => $attribute) {
            $value = strtoupper(Util::camelCaseToUnderscore($attribute));

            if (in_array($seed->getAttributeType($attribute), [Entity::VARCHAR, Entity::TEXT])) {
                $value = '"' . $value . '"';
            }

            $requestPayload .= "    \"" . $attribute . "\": " . $value;

            if ($i < count($attributeList) - 1) {
                $requestPayload .= ",";
            }

            $requestPayload .= "\n";
        }

        $requestPayload .= '}```';

        $entity->set('exampleRequestPayload', $requestPayload);
    }

    protected function beforeCreateEntity(Entity $entity, $data)
    {
        $apiKey = $this->createLeadCaptureService()->generateApiKey();

        $entity->set('apiKey', $apiKey);
    }

    protected function createLeadCaptureService(): LeadCaptureService
    {
        return $this->injectableFactory->create(LeadCaptureService::class);
    }
}
