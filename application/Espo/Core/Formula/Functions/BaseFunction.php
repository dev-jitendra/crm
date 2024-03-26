<?php


namespace Espo\Core\Formula\Functions;

use Espo\ORM\Entity;
use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Evaluatable;
use Espo\Core\Formula\Exceptions\BadArgumentType;
use Espo\Core\Formula\Exceptions\BadArgumentValue;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Exceptions\NotPassedEntity;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Processor;
use Espo\Core\Utils\Log;

use stdClass;


abstract class BaseFunction
{
    protected function getVariables(): stdClass
    {
        return $this->variables ?? (object) [];
    }

    
    protected function getEntity(): Entity
    {
        if (!$this->entity) {
            throw new NotPassedEntity('function: ' . $this->name);
        }

        return $this->entity;
    }

    public function __construct(
        protected string $name,
        protected Processor $processor,
        private ?Entity $entity = null,
        private ?stdClass $variables = null,
        protected ?Log $log = null
    ) {}

    
    public abstract function process(ArgumentList $args);

    
    protected function evaluate(Evaluatable $item)
    {
        return $this->processor->process($item);
    }

    
    protected function throwTooFewArguments(?int $number = null)
    {
        $msg = 'function: ' . $this->name;

        if ($number !== null) {
            $msg .= ', needs: ' . $number;
        }

        throw new TooFewArguments($msg);
    }

    
    protected function throwBadArgumentType(?int $index = null, ?string $type = null)
    {
        $msg = 'function: ' . $this->name;

        if ($index !== null) {
            $msg .= ', index: ' . $index;

            if ($type) {
                $msg .= ', should be: ' . $type;
            }
        }

        throw new BadArgumentType($msg);
    }

    
    protected function throwBadArgumentValue(?int $index = null, ?string $msg = null)
    {
        $string = 'function: ' . $this->name;

        if ($index !== null) {
            $string .= ', index: ' . $index;

            if ($msg) {
                $string .= ', ' . $msg;
            }
        }

        throw new BadArgumentValue($string);
    }

    
    protected function throwError(?string $msg = null)
    {
        $string = 'function: ' . $this->name;

        if ($msg) {
            $string .= ', ' . $msg;
        }

        throw new Error($string);
    }

    
    protected function logBadArgumentType(int $index, string $type): void
    {
        if (!$this->log) {
            return;
        }

        $this->log->warning("Formula function: {$this->name}, argument {$index} should be '{$type}'.");
    }

    
    protected function log(string $msg, string $level = 'notice'): void
    {
        if (!$this->log) {
            return;
        }

        $this->log->log($level, 'Formula function: ' . $this->name . ', ' . $msg);
    }
}
