<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;

class Statistical
{
    const LOG_GAMMA_X_MAX_VALUE = 2.55e305;
    const XMININ = 2.23e-308;
    const EPS = 2.22e-16;
    const MAX_VALUE = 1.2e308;
    const MAX_ITERATIONS = 256;
    const SQRT2PI = 2.5066282746310005024157652848110452530069867406099;

    private static function checkTrendArrays(&$array1, &$array2)
    {
        if (!is_array($array1)) {
            $array1 = [$array1];
        }
        if (!is_array($array2)) {
            $array2 = [$array2];
        }

        $array1 = Functions::flattenArray($array1);
        $array2 = Functions::flattenArray($array2);
        foreach ($array1 as $key => $value) {
            if ((is_bool($value)) || (is_string($value)) || ($value === null)) {
                unset($array1[$key], $array2[$key]);
            }
        }
        foreach ($array2 as $key => $value) {
            if ((is_bool($value)) || (is_string($value)) || ($value === null)) {
                unset($array1[$key], $array2[$key]);
            }
        }
        $array1 = array_merge($array1);
        $array2 = array_merge($array2);

        return true;
    }

    
    private static function incompleteBeta($x, $p, $q)
    {
        if ($x <= 0.0) {
            return 0.0;
        } elseif ($x >= 1.0) {
            return 1.0;
        } elseif (($p <= 0.0) || ($q <= 0.0) || (($p + $q) > self::LOG_GAMMA_X_MAX_VALUE)) {
            return 0.0;
        }
        $beta_gam = exp((0 - self::logBeta($p, $q)) + $p * log($x) + $q * log(1.0 - $x));
        if ($x < ($p + 1.0) / ($p + $q + 2.0)) {
            return $beta_gam * self::betaFraction($x, $p, $q) / $p;
        }

        return 1.0 - ($beta_gam * self::betaFraction(1 - $x, $q, $p) / $q);
    }

    
    private static $logBetaCacheP = 0.0;

    private static $logBetaCacheQ = 0.0;

    private static $logBetaCacheResult = 0.0;

    
    private static function logBeta($p, $q)
    {
        if ($p != self::$logBetaCacheP || $q != self::$logBetaCacheQ) {
            self::$logBetaCacheP = $p;
            self::$logBetaCacheQ = $q;
            if (($p <= 0.0) || ($q <= 0.0) || (($p + $q) > self::LOG_GAMMA_X_MAX_VALUE)) {
                self::$logBetaCacheResult = 0.0;
            } else {
                self::$logBetaCacheResult = self::logGamma($p) + self::logGamma($q) - self::logGamma($p + $q);
            }
        }

        return self::$logBetaCacheResult;
    }

    
    private static function betaFraction($x, $p, $q)
    {
        $c = 1.0;
        $sum_pq = $p + $q;
        $p_plus = $p + 1.0;
        $p_minus = $p - 1.0;
        $h = 1.0 - $sum_pq * $x / $p_plus;
        if (abs($h) < self::XMININ) {
            $h = self::XMININ;
        }
        $h = 1.0 / $h;
        $frac = $h;
        $m = 1;
        $delta = 0.0;
        while ($m <= self::MAX_ITERATIONS && abs($delta - 1.0) > Functions::PRECISION) {
            $m2 = 2 * $m;
            
            $d = $m * ($q - $m) * $x / (($p_minus + $m2) * ($p + $m2));
            $h = 1.0 + $d * $h;
            if (abs($h) < self::XMININ) {
                $h = self::XMININ;
            }
            $h = 1.0 / $h;
            $c = 1.0 + $d / $c;
            if (abs($c) < self::XMININ) {
                $c = self::XMININ;
            }
            $frac *= $h * $c;
            
            $d = -($p + $m) * ($sum_pq + $m) * $x / (($p + $m2) * ($p_plus + $m2));
            $h = 1.0 + $d * $h;
            if (abs($h) < self::XMININ) {
                $h = self::XMININ;
            }
            $h = 1.0 / $h;
            $c = 1.0 + $d / $c;
            if (abs($c) < self::XMININ) {
                $c = self::XMININ;
            }
            $delta = $h * $c;
            $frac *= $delta;
            ++$m;
        }

        return $frac;
    }

    

    
    private static $logGammaCacheResult = 0.0;

    private static $logGammaCacheX = 0.0;

    private static function logGamma($x)
    {
        
        static $lg_d1 = -0.5772156649015328605195174;
        static $lg_d2 = 0.4227843350984671393993777;
        static $lg_d4 = 1.791759469228055000094023;

        static $lg_p1 = [
            4.945235359296727046734888,
            201.8112620856775083915565,
            2290.838373831346393026739,
            11319.67205903380828685045,
            28557.24635671635335736389,
            38484.96228443793359990269,
            26377.48787624195437963534,
            7225.813979700288197698961,
        ];
        static $lg_p2 = [
            4.974607845568932035012064,
            542.4138599891070494101986,
            15506.93864978364947665077,
            184793.2904445632425417223,
            1088204.76946882876749847,
            3338152.967987029735917223,
            5106661.678927352456275255,
            3074109.054850539556250927,
        ];
        static $lg_p4 = [
            14745.02166059939948905062,
            2426813.369486704502836312,
            121475557.4045093227939592,
            2663432449.630976949898078,
            29403789566.34553899906876,
            170266573776.5398868392998,
            492612579337.743088758812,
            560625185622.3951465078242,
        ];
        static $lg_q1 = [
            67.48212550303777196073036,
            1113.332393857199323513008,
            7738.757056935398733233834,
            27639.87074403340708898585,
            54993.10206226157329794414,
            61611.22180066002127833352,
            36351.27591501940507276287,
            8785.536302431013170870835,
        ];
        static $lg_q2 = [
            183.0328399370592604055942,
            7765.049321445005871323047,
            133190.3827966074194402448,
            1136705.821321969608938755,
            5267964.117437946917577538,
            13467014.54311101692290052,
            17827365.30353274213975932,
            9533095.591844353613395747,
        ];
        static $lg_q4 = [
            2690.530175870899333379843,
            639388.5654300092398984238,
            41355999.30241388052042842,
            1120872109.61614794137657,
            14886137286.78813811542398,
            101680358627.2438228077304,
            341747634550.7377132798597,
            446315818741.9713286462081,
        ];
        static $lg_c = [
            -0.001910444077728,
            8.4171387781295e-4,
            -5.952379913043012e-4,
            7.93650793500350248e-4,
            -0.002777777777777681622553,
            0.08333333333333333331554247,
            0.0057083835261,
        ];

        
        static $lg_frtbig = 2.25e76;
        static $pnt68 = 0.6796875;

        if ($x == self::$logGammaCacheX) {
            return self::$logGammaCacheResult;
        }
        $y = $x;
        if ($y > 0.0 && $y <= self::LOG_GAMMA_X_MAX_VALUE) {
            if ($y <= self::EPS) {
                $res = -log($y);
            } elseif ($y <= 1.5) {
                
                
                
                if ($y < $pnt68) {
                    $corr = -log($y);
                    $xm1 = $y;
                } else {
                    $corr = 0.0;
                    $xm1 = $y - 1.0;
                }
                if ($y <= 0.5 || $y >= $pnt68) {
                    $xden = 1.0;
                    $xnum = 0.0;
                    for ($i = 0; $i < 8; ++$i) {
                        $xnum = $xnum * $xm1 + $lg_p1[$i];
                        $xden = $xden * $xm1 + $lg_q1[$i];
                    }
                    $res = $corr + $xm1 * ($lg_d1 + $xm1 * ($xnum / $xden));
                } else {
                    $xm2 = $y - 1.0;
                    $xden = 1.0;
                    $xnum = 0.0;
                    for ($i = 0; $i < 8; ++$i) {
                        $xnum = $xnum * $xm2 + $lg_p2[$i];
                        $xden = $xden * $xm2 + $lg_q2[$i];
                    }
                    $res = $corr + $xm2 * ($lg_d2 + $xm2 * ($xnum / $xden));
                }
            } elseif ($y <= 4.0) {
                
                
                
                $xm2 = $y - 2.0;
                $xden = 1.0;
                $xnum = 0.0;
                for ($i = 0; $i < 8; ++$i) {
                    $xnum = $xnum * $xm2 + $lg_p2[$i];
                    $xden = $xden * $xm2 + $lg_q2[$i];
                }
                $res = $xm2 * ($lg_d2 + $xm2 * ($xnum / $xden));
            } elseif ($y <= 12.0) {
                
                
                
                $xm4 = $y - 4.0;
                $xden = -1.0;
                $xnum = 0.0;
                for ($i = 0; $i < 8; ++$i) {
                    $xnum = $xnum * $xm4 + $lg_p4[$i];
                    $xden = $xden * $xm4 + $lg_q4[$i];
                }
                $res = $lg_d4 + $xm4 * ($xnum / $xden);
            } else {
                
                
                
                $res = 0.0;
                if ($y <= $lg_frtbig) {
                    $res = $lg_c[6];
                    $ysq = $y * $y;
                    for ($i = 0; $i < 6; ++$i) {
                        $res = $res / $ysq + $lg_c[$i];
                    }
                    $res /= $y;
                    $corr = log($y);
                    $res = $res + log(self::SQRT2PI) - 0.5 * $corr;
                    $res += $y * ($corr - 1.0);
                }
            }
        } else {
            
            
            
            $res = self::MAX_VALUE;
        }
        
        
        
        self::$logGammaCacheX = $x;
        self::$logGammaCacheResult = $res;

        return $res;
    }

    
    
    
    private static function incompleteGamma($a, $x)
    {
        static $max = 32;
        $summer = 0;
        for ($n = 0; $n <= $max; ++$n) {
            $divisor = $a;
            for ($i = 1; $i <= $n; ++$i) {
                $divisor *= ($a + $i);
            }
            $summer += ($x ** $n / $divisor);
        }

        return $x ** $a * exp(0 - $x) * $summer;
    }

    
    
    
    private static function gamma($data)
    {
        if ($data == 0.0) {
            return 0;
        }

        static $p0 = 1.000000000190015;
        static $p = [
            1 => 76.18009172947146,
            2 => -86.50532032941677,
            3 => 24.01409824083091,
            4 => -1.231739572450155,
            5 => 1.208650973866179e-3,
            6 => -5.395239384953e-6,
        ];

        $y = $x = $data;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);

