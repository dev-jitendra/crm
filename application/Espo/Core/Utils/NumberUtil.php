<?php


namespace Espo\Core\Utils;

class NumberUtil
{
    public function __construct(
        private ?string $decimalMark = '.',
        private ?string $thousandSeparator = ','
    ) {}

    
    public function format(
        $value,
        ?int $decimals = null,
        ?string $decimalMark = null,
        ?string $thousandSeparator = null
    ): string {

        if (is_null($decimalMark)) {
            $decimalMark = $this->decimalMark;
        }

        if (is_null($thousandSeparator)) {
            $thousandSeparator = $this->thousandSeparator;
        }

        if (!is_null($decimals)) {
            return number_format((float) $value, $decimals, $decimalMark, $thousandSeparator);
        }

        $arr = explode('.', strval($value));

        $r = '0';

        if (!empty($arr[0])) {
            $r = number_format(intval($arr[0]), 0, '.', $thousandSeparator);
        }

        if (!empty($arr[1])) {
            $r = $r . $decimalMark . $arr[1];
        }

        return $r;
    }
}
