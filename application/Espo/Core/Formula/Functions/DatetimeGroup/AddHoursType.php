<?php


namespace Espo\Core\Formula\Functions\DatetimeGroup;

class AddHoursType extends AddIntervalType
{
    protected $intervalTypeString = 'hours';

    protected $timeOnly = true;
}
