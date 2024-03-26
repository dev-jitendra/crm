<?php


namespace Espo\Core\Portal\Acl\AccessChecker;

use Closure;


class ScopeCheckerDataBuilder
{
    private Closure $isOwnChecker;
    private Closure $inAccountChecker;
    private Closure $inContactChecker;

    public function __construct()
    {
        $this->isOwnChecker = function (): bool {
            return false;
        };

        $this->inAccountChecker = function (): bool {
            return false;
        };

        $this->inContactChecker = function (): bool {
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

    public function setInAccount(bool $value): self
    {
        if ($value) {
            $this->inAccountChecker = function (): bool {
                return true;
            };

            return $this;
        }

        $this->inAccountChecker = function (): bool {
            return false;
        };

        return $this;
    }

    public function setInContact(bool $value): self
    {
        if ($value) {
            $this->inContactChecker = function (): bool {
                return true;
            };

            return $this;
        }

        $this->inContactChecker = function (): bool {
            return false;
        };

        return $this;
    }

    
    public function setIsOwnChecker(Closure $checker): self
    {
        $this->isOwnChecker = $checker;

        return $this;
    }

    
    public function setInAccountChecker(Closure $checker): self
    {
        $this->inAccountChecker = $checker;

        return $this;
    }

    
    public function setInContactChecker(Closure $checker): self
    {
        $this->inContactChecker = $checker;

        return $this;
    }

    public function build(): ScopeCheckerData
    {
        return new ScopeCheckerData($this->isOwnChecker, $this->inAccountChecker, $this->inContactChecker);
    }
}
