<?php


namespace Espo\Core\Formula;

use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Parser\Ast\Attribute;
use Espo\Core\Formula\Parser\Ast\Node;
use Espo\Core\Formula\Parser\Ast\Value;
use Espo\Core\Formula\Parser\Ast\Variable;


class Argument implements Evaluatable
{
    public function __construct(private mixed $data)
    {}

    
    public function getType(): string
    {
        if ($this->data instanceof Node) {
            return $this->data->getType();
        }

        if ($this->data instanceof Value) {
            return 'value';
        }

        if ($this->data instanceof Variable) {
            return 'variable';
        }

        if ($this->data instanceof Attribute) {
            return 'attribute';
        }

        throw new Error("Can't get type from scalar.");
    }

    
    public function getArgumentList(): ArgumentList
    {
        if ($this->data instanceof Node) {
            return new ArgumentList($this->data->getChildNodes());
        }

        if ($this->data instanceof Value) {
            return new ArgumentList([$this->data->getValue()]);
        }

        if ($this->data instanceof Variable) {
            return new ArgumentList([$this->data->getName()]);
        }

        if ($this->data instanceof Attribute) {
            return new ArgumentList([$this->data->getName()]);
        }

        throw new Error("Can't get argument list from a non-node item.");
    }

    
    public function getData(): mixed
    {
        return $this->data;
    }
}
