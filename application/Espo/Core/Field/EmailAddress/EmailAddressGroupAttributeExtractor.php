<?php


namespace Espo\Core\Field\EmailAddress;

use Espo\ORM\Value\AttributeExtractor;

use Espo\Core\Field\EmailAddressGroup;

use stdClass;
use InvalidArgumentException;


class EmailAddressGroupAttributeExtractor implements AttributeExtractor
{
    
    public function extract(object $group, string $field): stdClass
    {
        if (!$group instanceof EmailAddressGroup) {
            throw new InvalidArgumentException();
        }

        $primaryAddress = $group->getPrimary() ? $group->getPrimary()->getAddress() : null;

        $dataList = [];

        foreach ($group->getList() as $emailAddress) {
            $dataList[] = (object) [
                'emailAddress' => $emailAddress->getAddress(),
                'lower' => strtolower($emailAddress->getAddress()),
                'primary' => $primaryAddress && $emailAddress->getAddress() === $primaryAddress,
                'optOut' => $emailAddress->isOptedOut(),
                'invalid' => $emailAddress->isInvalid(),
            ];
        }

        return (object) [
            $field => $primaryAddress,
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
