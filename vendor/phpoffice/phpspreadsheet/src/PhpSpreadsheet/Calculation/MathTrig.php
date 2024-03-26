<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Exception;
use Matrix\Exception as MatrixException;
use Matrix\Matrix;

class MathTrig
{
    
    
    
    private static function factors($value)
    {
        $startVal = floor(sqrt($value));

        $factorArray = [];
        for ($i = $startVal; $i > 1; --$i) {
            if (($value % $i) == 0) {
                $factorArray = array_merge($factorArray, self::factors($value / $i));
                $factorArray = array_merge($factorArray, self::factors($i));
                if ($i <= sqrt($value)) {
                    break;
                }
            }
        }
        if (!empty($factorArray)) {
            rsort($factorArray);

            return $factorArray;
        }

        return [(int) $value];
    }

    private static function romanCut($num, $n)
    {
        return ($num - ($num % $n)) / $n;
    }

    
    public static function ARABIC($roman)
    {
        
        $roman = substr(trim(strtoupper((string) Functions::flattenSingleValue($roman))), 0, 255);
        if ($roman === '') {
            return 0;
        }

        
        $negativeNumber = $roman[0] === '-';
        if ($negativeNumber) {
            $roman = substr($roman, 1);
        }

        try {
            $arabic = self::calculateArabic(str_split($roman));
        } catch (Exception $e) {
            return Functions::VALUE(); 
        }

        if ($negativeNumber) {
            $arabic *= -1; 
        }

        return $arabic;
    }

    
    protected static function calculateArabic(array $roman, &$sum = 0, $subtract = 0)
    {
        $lookup = [
            'M' => 1000,
            'D' => 500,
            'C' => 100,
            'L' => 50,
            'X' => 10,
            'V' => 5,
            'I' => 1,
        ];

        $numeral = array_shift($roman);
        if (!isset($lookup[$numeral])) {
            throw new Exception('Invalid character detected');
        }

        $arabic = $lookup[$numeral];
        if (count($roman) > 0 && isset($lookup[$roman[0]]) && $arabic < $lookup[$roman[0]]) {
            $subtract += $arabic;
        } else {
            $sum += ($arabic - $subtract);
            $subtract = 0;
        }

        if (count($roman) > 0) {
            self::calculateArabic($roman, $sum, $subtract);
        }

        return $sum;
    }

    
    public static function ATAN2($xCoordinate = null, $yCoordinate = null)
    {
        $xCoordinate = Functions::flattenSingleValue($xCoordinate);
        $yCoordinate = Functions::flattenSingleValue($yCoordinate);

        $xCoordinate = ($xCoordinate !== null) ? $xCoordinate : 0.0;
        $yCoordinate = ($yCoordinate !== null) ? $yCoordinate : 0.0;

        if (
            ((is_numeric($xCoordinate)) || (is_bool($xCoordinate))) &&
            ((is_numeric($yCoordinate))) || (is_bool($yCoordinate))
        ) {
            $xCoordinate = (float) $xCoordinate;
            $yCoordinate = (float) $yCoordinate;

            if (($xCoordinate == 0) && ($yCoordinate == 0)) {
                return Functions::DIV0();
            }

            return atan2($yCoordinate, $xCoordinate);
        }

        return Functions::VALUE();
    }

    
    public static function BASE($number, $radix, $minLength = null)
    {
        $number = Functions::flattenSingleValue($number);
        $radix = Functions::flattenSingleValue($radix);
        $minLength = Functions::flattenSingleValue($minLength);

        if (is_numeric($number) && is_numeric($radix) && ($minLength === null || is_numeric($minLength))) {
            
            $number = (int) $number;
            $radix = (int) $radix;
            $minLength = (int) $minLength;

            if ($number < 0 || $number >= 2 ** 53 || $radix < 2 || $radix > 36) {
                return Functions::NAN(); 
            }

            $outcome = strtoupper((string) base_convert($number, 10, $radix));
            if ($minLength !== null) {
                $outcome = str_pad($outcome, $minLength, '0', STR_PAD_LEFT); 
            }

            return $outcome;
        }

        return Functions::VALUE();
    }

    
    public static function CEILING($number, $significance = null)
    {
        $number = Functions::flattenSingleValue($number);
        $significance = Functions::flattenSingleValue($significance);

        if (
            ($significance === null) &&
            (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC)
        ) {
            $significance = $number / abs($number);
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if (($number == 0.0) || ($significance == 0.0)) {
                return 0.0;
            } elseif (self::SIGN($number) == self::SIGN($significance)) {
                return ceil($number / $significance) * $significance;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }

    
    public static function COMBIN($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        if ((is_numeric($numObjs)) && (is_numeric($numInSet))) {
            if ($numObjs < $numInSet) {
                return Functions::NAN();
            } elseif ($numInSet < 0) {
                return Functions::NAN();
            }

            return round(self::FACT($numObjs) / self::FACT($numObjs - $numInSet)) / self::FACT($numInSet);
        }

        return Functions::VALUE();
    }

    
    public static function EVEN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if ($number === null) {
            return 0;
        } elseif (is_bool($number)) {
            $number = (int) $number;
        }

        if (is_numeric($number)) {
            $significance = 2 * self::SIGN($number);

            return (int) self::CEILING($number, $significance);
        }

        return Functions::VALUE();
    }

    
    public static function FACT($factVal)
    {
        $factVal = Functions::flattenSingleValue($factVal);

        if (is_numeric($factVal)) {
            if ($factVal < 0) {
                return Functions::NAN();
            }
            $factLoop = floor($factVal);
            if (
                (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) &&
                ($factVal > $factLoop)
            ) {
                return Functions::NAN();
            }

            $factorial = 1;
            while ($factLoop > 1) {
                $factorial *= $factLoop--;
            }

            return $factorial;
        }

        return Functions::VALUE();
    }

    
    public static function FACTDOUBLE($factVal)
    {
        $factLoop = Functions::flattenSingleValue($factVal);

        if (is_numeric($factLoop)) {
            $factLoop = floor($factLoop);
            if ($factVal < 0) {
                return Functions::NAN();
            }
            $factorial = 1;
            while ($factLoop > 1) {
                $factorial *= $factLoop--;
                --$factLoop;
            }

            return $factorial;
        }

        return Functions::VALUE();
    }

    
    public static function FLOOR($number, $significance = null)
    {
        $number = Functions::flattenSingleValue($number);
        $significance = Functions::flattenSingleValue($significance);

        if (
            ($significance === null) &&
            (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC)
        ) {
            $significance = $number / abs($number);
        }

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if ($significance == 0.0) {
                return Functions::DIV0();
            } elseif ($number == 0.0) {
                return 0.0;
            } elseif (self::SIGN($significance) == 1) {
                return floor($number / $significance) * $significance;
            } elseif (self::SIGN($number) == -1 && self::SIGN($significance) == -1) {
                return floor($number / $significance) * $significance;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }

    
    public static function FLOORMATH($number, $significance = null, $mode = 0)
    {
        $number = Functions::flattenSingleValue($number);
        $significance = Functions::flattenSingleValue($significance);
        $mode = Functions::flattenSingleValue($mode);

        if (is_numeric($number) && $significance === null) {
            $significance = $number / abs($number);
        }

        if (is_numeric($number) && is_numeric($significance) && is_numeric($mode)) {
            if ($significance == 0.0) {
                return Functions::DIV0();
            } elseif ($number == 0.0) {
                return 0.0;
            } elseif (self::SIGN($significance) == -1 || (self::SIGN($number) == -1 && !empty($mode))) {
                return ceil($number / $significance) * $significance;
            }

            return floor($number / $significance) * $significance;
        }

        return Functions::VALUE();
    }

    
    public static function FLOORPRECISE($number, $significance = 1)
    {
        $number = Functions::flattenSingleValue($number);
        $significance = Functions::flattenSingleValue($significance);

        if ((is_numeric($number)) && (is_numeric($significance))) {
            if ($significance == 0.0) {
                return Functions::DIV0();
            } elseif ($number == 0.0) {
                return 0.0;
            }

            return floor($number / abs($significance)) * abs($significance);
        }

        return Functions::VALUE();
    }

    private static function evaluateGCD($a, $b)
    {
        return $b ? self::evaluateGCD($b, $a % $b) : $a;
    }

    
    public static function GCD(...$args)
    {
        $args = Functions::flattenArray($args);
        
        foreach (Functions::flattenArray($args) as $value) {
            if (!is_numeric($value)) {
                return Functions::VALUE();
            } elseif ($value < 0) {
                return Functions::NAN();
            }
        }

        $gcd = (int) array_pop($args);
        do {
            $gcd = self::evaluateGCD($gcd, (int) array_pop($args));
        } while (!empty($args));

        return $gcd;
    }

    
    public static function INT($number)
    {
        $number = Functions::flattenSingleValue($number);

        if ($number === null) {
            return 0;
        } elseif (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            return (int) floor($number);
        }

        return Functions::VALUE();
    }

    
    public static function LCM(...$args)
    {
        $returnValue = 1;
        $allPoweredFactors = [];
        
        foreach (Functions::flattenArray($args) as $value) {
            if (!is_numeric($value)) {
                return Functions::VALUE();
            }
            if ($value == 0) {
                return 0;
            } elseif ($value < 0) {
                return Functions::NAN();
            }
            $myFactors = self::factors(floor($value));
            $myCountedFactors = array_count_values($myFactors);
            $myPoweredFactors = [];
            foreach ($myCountedFactors as $myCountedFactor => $myCountedPower) {
                $myPoweredFactors[$myCountedFactor] = $myCountedFactor ** $myCountedPower;
            }
            foreach ($myPoweredFactors as $myPoweredValue => $myPoweredFactor) {
                if (isset($allPoweredFactors[$myPoweredValue])) {
                    if ($allPoweredFactors[$myPoweredValue] < $myPoweredFactor) {
                        $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                    }
                } else {
                    $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                }
            }
        }
        foreach ($allPoweredFactors as $allPoweredFactor) {
            $returnValue *= (int) $allPoweredFactor;
        }

        return $returnValue;
    }

    
    public static function logBase($number = null, $base = 10)
    {
        $number = Functions::flattenSingleValue($number);
        $base = ($base === null) ? 10 : (float) Functions::flattenSingleValue($base);

        if ((!is_numeric($base)) || (!is_numeric($number))) {
            return Functions::VALUE();
        }
        if (($base <= 0) || ($number <= 0)) {
            return Functions::NAN();
        }

        return log($number, $base);
    }

    
    public static function MDETERM($matrixValues)
    {
        $matrixData = [];
        if (!is_array($matrixValues)) {
            $matrixValues = [[$matrixValues]];
        }

        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = [$matrixRow];
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if ((is_string($matrixCell)) || ($matrixCell === null)) {
                    return Functions::VALUE();
                }
                $matrixData[$row][$column] = $matrixCell;
                ++$column;
            }
            if ($column > $maxColumn) {
                $maxColumn = $column;
            }
            ++$row;
        }

        $matrix = new Matrix($matrixData);
        if (!$matrix->isSquare()) {
            return Functions::VALUE();
        }

        try {
            return $matrix->determinant();
        } catch (MatrixException $ex) {
            return Functions::VALUE();
        }
    }

    
    public static function MINVERSE($matrixValues)
    {
        $matrixData = [];
        if (!is_array($matrixValues)) {
            $matrixValues = [[$matrixValues]];
        }

        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = [$matrixRow];
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if ((is_string($matrixCell)) || ($matrixCell === null)) {
                    return Functions::VALUE();
                }
                $matrixData[$row][$column] = $matrixCell;
                ++$column;
            }
            if ($column > $maxColumn) {
                $maxColumn = $column;
            }
            ++$row;
        }

        $matrix = new Matrix($matrixData);
        if (!$matrix->isSquare()) {
            return Functions::VALUE();
        }

        if ($matrix->determinant() == 0.0) {
            return Functions::NAN();
        }

        try {
            return $matrix->inverse()->toArray();
        } catch (MatrixException $ex) {
            return Functions::VALUE();
        }
    }

    
    public static function MMULT($matrixData1, $matrixData2)
    {
        $matrixAData = $matrixBData = [];
        if (!is_array($matrixData1)) {
            $matrixData1 = [[$matrixData1]];
        }
        if (!is_array($matrixData2)) {
            $matrixData2 = [[$matrixData2]];
        }

        try {
            $rowA = 0;
            foreach ($matrixData1 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = [$matrixRow];
                }
                $columnA = 0;
                foreach ($matrixRow as $matrixCell) {
                    if ((!is_numeric($matrixCell)) || ($matrixCell === null)) {
                        return Functions::VALUE();
                    }
                    $matrixAData[$rowA][$columnA] = $matrixCell;
                    ++$columnA;
                }
                ++$rowA;
            }
            $matrixA = new Matrix($matrixAData);
            $rowB = 0;
            foreach ($matrixData2 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = [$matrixRow];
                }
                $columnB = 0;
                foreach ($matrixRow as $matrixCell) {
                    if ((!is_numeric($matrixCell)) || ($matrixCell === null)) {
                        return Functions::VALUE();
                    }
                    $matrixBData[$rowB][$columnB] = $matrixCell;
                    ++$columnB;
                }
                ++$rowB;
            }
            $matrixB = new Matrix($matrixBData);

            if ($columnA != $rowB) {
                return Functions::VALUE();
            }

            return $matrixA->multiply($matrixB)->toArray();
        } catch (MatrixException $ex) {
            return Functions::VALUE();
        }
    }

    
    public static function MOD($a = 1, $b = 1)
    {
        $a = (float) Functions::flattenSingleValue($a);
        $b = (float) Functions::flattenSingleValue($b);

        if ($b == 0.0) {
            return Functions::DIV0();
        } elseif (($a < 0.0) && ($b > 0.0)) {
            return $b - fmod(abs($a), $b);
        } elseif (($a > 0.0) && ($b < 0.0)) {
            return $b + fmod($a, abs($b));
        }

        return fmod($a, $b);
    }

    
    public static function MROUND($number, $multiple)
    {
        $number = Functions::flattenSingleValue($number);
        $multiple = Functions::flattenSingleValue($multiple);

        if ((is_numeric($number)) && (is_numeric($multiple))) {
            if ($multiple == 0) {
                return 0;
            }
            if ((self::SIGN($number)) == (self::SIGN($multiple))) {
                $multiplier = 1 / $multiple;

                return round($number * $multiplier) / $multiplier;
            }

            return Functions::NAN();
        }

        return Functions::VALUE();
    }

    
    public static function MULTINOMIAL(...$args)
    {
        $summer = 0;
        $divisor = 1;
        
        foreach (Functions::flattenArray($args) as $arg) {
            
            if (is_numeric($arg)) {
                if ($arg < 1) {
                    return Functions::NAN();
                }
                $summer += floor($arg);
                $divisor *= self::FACT($arg);
            } else {
                return Functions::VALUE();
            }
        }

        
        if ($summer > 0) {
            $summer = self::FACT($summer);

            return $summer / $divisor;
        }

        return 0;
    }

    
    public static function ODD($number)
    {
        $number = Functions::flattenSingleValue($number);

        if ($number === null) {
            return 1;
        } elseif (is_bool($number)) {
            return 1;
        } elseif (is_numeric($number)) {
            $significance = self::SIGN($number);
            if ($significance == 0) {
                return 1;
            }

            $result = self::CEILING($number, $significance);
            if ($result == self::EVEN($result)) {
                $result += $significance;
            }

            return (int) $result;
        }

        return Functions::VALUE();
    }

    
    public static function POWER($x = 0, $y = 2)
    {
        $x = Functions::flattenSingleValue($x);
        $y = Functions::flattenSingleValue($y);

        
        if ($x == 0.0 && $y == 0.0) {
            return Functions::NAN();
        } elseif ($x == 0.0 && $y < 0.0) {
            return Functions::DIV0();
        }

        
        $result = $x ** $y;

        return (!is_nan($result) && !is_infinite($result)) ? $result : Functions::NAN();
    }

    
    public static function PRODUCT(...$args)
    {
        
        $returnValue = null;

        
        foreach (Functions::flattenArray($args) as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($returnValue === null) {
                    $returnValue = $arg;
                } else {
                    $returnValue *= $arg;
                }
            }
        }

        
        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }

    
    public static function QUOTIENT(...$args)
    {
        
        $returnValue = null;

        
        foreach (Functions::flattenArray($args) as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($returnValue === null) {
                    $returnValue = ($arg == 0) ? 0 : $arg;
                } else {
                    if (($returnValue == 0) || ($arg == 0)) {
                        $returnValue = 0;
                    } else {
                        $returnValue /= $arg;
                    }
                }
            }
        }

        
        return (int) $returnValue;
    }

    
    public static function RAND($min = 0, $max = 0)
    {
        $min = Functions::flattenSingleValue($min);
        $max = Functions::flattenSingleValue($max);

        if ($min == 0 && $max == 0) {
            return (mt_rand(0, 10000000)) / 10000000;
        }

        return mt_rand($min, $max);
    }

    public static function ROMAN($aValue, $style = 0)
    {
        $aValue = Functions::flattenSingleValue($aValue);
        $style = ($style === null) ? 0 : (int) Functions::flattenSingleValue($style);
        if ((!is_numeric($aValue)) || ($aValue < 0) || ($aValue >= 4000)) {
            return Functions::VALUE();
        }
        $aValue = (int) $aValue;
        if ($aValue == 0) {
            return '';
        }

        $mill = ['', 'M', 'MM', 'MMM', 'MMMM', 'MMMMM'];
        $cent = ['', 'C', 'CC', 'CCC', 'CD', 'D', 'DC', 'DCC', 'DCCC', 'CM'];
        $tens = ['', 'X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC'];
        $ones = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX'];

        $roman = '';
        while ($aValue > 5999) {
            $roman .= 'M';
            $aValue -= 1000;
        }
        $m = self::romanCut($aValue, 1000);
        $aValue %= 1000;
        $c = self::romanCut($aValue, 100);
        $aValue %= 100;
        $t = self::romanCut($aValue, 10);
        $aValue %= 10;

        return $roman . $mill[$m] . $cent[$c] . $tens[$t] . $ones[$aValue];
    }

    
    public static function ROUNDUP($number, $digits)
    {
        $number = Functions::flattenSingleValue($number);
        $digits = Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            if ($number == 0.0) {
                return 0.0;
            }

            if ($number < 0.0) {
                return round($number - 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_DOWN);
            }

            return round($number + 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_DOWN);
        }

        return Functions::VALUE();
    }

    
    public static function ROUNDDOWN($number, $digits)
    {
        $number = Functions::flattenSingleValue($number);
        $digits = Functions::flattenSingleValue($digits);

        if ((is_numeric($number)) && (is_numeric($digits))) {
            if ($number == 0.0) {
                return 0.0;
            }

            if ($number < 0.0) {
                return round($number + 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_UP);
            }

            return round($number - 0.5 * 0.1 ** $digits, $digits, PHP_ROUND_HALF_UP);
        }

        return Functions::VALUE();
    }

    
    public static function SERIESSUM(...$args)
    {
        $returnValue = 0;

        
        $aArgs = Functions::flattenArray($args);

        $x = array_shift($aArgs);
        $n = array_shift($aArgs);
        $m = array_shift($aArgs);

        if ((is_numeric($x)) && (is_numeric($n)) && (is_numeric($m))) {
            
            $i = 0;
            foreach ($aArgs as $arg) {
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $returnValue += $arg * $x ** ($n + ($m * $i++));
                } else {
                    return Functions::VALUE();
                }
            }

            return $returnValue;
        }

        return Functions::VALUE();
    }

    
    public static function SIGN($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            if ($number == 0.0) {
                return 0;
            }

            return $number / abs($number);
        }

        return Functions::VALUE();
    }

    
    public static function SQRTPI($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (is_numeric($number)) {
            if ($number < 0) {
                return Functions::NAN();
            }

            return sqrt($number * M_PI);
        }

        return Functions::VALUE();
    }

    protected static function filterHiddenArgs($cellReference, $args)
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                [, $row, $column] = explode('.', $index);

                return $cellReference->getWorksheet()->getRowDimension($row)->getVisible() &&
                    $cellReference->getWorksheet()->getColumnDimension($column)->getVisible();
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    protected static function filterFormulaArgs($cellReference, $args)
    {
        return array_filter(
            $args,
            function ($index) use ($cellReference) {
                [, $row, $column] = explode('.', $index);
                if ($cellReference->getWorksheet()->cellExists($column . $row)) {
                    
                    $isFormula = $cellReference->getWorksheet()->getCell($column . $row)->isFormula();
                    $cellFormula = !preg_match('/^=.*\b(SUBTOTAL|AGGREGATE)\s*\(/i', $cellReference->getWorksheet()->getCell($column . $row)->getValue());

                    return !$isFormula || $cellFormula;
                }

                return true;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    
    public static function SUBTOTAL($functionType, ...$args)
    {
        $cellReference = array_pop($args);
        $aArgs = Functions::flattenArrayIndexed($args);
        $subtotal = Functions::flattenSingleValue($functionType);

        
        if ((is_numeric($subtotal)) && (!is_string($subtotal))) {
            if ($subtotal > 100) {
                $aArgs = self::filterHiddenArgs($cellReference, $aArgs);
                $subtotal -= 100;
            }

            $aArgs = self::filterFormulaArgs($cellReference, $aArgs);
            switch ($subtotal) {
                case 1:
                    return Statistical::AVERAGE($aArgs);
                case 2:
                    return Statistical::COUNT($aArgs);
                case 3:
                    return Statistical::COUNTA($aArgs);
                case 4:
                    return Statistical::MAX($aArgs);
                case 5:
                    return Statistical::MIN($aArgs);
                case 6:
                    return self::PRODUCT($aArgs);
                case 7:
                    return Statistical::STDEV($aArgs);
                case 8:
                    return Statistical::STDEVP($aArgs);
                case 9:
                    return self::SUM($aArgs);
                case 10:
                    return Statistical::VARFunc($aArgs);
                case 11:
                    return Statistical::VARP($aArgs);
            }
        }

        return Functions::VALUE();
    }

    
    public static function SUM(...$args)
    {
        $returnValue = 0;

        
        foreach (Functions::flattenArray($args) as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += $arg;
            } elseif (Functions::isError($arg)) {
                return $arg;
            }
        }

        return $returnValue;
    }

    
    public static function SUMIF($aArgs, $condition, $sumArgs = [])
    {
        $returnValue = 0;

        $aArgs = Functions::flattenArray($aArgs);
        $sumArgs = Functions::flattenArray($sumArgs);
        if (empty($sumArgs)) {
            $sumArgs = $aArgs;
        }
        $condition = Functions::ifCondition($condition);
        
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = str_replace('"', '""', $arg);
                $arg = Calculation::wrapResult(strtoupper($arg));
            }

            $testCondition = '=' . $arg . $condition;
            $sumValue = array_key_exists($key, $sumArgs) ? $sumArgs[$key] : 0;

            if (
                is_numeric($sumValue) &&
                Calculation::getInstance()->_calculateFormulaValue($testCondition)
            ) {
                
                $returnValue += $sumValue;
            }
        }

        return $returnValue;
    }

    
    public static function SUMIFS(...$args)
    {
        $arrayList = $args;

        
        $returnValue = 0;

        $sumArgs = Functions::flattenArray(array_shift($arrayList));
        $aArgsArray = [];
        $conditions = [];

        while (count($arrayList) > 0) {
            $aArgsArray[] = Functions::flattenArray(array_shift($arrayList));
            $conditions[] = Functions::ifCondition(array_shift($arrayList));
        }

        
        foreach ($sumArgs as $index => $value) {
            $valid = true;

            foreach ($conditions as $cidx => $condition) {
                $arg = $aArgsArray[$cidx][$index];

                
                if (!is_numeric($arg)) {
                    $arg = Calculation::wrapResult(strtoupper($arg));
                }
                $testCondition = '=' . $arg . $condition;
                if (!Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                    
                    $valid = false;

                    break; 
                }
            }

            if ($valid) {
                $returnValue += $value;
            }
        }

        
        return $returnValue;
    }

    
    public static function SUMPRODUCT(...$args)
    {
        $arrayList = $args;

        $wrkArray = Functions::flattenArray(array_shift($arrayList));
        $wrkCellCount = count($wrkArray);

        for ($i = 0; $i < $wrkCellCount; ++$i) {
            if ((!is_numeric($wrkArray[$i])) || (is_string($wrkArray[$i]))) {
                $wrkArray[$i] = 0;
            }
        }

        foreach ($arrayList as $matrixData) {
            $array2 = Functions::flattenArray($matrixData);
            $count = count($array2);
            if ($wrkCellCount != $count) {
                return Functions::VALUE();
            }

            foreach ($array2 as $i => $val) {
                if ((!is_numeric($val)) || (is_string($val))) {
                    $val = 0;
                }
                $wrkArray[$i] *= $val;
            }
        }

        return array_sum($wrkArray);
    }

    
    public static function SUMSQ(...$args)
    {
        $returnValue = 0;

        
        foreach (Functions::flattenArray($args) as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $returnValue += ($arg * $arg);
            }
        }

        return $returnValue;
    }

    
    public static function SUMX2MY2($matrixData1, $matrixData2)
    {
        $array1 = Functions::flattenArray($matrixData1);
        $array2 = Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (
                ((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))
            ) {
                $result += ($array1[$i] * $array1[$i]) - ($array2[$i] * $array2[$i]);
            }
        }

        return $result;
    }

    
    public static function SUMX2PY2($matrixData1, $matrixData2)
    {
        $array1 = Functions::flattenArray($matrixData1);
        $array2 = Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (
                ((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))
            ) {
                $result += ($array1[$i] * $array1[$i]) + ($array2[$i] * $array2[$i]);
            }
        }

        return $result;
    }

    
    public static function SUMXMY2($matrixData1, $matrixData2)
    {
        $array1 = Functions::flattenArray($matrixData1);
        $array2 = Functions::flattenArray($matrixData2);
        $count = min(count($array1), count($array2));

        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (
                ((is_numeric($array1[$i])) && (!is_string($array1[$i]))) &&
                ((is_numeric($array2[$i])) && (!is_string($array2[$i])))
            ) {
                $result += ($array1[$i] - $array2[$i]) * ($array1[$i] - $array2[$i]);
            }
        }

        return $result;
    }

    
    public static function TRUNC($value = 0, $digits = 0)
    {
        $value = Functions::flattenSingleValue($value);
        $digits = Functions::flattenSingleValue($digits);

        
        if ((!is_numeric($value)) || (!is_numeric($digits))) {
            return Functions::VALUE();
        }
        $digits = floor($digits);

        
        $adjust = 10 ** $digits;

        if (($digits > 0) && (rtrim((int) ((abs($value) - abs((int) $value)) * $adjust), '0') < $adjust / 10)) {
            return $value;
        }

        return ((int) ($value * $adjust)) / $adjust;
    }

    
    public static function SEC($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = cos($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    
    public static function SECH($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = cosh($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    
    public static function CSC($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = sin($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    
    public static function CSCH($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = sinh($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    
    public static function COT($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = tan($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    
    public static function COTH($angle)
    {
        $angle = Functions::flattenSingleValue($angle);

        if (!is_numeric($angle)) {
            return Functions::VALUE();
        }

        $result = tanh($angle);

        return ($result == 0.0) ? Functions::DIV0() : 1 / $result;
    }

    
    public static function ACOT($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        return (M_PI / 2) - atan($number);
    }

    
    public static function ACOTH($number)
    {
        $number = Functions::flattenSingleValue($number);

        if (!is_numeric($number)) {
            return Functions::VALUE();
        }

        $result = log(($number + 1) / ($number - 1)) / 2;

        return is_nan($result) ? Functions::NAN() : $result;
    }
}
