<?php


namespace Espo\Classes\FieldValidators\Email\Addresses;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;
use Espo\ORM\Entity;
use Espo\Entities\Email;

use LogicException;


class Valid implements Validator
{
    
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

        foreach ($addresses as $address) {
            if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
                return Failure::create();
            }
        }

        return null;
    }
}
