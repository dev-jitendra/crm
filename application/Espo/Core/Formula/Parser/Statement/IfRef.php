<?php


namespace Espo\Core\Formula\Parser\Statement;

class IfRef
{
    public const STATE_EMPTY = 0;
    public const STATE_CONDITION_STARTED = 1;
    public const STATE_CONDITION_ENDED = 2;
    public const STATE_THEN_STARTED = 3;
    public const STATE_THEN_ENDED = 4;
    public const STATE_ELSE_MET = 5;
    public const STATE_ELSE_STARTED = 6;
    public const STATE_ELSE_ENDED = 7;
    public const STATE_READY = 8;

    private ?int $conditionStart = null;
    private ?int $conditionEnd = null;
    private ?int $thenStart = null;
    private ?int $thenEnd = null;
    private ?int $elseStart = null;
    private ?int $elseEnd = null;
    private ?int $elseKeywordEnd = null;
    private bool $hasInlineElse = false;
    private int $state = self::STATE_EMPTY;

    public function __construct()
    {}

    public function setConditionStart(int $conditionStart): void
    {
        $this->conditionStart = $conditionStart;
        $this->state = self::STATE_CONDITION_STARTED;
    }

    public function setReady(): void
    {
        $this->state = self::STATE_READY;
    }

    public function setConditionEnd(int $conditionEnd): void
    {
        $this->conditionEnd = $conditionEnd;
        $this->state = self::STATE_CONDITION_ENDED;
    }

    public function setThenStart(int $thenStart): void
    {
        $this->thenStart = $thenStart;
        $this->state = self::STATE_THEN_STARTED;
    }

    public function setThenEnd(int $thenEnd): void
    {
        $this->thenEnd = $thenEnd;
        $this->state = self::STATE_THEN_ENDED;
    }

    public function setElseMet(int $elseKeywordEnd): void
    {
        $this->state = self::STATE_ELSE_MET;
        $this->elseKeywordEnd = $elseKeywordEnd;
    }

    public function setElseStart(int $elseStart, bool $isInlineElse = false): void
    {
        $this->elseStart = $elseStart;
        $this->state = self::STATE_ELSE_STARTED;
        $this->hasInlineElse = $isInlineElse;
    }

    public function setElseEnd(int $elseEnd): void
    {
        $this->elseEnd = $elseEnd;
        $this->state = self::STATE_ELSE_ENDED;
    }

    public function setState(int $state): void
    {
        $this->state = $state;
    }

    public function getState(): int
    {
        return $this->state;
    }

    public function isReady(): bool
    {
        return $this->state === self::STATE_READY;
    }

    public function getConditionStart(): ?int
    {
        return $this->conditionStart;
    }

    public function getConditionEnd(): ?int
    {
        return $this->conditionEnd;
    }

    public function getThenStart(): ?int
    {
        return $this->thenStart;
    }

    public function getThenEnd(): ?int
    {
        return $this->thenEnd;
    }

    public function getElseStart(): ?int
    {
        return $this->elseStart;
    }

    public function getElseEnd(): ?int
    {
        return $this->elseEnd;
    }

    public function getElseKeywordEnd(): ?int
    {
        return $this->elseKeywordEnd;
    }

    public function getEnd(): ?int
    {
        return $this->elseEnd ?? $this->thenEnd;
    }

    public function hasInlineElse(): bool
    {
        return $this->hasInlineElse;
    }
}
