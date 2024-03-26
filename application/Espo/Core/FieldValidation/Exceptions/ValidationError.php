<?php


namespace Espo\Core\FieldValidation\Exceptions;

use Espo\Core\Exceptions\BadRequest;
use Espo\Core\Exceptions\Error\Body;
use Espo\Core\Exceptions\HasLogMessage;
use Espo\Core\FieldValidation\Failure;

use LogicException;

class ValidationError extends BadRequest implements HasLogMessage
{
    private ?Failure $failure = null;

    
    public static function createWithBody(string $reason, string|Body $body): self
    {
        if ($body instanceof Body) {
            $body = $body->encode();
        }

        $exception = parent::createWithBody($reason, $body);

        if (!$exception instanceof self) {
            throw new LogicException();
        }

        return $exception;
    }

    public static function create(Failure $failure): self
    {
        $exception = self::createWithBody(
            'validationFailure',
            Body::create()
                ->withMessageTranslation('validationFailure', null, [
                    'field' => $failure->getField(),
                    'type' => $failure->getType(),
                ])
                ->encode()
        );

        $exception->failure = $failure;

        return $exception;
    }

    public function getFailure(): Failure
    {
        if (!$this->failure) {
            throw new LogicException();
        }

        return $this->failure;
    }

    public function getLogMessage(): string
    {
        if (!$this->failure) {
            return "Field validation failure.";
        }

        $entityType = $this->failure->getEntityType();
        $field = $this->failure->getField();
        $type = $this->failure->getType();

        return "Field validation failure; " .
            "entityType: {$entityType}, field: {$field}, type: {$type}.";
    }
}