        $summer = $p0;
        for ($j = 1; $j <= 6; ++$j) {
            $summer += ($p[$j] / ++$y);
        }

        return exp(0 - $tmp + log(self::SQRT2PI * $summer / $x));
    }

    
    private static function inverseNcdf($p)
    {
        
        
        
        
        

        
        
        

        

        
        static $a = [
            1 => -3.969683028665376e+01,
            2 => 2.209460984245205e+02,
            3 => -2.759285104469687e+02,
            4 => 1.383577518672690e+02,
            5 => -3.066479806614716e+01,
            6 => 2.506628277459239e+00,
        ];

        static $b = [
            1 => -5.447609879822406e+01,
            2 => 1.615858368580409e+02,
            3 => -1.556989798598866e+02,
            4 => 6.680131188771972e+01,
            5 => -1.328068155288572e+01,
        ];

        static $c = [
            1 => -7.784894002430293e-03,
            2 => -3.223964580411365e-01,
            3 => -2.400758277161838e+00,
            4 => -2.549732539343734e+00,
            5 => 4.374664141464968e+00,
            6 => 2.938163982698783e+00,
        ];

        static $d = [
            1 => 7.784695709041462e-03,
            2 => 3.224671290700398e-01,
            3 => 2.445134137142996e+00,
            4 => 3.754408661907416e+00,
        ];

        
        $p_low = 0.02425; 
        $p_high = 1 - $p_low; 

        if (0 < $p && $p < $p_low) {
            
            $q = sqrt(-2 * log($p));

            return ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                    (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        } elseif ($p_low <= $p && $p <= $p_high) {
            
            $q = $p - 0.5;
            $r = $q * $q;

            return ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $r + $a[6]) * $q /
                   ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + $b[5]) * $r + 1);
        } elseif ($p_high < $p && $p < 1) {
            
            $q = sqrt(-2 * log(1 - $p));

            return -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) /
                     (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        }
        
        return Functions::NULL();
    }

    
    private static function testAcceptedBoolean($arg, $k)
    {
        if (
            (is_bool($arg)) &&
            ((!Functions::isCellValue($k) && (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_EXCEL)) ||
                (Functions::getCompatibilityMode() === Functions::COMPATIBILITY_OPENOFFICE))
        ) {
            $arg = (int) $arg;
        }

        return $arg;
    }

    
    private static function isAcceptedCountable($arg, $k)
    {
        if (
            ((is_numeric($arg)) && (!is_string($arg))) ||
                ((is_numeric($arg)) && (!Functions::isCellValue($k)) &&
                    (Functions::getCompatibilityMode() !== Functions::COMPATIBILITY_GNUMERIC))
        ) {
            return true;
        }

        return false;
    }

    
    public static function AVEDEV(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        
        $returnValue = 0;

        $aMean = self::AVERAGE(...$args);
        if ($aMean === Functions::DIV0()) {
            return Functions::NAN();
        } elseif ($aMean === Functions::VALUE()) {
            return Functions::VALUE();
        }

        $aCount = 0;
        foreach ($aArgs as $k => $arg) {
            $arg = self::testAcceptedBoolean($arg, $k);
            
            
            
            if ((is_string($arg)) && (!is_numeric($arg)) && (!Functions::isCellValue($k))) {
                return Functions::VALUE();
            }
            if (self::isAcceptedCountable($arg, $k)) {
                $returnValue += abs($arg - $aMean);
                ++$aCount;
            }
        }

        
        if ($aCount === 0) {
            return Functions::DIV0();
        }

        return $returnValue / $aCount;
    }

    
    public static function AVERAGE(...$args)
    {
        $returnValue = $aCount = 0;

        
        foreach (Functions::flattenArrayIndexed($args) as $k => $arg) {
            $arg = self::testAcceptedBoolean($arg, $k);
            
            
            
            if ((is_string($arg)) && (!is_numeric($arg)) && (!Functions::isCellValue($k))) {
                return Functions::VALUE();
            }
            if (self::isAcceptedCountable($arg, $k)) {
                $returnValue += $arg;
                ++$aCount;
            }
        }

        
        if ($aCount > 0) {
            return $returnValue / $aCount;
        }

        return Functions::DIV0();
    }

    
    public static function AVERAGEA(...$args)
    {
        $returnValue = null;

        $aCount = 0;
        
        foreach (Functions::flattenArrayIndexed($args) as $k => $arg) {
            if (
                (is_bool($arg)) &&
                (!Functions::isMatrixValue($k))
            ) {
            } else {
                if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) && ($arg != '')))) {
                    if (is_bool($arg)) {
                        $arg = (int) $arg;
                    } elseif (is_string($arg)) {
                        $arg = 0;
                    }
                    $returnValue += $arg;
                    ++$aCount;
                }
            }
        }

        if ($aCount > 0) {
            return $returnValue / $aCount;
        }

        return Functions::DIV0();
    }

    
    public static function AVERAGEIF($aArgs, $condition, $averageArgs = [])
    {
        $returnValue = 0;

        $aArgs = Functions::flattenArray($aArgs);
        $averageArgs = Functions::flattenArray($averageArgs);
        if (empty($averageArgs)) {
            $averageArgs = $aArgs;
        }
        $condition = Functions::ifCondition($condition);
        $conditionIsNumeric = strpos($condition, '"') === false;

        
        $aCount = 0;
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                if ($conditionIsNumeric) {
                    continue;
                }
                $arg = Calculation::wrapResult(strtoupper($arg));
            } elseif (!$conditionIsNumeric) {
                continue;
            }
            $testCondition = '=' . $arg . $condition;
            if (Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                $returnValue += $averageArgs[$key];
                ++$aCount;
            }
        }

        if ($aCount > 0) {
            return $returnValue / $aCount;
        }

        return Functions::DIV0();
    }

    
    public static function BETADIST($value, $alpha, $beta, $rMin = 0, $rMax = 1)
    {
        $value = Functions::flattenSingleValue($value);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);
        $rMin = Functions::flattenSingleValue($rMin);
        $rMax = Functions::flattenSingleValue($rMax);

        if ((is_numeric($value)) && (is_numeric($alpha)) && (is_numeric($beta)) && (is_numeric($rMin)) && (is_numeric($rMax))) {
            if (($value < $rMin) || ($value > $rMax) || ($alpha <= 0) || ($beta <= 0) || ($rMin == $rMax)) {
                return Functions::NAN();
            }
            if ($rMin > $rMax) {
                $tmp = $rMin;
                $rMin = $rMax;
                $rMax = $tmp;
            }
            $value -= $rMin;
            $value /= ($rMax - $rMin);

            return self::incompleteBeta($value, $alpha, $beta);
        }

        return Functions::VALUE();
    }

    
    public static function BETAINV($probability, $alpha, $beta, $rMin = 0, $rMax = 1)
    {
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);
        $rMin = Functions::flattenSingleValue($rMin);
        $rMax = Functions::flattenSingleValue($rMax);

        if ((is_numeric($probability)) && (is_numeric($alpha)) && (is_numeric($beta)) && (is_numeric($rMin)) && (is_numeric($rMax))) {
            if (($alpha <= 0) || ($beta <= 0) || ($rMin == $rMax) || ($probability <= 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if ($rMin > $rMax) {
                $tmp = $rMin;
                $rMin = $rMax;
                $rMax = $tmp;
            }
            $a = 0;
            $b = 2;

            $i = 0;
            while ((($b - $a) > Functions::PRECISION) && ($i++ < self::MAX_ITERATIONS)) {
                $guess = ($a + $b) / 2;
                $result = self::BETADIST($guess, $alpha, $beta);
                if (($result == $probability) || ($result == 0)) {
                    $b = $a;
                } elseif ($result > $probability) {
                    $b = $guess;
                } else {
                    $a = $guess;
                }
            }
            if ($i == self::MAX_ITERATIONS) {
                return Functions::NA();
            }

            return round($rMin + $guess * ($rMax - $rMin), 12);
        }

        return Functions::VALUE();
    }

    
    public static function BINOMDIST($value, $trials, $probability, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $trials = Functions::flattenSingleValue($trials);
        $probability = Functions::flattenSingleValue($probability);

        if ((is_numeric($value)) && (is_numeric($trials)) && (is_numeric($probability))) {
            $value = floor($value);
            $trials = floor($trials);
            if (($value < 0) || ($value > $trials)) {
                return Functions::NAN();
            }
            if (($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    $summer = 0;
                    for ($i = 0; $i <= $value; ++$i) {
                        $summer += MathTrig::COMBIN($trials, $i) * $probability ** $i * (1 - $probability) ** ($trials - $i);
                    }

                    return $summer;
                }

                return MathTrig::COMBIN($trials, $value) * $probability ** $value * (1 - $probability) ** ($trials - $value);
            }
        }

        return Functions::VALUE();
    }

    
    public static function CHIDIST($value, $degrees)
    {
        $value = Functions::flattenSingleValue($value);
        $degrees = Functions::flattenSingleValue($degrees);

        if ((is_numeric($value)) && (is_numeric($degrees))) {
            $degrees = floor($degrees);
            if ($degrees < 1) {
                return Functions::NAN();
            }
            if ($value < 0) {
                if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                    return 1;
                }

                return Functions::NAN();
            }

            return 1 - (self::incompleteGamma($degrees / 2, $value / 2) / self::gamma($degrees / 2));
        }

        return Functions::VALUE();
    }

    
    public static function CHIINV($probability, $degrees)
    {
        $probability = Functions::flattenSingleValue($probability);
        $degrees = Functions::flattenSingleValue($degrees);

        if ((is_numeric($probability)) && (is_numeric($degrees))) {
            $degrees = floor($degrees);

            $xLo = 100;
            $xHi = 0;

            $x = $xNew = 1;
            $dx = 1;
            $i = 0;

            while ((abs($dx) > Functions::PRECISION) && ($i++ < self::MAX_ITERATIONS)) {
                
                $result = 1 - (self::incompleteGamma($degrees / 2, $x / 2) / self::gamma($degrees / 2));
                $error = $result - $probability;
                if ($error == 0.0) {
                    $dx = 0;
                } elseif ($error < 0.0) {
                    $xLo = $x;
                } else {
                    $xHi = $x;
                }
                
                if ($result != 0.0) {
                    $dx = $error / $result;
                    $xNew = $x - $dx;
                }
                
                
                
                if (($xNew < $xLo) || ($xNew > $xHi) || ($result == 0.0)) {
                    $xNew = ($xLo + $xHi) / 2;
                    $dx = $xNew - $x;
                }
                $x = $xNew;
            }
            if ($i == self::MAX_ITERATIONS) {
                return Functions::NA();
            }

            return round($x, 12);
        }

        return Functions::VALUE();
    }

    
    public static function CONFIDENCE($alpha, $stdDev, $size)
    {
        $alpha = Functions::flattenSingleValue($alpha);
        $stdDev = Functions::flattenSingleValue($stdDev);
        $size = Functions::flattenSingleValue($size);

        if ((is_numeric($alpha)) && (is_numeric($stdDev)) && (is_numeric($size))) {
            $size = floor($size);
            if (($alpha <= 0) || ($alpha >= 1)) {
                return Functions::NAN();
            }
            if (($stdDev <= 0) || ($size < 1)) {
                return Functions::NAN();
            }

            return self::NORMSINV(1 - $alpha / 2) * $stdDev / sqrt($size);
        }

        return Functions::VALUE();
    }

    
    public static function CORREL($yValues, $xValues = null)
    {
        if (($xValues === null) || (!is_array($yValues)) || (!is_array($xValues))) {
            return Functions::VALUE();
        }
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getCorrelation();
    }

    
    public static function COUNT(...$args)
    {
        $returnValue = 0;

        
        $aArgs = Functions::flattenArrayIndexed($args);
        foreach ($aArgs as $k => $arg) {
            $arg = self::testAcceptedBoolean($arg, $k);
            
            
            
            if (self::isAcceptedCountable($arg, $k)) {
                ++$returnValue;
            }
        }

        return $returnValue;
    }

    
    public static function COUNTA(...$args)
    {
        $returnValue = 0;

        
        $aArgs = Functions::flattenArrayIndexed($args);
        foreach ($aArgs as $k => $arg) {
            
            if ($arg !== null || (!Functions::isCellValue($k))) {
                ++$returnValue;
            }
        }

        return $returnValue;
    }

    
    public static function COUNTBLANK(...$args)
    {
        $returnValue = 0;

        
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            
            if (($arg === null) || ((is_string($arg)) && ($arg == ''))) {
                ++$returnValue;
            }
        }

        return $returnValue;
    }

    
    public static function COUNTIF($aArgs, $condition)
    {
        $returnValue = 0;

        $aArgs = Functions::flattenArray($aArgs);
        $condition = Functions::ifCondition($condition);
        $conditionIsNumeric = strpos($condition, '"') === false;
        
        foreach ($aArgs as $arg) {
            if (!is_numeric($arg)) {
                if ($conditionIsNumeric) {
                    continue;
                }
                $arg = Calculation::wrapResult(strtoupper($arg));
            } elseif (!$conditionIsNumeric) {
                continue;
            }
            $testCondition = '=' . $arg . $condition;
            if (Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                
                ++$returnValue;
            }
        }

        return $returnValue;
    }

    
    public static function COUNTIFS(...$args)
    {
        $arrayList = $args;

        
        $returnValue = 0;

        if (empty($arrayList)) {
            return $returnValue;
        }

        $aArgsArray = [];
        $conditions = [];

        while (count($arrayList) > 0) {
            $aArgsArray[] = Functions::flattenArray(array_shift($arrayList));
            $conditions[] = Functions::ifCondition(array_shift($arrayList));
        }

        
        foreach (array_keys($aArgsArray[0]) as $index) {
            $valid = true;

            foreach ($conditions as $cidx => $condition) {
                $conditionIsNumeric = strpos($condition, '"') === false;
                $arg = $aArgsArray[$cidx][$index];

                
                if (!is_numeric($arg)) {
                    if ($conditionIsNumeric) {
                        $valid = false;

                        break; 
                    }
                    $arg = Calculation::wrapResult(strtoupper($arg));
                } elseif (!$conditionIsNumeric) {
                    $valid = false;

                    break; 
                }
                $testCondition = '=' . $arg . $condition;
                if (!Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                    
                    $valid = false;

                    break; 
                }
            }

            if ($valid) {
                ++$returnValue;
            }
        }

        
        return $returnValue;
    }

    
    public static function COVAR($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getCovariance();
    }

    
    public static function CRITBINOM($trials, $probability, $alpha)
    {
        $trials = floor(Functions::flattenSingleValue($trials));
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);

        if ((is_numeric($trials)) && (is_numeric($probability)) && (is_numeric($alpha))) {
            $trials = (int) $trials;
            if ($trials < 0) {
                return Functions::NAN();
            } elseif (($probability < 0.0) || ($probability > 1.0)) {
                return Functions::NAN();
            } elseif (($alpha < 0.0) || ($alpha > 1.0)) {
                return Functions::NAN();
            }

            if ($alpha <= 0.5) {
                $t = sqrt(log(1 / ($alpha * $alpha)));
                $trialsApprox = 0 - ($t + (2.515517 + 0.802853 * $t + 0.010328 * $t * $t) / (1 + 1.432788 * $t + 0.189269 * $t * $t + 0.001308 * $t * $t * $t));
            } else {
                $t = sqrt(log(1 / (1 - $alpha) ** 2));
                $trialsApprox = $t - (2.515517 + 0.802853 * $t + 0.010328 * $t * $t) / (1 + 1.432788 * $t + 0.189269 * $t * $t + 0.001308 * $t * $t * $t);
            }

            $Guess = floor($trials * $probability + $trialsApprox * sqrt($trials * $probability * (1 - $probability)));
            if ($Guess < 0) {
                $Guess = 0;
            } elseif ($Guess > $trials) {
                $Guess = $trials;
            }

            $TotalUnscaledProbability = $UnscaledPGuess = $UnscaledCumPGuess = 0.0;
            $EssentiallyZero = 10e-12;

            $m = floor($trials * $probability);
            ++$TotalUnscaledProbability;
            if ($m == $Guess) {
                ++$UnscaledPGuess;
            }
            if ($m <= $Guess) {
                ++$UnscaledCumPGuess;
            }

            $PreviousValue = 1;
            $Done = false;
            $k = $m + 1;
            while ((!$Done) && ($k <= $trials)) {
                $CurrentValue = $PreviousValue * ($trials - $k + 1) * $probability / ($k * (1 - $probability));
                $TotalUnscaledProbability += $CurrentValue;
                if ($k == $Guess) {
                    $UnscaledPGuess += $CurrentValue;
                }
                if ($k <= $Guess) {
                    $UnscaledCumPGuess += $CurrentValue;
                }
                if ($CurrentValue <= $EssentiallyZero) {
                    $Done = true;
                }
                $PreviousValue = $CurrentValue;
                ++$k;
            }

            $PreviousValue = 1;
            $Done = false;
            $k = $m - 1;
            while ((!$Done) && ($k >= 0)) {
                $CurrentValue = $PreviousValue * $k + 1 * (1 - $probability) / (($trials - $k) * $probability);
                $TotalUnscaledProbability += $CurrentValue;
                if ($k == $Guess) {
                    $UnscaledPGuess += $CurrentValue;
                }
                if ($k <= $Guess) {
                    $UnscaledCumPGuess += $CurrentValue;
                }
                if ($CurrentValue <= $EssentiallyZero) {
                    $Done = true;
                }
                $PreviousValue = $CurrentValue;
                --$k;
            }

            $PGuess = $UnscaledPGuess / $TotalUnscaledProbability;
            $CumPGuess = $UnscaledCumPGuess / $TotalUnscaledProbability;

            $CumPGuessMinus1 = $CumPGuess - 1;

            while (true) {
                if (($CumPGuessMinus1 < $alpha) && ($CumPGuess >= $alpha)) {
                    return $Guess;
                } elseif (($CumPGuessMinus1 < $alpha) && ($CumPGuess < $alpha)) {
                    $PGuessPlus1 = $PGuess * ($trials - $Guess) * $probability / $Guess / (1 - $probability);
                    $CumPGuessMinus1 = $CumPGuess;
                    $CumPGuess = $CumPGuess + $PGuessPlus1;
                    $PGuess = $PGuessPlus1;
                    ++$Guess;
                } elseif (($CumPGuessMinus1 >= $alpha) && ($CumPGuess >= $alpha)) {
                    $PGuessMinus1 = $PGuess * $Guess * (1 - $probability) / ($trials - $Guess + 1) / $probability;
                    $CumPGuess = $CumPGuessMinus1;
                    $CumPGuessMinus1 = $CumPGuessMinus1 - $PGuess;
                    $PGuess = $PGuessMinus1;
                    --$Guess;
                }
            }
        }

        return Functions::VALUE();
    }

    
    public static function DEVSQ(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        
        $returnValue = null;

        $aMean = self::AVERAGE($aArgs);
        if ($aMean != Functions::DIV0()) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                
                if (
                    (is_bool($arg)) &&
                    ((!Functions::isCellValue($k)) ||
                    (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
                ) {
                    $arg = (int) $arg;
                }
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    if ($returnValue === null) {
                        $returnValue = ($arg - $aMean) ** 2;
                    } else {
                        $returnValue += ($arg - $aMean) ** 2;
                    }
                    ++$aCount;
                }
            }

            
            if ($returnValue === null) {
                return Functions::NAN();
            }

            return $returnValue;
        }

        return Functions::NA();
    }

    
    public static function EXPONDIST($value, $lambda, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $lambda = Functions::flattenSingleValue($lambda);
        $cumulative = Functions::flattenSingleValue($cumulative);

        if ((is_numeric($value)) && (is_numeric($lambda))) {
            if (($value < 0) || ($lambda < 0)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return 1 - exp(0 - $value * $lambda);
                }

                return $lambda * exp(0 - $value * $lambda);
            }
        }

        return Functions::VALUE();
    }

    private static function betaFunction($a, $b)
    {
        return (self::gamma($a) * self::gamma($b)) / self::gamma($a + $b);
    }

    private static function regularizedIncompleteBeta($value, $a, $b)
    {
        return self::incompleteBeta($value, $a, $b) / self::betaFunction($a, $b);
    }

    
    public static function FDIST2($value, $u, $v, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $u = Functions::flattenSingleValue($u);
        $v = Functions::flattenSingleValue($v);
        $cumulative = Functions::flattenSingleValue($cumulative);

        if (is_numeric($value) && is_numeric($u) && is_numeric($v)) {
            if ($value < 0 || $u < 1 || $v < 1) {
                return Functions::NAN();
            }

            $cumulative = (bool) $cumulative;
            $u = (int) $u;
            $v = (int) $v;

            if ($cumulative) {
                $adjustedValue = ($u * $value) / ($u * $value + $v);

                return self::incompleteBeta($adjustedValue, $u / 2, $v / 2);
            }

            return (self::gamma(($v + $u) / 2) / (self::gamma($u / 2) * self::gamma($v / 2))) *
                (($u / $v) ** ($u / 2)) *
                (($value ** (($u - 2) / 2)) / ((1 + ($u / $v) * $value) ** (($u + $v) / 2)));
        }

        return Functions::VALUE();
    }

    
    public static function FISHER($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_numeric($value)) {
            if (($value <= -1) || ($value >= 1)) {
                return Functions::NAN();
            }

            return 0.5 * log((1 + $value) / (1 - $value));
        }

        return Functions::VALUE();
    }

    
    public static function FISHERINV($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_numeric($value)) {
            return (exp(2 * $value) - 1) / (exp(2 * $value) + 1);
        }

        return Functions::VALUE();
    }

    
    public static function FORECAST($xValue, $yValues, $xValues)
    {
        $xValue = Functions::flattenSingleValue($xValue);
        if (!is_numeric($xValue)) {
            return Functions::VALUE();
        } elseif (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getValueOfYForX($xValue);
    }

    
    public static function GAMMAFunction($value)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        } elseif ((((int) $value) == ((float) $value)) && $value <= 0.0) {
            return Functions::NAN();
        }

        return self::gamma($value);
    }

    
    public static function GAMMADIST($value, $a, $b, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        if ((is_numeric($value)) && (is_numeric($a)) && (is_numeric($b))) {
            if (($value < 0) || ($a <= 0) || ($b <= 0)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return self::incompleteGamma($a, $value / $b) / self::gamma($a);
                }

                return (1 / ($b ** $a * self::gamma($a))) * $value ** ($a - 1) * exp(0 - ($value / $b));
            }
        }

        return Functions::VALUE();
    }

    
    public static function GAMMAINV($probability, $alpha, $beta)
    {
        $probability = Functions::flattenSingleValue($probability);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);

        if ((is_numeric($probability)) && (is_numeric($alpha)) && (is_numeric($beta))) {
            if (($alpha <= 0) || ($beta <= 0) || ($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }

            $xLo = 0;
            $xHi = $alpha * $beta * 5;

            $x = $xNew = 1;
            $dx = 1024;
            $i = 0;

            while ((abs($dx) > Functions::PRECISION) && ($i++ < self::MAX_ITERATIONS)) {
                
                $error = self::GAMMADIST($x, $alpha, $beta, true) - $probability;
                if ($error < 0.0) {
                    $xLo = $x;
                } else {
                    $xHi = $x;
                }
                $pdf = self::GAMMADIST($x, $alpha, $beta, false);
                
                if ($pdf != 0.0) {
                    $dx = $error / $pdf;
                    $xNew = $x - $dx;
                }
                
                
                
                if (($xNew < $xLo) || ($xNew > $xHi) || ($pdf == 0.0)) {
                    $xNew = ($xLo + $xHi) / 2;
                    $dx = $xNew - $x;
                }
                $x = $xNew;
            }
            if ($i == self::MAX_ITERATIONS) {
                return Functions::NA();
            }

            return $x;
        }

        return Functions::VALUE();
    }

    
    public static function GAMMALN($value)
    {
        $value = Functions::flattenSingleValue($value);

        if (is_numeric($value)) {
            if ($value <= 0) {
                return Functions::NAN();
            }

            return log(self::gamma($value));
        }

        return Functions::VALUE();
    }

    
    public static function GAUSS($value)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }

        return self::NORMDIST($value, 0, 1, true) - 0.5;
    }

    
    public static function GEOMEAN(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        $aMean = MathTrig::PRODUCT($aArgs);
        if (is_numeric($aMean) && ($aMean > 0)) {
            $aCount = self::COUNT($aArgs);
            if (self::MIN($aArgs) > 0) {
                return $aMean ** (1 / $aCount);
            }
        }

        return Functions::NAN();
    }

    
    public static function GROWTH($yValues, $xValues = [], $newValues = [], $const = true)
    {
        $yValues = Functions::flattenArray($yValues);
        $xValues = Functions::flattenArray($xValues);
        $newValues = Functions::flattenArray($newValues);
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);

        $bestFitExponential = Trend::calculate(Trend::TREND_EXPONENTIAL, $yValues, $xValues, $const);
        if (empty($newValues)) {
            $newValues = $bestFitExponential->getXValues();
        }

        $returnArray = [];
        foreach ($newValues as $xValue) {
            $returnArray[0][] = $bestFitExponential->getValueOfYForX($xValue);
        }

        return $returnArray;
    }

    
    public static function HARMEAN(...$args)
    {
        
        $returnValue = 0;

        
        $aArgs = Functions::flattenArray($args);
        if (self::MIN($aArgs) < 0) {
            return Functions::NAN();
        }
        $aCount = 0;
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if ($arg <= 0) {
                    return Functions::NAN();
                }
                $returnValue += (1 / $arg);
                ++$aCount;
            }
        }

        
        if ($aCount > 0) {
            return 1 / ($returnValue / $aCount);
        }

        return Functions::NA();
    }

    
    public static function HYPGEOMDIST($sampleSuccesses, $sampleNumber, $populationSuccesses, $populationNumber)
    {
        $sampleSuccesses = Functions::flattenSingleValue($sampleSuccesses);
        $sampleNumber = Functions::flattenSingleValue($sampleNumber);
        $populationSuccesses = Functions::flattenSingleValue($populationSuccesses);
        $populationNumber = Functions::flattenSingleValue($populationNumber);

        if ((is_numeric($sampleSuccesses)) && (is_numeric($sampleNumber)) && (is_numeric($populationSuccesses)) && (is_numeric($populationNumber))) {
            $sampleSuccesses = floor($sampleSuccesses);
            $sampleNumber = floor($sampleNumber);
            $populationSuccesses = floor($populationSuccesses);
            $populationNumber = floor($populationNumber);

            if (($sampleSuccesses < 0) || ($sampleSuccesses > $sampleNumber) || ($sampleSuccesses > $populationSuccesses)) {
                return Functions::NAN();
            }
            if (($sampleNumber <= 0) || ($sampleNumber > $populationNumber)) {
                return Functions::NAN();
            }
            if (($populationSuccesses <= 0) || ($populationSuccesses > $populationNumber)) {
                return Functions::NAN();
            }

            return MathTrig::COMBIN($populationSuccesses, $sampleSuccesses) *
                   MathTrig::COMBIN($populationNumber - $populationSuccesses, $sampleNumber - $sampleSuccesses) /
                   MathTrig::COMBIN($populationNumber, $sampleNumber);
        }

        return Functions::VALUE();
    }

    
    public static function INTERCEPT($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getIntersect();
    }

    
    public static function KURT(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);
        $mean = self::AVERAGE($aArgs);
        $stdDev = self::STDEV($aArgs);

        if ($stdDev > 0) {
            $count = $summer = 0;
            
            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    (!Functions::isMatrixValue($k))
                ) {
                } else {
                    
                    if ((is_numeric($arg)) && (!is_string($arg))) {
                        $summer += (($arg - $mean) / $stdDev) ** 4;
                        ++$count;
                    }
                }
            }

            
            if ($count > 3) {
                return $summer * ($count * ($count + 1) / (($count - 1) * ($count - 2) * ($count - 3))) - (3 * ($count - 1) ** 2 / (($count - 2) * ($count - 3)));
            }
        }

        return Functions::DIV0();
    }

    
    public static function LARGE(...$args)
    {
        $aArgs = Functions::flattenArray($args);
        $entry = array_pop($aArgs);

        if ((is_numeric($entry)) && (!is_string($entry))) {
            $entry = (int) floor($entry);

            
            $mArgs = [];
            foreach ($aArgs as $arg) {
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }
            $count = self::COUNT($mArgs);
            --$entry;
            if (($entry < 0) || ($entry >= $count) || ($count == 0)) {
                return Functions::NAN();
            }
            rsort($mArgs);

            return $mArgs[$entry];
        }

        return Functions::VALUE();
    }

    
    public static function LINEST($yValues, $xValues = null, $const = true, $stats = false)
    {
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);
        $stats = ($stats === null) ? false : (bool) Functions::flattenSingleValue($stats);
        if ($xValues === null) {
            $xValues = range(1, count(Functions::flattenArray($yValues)));
        }

        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return 0;
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues, $const);
        if ($stats) {
            return [
                [
                    $bestFitLinear->getSlope(),
                    $bestFitLinear->getSlopeSE(),
                    $bestFitLinear->getGoodnessOfFit(),
                    $bestFitLinear->getF(),
                    $bestFitLinear->getSSRegression(),
                ],
                [
                    $bestFitLinear->getIntersect(),
                    $bestFitLinear->getIntersectSE(),
                    $bestFitLinear->getStdevOfResiduals(),
                    $bestFitLinear->getDFResiduals(),
                    $bestFitLinear->getSSResiduals(),
                ],
            ];
        }

        return [
            $bestFitLinear->getSlope(),
            $bestFitLinear->getIntersect(),
        ];
    }

    
    public static function LOGEST($yValues, $xValues = null, $const = true, $stats = false)
    {
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);
        $stats = ($stats === null) ? false : (bool) Functions::flattenSingleValue($stats);
        if ($xValues === null) {
            $xValues = range(1, count(Functions::flattenArray($yValues)));
        }

        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        foreach ($yValues as $value) {
            if ($value <= 0.0) {
                return Functions::NAN();
            }
        }

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return 1;
        }

        $bestFitExponential = Trend::calculate(Trend::TREND_EXPONENTIAL, $yValues, $xValues, $const);
        if ($stats) {
            return [
                [
                    $bestFitExponential->getSlope(),
                    $bestFitExponential->getSlopeSE(),
                    $bestFitExponential->getGoodnessOfFit(),
                    $bestFitExponential->getF(),
                    $bestFitExponential->getSSRegression(),
                ],
                [
                    $bestFitExponential->getIntersect(),
                    $bestFitExponential->getIntersectSE(),
                    $bestFitExponential->getStdevOfResiduals(),
                    $bestFitExponential->getDFResiduals(),
                    $bestFitExponential->getSSResiduals(),
                ],
            ];
        }

        return [
            $bestFitExponential->getSlope(),
            $bestFitExponential->getIntersect(),
        ];
    }

    
    public static function LOGINV($probability, $mean, $stdDev)
    {
        $probability = Functions::flattenSingleValue($probability);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($probability)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($probability < 0) || ($probability > 1) || ($stdDev <= 0)) {
                return Functions::NAN();
            }

            return exp($mean + $stdDev * self::NORMSINV($probability));
        }

        return Functions::VALUE();
    }

    
    public static function LOGNORMDIST($value, $mean, $stdDev)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($value <= 0) || ($stdDev <= 0)) {
                return Functions::NAN();
            }

            return self::NORMSDIST((log($value) - $mean) / $stdDev);
        }

        return Functions::VALUE();
    }

    
    public static function LOGNORMDIST2($value, $mean, $stdDev, $cumulative = false)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);
        $cumulative = (bool) Functions::flattenSingleValue($cumulative);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($value <= 0) || ($stdDev <= 0)) {
                return Functions::NAN();
            }

            if ($cumulative === true) {
                return self::NORMSDIST2((log($value) - $mean) / $stdDev, true);
            }

            return (1 / (sqrt(2 * M_PI) * $stdDev * $value)) *
                exp(0 - ((log($value) - $mean) ** 2 / (2 * $stdDev ** 2)));
        }

        return Functions::VALUE();
    }

    
    public static function MAX(...$args)
    {
        $returnValue = null;

        
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if (($returnValue === null) || ($arg > $returnValue)) {
                    $returnValue = $arg;
                }
            }
        }

        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }

    
    public static function MAXA(...$args)
    {
        $returnValue = null;

        
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) && ($arg != '')))) {
                if (is_bool($arg)) {
                    $arg = (int) $arg;
                } elseif (is_string($arg)) {
                    $arg = 0;
                }
                if (($returnValue === null) || ($arg > $returnValue)) {
                    $returnValue = $arg;
                }
            }
        }

        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }

    
    public static function MAXIFS(...$args)
    {
        $arrayList = $args;

        
        $returnValue = null;

        $maxArgs = Functions::flattenArray(array_shift($arrayList));
        $aArgsArray = [];
        $conditions = [];

        while (count($arrayList) > 0) {
            $aArgsArray[] = Functions::flattenArray(array_shift($arrayList));
            $conditions[] = Functions::ifCondition(array_shift($arrayList));
        }

        
        foreach ($maxArgs as $index => $value) {
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
                $returnValue = $returnValue === null ? $value : max($value, $returnValue);
            }
        }

        
        return $returnValue;
    }

    
    public static function MEDIAN(...$args)
    {
        $returnValue = Functions::NAN();

        $mArgs = [];
        
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $mArgs[] = $arg;
            }
        }

        $mValueCount = count($mArgs);
        if ($mValueCount > 0) {
            sort($mArgs, SORT_NUMERIC);
            $mValueCount = $mValueCount / 2;
            if ($mValueCount == floor($mValueCount)) {
                $returnValue = ($mArgs[$mValueCount--] + $mArgs[$mValueCount]) / 2;
            } else {
                $mValueCount = floor($mValueCount);
                $returnValue = $mArgs[$mValueCount];
            }
        }

        return $returnValue;
    }

    
    public static function MIN(...$args)
    {
        $returnValue = null;

        
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                if (($returnValue === null) || ($arg < $returnValue)) {
                    $returnValue = $arg;
                }
            }
        }

        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }

    
    public static function MINA(...$args)
    {
        $returnValue = null;

        
        $aArgs = Functions::flattenArray($args);
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) && ($arg != '')))) {
                if (is_bool($arg)) {
                    $arg = (int) $arg;
                } elseif (is_string($arg)) {
                    $arg = 0;
                }
                if (($returnValue === null) || ($arg < $returnValue)) {
                    $returnValue = $arg;
                }
            }
        }

        if ($returnValue === null) {
            return 0;
        }

        return $returnValue;
    }

    
    public static function MINIFS(...$args)
    {
        $arrayList = $args;

        
        $returnValue = null;

        $minArgs = Functions::flattenArray(array_shift($arrayList));
        $aArgsArray = [];
        $conditions = [];

        while (count($arrayList) > 0) {
            $aArgsArray[] = Functions::flattenArray(array_shift($arrayList));
            $conditions[] = Functions::ifCondition(array_shift($arrayList));
        }

        
        foreach ($minArgs as $index => $value) {
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
                $returnValue = $returnValue === null ? $value : min($value, $returnValue);
            }
        }

        
        return $returnValue;
    }

    
    
    
    
    private static function modeCalc($data)
    {
        $frequencyArray = [];
        $index = 0;
        $maxfreq = 0;
        $maxfreqkey = '';
        $maxfreqdatum = '';
        foreach ($data as $datum) {
            $found = false;
            ++$index;
            foreach ($frequencyArray as $key => $value) {
                if ((string) $value['value'] == (string) $datum) {
                    ++$frequencyArray[$key]['frequency'];
                    $freq = $frequencyArray[$key]['frequency'];
                    if ($freq > $maxfreq) {
                        $maxfreq = $freq;
                        $maxfreqkey = $key;
                        $maxfreqdatum = $datum;
                    } elseif ($freq == $maxfreq) {
                        if ($frequencyArray[$key]['index'] < $frequencyArray[$maxfreqkey]['index']) {
                            $maxfreqkey = $key;
                            $maxfreqdatum = $datum;
                        }
                    }
                    $found = true;

                    break;
                }
            }
            if (!$found) {
                $frequencyArray[] = [
                    'value' => $datum,
                    'frequency' => 1,
                    'index' => $index,
                ];
            }
        }

        if ($maxfreq <= 1) {
            return Functions::NA();
        }

        return $maxfreqdatum;
    }

    
    public static function MODE(...$args)
    {
        $returnValue = Functions::NA();

        
        $aArgs = Functions::flattenArray($args);

        $mArgs = [];
        foreach ($aArgs as $arg) {
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $mArgs[] = $arg;
            }
        }

        if (!empty($mArgs)) {
            return self::modeCalc($mArgs);
        }

        return $returnValue;
    }

    
    public static function NEGBINOMDIST($failures, $successes, $probability)
    {
        $failures = floor(Functions::flattenSingleValue($failures));
        $successes = floor(Functions::flattenSingleValue($successes));
        $probability = Functions::flattenSingleValue($probability);

        if ((is_numeric($failures)) && (is_numeric($successes)) && (is_numeric($probability))) {
            if (($failures < 0) || ($successes < 1)) {
                return Functions::NAN();
            } elseif (($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
                if (($failures + $successes - 1) <= 0) {
                    return Functions::NAN();
                }
            }

            return (MathTrig::COMBIN($failures + $successes - 1, $successes - 1)) * ($probability ** $successes) * ((1 - $probability) ** $failures);
        }

        return Functions::VALUE();
    }

    
    public static function NORMDIST($value, $mean, $stdDev, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if ($stdDev < 0) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return 0.5 * (1 + Engineering::erfVal(($value - $mean) / ($stdDev * sqrt(2))));
                }

                return (1 / (self::SQRT2PI * $stdDev)) * exp(0 - (($value - $mean) ** 2 / (2 * ($stdDev * $stdDev))));
            }
        }

        return Functions::VALUE();
    }

    
    public static function NORMINV($probability, $mean, $stdDev)
    {
        $probability = Functions::flattenSingleValue($probability);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($probability)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if (($probability < 0) || ($probability > 1)) {
                return Functions::NAN();
            }
            if ($stdDev < 0) {
                return Functions::NAN();
            }

            return (self::inverseNcdf($probability) * $stdDev) + $mean;
        }

        return Functions::VALUE();
    }

    
    public static function NORMSDIST($value)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }

        return self::NORMDIST($value, 0, 1, true);
    }

    
    public static function NORMSDIST2($value, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        if (!is_numeric($value)) {
            return Functions::VALUE();
        }
        $cumulative = (bool) Functions::flattenSingleValue($cumulative);

        return self::NORMDIST($value, 0, 1, $cumulative);
    }

    
    public static function NORMSINV($value)
    {
        return self::NORMINV($value, 0, 1);
    }

    
    public static function PERCENTILE(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        
        $entry = array_pop($aArgs);

        if ((is_numeric($entry)) && (!is_string($entry))) {
            if (($entry < 0) || ($entry > 1)) {
                return Functions::NAN();
            }
            $mArgs = [];
            foreach ($aArgs as $arg) {
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }
            $mValueCount = count($mArgs);
            if ($mValueCount > 0) {
                sort($mArgs);
                $count = self::COUNT($mArgs);
                $index = $entry * ($count - 1);
                $iBase = floor($index);
                if ($index == $iBase) {
                    return $mArgs[$index];
                }
                $iNext = $iBase + 1;
                $iProportion = $index - $iBase;

                return $mArgs[$iBase] + (($mArgs[$iNext] - $mArgs[$iBase]) * $iProportion);
            }
        }

        return Functions::VALUE();
    }

    
    public static function PERCENTRANK($valueSet, $value, $significance = 3)
    {
        $valueSet = Functions::flattenArray($valueSet);
        $value = Functions::flattenSingleValue($value);
        $significance = ($significance === null) ? 3 : (int) Functions::flattenSingleValue($significance);

        foreach ($valueSet as $key => $valueEntry) {
            if (!is_numeric($valueEntry)) {
                unset($valueSet[$key]);
            }
        }
        sort($valueSet, SORT_NUMERIC);
        $valueCount = count($valueSet);
        if ($valueCount == 0) {
            return Functions::NAN();
        }

        $valueAdjustor = $valueCount - 1;
        if (($value < $valueSet[0]) || ($value > $valueSet[$valueAdjustor])) {
            return Functions::NA();
        }

        $pos = array_search($value, $valueSet);
        if ($pos === false) {
            $pos = 0;
            $testValue = $valueSet[0];
            while ($testValue < $value) {
                $testValue = $valueSet[++$pos];
            }
            --$pos;
            $pos += (($value - $valueSet[$pos]) / ($testValue - $valueSet[$pos]));
        }

        return round($pos / $valueAdjustor, $significance);
    }

    
    public static function PERMUT($numObjs, $numInSet)
    {
        $numObjs = Functions::flattenSingleValue($numObjs);
        $numInSet = Functions::flattenSingleValue($numInSet);

        if ((is_numeric($numObjs)) && (is_numeric($numInSet))) {
            $numInSet = floor($numInSet);
            if ($numObjs < $numInSet) {
                return Functions::NAN();
            }

            return round(MathTrig::FACT($numObjs) / MathTrig::FACT($numObjs - $numInSet));
        }

        return Functions::VALUE();
    }

    
    public static function POISSON($value, $mean, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);

        if ((is_numeric($value)) && (is_numeric($mean))) {
            if (($value < 0) || ($mean <= 0)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    $summer = 0;
                    $floor = floor($value);
                    for ($i = 0; $i <= $floor; ++$i) {
                        $summer += $mean ** $i / MathTrig::FACT($i);
                    }

                    return exp(0 - $mean) * $summer;
                }

                return (exp(0 - $mean) * $mean ** $value) / MathTrig::FACT($value);
            }
        }

        return Functions::VALUE();
    }

    
    public static function QUARTILE(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        
        $entry = floor(array_pop($aArgs));

        if ((is_numeric($entry)) && (!is_string($entry))) {
            $entry /= 4;
            if (($entry < 0) || ($entry > 1)) {
                return Functions::NAN();
            }

            return self::PERCENTILE($aArgs, $entry);
        }

        return Functions::VALUE();
    }

    
    public static function RANK($value, $valueSet, $order = 0)
    {
        $value = Functions::flattenSingleValue($value);
        $valueSet = Functions::flattenArray($valueSet);
        $order = ($order === null) ? 0 : (int) Functions::flattenSingleValue($order);

        foreach ($valueSet as $key => $valueEntry) {
            if (!is_numeric($valueEntry)) {
                unset($valueSet[$key]);
            }
        }

        if ($order == 0) {
            rsort($valueSet, SORT_NUMERIC);
        } else {
            sort($valueSet, SORT_NUMERIC);
        }
        $pos = array_search($value, $valueSet);
        if ($pos === false) {
            return Functions::NA();
        }

        return ++$pos;
    }

    
    public static function RSQ($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getGoodnessOfFit();
    }

    
    public static function SKEW(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);
        $mean = self::AVERAGE($aArgs);
        $stdDev = self::STDEV($aArgs);

        $count = $summer = 0;
        
        foreach ($aArgs as $k => $arg) {
            if (
                (is_bool($arg)) &&
                (!Functions::isMatrixValue($k))
            ) {
            } else {
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $summer += (($arg - $mean) / $stdDev) ** 3;
                    ++$count;
                }
            }
        }

        if ($count > 2) {
            return $summer * ($count / (($count - 1) * ($count - 2)));
        }

        return Functions::DIV0();
    }

    
    public static function SLOPE($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getSlope();
    }

    
    public static function SMALL(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        
        $entry = array_pop($aArgs);

        if ((is_numeric($entry)) && (!is_string($entry))) {
            $entry = (int) floor($entry);

            $mArgs = [];
            foreach ($aArgs as $arg) {
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }
            $count = self::COUNT($mArgs);
            --$entry;
            if (($entry < 0) || ($entry >= $count) || ($count == 0)) {
                return Functions::NAN();
            }
            sort($mArgs);

            return $mArgs[$entry];
        }

        return Functions::VALUE();
    }

    
    public static function STANDARDIZE($value, $mean, $stdDev)
    {
        $value = Functions::flattenSingleValue($value);
        $mean = Functions::flattenSingleValue($mean);
        $stdDev = Functions::flattenSingleValue($stdDev);

        if ((is_numeric($value)) && (is_numeric($mean)) && (is_numeric($stdDev))) {
            if ($stdDev <= 0) {
                return Functions::NAN();
            }

            return ($value - $mean) / $stdDev;
        }

        return Functions::VALUE();
    }

    
    public static function STDEV(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        
        $returnValue = null;

        $aMean = self::AVERAGE($aArgs);
        if ($aMean !== null) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    ((!Functions::isCellValue($k)) || (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
                ) {
                    $arg = (int) $arg;
                }
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    if ($returnValue === null) {
                        $returnValue = ($arg - $aMean) ** 2;
                    } else {
                        $returnValue += ($arg - $aMean) ** 2;
                    }
                    ++$aCount;
                }
            }

            
            if (($aCount > 0) && ($returnValue >= 0)) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    
    public static function STDEVA(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $returnValue = null;

        $aMean = self::AVERAGEA($aArgs);
        if ($aMean !== null) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    (!Functions::isMatrixValue($k))
                ) {
                } else {
                    
                    if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) & ($arg != '')))) {
                        if (is_bool($arg)) {
                            $arg = (int) $arg;
                        } elseif (is_string($arg)) {
                            $arg = 0;
                        }
                        if ($returnValue === null) {
                            $returnValue = ($arg - $aMean) ** 2;
                        } else {
                            $returnValue += ($arg - $aMean) ** 2;
                        }
                        ++$aCount;
                    }
                }
            }

            if (($aCount > 0) && ($returnValue >= 0)) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    
    public static function STDEVP(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $returnValue = null;

        $aMean = self::AVERAGE($aArgs);
        if ($aMean !== null) {
            $aCount = 0;
            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    ((!Functions::isCellValue($k)) || (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE))
                ) {
                    $arg = (int) $arg;
                }
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    if ($returnValue === null) {
                        $returnValue = ($arg - $aMean) ** 2;
                    } else {
                        $returnValue += ($arg - $aMean) ** 2;
                    }
                    ++$aCount;
                }
            }

            if (($aCount > 0) && ($returnValue >= 0)) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    
    public static function STDEVPA(...$args)
    {
        $aArgs = Functions::flattenArrayIndexed($args);

        $returnValue = null;

        $aMean = self::AVERAGEA($aArgs);
        if ($aMean !== null) {
            $aCount = 0;
            foreach ($aArgs as $k => $arg) {
                if (
                    (is_bool($arg)) &&
                    (!Functions::isMatrixValue($k))
                ) {
                } else {
                    
                    if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) & ($arg != '')))) {
                        if (is_bool($arg)) {
                            $arg = (int) $arg;
                        } elseif (is_string($arg)) {
                            $arg = 0;
                        }
                        if ($returnValue === null) {
                            $returnValue = ($arg - $aMean) ** 2;
                        } else {
                            $returnValue += ($arg - $aMean) ** 2;
                        }
                        ++$aCount;
                    }
                }
            }

            if (($aCount > 0) && ($returnValue >= 0)) {
                return sqrt($returnValue / $aCount);
            }
        }

        return Functions::DIV0();
    }

    
    public static function STEYX($yValues, $xValues)
    {
        if (!self::checkTrendArrays($yValues, $xValues)) {
            return Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);

        if (($yValueCount == 0) || ($yValueCount != $xValueCount)) {
            return Functions::NA();
        } elseif ($yValueCount == 1) {
            return Functions::DIV0();
        }

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues);

        return $bestFitLinear->getStdevOfResiduals();
    }

    
    public static function TDIST($value, $degrees, $tails)
    {
        $value = Functions::flattenSingleValue($value);
        $degrees = floor(Functions::flattenSingleValue($degrees));
        $tails = floor(Functions::flattenSingleValue($tails));

        if ((is_numeric($value)) && (is_numeric($degrees)) && (is_numeric($tails))) {
            if (($value < 0) || ($degrees < 1) || ($tails < 1) || ($tails > 2)) {
                return Functions::NAN();
            }
            
            
            
            
            
            
            
            
            
            $tterm = $degrees;
            $ttheta = atan2($value, sqrt($tterm));
            $tc = cos($ttheta);
            $ts = sin($ttheta);

            if (($degrees % 2) == 1) {
                $ti = 3;
                $tterm = $tc;
            } else {
                $ti = 2;
                $tterm = 1;
            }

            $tsum = $tterm;
            while ($ti < $degrees) {
                $tterm *= $tc * $tc * ($ti - 1) / $ti;
                $tsum += $tterm;
                $ti += 2;
            }
            $tsum *= $ts;
            if (($degrees % 2) == 1) {
                $tsum = Functions::M_2DIVPI * ($tsum + $ttheta);
            }
            $tValue = 0.5 * (1 + $tsum);
            if ($tails == 1) {
                return 1 - abs($tValue);
            }

            return 1 - abs((1 - $tValue) - $tValue);
        }

        return Functions::VALUE();
    }

    
    public static function TINV($probability, $degrees)
    {
        $probability = Functions::flattenSingleValue($probability);
        $degrees = floor(Functions::flattenSingleValue($degrees));

        if ((is_numeric($probability)) && (is_numeric($degrees))) {
            $xLo = 100;
            $xHi = 0;

            $x = $xNew = 1;
            $dx = 1;
            $i = 0;

            while ((abs($dx) > Functions::PRECISION) && ($i++ < self::MAX_ITERATIONS)) {
                
                $result = self::TDIST($x, $degrees, 2);
                $error = $result - $probability;
                if ($error == 0.0) {
                    $dx = 0;
                } elseif ($error < 0.0) {
                    $xLo = $x;
                } else {
                    $xHi = $x;
                }
                
                if ($result != 0.0) {
                    $dx = $error / $result;
                    $xNew = $x - $dx;
                }
                
                
                
                if (($xNew < $xLo) || ($xNew > $xHi) || ($result == 0.0)) {
                    $xNew = ($xLo + $xHi) / 2;
                    $dx = $xNew - $x;
                }
                $x = $xNew;
            }
            if ($i == self::MAX_ITERATIONS) {
                return Functions::NA();
            }

            return round($x, 12);
        }

        return Functions::VALUE();
    }

    
    public static function TREND($yValues, $xValues = [], $newValues = [], $const = true)
    {
        $yValues = Functions::flattenArray($yValues);
        $xValues = Functions::flattenArray($xValues);
        $newValues = Functions::flattenArray($newValues);
        $const = ($const === null) ? true : (bool) Functions::flattenSingleValue($const);

        $bestFitLinear = Trend::calculate(Trend::TREND_LINEAR, $yValues, $xValues, $const);
        if (empty($newValues)) {
            $newValues = $bestFitLinear->getXValues();
        }

        $returnArray = [];
        foreach ($newValues as $xValue) {
            $returnArray[0][] = $bestFitLinear->getValueOfYForX($xValue);
        }

        return $returnArray;
    }

    
    public static function TRIMMEAN(...$args)
    {
        $aArgs = Functions::flattenArray($args);

        
        $percent = array_pop($aArgs);

        if ((is_numeric($percent)) && (!is_string($percent))) {
            if (($percent < 0) || ($percent > 1)) {
                return Functions::NAN();
            }
            $mArgs = [];
            foreach ($aArgs as $arg) {
                
                if ((is_numeric($arg)) && (!is_string($arg))) {
                    $mArgs[] = $arg;
                }
            }
            $discard = floor(self::COUNT($mArgs) * $percent / 2);
            sort($mArgs);
            for ($i = 0; $i < $discard; ++$i) {
                array_pop($mArgs);
                array_shift($mArgs);
            }

            return self::AVERAGE($mArgs);
        }

        return Functions::VALUE();
    }

    
    public static function VARFunc(...$args)
    {
        $returnValue = Functions::DIV0();

        $summerA = $summerB = 0;

        
        $aArgs = Functions::flattenArray($args);
        $aCount = 0;
        foreach ($aArgs as $arg) {
            if (is_bool($arg)) {
                $arg = (int) $arg;
            }
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $summerA += ($arg * $arg);
                $summerB += $arg;
                ++$aCount;
            }
        }

        if ($aCount > 1) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * ($aCount - 1));
        }

        return $returnValue;
    }

    
    public static function VARA(...$args)
    {
        $returnValue = Functions::DIV0();

        $summerA = $summerB = 0;

        
        $aArgs = Functions::flattenArrayIndexed($args);
        $aCount = 0;
        foreach ($aArgs as $k => $arg) {
            if (
                (is_string($arg)) &&
                (Functions::isValue($k))
            ) {
                return Functions::VALUE();
            } elseif (
                (is_string($arg)) &&
                (!Functions::isMatrixValue($k))
            ) {
            } else {
                
                if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) & ($arg != '')))) {
                    if (is_bool($arg)) {
                        $arg = (int) $arg;
                    } elseif (is_string($arg)) {
                        $arg = 0;
                    }
                    $summerA += ($arg * $arg);
                    $summerB += $arg;
                    ++$aCount;
                }
            }
        }

        if ($aCount > 1) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * ($aCount - 1));
        }

        return $returnValue;
    }

    
    public static function VARP(...$args)
    {
        
        $returnValue = Functions::DIV0();

        $summerA = $summerB = 0;

        
        $aArgs = Functions::flattenArray($args);
        $aCount = 0;
        foreach ($aArgs as $arg) {
            if (is_bool($arg)) {
                $arg = (int) $arg;
            }
            
            if ((is_numeric($arg)) && (!is_string($arg))) {
                $summerA += ($arg * $arg);
                $summerB += $arg;
                ++$aCount;
            }
        }

        if ($aCount > 0) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * $aCount);
        }

        return $returnValue;
    }

    
    public static function VARPA(...$args)
    {
        $returnValue = Functions::DIV0();

        $summerA = $summerB = 0;

        
        $aArgs = Functions::flattenArrayIndexed($args);
        $aCount = 0;
        foreach ($aArgs as $k => $arg) {
            if (
                (is_string($arg)) &&
                (Functions::isValue($k))
            ) {
                return Functions::VALUE();
            } elseif (
                (is_string($arg)) &&
                (!Functions::isMatrixValue($k))
            ) {
            } else {
                
                if ((is_numeric($arg)) || (is_bool($arg)) || ((is_string($arg) & ($arg != '')))) {
                    if (is_bool($arg)) {
                        $arg = (int) $arg;
                    } elseif (is_string($arg)) {
                        $arg = 0;
                    }
                    $summerA += ($arg * $arg);
                    $summerB += $arg;
                    ++$aCount;
                }
            }
        }

        if ($aCount > 0) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * $aCount);
        }

        return $returnValue;
    }

    
    public static function WEIBULL($value, $alpha, $beta, $cumulative)
    {
        $value = Functions::flattenSingleValue($value);
        $alpha = Functions::flattenSingleValue($alpha);
        $beta = Functions::flattenSingleValue($beta);

        if ((is_numeric($value)) && (is_numeric($alpha)) && (is_numeric($beta))) {
            if (($value < 0) || ($alpha <= 0) || ($beta <= 0)) {
                return Functions::NAN();
            }
            if ((is_numeric($cumulative)) || (is_bool($cumulative))) {
                if ($cumulative) {
                    return 1 - exp(0 - ($value / $beta) ** $alpha);
                }

                return ($alpha / $beta ** $alpha) * $value ** ($alpha - 1) * exp(0 - ($value / $beta) ** $alpha);
            }
        }

        return Functions::VALUE();
    }

    
    public static function ZTEST($dataSet, $m0, $sigma = null)
    {
        $dataSet = Functions::flattenArrayIndexed($dataSet);
        $m0 = Functions::flattenSingleValue($m0);
        $sigma = Functions::flattenSingleValue($sigma);

        if ($sigma === null) {
            $sigma = self::STDEV($dataSet);
        }
        $n = count($dataSet);

        return 1 - self::NORMSDIST((self::AVERAGE($dataSet) - $m0) / ($sigma / sqrt($n)));
    }
}
