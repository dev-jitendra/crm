<?php


namespace Espo\Core\Formula\Functions\EnvGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class UserAttributeType extends BaseFunction implements
    Di\UserAware
{
    use Di\UserSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $attribute = $this->evaluate($args[0]);

        if (!is_string($attribute)) {
            $this->throwBadArgumentType(1, 'string');
        }

        return $this->user->get($attribute);
    }
}
