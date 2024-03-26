<?php


namespace Espo\Core\Select\Where;

use InvalidArgumentException;


class Params
{
    private bool $applyPermissionCheck = false;
    private bool $forbidComplexExpressions = false;

    private function __construct()
    {}

    
    public static function fromAssoc(array $params): self
    {
        $object = new self();

        $object->applyPermissionCheck = $params['applyPermissionCheck'] ?? false;
        $object->forbidComplexExpressions = $params['forbidComplexExpressions'] ?? false;

        foreach ($params as $key => $value) {
            if (!property_exists($object, $key)) {
                throw new InvalidArgumentException("Unknown parameter '{$key}'.");
            }
        }

        return $object;
    }

    
    public function applyPermissionCheck(): bool
    {
        return $this->applyPermissionCheck;
    }

    
    public function forbidComplexExpressions(): bool
    {
        return $this->forbidComplexExpressions;
    }
}
