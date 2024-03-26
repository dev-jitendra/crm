<?php


namespace Espo\Core\Formula\Functions\PasswordGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Utils\Util;

use Espo\Core\Di;

class GenerateType extends BaseFunction implements
    Di\ConfigAware
{
    use Di\ConfigSetter;

    public function process(ArgumentList $args)
    {
        $config = $this->config;

        $length = $config->get('passwordGenerateLength', 10);
        $letterCount = $config->get('passwordGenerateLetterCount', 4);
        $numberCount = $config->get('passwordGenerateNumberCount', 2);

        $password = Util::generatePassword($length, $letterCount, $numberCount, true);

        return $password;
    }
}
