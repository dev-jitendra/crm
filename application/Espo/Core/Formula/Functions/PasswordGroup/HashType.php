<?php


namespace Espo\Core\Formula\Functions\PasswordGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
    Processor,
};

use Espo\Core\Utils\PasswordHash;

class HashType extends BaseFunction
{
    protected PasswordHash $passwordHash;

    public function __construct(Processor $processor, PasswordHash $passwordHash)
    {
        $this->processor = $processor;
        $this->passwordHash = $passwordHash;
    }

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $password = $this->evaluate($args[0]);

        if (!is_string($password)) {
            $this->throwBadArgumentType(1, 'string');
        }

        $hash = $this->passwordHash->hash($password);

        return $hash;
    }
}
