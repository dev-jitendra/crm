<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Platforms\Keywords;

use Doctrine\Deprecations\Deprecation;


class PostgreSQL100Keywords extends PostgreSQL94Keywords
{
    
    public function getName(): string
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQL100Keywords::getName() is deprecated.',
        );

        return 'PostgreSQL100';
    }
}
