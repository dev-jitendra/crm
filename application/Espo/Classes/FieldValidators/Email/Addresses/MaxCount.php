<?php


namespace Espo\Classes\FieldValidators\Email\Addresses;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;
use Espo\Core\Utils\Config;
use Espo\Entities\Email;
use Espo\ORM\Entity;

use LogicException;


class MaxCount implements Validator
{
    private const MAX_COUNT = 100;

    public function __construct(private Config $config) {}

    
    public function validate(Entity $entity, string $field, Data $data): ?Failure
    {
        if ($field === 'to') {
            $addresses = $entity->getToAddressList();
        }
        else if ($field === 'cc') {
            $addresses = $entity->getCcAddressList();
        }
        else if ($field === 'bcc') {
            $addresses = $entity->getBccAddressList();
        }
        else {
            throw new LogicException();
        }

        $maxCount = $this->config->get('emailRecipientAddressMaxCount') ?? self::MAX_COUNT;

        if (count($addresses) > $maxCount) {
            return Failure::create();
        }

        return null;
    }
}
