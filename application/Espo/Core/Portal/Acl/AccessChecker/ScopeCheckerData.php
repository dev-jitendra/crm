<?php


namespace Espo\Core\Portal\Acl\AccessChecker;

use Closure;


class ScopeCheckerData
{
    public function __construct(
        private Closure $isOwnChecker,
        private Closure $inAccountChecker,
        private Closure $inContactChecker
    ) {}

    public function isOwn(): bool
    {
        return ($this->isOwnChecker)();
    }

    public function inAccount(): bool
    {
        return ($this->inAccountChecker)();
    }

    public function inContact(): bool
    {
        return ($this->inContactChecker)();
    }

    public static function createBuilder(): ScopeCheckerDataBuilder
    {
        return new ScopeCheckerDataBuilder();
    }
}
