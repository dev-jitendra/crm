<?php


namespace Espo\Core\Job\Job;

use Espo\Core\Utils\ObjectUtil;

use TypeError;
use stdClass;

class Data
{
    private stdClass $data;
    private ?string $targetId = null;
    private ?string $targetType = null;

    public function __construct(?stdClass $data = null)
    {
        $this->data = $data ?? (object) [];
    }

    
    public static function create($data = null): self
    {
        

        if ($data !== null && !is_object($data) && !is_array($data)) {
            throw new TypeError();
        }

        if (is_array($data)) {
            $data = (object) $data;
        }

        

        return new self($data);
    }

    public function getRaw(): stdClass
    {
        return ObjectUtil::clone($this->data);
    }

    
    public function get(string $name)
    {
        return $this->getRaw()->$name ?? null;
    }

    public function has(string $name): bool
    {
        return property_exists($this->data, $name);
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    public function getTargetType(): ?string
    {
        return $this->targetType;
    }

    public function withTargetId(?string $targetId): self
    {
        $obj = clone $this;
        $obj->targetId = $targetId;

        return $obj;
    }

    public function withTargetType(?string $targetType): self
    {
        $obj = clone $this;
        $obj->targetType = $targetType;

        return $obj;
    }
}
