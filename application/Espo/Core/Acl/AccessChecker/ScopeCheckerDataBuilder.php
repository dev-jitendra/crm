<?php


namespace Espo\Core\Acl\AccessChecker;

use Closure;


class ScopeCheckerDataBuilder
{
    private Closure $isOwnChecker;
    private Closure $inTeamChecker;

    public function __construct()
    {
        $this->isOwnChecker = function (): bool {
            return false;
        };

        $this->inTeamChecker = function (): bool {
            return false;
        };
    }

    public function setIsOwn(bool $value): self
    {
        if ($value) {
            $this->isOwnChecker = function (): bool {
                return true;
            };

            return $this;
        }

        $this->isOwnChecker = function (): bool {
            return false;
        };

        return $this;
    }

    public function setInTeam(bool $value): self
    {
        if ($value) {
            $this->inTeamChecker = function (): bool {
                return true;
            };

            return $this;
        }

        $this->inTeamChecker = function (): bool {
            return false;
        };

        return $this;
    }

    
    public function setIsOwnChecker(Closure $checker): self
    {
        $this->isOwnChecker = $checker;

        return $this;
    }

    
    public function setInTeamChecker(Closure $checker): self
    {
        $this->inTeamChecker = $checker;

        return $this;
    }

    public function build(): ScopeCheckerData
    {
        return new ScopeCheckerData($this->isOwnChecker, $this->inTeamChecker);
    }
}
