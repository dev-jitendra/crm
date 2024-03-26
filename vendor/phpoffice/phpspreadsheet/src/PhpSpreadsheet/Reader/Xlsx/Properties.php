<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Document\Properties as DocumentProperties;
use PhpOffice\PhpSpreadsheet\Reader\Security\XmlScanner;
use PhpOffice\PhpSpreadsheet\Settings;
use SimpleXMLElement;

class Properties
{
    private $securityScanner;

    private $docProps;

    public function __construct(XmlScanner $securityScanner, DocumentProperties $docProps)
    {
        $this->securityScanner = $securityScanner;
        $this->docProps = $docProps;
    }

    private function extractPropertyData($propertyData)
    {
        return simplexml_load_string(
            $this->securityScanner->scan($propertyData),
            'SimpleXMLElement',
            Settings::getLibXmlLoaderOptions()
        );
    }

    public function readCoreProperties($propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            $xmlCore->registerXPathNamespace('dc', 'http:
            $xmlCore->registerXPathNamespace('dcterms', 'http:
            $xmlCore->registerXPathNamespace('cp', 'http:

            $this->docProps->setCreator((string) self::getArrayItem($xmlCore->xpath('dc:creator')));
            $this->docProps->setLastModifiedBy((string) self::getArrayItem($xmlCore->xpath('cp:lastModifiedBy')));
            $this->docProps->setCreated(strtotime(self::getArrayItem($xmlCore->xpath('dcterms:created')))); 
            $this->docProps->setModified(strtotime(self::getArrayItem($xmlCore->xpath('dcterms:modified')))); 
            $this->docProps->setTitle((string) self::getArrayItem($xmlCore->xpath('dc:title')));
            $this->docProps->setDescription((string) self::getArrayItem($xmlCore->xpath('dc:description')));
            $this->docProps->setSubject((string) self::getArrayItem($xmlCore->xpath('dc:subject')));
            $this->docProps->setKeywords((string) self::getArrayItem($xmlCore->xpath('cp:keywords')));
            $this->docProps->setCategory((string) self::getArrayItem($xmlCore->xpath('cp:category')));
        }
    }

    public function readExtendedProperties($propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            if (isset($xmlCore->Company)) {
                $this->docProps->setCompany((string) $xmlCore->Company);
            }
            if (isset($xmlCore->Manager)) {
                $this->docProps->setManager((string) $xmlCore->Manager);
            }
        }
    }

    public function readCustomProperties($propertyData): void
    {
        $xmlCore = $this->extractPropertyData($propertyData);

        if (is_object($xmlCore)) {
            foreach ($xmlCore as $xmlProperty) {
                
                $cellDataOfficeAttributes = $xmlProperty->attributes();
                if (isset($cellDataOfficeAttributes['name'])) {
                    $propertyName = (string) $cellDataOfficeAttributes['name'];
                    $cellDataOfficeChildren = $xmlProperty->children('http:

                    $attributeType = $cellDataOfficeChildren->getName();
                    $attributeValue = (string) $cellDataOfficeChildren->{$attributeType};
                    $attributeValue = DocumentProperties::convertProperty($attributeValue, $attributeType);
                    $attributeType = DocumentProperties::convertPropertyType($attributeType);
                    $this->docProps->setCustomProperty($propertyName, $attributeValue, $attributeType);
                }
            }
        }
    }

    private static function getArrayItem(array $array, $key = 0)
    {
        return $array[$key] ?? null;
    }
}
