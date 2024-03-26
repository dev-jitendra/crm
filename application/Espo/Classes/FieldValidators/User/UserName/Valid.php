<?php


namespace Espo\Classes\FieldValidators\User\UserName;

use Espo\Core\FieldValidation\Validator;
use Espo\Core\FieldValidation\Validator\Data;
use Espo\Core\FieldValidation\Validator\Failure;
use Espo\Core\Utils\Config;
use Espo\Entities\User;
use Espo\ORM\Entity;
use RuntimeException;


class Valid implements Validator
{
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    
    public function validate(Entity $entity, string $field, Data $data): ?Failure
    {
        $value = $entity->getUserName();

        if ($value === null) {
            return null;
        }

        
        $regExp = $this->config->get('userNameRegularExpression');

        if (!$regExp) {
            throw new RuntimeException("No `userNameRegularExpression` in config.");
        }

        if (strpos($value, ' ') !== false) {
            return Failure::create();
        }

        if (preg_replace("/{$regExp}/", '_', $value) !== $value) {
            return Failure::create();
        }

        return null;
    }
}
