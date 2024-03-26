<?php


namespace Espo\Core\Formula\Functions\DatetimeGroup;

class AddMinutesType extends AddIntervalType
{
    
    protected $intervalTypeString = 'minutes';

    
    protected $timeOnly = true;
}
