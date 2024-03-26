<?php


namespace Espo\Core\Formula\Functions\DatetimeGroup;

use Espo\Core\Formula\ArgumentList;
use Espo\Core\Formula\Exceptions\Error;
use Espo\Core\Formula\Exceptions\ExecutionException;
use Espo\Core\Formula\Exceptions\TooFewArguments;
use Espo\Core\Formula\Functions\BaseFunction;

use DateTime;

class DiffType extends BaseFunction
{
    
    protected $intervalTypePropertyMap = [
        'years' => 'y',
        'months' => 'm',
        'days' => 'd',
        'hours' => 'h',
        'minutes' => 'i',
        'seconds' => 's',
    ];

    
    public function process(ArgumentList $args)
    {
        $args = $this->evaluate($args);

        if (count($args) < 2) {
            $this->throwTooFewArguments();
        }

        $dateTime1String = $args[0];
        $dateTime2String = $args[1];

        if (!$dateTime1String) {
            return null;
        }

        if (!$dateTime2String) {
            return null;
        }

        if (!is_string($dateTime1String)) {
            $this->throwBadArgumentType(1, 'string');
        }

        if (!is_string($dateTime2String)) {
            $this->throwBadArgumentType(2, 'string');
        }

        $intervalType = 'days';

        if (count($args) > 2) {
            $intervalType = $args[2];
        }

        if (!is_string($intervalType)) {
            $this->throwBadArgumentType(3, 'string');
        }

        if (!array_key_exists($intervalType, $this->intervalTypePropertyMap)) {
            $this->throwBadArgumentValue(3, "not supported interval type '{$intervalType}'");
        }

        $isTime = false;

        if (strlen($dateTime1String) > 10) {
            $isTime = true;
        }

        try {
            $dateTime1 = new DateTime($dateTime1String);
            $dateTime2 = new DateTime($dateTime2String);
        }
        catch (\Exception $e) {
            return null;
        }

        $t1 = $dateTime1->getTimestamp();
        $t2 = $dateTime2->getTimestamp();

        $secondsDiff = $t1 - $t2;

        if ($intervalType === 'seconds') {
            $number = $secondsDiff;
        } else if ($intervalType === 'minutes') {
            $number = floor($secondsDiff / 60);
        } else if ($intervalType === 'hours') {
            $number = floor($secondsDiff / (60 * 60));
        } else if ($intervalType === 'days') {
            $number = floor($secondsDiff / (60 * 60 * 24));
        } else {
            $property = $this->intervalTypePropertyMap[$intervalType];
            $interval = $dateTime2->diff($dateTime1);
            $number = $interval->$property;

            if ($interval->invert) {
                $number *= -1;
            }

            if ($intervalType === 'months') {
                $number += $interval->y * 12;
            }
        }

        return $number;
    }
}
