<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Complex\Complex;
use Complex\Exception as ComplexException;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM;

class Engineering
{
    
    const EULER = 2.71828182845904523536;

    
    public static function parseComplex($complexNumber)
    {
        $complex = new Complex($complexNumber);

        return [
            'real' => $complex->getReal(),
            'imaginary' => $complex->getImaginary(),
            'suffix' => $complex->getSuffix(),
        ];
    }

    
    private static function nbrConversionFormat($xVal, $places)
    {
        if ($places !== null) {
            if (is_numeric($places)) {
                $places = (int) $places;
            } else {
                return Functions::VALUE();
            }
            if ($places < 0) {
                return Functions::NAN();
            }
            if (strlen($xVal) <= $places) {
                return substr(str_pad($xVal, $places, '0', STR_PAD_LEFT), -10);
            }

            return Functions::NAN();
        }

        return substr($xVal, -10);
    }

    
    public static function BESSELI($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            $ord = floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            if (abs($x) <= 30) {
                $fResult = $fTerm = ($x / 2) ** $ord / MathTrig::FACT($ord);
                $ordK = 1;
                $fSqrX = ($x * $x) / 4;
                do {
                    $fTerm *= $fSqrX;
                    $fTerm /= ($ordK * ($ordK + $ord));
                    $fResult += $fTerm;
                } while ((abs($fTerm) > 1e-12) && (++$ordK < 100));
            } else {
                $f_2_PI = 2 * M_PI;

                $fXAbs = abs($x);
                $fResult = exp($fXAbs) / sqrt($f_2_PI * $fXAbs);
                if (($ord & 1) && ($x < 0)) {
                    $fResult = -$fResult;
                }
            }

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    
    public static function BESSELJ($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            $ord = floor($ord);
            if ($ord < 0) {
                return Functions::NAN();
            }

            $fResult = 0;
            if (abs($x) <= 30) {
                $fResult = $fTerm = ($x / 2) ** $ord / MathTrig::FACT($ord);
                $ordK = 1;
                $fSqrX = ($x * $x) / -4;
                do {
                    $fTerm *= $fSqrX;
                    $fTerm /= ($ordK * ($ordK + $ord));
                    $fResult += $fTerm;
                } while ((abs($fTerm) > 1e-12) && (++$ordK < 100));
            } else {
                $f_PI_DIV_2 = M_PI / 2;
                $f_PI_DIV_4 = M_PI / 4;

                $fXAbs = abs($x);
                $fResult = sqrt(Functions::M_2DIVPI / $fXAbs) * cos($fXAbs - $ord * $f_PI_DIV_2 - $f_PI_DIV_4);
                if (($ord & 1) && ($x < 0)) {
                    $fResult = -$fResult;
                }
            }

            return (is_nan($fResult)) ? Functions::NAN() : $fResult;
        }

        return Functions::VALUE();
    }

