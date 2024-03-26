<?php



namespace Carbon\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class CarbonType extends DateTimeType implements CarbonDoctrineType
{
    
    public function getName()
    {
        return 'carbon';
    }

    
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
