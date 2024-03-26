<?php


namespace Espo\Core\Utils\Metadata;

use Espo\Core\Utils\Metadata;

class Helper
{
    public function __construct(private Metadata $metadata)
    {}

    
    public function getFieldDefsByType($defs)
    {
        if (isset($defs['type'])) {
            return $this->metadata->get('fields.' . $defs['type']);
        }

        return null;
    }

    
    public function getFieldDefsInFieldMetadata($defs)
    {
        $fieldDefsByType = $this->getFieldDefsByType($defs);

        if (isset($fieldDefsByType['fieldDefs'])) {
            return $fieldDefsByType['fieldDefs'];
        }

        return null;
    }

    
    public function getLinkDefsInFieldMeta($entityType, $defs)
    {
        $fieldDefsByType = $this->getFieldDefsByType($defs);

        if (!isset($fieldDefsByType['linkDefs'])) {
            return null;
        }

        $linkFieldDefsByType = $fieldDefsByType['linkDefs'];

        foreach ($linkFieldDefsByType as &$paramValue) {
            if (preg_match('/{(.*?)}/', $paramValue, $matches)) {
                if (in_array($matches[1], array_keys($defs))) {
                    $value = $defs[$matches[1]];
                }
                else if (strtolower($matches[1]) == 'entity') {
                    $value = $entityType;
                }

                if (isset($value)) {
                    $paramValue = str_replace('{'.$matches[1].'}', $value, $paramValue);
                }
            }
        }

        return $linkFieldDefsByType;
    }
}
