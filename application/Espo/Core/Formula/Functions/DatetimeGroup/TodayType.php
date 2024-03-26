<?php


namespace Espo\Core\Formula\Functions\DatetimeGroup;

use Espo\Core\Utils\DateTime;

use Espo\Core\Formula\{
    Functions\BaseFunction,
    ArgumentList,
};

class TodayType extends BaseFunction
{
    public function process(ArgumentList $args)
    {
        return DateTime::getSystemTodayString();
    }
}
