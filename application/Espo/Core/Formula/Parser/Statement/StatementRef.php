<?php


namespace Espo\Core\Formula\Parser\Statement;

class StatementRef
{
    private bool $endedWithSemicolon = false;

    public function __construct(private int $start, private ?int $end = null)
    {}

    public function setEnd(int $end, bool $endedWithSemicolon = false): void
    {
        $this->end = $end;
        $this->endedWithSemicolon = $endedWithSemicolon;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): ?int
    {
        return $this->end;
    }

    public function isReady(): bool
    {
        return $this->end !== null;
    }

    public function isEndedWithSemicolon(): bool
    {
        return $this->endedWithSemicolon;
    }
}
