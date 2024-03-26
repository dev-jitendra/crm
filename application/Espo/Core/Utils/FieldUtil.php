<?php


namespace Espo\Core\Utils;

class FieldUtil
{
    
    private $fieldByTypeListCache = [];

    public function __construct(private Metadata $metadata)
    {}

    
    public function getFieldType(string $entityType, string $field): ?string
    {
        return $this->metadata->get("entityDefs.$entityType.fields.$field.type");
    }

    
    private function getAttributeListByType(string $entityType, string $name, string $type): array
    {
        $fieldType = $this->getFieldType($entityType, $name);

        if (!$fieldType) {
            return [];
        }

        $defs = $this->metadata->get('fields.' . $fieldType);

        if (!$defs) {
            return [];
        }

        if (is_object($defs)) {
            $defs = get_object_vars($defs);
        }

        $fieldList = [];

        if (isset($defs[$type . 'Fields'])) {
            $list = $defs[$type . 'Fields'];

            $naming = 'suffix';

            if (isset($defs['naming'])) {
                $naming = $defs['naming'];
            }

            if ($naming == 'prefix') {
                foreach ($list as $f) {
                    if ($f === '') {
                        $fieldList[] = $name;
                    } else {
                        $fieldList[] = $f . ucfirst($name);
                    }
                }
            }
            else {
                foreach ($list as $f) {
                    $fieldList[] = $name . ucfirst($f);
                }
            }
        }
        else {
            if ($type == 'actual') {
                $fieldList[] = $name;
            }
        }

        return $fieldList;
    }

    
    public function getAdditionalActualAttributeList(string $entityType, string $name): array
    {
        $attributeList = [];

        $list = $this->metadata->get(['entityDefs', $entityType, 'fields', $name, 'additionalAttributeList']);

        if (empty($list)) {
            return [];
        }

        $type = $this->metadata->get(['entityDefs', $entityType, 'fields', $name, 'type']);

        if (!$type) {
            return [];
        }

        $naming = $this->metadata->get(['fields', $type, 'naming'], 'suffix');

        if ($naming == 'prefix') {
            foreach ($list as $f) {
                $attributeList[] = $f . ucfirst($name);
            }
        } else {
            foreach ($list as $f) {
                $attributeList[] = $name . ucfirst($f);
            }
        }

        return $attributeList;
    }

    
    public function getActualAttributeList(string $entityType, string $field): array
    {
        return array_merge(
            $this->getAttributeListByType($entityType, $field, 'actual'),
            $this->getAdditionalActualAttributeList($entityType, $field)
        );
    }

    
    public function getNotActualAttributeList(string $entityType, string $field): array
    {
        return $this->getAttributeListByType($entityType, $field, 'notActual');
    }

    
    public function getAttributeList(string $entityType, string $field): array
    {
        return array_merge(
            $this->getActualAttributeList($entityType, $field),
            $this->getNotActualAttributeList($entityType, $field)
        );
    }

    
    public function getFieldByTypeList(string $entityType, string $type): array
    {
        if (!array_key_exists($entityType, $this->fieldByTypeListCache)) {
            $this->fieldByTypeListCache[$entityType] = [];
        }

        if (!array_key_exists($type, $this->fieldByTypeListCache[$entityType])) {
            
            $fieldDefs = $this->metadata->get(['entityDefs', $entityType, 'fields'], []);

            $list = [];

            foreach ($fieldDefs as $field => $defs) {
                if (isset($defs['type']) && $defs['type'] === $type) {
                    $list[] = $field;
                }
            }

            $this->fieldByTypeListCache[$entityType][$type] = $list;
        }

        return $this->fieldByTypeListCache[$entityType][$type];
    }

    
    private function getFieldTypeAttributeListByType(string $fieldType, string $name, string $type): array
    {
        $defs = $this->metadata->get(['fields', $fieldType]);

        if (!$defs) {
            return [];
        }

        $attributeList = [];

        if (isset($defs[$type . 'Fields'])) {
            $list = $defs[$type . 'Fields'];

            $naming = 'suffix';

            if (isset($defs['naming'])) {
                $naming = $defs['naming'];
            }

            if ($naming == 'prefix') {
                foreach ($list as $f) {
                    $attributeList[] = $f . ucfirst($name);
                }
            } else {
                foreach ($list as $f) {
                    $attributeList[] = $name . ucfirst($f);
                }
            }
        } else {
            if ($type == 'actual') {
                $attributeList[] = $name;
            }
        }

        return $attributeList;
    }

    
    public function getFieldTypeAttributeList(string $fieldType, string $name): array
    {
        return array_merge(
            $this->getFieldTypeAttributeListByType($fieldType, $name, 'actual'),
            $this->getFieldTypeAttributeListByType($fieldType, $name, 'notActual')
        );
    }

    
    public function getEntityTypeFieldList(string $entityType): array
    {
        
        return array_keys($this->metadata->get(['entityDefs', $entityType, 'fields'], []));
    }

    
    public function getEntityTypeFieldParam(string $entityType, string $field, string $param)
    {
        return $this->metadata->get(['entityDefs', $entityType, 'fields', $field, $param]);
    }

    
    public function getEntityTypeAttributeList(string $entityType): array
    {
        $attributeList = [];

        foreach ($this->getEntityTypeFieldList($entityType) as $field) {
            $attributeList = array_merge(
                $attributeList,
                $this->getAttributeList($entityType, $field)
            );
        }

        return $attributeList;
    }
}
