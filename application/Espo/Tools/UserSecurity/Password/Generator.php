<?php


namespace Espo\Tools\UserSecurity\Password;

use Espo\Core\Utils\Config;
use Espo\Core\Utils\Util;


class Generator
{
    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    
    public function generate(): string
    {
        $length = $this->config->get('passwordStrengthLength');
        $letterCount = $this->config->get('passwordStrengthLetterCount');
        $numberCount = $this->config->get('passwordStrengthNumberCount');

        $generateLength = $this->config->get('passwordGenerateLength', 10);
        $generateLetterCount = $this->config->get('passwordGenerateLetterCount', 4);
        $generateNumberCount = $this->config->get('passwordGenerateNumberCount', 2);

        $length = is_null($length) ? $generateLength : $length;
        $letterCount = is_null($letterCount) ? $generateLetterCount : $letterCount;
        $numberCount = is_null($letterCount) ? $generateNumberCount : $numberCount;

        if ($length < $generateLength) {
            $length = $generateLength;
        }

        if ($letterCount < $generateLetterCount) {
            $letterCount = $generateLetterCount;
        }

        if ($numberCount < $generateNumberCount) {
            $numberCount = $generateNumberCount;
        }

        return Util::generatePassword($length, $letterCount, $numberCount, true);
    }
}
