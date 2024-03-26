<?php


namespace Espo\Core\Field\PhoneNumber;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\PhoneNumberGroup;

use stdClass;
use InvalidArgumentException;


class PhoneNumberGroupAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $group, string $field): stdClass
    {
        if (!$group instanceof PhoneNumberGroup) {
            throw new InvalidArgumentException();
        }

        $primaryNumber = $group->getPrimary() ? $group->getPrimary()->getNumber() : null;

        $dataList = [];

        foreach ($group->getList() as $phoneNumber) {
            $dataList[] = (object) [
                'phoneNumber' => $phoneNumber->getNumber(),
                'type' => $phoneNumber->getType(),
                'primary' => $primaryNumber && $phoneNumber->getNumber() === $primaryNumber,
                'optOut' => $phoneNumber->isOptedOut(),
                'invalid' => $phoneNumber->isInvalid(),
            ];
        }

        return (object) [
            $field => $primaryNumber,
            $field . 'Data' => $dataList,
        ];
    }

    public function extractFromNull(string $field): stdClass
    {
        return (object) [
            $field => null,
            $field . 'Data' => [],
        ];
    }
}
