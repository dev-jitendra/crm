<?php


namespace Espo\Core\Acl\AccessChecker;

use Closure;


class ScopeCheckerData
{
    public function __construct(
        private Closure $isOwnChecker,
        private Closure $inTeamChecker
    ) {}

    public function isOwn(): bool
    {
        return ($this->isOwnChecker)();
    }

    public function inTeam(): bool
    {
        return ($this->inTeamChecker)();
    }

    public static function createBuilder(): ScopeCheckerDataBuilder
    {
        return new ScopeCheckerDataBuilder();
    }
}
