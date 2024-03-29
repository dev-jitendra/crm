<?php

namespace Laminas\Validator;

use function explode;
use function preg_match;
use function preg_match_all;
use function preg_replace;
use function str_contains;
use function str_replace;

final class GpsPoint extends AbstractValidator
{
    public const OUT_OF_BOUNDS         = 'gpsPointOutOfBounds';
    public const CONVERT_ERROR         = 'gpsPointConvertError';
    public const INCOMPLETE_COORDINATE = 'gpsPointIncompleteCoordinate';

    
    protected $messageTemplates = [
        'gpsPointOutOfBounds'          => '%value% is out of Bounds.',
        'gpsPointConvertError'         => '%value% can not converted into a Decimal Degree Value.',
        'gpsPointIncompleteCoordinate' => '%value% did not provided a complete Coordinate',
    ];

    
    public function isValid($value)
    {
        if (! str_contains($value, ',')) {
            $this->error(self::INCOMPLETE_COORDINATE, $value);
            return false;
        }

        [$lat, $long] = explode(',', $value);

        if ($this->isValidCoordinate($lat, 90.0000) && $this->isValidCoordinate($long, 180.000)) {
            return true;
        }

        return false;
    }

    
    private function isValidCoordinate($value, float $maxBoundary): bool
    {
        $this->value = $value;

        $value = $this->removeWhiteSpace($value);
        if ($this->isDMSValue($value)) {
            $value = $this->convertValue($value);
        } else {
            $value = $this->removeDegreeSign($value);
        }

        if ($value === false || $value === null) {
            $this->error(self::CONVERT_ERROR);
            return false;
        }

        $doubleLatitude = (double) $value;

        if ($doubleLatitude <= $maxBoundary && $doubleLatitude >= $maxBoundary * -1) {
            return true;
        }

        $this->error(self::OUT_OF_BOUNDS);
        return false;
    }

    
    private function isDMSValue(string $value): bool
    {
        return preg_match('/([°\'"]+[NESW])/', $value) > 0;
    }

    
    private function convertValue($value)
    {
        $matches = [];
        $result  = preg_match_all('/(\d{1,3})°(\d{1,2})\'(\d{1,2}[\.\d]{0,6})"[NESW]/i', $value, $matches);

        if ($result === false || $result === 0) {
            return false;
        }

        return $matches[1][0] + $matches[2][0] / 60 + ((double) $matches[3][0]) / 3600;
    }

    
    private function removeWhiteSpace($value)
    {
        return preg_replace('/\s/', '', $value);
    }

    
    private function removeDegreeSign($value)
    {
        return str_replace('°', '', $value);
    }
}
