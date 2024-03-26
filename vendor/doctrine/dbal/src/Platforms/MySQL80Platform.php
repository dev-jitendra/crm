<?php

namespace Doctrine\DBAL\Platforms;

use Doctrine\Deprecations\Deprecation;


class MySQL80Platform extends MySQL57Platform
{
    
    protected function getReservedKeywordsClass()
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'MySQL80Platform::getReservedKeywordsClass() is deprecated,'
                . ' use MySQL80Platform::createReservedKeywordsList() instead.',
        );

        return Keywords\MySQL80Keywords::class;
    }
}
