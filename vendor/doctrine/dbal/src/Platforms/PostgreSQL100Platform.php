<?php

declare(strict_types=1);

namespace Doctrine\DBAL\Platforms;

use Doctrine\DBAL\Platforms\Keywords\PostgreSQL100Keywords;
use Doctrine\Deprecations\Deprecation;


class PostgreSQL100Platform extends PostgreSQL94Platform
{
    
    protected function getReservedKeywordsClass(): string
    {
        Deprecation::triggerIfCalledFromOutside(
            'doctrine/dbal',
            'https:
            'PostgreSQL100Platform::getReservedKeywordsClass() is deprecated,'
                . ' use PostgreSQL100Platform::createReservedKeywordsList() instead.',
        );

        return PostgreSQL100Keywords::class;
    }
}
