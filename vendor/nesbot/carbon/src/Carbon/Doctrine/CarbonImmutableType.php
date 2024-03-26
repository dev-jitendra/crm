<?php



namespace Carbon\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class CarbonImmutableType extends DateTimeImmutableType implements CarbonDoctrineType
{
    
    public function getName()
    {
        return 'carbon_immutable';
    }

    
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
