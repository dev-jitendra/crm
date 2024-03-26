<?php


namespace Espo\Core\Utils\Metadata;

use Espo\Core\Utils\Util;

class BuilderHelper
{
    
    private array $copiedDefParams = [
        'readOnly',
        'disabled',
        'notStorable',
        'layoutListDisabled',
        'layoutDetailDisabled',
        'layoutMassUpdateDisabled',
        'layoutFiltersDisabled',
        'directAccessDisabled',
        'directUpdateDisabled',
        'customizationDisabled',
        'importDisabled',
        'exportDisabled',
    ];

    private string $defaultFieldNaming = 'postfix';

    
    public function getAdditionalFieldList(string $fieldName, array $fieldParams, array $definitionList): ?array
    {
        if (empty($fieldParams['type']) || empty($definitionList)) {
            return null;
        }

        $fieldType = $fieldParams['type'];
        $fieldDefinition = $definitionList[$fieldType] ?? null;

        if (
            isset($fieldDefinition) &&
            !empty($fieldDefinition['fields']) &&
            is_array($fieldDefinition['fields'])
        ) {
            $copiedParams = array_intersect_key($fieldParams, array_flip($this->copiedDefParams));

            $additionalFields = [];

            foreach ($fieldDefinition['fields'] as $subFieldName => $subFieldParams) {
                $namingType = $fieldDefinition['naming'] ?? $this->defaultFieldNaming;

                $subFieldNaming = Util::getNaming($fieldName, $subFieldName, $namingType);

                $additionalFields[$subFieldNaming] = array_merge($copiedParams, $subFieldParams);
            }

            return $additionalFields;
        }

        return null;
    }
}
