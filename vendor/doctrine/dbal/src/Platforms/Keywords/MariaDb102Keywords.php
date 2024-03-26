<?php

namespace Doctrine\DBAL\Platforms\Keywords;

use Doctrine\Deprecations\Deprecation;


final class MariaDb102Keywords extends MariaDBKeywords
{
    
    public function getName(): string
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'MariaDb102Keywords::getName() is deprecated.',
        );

        return 'MariaDb102';
    }
}
