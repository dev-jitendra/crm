<?php


namespace Espo\Core\Formula\Functions\LanguageGroup;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

use Espo\Core\Di;

class TranslateType extends BaseFunction implements Di\DefaultLanguageAware
{
    use Di\DefaultLanguageSetter;

    public function process(ArgumentList $args)
    {
        if (count($args) < 1) {
            $this->throwTooFewArguments();
        }

        $args = $this->evaluate($args);

        return $this->defaultLanguage->translate(
            $args[0],
            $args[1] ?? null,
            $args[2] ?? null
        );
    }
}
