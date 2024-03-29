<?php

namespace Laminas\Mail\Header;

use function explode;
use function implode;
use function strpos;


class GenericMultiHeader extends GenericHeader implements MultipleHeadersInterface
{
    
    public static function fromString($headerLine)
    {
        [$fieldName, $fieldValue] = GenericHeader::splitHeaderLine($headerLine);
        $fieldValue               = HeaderWrap::mimeDecodeValue($fieldValue);

        if (strpos($fieldValue, ',')) {
            $headers = [];
            foreach (explode(',', $fieldValue) as $multiValue) {
                $headers[] = new static($fieldName, $multiValue);
            }
            return $headers;
        }

        return new static($fieldName, $fieldValue);
    }

    
    public function toStringMultipleHeaders(array $headers)
    {
        $name   = $this->getFieldName();
        $values = [$this->getFieldValue(HeaderInterface::FORMAT_ENCODED)];

        foreach ($headers as $header) {
            if (! $header instanceof static) {
                throw new Exception\InvalidArgumentException(
                    'This method toStringMultipleHeaders was expecting an array of headers of the same type'
                );
            }
            $values[] = $header->getFieldValue(HeaderInterface::FORMAT_ENCODED);
        }

        return $name . ': ' . implode(',', $values);
    }
}
