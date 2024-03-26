<?php


namespace Espo\Core\Formula\Functions;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\BreakLoop;
use Espo\Core\Formula\Exceptions\ContinueLoop;

class WhileType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        if (count($args) < 2) {
            $this->throwTooFewArguments(2);
        }

        while ($this->evaluate($args[0])) {
            try {
                $this->evaluate($args[1]);
            }
            catch (BreakLoop) {
                break;
            }
            catch (ContinueLoop) {
                continue;
            }
        }
    }
}
