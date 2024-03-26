<?php



namespace Carbon\Traits;

use Carbon\Carbon;
use Carbon\CarbonImmutable;


trait Mutability
{
    use Cast;

    
    public static function isMutable()
    {
        return false;
    }

    
    public static function isImmutable()
    {
        return !static::isMutable();
    }

    
    public function toMutable()
    {
        
        $date = $this->cast(Carbon::class);

        return $date;
    }

    
    public function toImmutable()
    {
        
        $date = $this->cast(CarbonImmutable::class);

        return $date;
    }
}
