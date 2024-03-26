<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Driver\OCI8;


final class ExecutionMode
{
    private bool $isAutoCommitEnabled = true;

    public function enableAutoCommit(): void
    {
        $this->isAutoCommitEnabled = true;
    }

    public function disableAutoCommit(): void
    {
        $this->isAutoCommitEnabled = false;
    }

    public function isAutoCommitEnabled(): bool
    {
        return $this->isAutoCommitEnabled;
    }
}
