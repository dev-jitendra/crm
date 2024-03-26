<?php

namespace Doctrine\DBAL\Driver\API\SQLite;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\Deprecations\Deprecation;

use function array_merge;
use function strpos;


final class UserDefinedFunctions
{
    private const DEFAULT_FUNCTIONS = [
        'sqrt' => ['callback' => [SqlitePlatform::class, 'udfSqrt'], 'numArgs' => 1],
        'mod'  => ['callback' => [SqlitePlatform::class, 'udfMod'], 'numArgs' => 2],
        'locate'  => ['callback' => [SqlitePlatform::class, 'udfLocate'], 'numArgs' => -1],
    ];

    
    public static function register(callable $callback, array $additionalFunctions = []): void
    {
        $userDefinedFunctions = array_merge(self::DEFAULT_FUNCTIONS, $additionalFunctions);

        foreach ($userDefinedFunctions as $function => $data) {
            $callback($function, $data['callback'], $data['numArgs']);
        }
    }

    
    public static function mod($a, $b): int
    {
        return $a % $b;
    }

    
    public static function locate($str, $substr, $offset = 0): int
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https:
            'Relying on DBAL\'s emulated LOCATE() function is deprecated. '
                . 'Use INSTR() or %s::getLocateExpression() instead.',
            AbstractPlatform::class,
        );

        
        
        if ($offset > 0) {
            $offset -= 1;
        }

        $pos = strpos($str, $substr, $offset);

        if ($pos !== false) {
            return $pos + 1;
        }

        return 0;
    }
}
