<?php


namespace Espo\Core\Formula\Parser\Statement;

class WhileRef
{
    public const STATE_EMPTY = 0;
    public const STATE_CONDITION_STARTED = 1;
    public const STATE_CONDITION_ENDED = 2;
    public const STATE_BODY_STARTED = 3;
    public const STATE_READY = 4;

    private ?int $conditionStart = null;
    private ?int $conditionEnd = null;
    private ?int $bodyStart = null;
    private ?int $bodyEnd = null;
    private int $state = self::STATE_EMPTY;

    public function __construct(private int $start)
    {}

    public function setConditionStart(int $conditionStart): void
    {
        $this->conditionStart = $conditionStart;
        $this->state = self::STATE_CONDITION_STARTED;
    }

    public function setConditionEnd(int $conditionEnd): void
    {
        $this->conditionEnd = $conditionEnd;
        $this->state = self::STATE_CONDITION_ENDED;
    }

    public function setBodyStart(int $bodyStart): void
    {
        $this->bodyStart = $bodyStart;
        $this->state = self::STATE_BODY_STARTED;
    }

    public function setBodyEnd(int $bodyEnd): void
    {
        $this->bodyEnd = $bodyEnd;
        $this->state = self::STATE_READY;
    }

    public function isReady(): bool
    {
        return $this->state === self::STATE_READY;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function getConditionStart(): ?int
    {
        return $this->conditionStart;
    }

    public function getConditionEnd(): ?int
    {
        return $this->conditionEnd;
    }

    public function getBodyStart(): ?int
    {
        return $this->bodyStart;
    }

    public function getBodyEnd(): ?int
    {
        return $this->bodyEnd;
    }

    public function getEnd(): ?int
    {
        return $this->bodyEnd;
    }

    public function getStart(): int
    {
        return $this->start;
    }
}