    private static function besselK0($fNum)
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);
            $fRet = -log($fNum2) * self::BESSELI($fNum, 0) +
                (-0.57721566 + $y * (0.42278420 + $y * (0.23069756 + $y * (0.3488590e-1 + $y * (0.262698e-2 + $y *
                                    (0.10750e-3 + $y * 0.74e-5))))));
        } else {
            $y = 2 / $fNum;
            $fRet = exp(-$fNum) / sqrt($fNum) *
                (1.25331414 + $y * (-0.7832358e-1 + $y * (0.2189568e-1 + $y * (-0.1062446e-1 + $y *
                                (0.587872e-2 + $y * (-0.251540e-2 + $y * 0.53208e-3))))));
        }

        return $fRet;
    }

    private static function besselK1($fNum)
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = ($fNum2 * $fNum2);
            $fRet = log($fNum2) * self::BESSELI($fNum, 1) +
                (1 + $y * (0.15443144 + $y * (-0.67278579 + $y * (-0.18156897 + $y * (-0.1919402e-1 + $y *
                                    (-0.110404e-2 + $y * (-0.4686e-4))))))) / $fNum;
        } else {
            $y = 2 / $fNum;
            $fRet = exp(-$fNum) / sqrt($fNum) *
                (1.25331414 + $y * (0.23498619 + $y * (-0.3655620e-1 + $y * (0.1504268e-1 + $y * (-0.780353e-2 + $y *
                                    (0.325614e-2 + $y * (-0.68245e-3)))))));
        }

        return $fRet;
    }

    
    public static function BESSELK($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            if (($ord < 0) || ($x == 0.0)) {
                return Functions::NAN();
            }

            switch (floor($ord)) {
                case 0:
                    $fBk = self::besselK0($x);

                    break;
                case 1:
                    $fBk = self::besselK1($x);

                    break;
                default:
                    $fTox = 2 / $x;
                    $fBkm = self::besselK0($x);
                    $fBk = self::besselK1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fBkp = $fBkm + $n * $fTox * $fBk;
                        $fBkm = $fBk;
                        $fBk = $fBkp;
                    }
            }

            return (is_nan($fBk)) ? Functions::NAN() : $fBk;
        }

        return Functions::VALUE();
    }

    private static function besselY0($fNum)
    {
        if ($fNum < 8.0) {
            $y = ($fNum * $fNum);
            $f1 = -2957821389.0 + $y * (7062834065.0 + $y * (-512359803.6 + $y * (10879881.29 + $y * (-86327.92757 + $y * 228.4622733))));
            $f2 = 40076544269.0 + $y * (745249964.8 + $y * (7189466.438 + $y * (47447.26470 + $y * (226.1030244 + $y))));
            $fRet = $f1 / $f2 + 0.636619772 * self::BESSELJ($fNum, 0) * log($fNum);
        } else {
            $z = 8.0 / $fNum;
            $y = ($z * $z);
            $xx = $fNum - 0.785398164;
            $f1 = 1 + $y * (-0.1098628627e-2 + $y * (0.2734510407e-4 + $y * (-0.2073370639e-5 + $y * 0.2093887211e-6)));
            $f2 = -0.1562499995e-1 + $y * (0.1430488765e-3 + $y * (-0.6911147651e-5 + $y * (0.7621095161e-6 + $y * (-0.934945152e-7))));
            $fRet = sqrt(0.636619772 / $fNum) * (sin($xx) * $f1 + $z * cos($xx) * $f2);
        }

        return $fRet;
    }

    private static function besselY1($fNum)
    {
        if ($fNum < 8.0) {
            $y = ($fNum * $fNum);
            $f1 = $fNum * (-0.4900604943e13 + $y * (0.1275274390e13 + $y * (-0.5153438139e11 + $y * (0.7349264551e9 + $y *
                                (-0.4237922726e7 + $y * 0.8511937935e4)))));
            $f2 = 0.2499580570e14 + $y * (0.4244419664e12 + $y * (0.3733650367e10 + $y * (0.2245904002e8 + $y *
                            (0.1020426050e6 + $y * (0.3549632885e3 + $y)))));
            $fRet = $f1 / $f2 + 0.636619772 * (self::BESSELJ($fNum, 1) * log($fNum) - 1 / $fNum);
        } else {
            $fRet = sqrt(0.636619772 / $fNum) * sin($fNum - 2.356194491);
        }

        return $fRet;
    }

    
    public static function BESSELY($x, $ord)
    {
        $x = ($x === null) ? 0.0 : Functions::flattenSingleValue($x);
        $ord = ($ord === null) ? 0.0 : Functions::flattenSingleValue($ord);

        if ((is_numeric($x)) && (is_numeric($ord))) {
            if (($ord < 0) || ($x == 0.0)) {
                return Functions::NAN();
            }

            switch (floor($ord)) {
                case 0:
                    $fBy = self::besselY0($x);

                    break;
                case 1:
                    $fBy = self::besselY1($x);

                    break;
                default:
                    $fTox = 2 / $x;
                    $fBym = self::besselY0($x);
                    $fBy = self::besselY1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fByp = $n * $fTox * $fBy - $fBym;
                        $fBym = $fBy;
                        $fBy = $fByp;
                    }
            }

            return (is_nan($fBy)) ? Functions::NAN() : $fBy;
        }

        return Functions::VALUE();
    }

    
    public static function BINTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            
            $x = substr($x, -9);

            return '-' . (512 - bindec($x));
        }

        return bindec($x);
    }

    
    public static function BINTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        
        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            
            return str_repeat('F', 8) . substr(strtoupper(dechex(bindec(substr($x, -9)))), -2);
        }
        $hexVal = (string) strtoupper(dechex(bindec($x)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    
    public static function BINTOOCT($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            
            return str_repeat('7', 7) . substr(strtoupper(decoct(bindec(substr($x, -9)))), -3);
        }
        $octVal = (string) decoct(bindec($x));

        return self::nbrConversionFormat($octVal, $places);
    }

    
    public static function DECTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }

        $x = (string) floor($x);
        if ($x < -512 || $x > 511) {
            return Functions::NAN();
        }

        $r = decbin($x);
        
        $r = substr($r, -10);
        if (strlen($r) >= 11) {
            return Functions::NAN();
        }

        return self::nbrConversionFormat($r, $places);
    }

    
    public static function DECTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = strtoupper(dechex($x));
        if (strlen($r) == 8) {
            
            $r = 'FF' . $r;
        }

        return self::nbrConversionFormat($r, $places);
    }

    
    public static function DECTOOCT($x, $places = null)
    {
        $xorig = $x;
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = decoct($x);
        if (strlen($r) == 11) {
            
            $r = substr($r, -10);
        }

        return self::nbrConversionFormat($r, $places);
    }

    
    public static function HEXTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::HEXTODEC($x), $places);
    }

    
    public static function HEXTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        if (strlen($x) > 10) {
            return Functions::NAN();
        }

        $binX = '';
        foreach (str_split($x) as $char) {
            $binX .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 40 && $binX[0] == '1') {
            for ($i = 0; $i < 40; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    
    public static function HEXTOOCT($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        $decimal = self::HEXTODEC($x);
        if ($decimal < -536870912 || $decimal > 536870911) {
            return Functions::NAN();
        }

        return self::DECTOOCT($decimal, $places);
    }

    
    public static function OCTTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::OCTTODEC($x), $places);
    }

    
    public static function OCTTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }
        $binX = '';
        foreach (str_split($x) as $char) {
            $binX .= str_pad(decbin((int) $char), 3, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 30 && $binX[0] == '1') {
            for ($i = 0; $i < 30; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    
    public static function OCTTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }
        $hexVal = strtoupper(dechex(self::OCTTODEC($x)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    
    public static function COMPLEX($realNumber = 0.0, $imaginary = 0.0, $suffix = 'i')
    {
        $realNumber = ($realNumber === null) ? 0.0 : Functions::flattenSingleValue($realNumber);
        $imaginary = ($imaginary === null) ? 0.0 : Functions::flattenSingleValue($imaginary);
        $suffix = ($suffix === null) ? 'i' : Functions::flattenSingleValue($suffix);

        if (
            ((is_numeric($realNumber)) && (is_numeric($imaginary))) &&
            (($suffix == 'i') || ($suffix == 'j') || ($suffix == ''))
        ) {
            $complex = new Complex($realNumber, $imaginary, $suffix);

            return (string) $complex;
        }

        return Functions::VALUE();
    }

    
    public static function IMAGINARY($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->getImaginary();
    }

    
    public static function IMREAL($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->getReal();
    }

    
    public static function IMABS($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->abs();
    }

    
    public static function IMARGUMENT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::DIV0();
        }

        return $complex->argument();
    }

    
    public static function IMCONJUGATE($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->conjugate();
    }

    
    public static function IMCOS($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cos();
    }

    
    public static function IMCOSH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cosh();
    }

    
    public static function IMCOT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cot();
    }

    
    public static function IMCSC($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->csc();
    }

    
    public static function IMCSCH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->csch();
    }

    
    public static function IMSIN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sin();
    }

    
    public static function IMSINH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sinh();
    }

    
    public static function IMSEC($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sec();
    }

    
    public static function IMSECH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sech();
    }

    
    public static function IMTAN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->tan();
    }

    
    public static function IMSQRT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $theta = self::IMARGUMENT($complexNumber);
        if ($theta === Functions::DIV0()) {
            return '0';
        }

        return (string) (new Complex($complexNumber))->sqrt();
    }

    
    public static function IMLN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->ln();
    }

    
    public static function IMLOG10($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->log10();
    }

    
    public static function IMLOG2($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->log2();
    }

    
    public static function IMEXP($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->exp();
    }

    
    public static function IMPOWER($complexNumber, $realNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);
        $realNumber = Functions::flattenSingleValue($realNumber);

        if (!is_numeric($realNumber)) {
            return Functions::VALUE();
        }

        return (string) (new Complex($complexNumber))->pow($realNumber);
    }

    
    public static function IMDIV($complexDividend, $complexDivisor)
    {
        $complexDividend = Functions::flattenSingleValue($complexDividend);
        $complexDivisor = Functions::flattenSingleValue($complexDivisor);

        try {
            return (string) (new Complex($complexDividend))->divideby(new Complex($complexDivisor));
        } catch (ComplexException $e) {
            return Functions::NAN();
        }
    }

    
    public static function IMSUB($complexNumber1, $complexNumber2)
    {
        $complexNumber1 = Functions::flattenSingleValue($complexNumber1);
        $complexNumber2 = Functions::flattenSingleValue($complexNumber2);

        try {
            return (string) (new Complex($complexNumber1))->subtract(new Complex($complexNumber2));
        } catch (ComplexException $e) {
            return Functions::NAN();
        }
    }

    
    public static function IMSUM(...$complexNumbers)
    {
        
        $returnValue = new Complex(0.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->add(new Complex($complex));
            }
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return (string) $returnValue;
    }

    
    public static function IMPRODUCT(...$complexNumbers)
    {
        
        $returnValue = new Complex(1.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->multiply(new Complex($complex));
            }
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return (string) $returnValue;
    }

    
    public static function DELTA($a, $b = 0)
    {
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        return (int) ($a == $b);
    }

    
    public static function GESTEP($number, $step = 0)
    {
        $number = Functions::flattenSingleValue($number);
        $step = Functions::flattenSingleValue($step);

        return (int) ($number >= $step);
    }

    
    
    
    private static $twoSqrtPi = 1.128379167095512574;

    public static function erfVal($x)
    {
        if (abs($x) > 2.2) {
            return 1 - self::erfcVal($x);
        }
        $sum = $term = $x;
        $xsqr = ($x * $x);
        $j = 1;
        do {
            $term *= $xsqr / $j;
            $sum -= $term / (2 * $j + 1);
            ++$j;
            $term *= $xsqr / $j;
            $sum += $term / (2 * $j + 1);
            ++$j;
            if ($sum == 0.0) {
                break;
            }
        } while (abs($term / $sum) > Functions::PRECISION);

        return self::$twoSqrtPi * $sum;
    }

    
    private static function validateBitwiseArgument($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_int($value)) {
            return $value;
        } elseif (is_numeric($value)) {
            if ($value == (int) ($value)) {
                $value = (int) ($value);
                if (($value > 2 ** 48 - 1) || ($value < 0)) {
                    throw new Exception(Functions::NAN());
                }

                return $value;
            }

            throw new Exception(Functions::NAN());
        }

        throw new Exception(Functions::VALUE());
    }

    
    public static function BITAND($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 & $number2;
    }

    
    public static function BITOR($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 | $number2;
    }

    
    public static function BITXOR($number1, $number2)
    {
        try {
            $number1 = self::validateBitwiseArgument($number1);
            $number2 = self::validateBitwiseArgument($number2);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $number1 ^ $number2;
    }

    
    public static function BITLSHIFT($number, $shiftAmount)
    {
        try {
            $number = self::validateBitwiseArgument($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $shiftAmount = Functions::flattenSingleValue($shiftAmount);

        $result = $number << $shiftAmount;
        if ($result > 2 ** 48 - 1) {
            return Functions::NAN();
        }

        return $result;
    }

    
    public static function BITRSHIFT($number, $shiftAmount)
    {
        try {
            $number = self::validateBitwiseArgument($number);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $shiftAmount = Functions::flattenSingleValue($shiftAmount);

        return $number >> $shiftAmount;
    }

    
    public static function ERF($lower, $upper = null)
    {
        $lower = Functions::flattenSingleValue($lower);
        $upper = Functions::flattenSingleValue($upper);

        if (is_numeric($lower)) {
            if ($upper === null) {
                return self::erfVal($lower);
            }
            if (is_numeric($upper)) {
                return self::erfVal($upper) - self::erfVal($lower);
            }
        }

        return Functions::VALUE();
    }

    
    public static function ERFPRECISE($limit)
    {
        $limit = Functions::flattenSingleValue($limit);

        return self::ERF($limit);
    }

    
    
    
    private static $oneSqrtPi = 0.564189583547756287;

    private static function erfcVal($x)
    {
        if (abs($x) < 2.2) {
            return 1 - self::erfVal($x);
        }
        if ($x < 0) {
            return 2 - self::ERFC(-$x);
        }
        $a = $n = 1;
        $b = $c = $x;
        $d = ($x * $x) + 0.5;
        $q1 = $q2 = $b / $d;
        $t = 0;
        do {
            $t = $a * $n + $b * $x;
            $a = $b;
            $b = $t;
            $t = $c * $n + $d * $x;
            $c = $d;
            $d = $t;
            $n += 0.5;
            $q1 = $q2;
            $q2 = $b / $d;
        } while ((abs($q1 - $q2) / $q2) > Functions::PRECISION);

        return self::$oneSqrtPi * exp(-$x * $x) * $q2;
    }

    
    public static function ERFC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_numeric($x)) {
            return self::erfcVal($x);
        }

        return Functions::VALUE();
    }

    
    public static function getConversionGroups()
    {
        return Engineering\ConvertUOM::getConversionCategories();
    }

    
    public static function getConversionGroupUnits($category = null)
    {
        return Engineering\ConvertUOM::getConversionCategoryUnits($category);
    }

    
    public static function getConversionGroupUnitDetails($category = null)
    {
        return Engineering\ConvertUOM::getConversionCategoryUnitDetails($category);
    }

    
    public static function getConversionMultipliers()
    {
        return Engineering\ConvertUOM::getConversionMultipliers();
    }

    
    public static function getBinaryConversionMultipliers()
    {
        return Engineering\ConvertUOM::getBinaryConversionMultipliers();
    }

    
    public static function CONVERTUOM($value, $fromUOM, $toUOM)
    {
        return Engineering\ConvertUOM::CONVERT($value, $fromUOM, $toUOM);
    }
}
