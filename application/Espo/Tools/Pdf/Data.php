<?php


namespace Espo\Tools\Pdf;

use stdClass;

class Data
{
    
    private $additionalTemplateData = [];

    public function getAdditionalTemplateData(): stdClass
    {
        return (object) $this->additionalTemplateData;
    }

    public function withAdditionalTemplateData(stdClass $additionalTemplateData): self
    {
        $obj = clone $this;

        $obj->additionalTemplateData = array_merge(
            $obj->additionalTemplateData,
            get_object_vars($additionalTemplateData)
        );

        return $obj;
    }

    public static function create(): self
    {
        return new self();
    }
}
