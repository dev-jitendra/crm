<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use PhpOffice\PhpSpreadsheet\Shared\Date;

class Financial
{
    const FINANCIAL_MAX_ITERATIONS = 128;

    const FINANCIAL_PRECISION = 1.0e-08;

    
    private static function isLastDayOfMonth(\DateTime $testDate)
    {
        return $testDate->format('d') == $testDate->format('t');
    }

    private static function couponFirstPeriodDate($settlement, $maturity, $frequency, $next)
    {
        $months = 12 / $frequency;

        $result = Date::excelToDateTimeObject($maturity);
        $eom = self::isLastDayOfMonth($result);

        while ($settlement < Date::PHPToExcel($result)) {
            $result->modify('-' . $months . ' months');
        }
        if ($next) {
            $result->modify('+' . $months . ' months');
        }

        if ($eom) {
            $result->modify('-1 day');
        }

        return Date::PHPToExcel($result);
    }

    private static function isValidFrequency($frequency)
    {
        if (($frequency == 1) || ($frequency == 2) || ($frequency == 4)) {
            return true;
        }

        return false;
    }

    
    private static function daysPerYear($year, $basis = 0)
    {
        switch ($basis) {
            case 0:
            case 2:
            case 4:
                $daysPerYear = 360;

                break;
            case 3:
                $daysPerYear = 365;

                break;
            case 1:
                $daysPerYear = (DateTime::isLeapYear($year)) ? 366 : 365;

                break;
            default:
                return Functions::NAN();
        }

        return $daysPerYear;
    }

    private static function interestAndPrincipal($rate = 0, $per = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $pmt = self::PMT($rate, $nper, $pv, $fv, $type);
        $capital = $pv;
        for ($i = 1; $i <= $per; ++$i) {
            $interest = ($type && $i == 1) ? 0 : -$capital * $rate;
            $principal = $pmt - $interest;
            $capital += $principal;
        }

        return [$interest, $principal];
    }

    
    public static function ACCRINT($issue, $firstinterest, $settlement, $rate, $par = 1000, $frequency = 1, $basis = 0)
    {
        $issue = Functions::flattenSingleValue($issue);
        $firstinterest = Functions::flattenSingleValue($firstinterest);
        $settlement = Functions::flattenSingleValue($settlement);
        $rate = Functions::flattenSingleValue($rate);
        $par = ($par === null) ? 1000 : Functions::flattenSingleValue($par);
        $frequency = ($frequency === null) ? 1 : Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        
        if ((is_numeric($rate)) && (is_numeric($par))) {
            $rate = (float) $rate;
            $par = (float) $par;
            if (($rate <= 0) || ($par <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenIssueAndSettlement = DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                
                return $daysBetweenIssueAndSettlement;
            }

            return $par * $rate * $daysBetweenIssueAndSettlement;
        }

        return Functions::VALUE();
    }

    
    public static function ACCRINTM($issue, $settlement, $rate, $par = 1000, $basis = 0)
    {
        $issue = Functions::flattenSingleValue($issue);
        $settlement = Functions::flattenSingleValue($settlement);
        $rate = Functions::flattenSingleValue($rate);
        $par = ($par === null) ? 1000 : Functions::flattenSingleValue($par);
        $basis = ($basis === null) ? 0 : Functions::flattenSingleValue($basis);

        
        if ((is_numeric($rate)) && (is_numeric($par))) {
            $rate = (float) $rate;
            $par = (float) $par;
            if (($rate <= 0) || ($par <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenIssueAndSettlement = DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                
                return $daysBetweenIssueAndSettlement;
            }

            return $par * $rate * $daysBetweenIssueAndSettlement;
        }

        return Functions::VALUE();
    }

    
    public static function AMORDEGRC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis = 0)
    {
        $cost = Functions::flattenSingleValue($cost);
        $purchased = Functions::flattenSingleValue($purchased);
        $firstPeriod = Functions::flattenSingleValue($firstPeriod);
        $salvage = Functions::flattenSingleValue($salvage);
        $period = floor(Functions::flattenSingleValue($period));
        $rate = Functions::flattenSingleValue($rate);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        
        
        
        
        
        
        $fUsePer = 1.0 / $rate;
        if ($fUsePer < 3.0) {
            $amortiseCoeff = 1.0;
        } elseif ($fUsePer < 5.0) {
            $amortiseCoeff = 1.5;
        } elseif ($fUsePer <= 6.0) {
            $amortiseCoeff = 2.0;
        } else {
            $amortiseCoeff = 2.5;
        }

        $rate *= $amortiseCoeff;
        $fNRate = round(DateTime::YEARFRAC($purchased, $firstPeriod, $basis) * $rate * $cost, 0);
        $cost -= $fNRate;
        $fRest = $cost - $salvage;

        for ($n = 0; $n < $period; ++$n) {
            $fNRate = round($rate * $cost, 0);
            $fRest -= $fNRate;

            if ($fRest < 0.0) {
                switch ($period - $n) {
                    case 0:
                    case 1:
                        return round($cost * 0.5, 0);
                    default:
                        return 0.0;
                }
            }
            $cost -= $fNRate;
        }

        return $fNRate;
    }

    
    public static function AMORLINC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis = 0)
    {
        $cost = Functions::flattenSingleValue($cost);
        $purchased = Functions::flattenSingleValue($purchased);
        $firstPeriod = Functions::flattenSingleValue($firstPeriod);
        $salvage = Functions::flattenSingleValue($salvage);
        $period = Functions::flattenSingleValue($period);
        $rate = Functions::flattenSingleValue($rate);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        $fOneRate = $cost * $rate;
        $fCostDelta = $cost - $salvage;
        
        $purchasedYear = DateTime::YEAR($purchased);
        $yearFrac = DateTime::YEARFRAC($purchased, $firstPeriod, $basis);

        if (($basis == 1) && ($yearFrac < 1) && (DateTime::isLeapYear($purchasedYear))) {
            $yearFrac *= 365 / 366;
        }

        $f0Rate = $yearFrac * $rate * $cost;
        $nNumOfFullPeriods = (int) (($cost - $salvage - $f0Rate) / $fOneRate);

        if ($period == 0) {
            return $f0Rate;
        } elseif ($period <= $nNumOfFullPeriods) {
            return $fOneRate;
        } elseif ($period == ($nNumOfFullPeriods + 1)) {
            return $fCostDelta - $fOneRate * $nNumOfFullPeriods - $f0Rate;
        }

        return 0.0;
    }

    
    public static function COUPDAYBS($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = (int) Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        if (is_string($settlement = DateTime::getDateValue($settlement))) {
            return Functions::VALUE();
        }
        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (
            ($settlement >= $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        $daysPerYear = self::daysPerYear(DateTime::YEAR($settlement), $basis);
        $prev = self::couponFirstPeriodDate($settlement, $maturity, $frequency, false);

        if ($basis == 1) {
            return abs(DateTime::DAYS($prev, $settlement));
        }

        return DateTime::YEARFRAC($prev, $settlement, $basis) * $daysPerYear;
    }

    
    public static function COUPDAYS($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = (int) Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        if (is_string($settlement = DateTime::getDateValue($settlement))) {
            return Functions::VALUE();
        }
        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (
            ($settlement >= $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        switch ($basis) {
            case 3:
                
                return 365 / $frequency;
            case 1:
                
                if ($frequency == 1) {
                    $daysPerYear = self::daysPerYear(DateTime::YEAR($settlement), $basis);

                    return $daysPerYear / $frequency;
                }
                $prev = self::couponFirstPeriodDate($settlement, $maturity, $frequency, false);
                $next = self::couponFirstPeriodDate($settlement, $maturity, $frequency, true);

                return $next - $prev;
            default:
                
                return 360 / $frequency;
        }
    }

    
    public static function COUPDAYSNC($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = (int) Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        if (is_string($settlement = DateTime::getDateValue($settlement))) {
            return Functions::VALUE();
        }
        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (
            ($settlement >= $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        $daysPerYear = self::daysPerYear(DateTime::YEAR($settlement), $basis);
        $next = self::couponFirstPeriodDate($settlement, $maturity, $frequency, true);

        return DateTime::YEARFRAC($settlement, $next, $basis) * $daysPerYear;
    }

    
    public static function COUPNCD($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = (int) Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        if (is_string($settlement = DateTime::getDateValue($settlement))) {
            return Functions::VALUE();
        }
        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (
            ($settlement >= $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        return self::couponFirstPeriodDate($settlement, $maturity, $frequency, true);
    }

    
    public static function COUPNUM($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = (int) Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        if (is_string($settlement = DateTime::getDateValue($settlement))) {
            return Functions::VALUE();
        }
        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (
            ($settlement >= $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        $yearsBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, 0);

        return ceil($yearsBetweenSettlementAndMaturity * $frequency);
    }

    
    public static function COUPPCD($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $frequency = (int) Functions::flattenSingleValue($frequency);
        $basis = ($basis === null) ? 0 : (int) Functions::flattenSingleValue($basis);

        if (is_string($settlement = DateTime::getDateValue($settlement))) {
            return Functions::VALUE();
        }
        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (
            ($settlement >= $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        return self::couponFirstPeriodDate($settlement, $maturity, $frequency, false);
    }

    
    public static function CUMIPMT($rate, $nper, $pv, $start, $end, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $start = (int) Functions::flattenSingleValue($start);
        $end = (int) Functions::flattenSingleValue($end);
        $type = (int) Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($start < 1 || $start > $end) {
            return Functions::VALUE();
        }

        
        $interest = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $interest += self::IPMT($rate, $per, $nper, $pv, 0, $type);
        }

        return $interest;
    }

    
    public static function CUMPRINC($rate, $nper, $pv, $start, $end, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $start = (int) Functions::flattenSingleValue($start);
        $end = (int) Functions::flattenSingleValue($end);
        $type = (int) Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($start < 1 || $start > $end) {
            return Functions::VALUE();
        }

        
        $principal = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $principal += self::PPMT($rate, $per, $nper, $pv, 0, $type);
        }

        return $principal;
    }

    
    public static function DB($cost, $salvage, $life, $period, $month = 12)
    {
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);
        $period = Functions::flattenSingleValue($period);
        $month = Functions::flattenSingleValue($month);

        
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period)) && (is_numeric($month))) {
            $cost = (float) $cost;
            $salvage = (float) $salvage;
            $life = (int) $life;
            $period = (int) $period;
            $month = (int) $month;
            if ($cost == 0) {
                return 0.0;
            } elseif (($cost < 0) || (($salvage / $cost) < 0) || ($life <= 0) || ($period < 1) || ($month < 1)) {
                return Functions::NAN();
            }
            
            $fixedDepreciationRate = 1 - ($salvage / $cost) ** (1 / $life);
            $fixedDepreciationRate = round($fixedDepreciationRate, 3);

            
            $previousDepreciation = 0;
            $depreciation = 0;
            for ($per = 1; $per <= $period; ++$per) {
                if ($per == 1) {
                    $depreciation = $cost * $fixedDepreciationRate * $month / 12;
                } elseif ($per == ($life + 1)) {
                    $depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate * (12 - $month) / 12;
                } else {
                    $depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate;
                }
                $previousDepreciation += $depreciation;
            }

            return $depreciation;
        }

        return Functions::VALUE();
    }

    
    public static function DDB($cost, $salvage, $life, $period, $factor = 2.0)
    {
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);
        $period = Functions::flattenSingleValue($period);
        $factor = Functions::flattenSingleValue($factor);

        
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period)) && (is_numeric($factor))) {
            $cost = (float) $cost;
            $salvage = (float) $salvage;
            $life = (int) $life;
            $period = (int) $period;
            $factor = (float) $factor;
            if (($cost <= 0) || (($salvage / $cost) < 0) || ($life <= 0) || ($period < 1) || ($factor <= 0.0) || ($period > $life)) {
                return Functions::NAN();
            }
            
            $fixedDepreciationRate = 1 - ($salvage / $cost) ** (1 / $life);
            $fixedDepreciationRate = round($fixedDepreciationRate, 3);

            
            $previousDepreciation = 0;
            $depreciation = 0;
            for ($per = 1; $per <= $period; ++$per) {
                $depreciation = min(($cost - $previousDepreciation) * ($factor / $life), ($cost - $salvage - $previousDepreciation));
                $previousDepreciation += $depreciation;
            }

            return $depreciation;
        }

        return Functions::VALUE();
    }

    
    public static function DISC($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = Functions::flattenSingleValue($basis);

        
        if ((is_numeric($price)) && (is_numeric($redemption)) && (is_numeric($basis))) {
            $price = (float) $price;
            $redemption = (float) $redemption;
            $basis = (int) $basis;
            if (($price <= 0) || ($redemption <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }

            return (1 - $price / $redemption) / $daysBetweenSettlementAndMaturity;
        }

        return Functions::VALUE();
    }

    
    public static function DOLLARDE($fractional_dollar = null, $fraction = 0)
    {
        $fractional_dollar = Functions::flattenSingleValue($fractional_dollar);
        $fraction = (int) Functions::flattenSingleValue($fraction);

        
        if ($fractional_dollar === null || $fraction < 0) {
            return Functions::NAN();
        }
        if ($fraction == 0) {
            return Functions::DIV0();
        }

        $dollars = floor($fractional_dollar);
        $cents = fmod($fractional_dollar, 1);
        $cents /= $fraction;
        $cents *= 10 ** ceil(log10($fraction));

        return $dollars + $cents;
    }

    
    public static function DOLLARFR($decimal_dollar = null, $fraction = 0)
    {
        $decimal_dollar = Functions::flattenSingleValue($decimal_dollar);
        $fraction = (int) Functions::flattenSingleValue($fraction);

        
        if ($decimal_dollar === null || $fraction < 0) {
            return Functions::NAN();
        }
        if ($fraction == 0) {
            return Functions::DIV0();
        }

        $dollars = floor($decimal_dollar);
        $cents = fmod($decimal_dollar, 1);
        $cents *= $fraction;
        $cents *= 10 ** (-ceil(log10($fraction)));

        return $dollars + $cents;
    }

    
    public static function EFFECT($nominal_rate = 0, $npery = 0)
    {
        $nominal_rate = Functions::flattenSingleValue($nominal_rate);
        $npery = (int) Functions::flattenSingleValue($npery);

        
        if ($nominal_rate <= 0 || $npery < 1) {
            return Functions::NAN();
        }

        return (1 + $nominal_rate / $npery) ** $npery - 1;
    }

    
    public static function FV($rate = 0, $nper = 0, $pmt = 0, $pv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = Functions::flattenSingleValue($nper);
        $pmt = Functions::flattenSingleValue($pmt);
        $pv = Functions::flattenSingleValue($pv);
        $type = Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        
        if ($rate !== null && $rate != 0) {
            return -$pv * (1 + $rate) ** $nper - $pmt * (1 + $rate * $type) * ((1 + $rate) ** $nper - 1) / $rate;
        }

        return -$pv - $pmt * $nper;
    }

    
    public static function FVSCHEDULE($principal, $schedule)
    {
        $principal = Functions::flattenSingleValue($principal);
        $schedule = Functions::flattenArray($schedule);

        foreach ($schedule as $rate) {
            $principal *= 1 + $rate;
        }

        return $principal;
    }

    
    public static function INTRATE($settlement, $maturity, $investment, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $investment = Functions::flattenSingleValue($investment);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = Functions::flattenSingleValue($basis);

        
        if ((is_numeric($investment)) && (is_numeric($redemption)) && (is_numeric($basis))) {
            $investment = (float) $investment;
            $redemption = (float) $redemption;
            $basis = (int) $basis;
            if (($investment <= 0) || ($redemption <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }

            return (($redemption / $investment) - 1) / ($daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }

    
    public static function IPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $per = (int) Functions::flattenSingleValue($per);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = (int) Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($per <= 0 || $per > $nper) {
            return Functions::VALUE();
        }

        
        $interestAndPrincipal = self::interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);

        return $interestAndPrincipal[0];
    }

    
    public static function IRR($values, $guess = 0.1)
    {
        if (!is_array($values)) {
            return Functions::VALUE();
        }
        $values = Functions::flattenArray($values);
        $guess = Functions::flattenSingleValue($guess);

        
        $x1 = 0.0;
        $x2 = $guess;
        $f1 = self::NPV($x1, $values);
        $f2 = self::NPV($x2, $values);
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            if (($f1 * $f2) < 0.0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = self::NPV($x1 += 1.6 * ($x1 - $x2), $values);
            } else {
                $f2 = self::NPV($x2 += 1.6 * ($x2 - $x1), $values);
            }
        }
        if (($f1 * $f2) > 0.0) {
            return Functions::VALUE();
        }

        $f = self::NPV($x1, $values);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::NPV($x_mid, $values);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_PRECISION) || (abs($dx) < self::FINANCIAL_PRECISION)) {
                return $x_mid;
            }
        }

        return Functions::VALUE();
    }

    
    public static function ISPMT(...$args)
    {
        
        $returnValue = 0;

        
        $aArgs = Functions::flattenArray($args);
        $interestRate = array_shift($aArgs);
        $period = array_shift($aArgs);
        $numberPeriods = array_shift($aArgs);
        $principleRemaining = array_shift($aArgs);

        
        $principlePayment = ($principleRemaining * 1.0) / ($numberPeriods * 1.0);
        for ($i = 0; $i <= $period; ++$i) {
            $returnValue = $interestRate * $principleRemaining * -1;
            $principleRemaining -= $principlePayment;
            
            if ($i == $numberPeriods) {
                $returnValue = 0;
            }
        }

        return $returnValue;
    }

    
    public static function MIRR($values, $finance_rate, $reinvestment_rate)
    {
        if (!is_array($values)) {
            return Functions::VALUE();
        }
        $values = Functions::flattenArray($values);
        $finance_rate = Functions::flattenSingleValue($finance_rate);
        $reinvestment_rate = Functions::flattenSingleValue($reinvestment_rate);
        $n = count($values);

        $rr = 1.0 + $reinvestment_rate;
        $fr = 1.0 + $finance_rate;

        $npv_pos = $npv_neg = 0.0;
        foreach ($values as $i => $v) {
            if ($v >= 0) {
                $npv_pos += $v / $rr ** $i;
            } else {
                $npv_neg += $v / $fr ** $i;
            }
        }

        if (($npv_neg == 0) || ($npv_pos == 0) || ($reinvestment_rate <= -1)) {
            return Functions::VALUE();
        }

        $mirr = ((-$npv_pos * $rr ** $n)
                / ($npv_neg * ($rr))) ** (1.0 / ($n - 1)) - 1.0;

        return is_finite($mirr) ? $mirr : Functions::VALUE();
    }

    
    public static function NOMINAL($effect_rate = 0, $npery = 0)
    {
        $effect_rate = Functions::flattenSingleValue($effect_rate);
        $npery = (int) Functions::flattenSingleValue($npery);

        
        if ($effect_rate <= 0 || $npery < 1) {
            return Functions::NAN();
        }

        
        return $npery * (($effect_rate + 1) ** (1 / $npery) - 1);
    }

    
    public static function NPER($rate = 0, $pmt = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $pmt = Functions::flattenSingleValue($pmt);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        
        if ($rate !== null && $rate != 0) {
            if ($pmt == 0 && $pv == 0) {
                return Functions::NAN();
            }

            return log(($pmt * (1 + $rate * $type) / $rate - $fv) / ($pv + $pmt * (1 + $rate * $type) / $rate)) / log(1 + $rate);
        }
        if ($pmt == 0) {
            return Functions::NAN();
        }

        return (-$pv - $fv) / $pmt;
    }

    
    public static function NPV(...$args)
    {
        
        $returnValue = 0;

        
        $aArgs = Functions::flattenArray($args);

        
        $rate = array_shift($aArgs);
        $countArgs = count($aArgs);
        for ($i = 1; $i <= $countArgs; ++$i) {
            
            if (is_numeric($aArgs[$i - 1])) {
                $returnValue += $aArgs[$i - 1] / (1 + $rate) ** $i;
            }
        }

        
        return $returnValue;
    }

    
    public static function PDURATION($rate = 0, $pv = 0, $fv = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);

        
        if (!is_numeric($rate) || !is_numeric($pv) || !is_numeric($fv)) {
            return Functions::VALUE();
        } elseif ($rate <= 0.0 || $pv <= 0.0 || $fv <= 0.0) {
            return Functions::NAN();
        }

        return (log($fv) - log($pv)) / log(1 + $rate);
    }

    
    public static function PMT($rate = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        
        if ($rate !== null && $rate != 0) {
            return (-$fv - $pv * (1 + $rate) ** $nper) / (1 + $rate * $type) / (((1 + $rate) ** $nper - 1) / $rate);
        }

        return (-$pv - $fv) / $nper;
    }

    
    public static function PPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $per = (int) Functions::flattenSingleValue($per);
        $nper = (int) Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);
        $type = (int) Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }
        if ($per <= 0 || $per > $nper) {
            return Functions::VALUE();
        }

        
        $interestAndPrincipal = self::interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);

        return $interestAndPrincipal[1];
    }

    private static function validatePrice($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis)
    {
        if (is_string($settlement)) {
            return Functions::VALUE();
        }
        if (is_string($maturity)) {
            return Functions::VALUE();
        }
        if (!is_numeric($rate)) {
            return Functions::VALUE();
        }
        if (!is_numeric($yield)) {
            return Functions::VALUE();
        }
        if (!is_numeric($redemption)) {
            return Functions::VALUE();
        }
        if (!is_numeric($frequency)) {
            return Functions::VALUE();
        }
        if (!is_numeric($basis)) {
            return Functions::VALUE();
        }

        return '';
    }

    public static function PRICE($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $rate = Functions::flattenSingleValue($rate);
        $yield = Functions::flattenSingleValue($yield);
        $redemption = Functions::flattenSingleValue($redemption);
        $frequency = Functions::flattenSingleValue($frequency);
        $basis = Functions::flattenSingleValue($basis);

        $settlement = DateTime::getDateValue($settlement);
        $maturity = DateTime::getDateValue($maturity);
        $rslt = self::validatePrice($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis);
        if ($rslt) {
            return $rslt;
        }
        $rate = (float) $rate;
        $yield = (float) $yield;
        $redemption = (float) $redemption;
        $frequency = (int) $frequency;
        $basis = (int) $basis;

        if (
            ($settlement > $maturity) ||
            (!self::isValidFrequency($frequency)) ||
            (($basis < 0) || ($basis > 4))
        ) {
            return Functions::NAN();
        }

        $dsc = self::COUPDAYSNC($settlement, $maturity, $frequency, $basis);
        $e = self::COUPDAYS($settlement, $maturity, $frequency, $basis);
        $n = self::COUPNUM($settlement, $maturity, $frequency, $basis);
        $a = self::COUPDAYBS($settlement, $maturity, $frequency, $basis);

        $baseYF = 1.0 + ($yield / $frequency);
        $rfp = 100 * ($rate / $frequency);
        $de = $dsc / $e;

        $result = $redemption / $baseYF ** (--$n + $de);
        for ($k = 0; $k <= $n; ++$k) {
            $result += $rfp / ($baseYF ** ($k + $de));
        }
        $result -= $rfp * ($a / $e);

        return $result;
    }

    
    public static function PRICEDISC($settlement, $maturity, $discount, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = (float) Functions::flattenSingleValue($discount);
        $redemption = (float) Functions::flattenSingleValue($redemption);
        $basis = (int) Functions::flattenSingleValue($basis);

        
        if ((is_numeric($discount)) && (is_numeric($redemption)) && (is_numeric($basis))) {
            if (($discount <= 0) || ($redemption <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }

            return $redemption * (1 - $discount * $daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }

    
    public static function PRICEMAT($settlement, $maturity, $issue, $rate, $yield, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $issue = Functions::flattenSingleValue($issue);
        $rate = Functions::flattenSingleValue($rate);
        $yield = Functions::flattenSingleValue($yield);
        $basis = (int) Functions::flattenSingleValue($basis);

        
        if (is_numeric($rate) && is_numeric($yield)) {
            if (($rate <= 0) || ($yield <= 0)) {
                return Functions::NAN();
            }
            $daysPerYear = self::daysPerYear(DateTime::YEAR($settlement), $basis);
            if (!is_numeric($daysPerYear)) {
                return $daysPerYear;
            }
            $daysBetweenIssueAndSettlement = DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                
                return $daysBetweenIssueAndSettlement;
            }
            $daysBetweenIssueAndSettlement *= $daysPerYear;
            $daysBetweenIssueAndMaturity = DateTime::YEARFRAC($issue, $maturity, $basis);
            if (!is_numeric($daysBetweenIssueAndMaturity)) {
                
                return $daysBetweenIssueAndMaturity;
            }
            $daysBetweenIssueAndMaturity *= $daysPerYear;
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }
            $daysBetweenSettlementAndMaturity *= $daysPerYear;

            return (100 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate * 100)) /
                   (1 + (($daysBetweenSettlementAndMaturity / $daysPerYear) * $yield)) -
                   (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate * 100);
        }

        return Functions::VALUE();
    }

    
    public static function PV($rate = 0, $nper = 0, $pmt = 0, $fv = 0, $type = 0)
    {
        $rate = Functions::flattenSingleValue($rate);
        $nper = Functions::flattenSingleValue($nper);
        $pmt = Functions::flattenSingleValue($pmt);
        $fv = Functions::flattenSingleValue($fv);
        $type = Functions::flattenSingleValue($type);

        
        if ($type != 0 && $type != 1) {
            return Functions::NAN();
        }

        
        if ($rate !== null && $rate != 0) {
            return (-$pmt * (1 + $rate * $type) * (((1 + $rate) ** $nper - 1) / $rate) - $fv) / (1 + $rate) ** $nper;
        }

        return -$fv - $pmt * $nper;
    }

    
    public static function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1)
    {
        $nper = (int) Functions::flattenSingleValue($nper);
        $pmt = Functions::flattenSingleValue($pmt);
        $pv = Functions::flattenSingleValue($pv);
        $fv = ($fv === null) ? 0.0 : Functions::flattenSingleValue($fv);
        $type = ($type === null) ? 0 : (int) Functions::flattenSingleValue($type);
        $guess = ($guess === null) ? 0.1 : Functions::flattenSingleValue($guess);

        $rate = $guess;
        
        $close = false;
        $iter = 0;
        while (!$close && $iter < self::FINANCIAL_MAX_ITERATIONS) {
            $nextdiff = self::rateNextGuess($rate, $nper, $pmt, $pv, $fv, $type);
            if (!is_numeric($nextdiff)) {
                break;
            }
            $rate1 = $rate - $nextdiff;
            $close = abs($rate1 - $rate) < self::FINANCIAL_PRECISION;
            ++$iter;
            $rate = $rate1;
        }

        return $close ? $rate : Functions::NAN();
    }

    private static function rateNextGuess($rate, $nper, $pmt, $pv, $fv, $type)
    {
        if ($rate == 0) {
            return Functions::NAN();
        }
        $tt1 = ($rate + 1) ** $nper;
        $tt2 = ($rate + 1) ** ($nper - 1);
        $numerator = $fv + $tt1 * $pv + $pmt * ($tt1 - 1) * ($rate * $type + 1) / $rate;
        $denominator = $nper * $tt2 * $pv - $pmt * ($tt1 - 1) * ($rate * $type + 1) / ($rate * $rate)
             + $nper * $pmt * $tt2 * ($rate * $type + 1) / $rate
             + $pmt * ($tt1 - 1) * $type / $rate;
        if ($denominator == 0) {
            return Functions::NAN();
        }

        return $numerator / $denominator;
    }

    
    public static function RECEIVED($settlement, $maturity, $investment, $discount, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $investment = (float) Functions::flattenSingleValue($investment);
        $discount = (float) Functions::flattenSingleValue($discount);
        $basis = (int) Functions::flattenSingleValue($basis);

        
        if ((is_numeric($investment)) && (is_numeric($discount)) && (is_numeric($basis))) {
            if (($investment <= 0) || ($discount <= 0)) {
                return Functions::NAN();
            }
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }

            return $investment / (1 - ($discount * $daysBetweenSettlementAndMaturity));
        }

        return Functions::VALUE();
    }

    
    public static function RRI($nper = 0, $pv = 0, $fv = 0)
    {
        $nper = Functions::flattenSingleValue($nper);
        $pv = Functions::flattenSingleValue($pv);
        $fv = Functions::flattenSingleValue($fv);

        
        if (!is_numeric($nper) || !is_numeric($pv) || !is_numeric($fv)) {
            return Functions::VALUE();
        } elseif ($nper <= 0.0 || $pv <= 0.0 || $fv < 0.0) {
            return Functions::NAN();
        }

        return ($fv / $pv) ** (1 / $nper) - 1;
    }

    
    public static function SLN($cost, $salvage, $life)
    {
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);

        
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life))) {
            if ($life < 0) {
                return Functions::NAN();
            }

            return ($cost - $salvage) / $life;
        }

        return Functions::VALUE();
    }

    
    public static function SYD($cost, $salvage, $life, $period)
    {
        $cost = Functions::flattenSingleValue($cost);
        $salvage = Functions::flattenSingleValue($salvage);
        $life = Functions::flattenSingleValue($life);
        $period = Functions::flattenSingleValue($period);

        
        if ((is_numeric($cost)) && (is_numeric($salvage)) && (is_numeric($life)) && (is_numeric($period))) {
            if (($life < 1) || ($period > $life)) {
                return Functions::NAN();
            }

            return (($cost - $salvage) * ($life - $period + 1) * 2) / ($life * ($life + 1));
        }

        return Functions::VALUE();
    }

    
    public static function TBILLEQ($settlement, $maturity, $discount)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);

        
        $testValue = self::TBILLPRICE($settlement, $maturity, $discount);
        if (is_string($testValue)) {
            return $testValue;
        }

        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
            ++$maturity;
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity) * 360;
        } else {
            $daysBetweenSettlementAndMaturity = (DateTime::getDateValue($maturity) - DateTime::getDateValue($settlement));
        }

        return (365 * $discount) / (360 - $discount * $daysBetweenSettlementAndMaturity);
    }

    
    public static function TBILLPRICE($settlement, $maturity, $discount)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $discount = Functions::flattenSingleValue($discount);

        if (is_string($maturity = DateTime::getDateValue($maturity))) {
            return Functions::VALUE();
        }

        
        if (is_numeric($discount)) {
            if ($discount <= 0) {
                return Functions::NAN();
            }

            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                ++$maturity;
                $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity) * 360;
                if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                    
                    return $daysBetweenSettlementAndMaturity;
                }
            } else {
                $daysBetweenSettlementAndMaturity = (DateTime::getDateValue($maturity) - DateTime::getDateValue($settlement));
            }

            if ($daysBetweenSettlementAndMaturity > 360) {
                return Functions::NAN();
            }

            $price = 100 * (1 - (($discount * $daysBetweenSettlementAndMaturity) / 360));
            if ($price <= 0) {
                return Functions::NAN();
            }

            return $price;
        }

        return Functions::VALUE();
    }

    
    public static function TBILLYIELD($settlement, $maturity, $price)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);

        
        if (is_numeric($price)) {
            if ($price <= 0) {
                return Functions::NAN();
            }

            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                ++$maturity;
                $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity) * 360;
                if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                    
                    return $daysBetweenSettlementAndMaturity;
                }
            } else {
                $daysBetweenSettlementAndMaturity = (DateTime::getDateValue($maturity) - DateTime::getDateValue($settlement));
            }

            if ($daysBetweenSettlementAndMaturity > 360) {
                return Functions::NAN();
            }

            return ((100 - $price) / $price) * (360 / $daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }

    private static function bothNegAndPos($neg, $pos)
    {
        return $neg && $pos;
    }

    private static function xirrPart2(&$values)
    {
        $valCount = count($values);
        $foundpos = false;
        $foundneg = false;
        for ($i = 0; $i < $valCount; ++$i) {
            $fld = $values[$i];
            if (!is_numeric($fld)) {
                return Functions::VALUE();
            } elseif ($fld > 0) {
                $foundpos = true;
            } elseif ($fld < 0) {
                $foundneg = true;
            }
        }
        if (!self::bothNegAndPos($foundneg, $foundpos)) {
            return Functions::NAN();
        }

        return '';
    }

    private static function xirrPart1(&$values, &$dates)
    {
        if ((!is_array($values)) && (!is_array($dates))) {
            return Functions::NA();
        }
        $values = Functions::flattenArray($values);
        $dates = Functions::flattenArray($dates);
        if (count($values) != count($dates)) {
            return Functions::NAN();
        }

        $datesCount = count($dates);
        for ($i = 0; $i < $datesCount; ++$i) {
            $dates[$i] = DateTime::getDateValue($dates[$i]);
            if (!is_numeric($dates[$i])) {
                return Functions::VALUE();
            }
        }

        return self::xirrPart2($values);
    }

    private static function xirrPart3($values, $dates, $x1, $x2)
    {
        $f = self::xnpvOrdered($x1, $values, $dates, false);
        if ($f < 0.0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }

        $rslt = Functions::VALUE();
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::xnpvOrdered($x_mid, $values, $dates, false);
            if ($f_mid <= 0.0) {
                $rtb = $x_mid;
            }
            if ((abs($f_mid) < self::FINANCIAL_PRECISION) || (abs($dx) < self::FINANCIAL_PRECISION)) {
                $rslt = $x_mid;

                break;
            }
        }

        return $rslt;
    }

    
    public static function XIRR($values, $dates, $guess = 0.1)
    {
        $rslt = self::xirrPart1($values, $dates);
        if ($rslt) {
            return $rslt;
        }

        
        $guess = Functions::flattenSingleValue($guess);
        $x1 = 0.0;
        $x2 = $guess ? $guess : 0.1;
        $f1 = self::xnpvOrdered($x1, $values, $dates, false);
        $f2 = self::xnpvOrdered($x2, $values, $dates, false);
        $found = false;
        for ($i = 0; $i < self::FINANCIAL_MAX_ITERATIONS; ++$i) {
            if (!is_numeric($f1) || !is_numeric($f2)) {
                break;
            }
            if (($f1 * $f2) < 0.0) {
                $found = true;

                break;
            } elseif (abs($f1) < abs($f2)) {
                $f1 = self::xnpvOrdered($x1 += 1.6 * ($x1 - $x2), $values, $dates, false);
            } else {
                $f2 = self::xnpvOrdered($x2 += 1.6 * ($x2 - $x1), $values, $dates, false);
            }
        }
        if (!$found) {
            return Functions::NAN();
        }

        return self::xirrPart3($values, $dates, $x1, $x2);
    }

    
    public static function XNPV($rate, $values, $dates)
    {
        return self::xnpvOrdered($rate, $values, $dates, true);
    }

    private static function validateXnpv($rate, $values, $dates)
    {
        if (!is_numeric($rate)) {
            return Functions::VALUE();
        }
        $valCount = count($values);
        if ($valCount != count($dates)) {
            return Functions::NAN();
        }
        if ($valCount > 1 && ((min($values) > 0) || (max($values) < 0))) {
            return Functions::NAN();
        }
        $date0 = DateTime::getDateValue($dates[0]);
        if (is_string($date0)) {
            return Functions::VALUE();
        }

        return '';
    }

    private static function xnpvOrdered($rate, $values, $dates, $ordered = true)
    {
        $rate = Functions::flattenSingleValue($rate);
        $values = Functions::flattenArray($values);
        $dates = Functions::flattenArray($dates);
        $valCount = count($values);
        $date0 = DateTime::getDateValue($dates[0]);
        $rslt = self::validateXnpv($rate, $values, $dates);
        if ($rslt) {
            return $rslt;
        }
        $xnpv = 0.0;
        for ($i = 0; $i < $valCount; ++$i) {
            if (!is_numeric($values[$i])) {
                return Functions::VALUE();
            }
            $datei = DateTime::getDateValue($dates[$i]);
            if (is_string($datei)) {
                return Functions::VALUE();
            }
            if ($date0 > $datei) {
                $dif = $ordered ? Functions::NAN() : -DateTime::DATEDIF($datei, $date0, 'd');
            } else {
                $dif = DateTime::DATEDIF($date0, $datei, 'd');
            }
            if (!is_numeric($dif)) {
                return $dif;
            }
            $xnpv += $values[$i] / (1 + $rate) ** ($dif / 365);
        }

        return is_finite($xnpv) ? $xnpv : Functions::VALUE();
    }

    
    public static function YIELDDISC($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $price = Functions::flattenSingleValue($price);
        $redemption = Functions::flattenSingleValue($redemption);
        $basis = (int) Functions::flattenSingleValue($basis);

        
        if (is_numeric($price) && is_numeric($redemption)) {
            if (($price <= 0) || ($redemption <= 0)) {
                return Functions::NAN();
            }
            $daysPerYear = self::daysPerYear(DateTime::YEAR($settlement), $basis);
            if (!is_numeric($daysPerYear)) {
                return $daysPerYear;
            }
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }
            $daysBetweenSettlementAndMaturity *= $daysPerYear;

            return (($redemption - $price) / $price) * ($daysPerYear / $daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }

    
    public static function YIELDMAT($settlement, $maturity, $issue, $rate, $price, $basis = 0)
    {
        $settlement = Functions::flattenSingleValue($settlement);
        $maturity = Functions::flattenSingleValue($maturity);
        $issue = Functions::flattenSingleValue($issue);
        $rate = Functions::flattenSingleValue($rate);
        $price = Functions::flattenSingleValue($price);
        $basis = (int) Functions::flattenSingleValue($basis);

        
        if (is_numeric($rate) && is_numeric($price)) {
            if (($rate <= 0) || ($price <= 0)) {
                return Functions::NAN();
            }
            $daysPerYear = self::daysPerYear(DateTime::YEAR($settlement), $basis);
            if (!is_numeric($daysPerYear)) {
                return $daysPerYear;
            }
            $daysBetweenIssueAndSettlement = DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                
                return $daysBetweenIssueAndSettlement;
            }
            $daysBetweenIssueAndSettlement *= $daysPerYear;
            $daysBetweenIssueAndMaturity = DateTime::YEARFRAC($issue, $maturity, $basis);
            if (!is_numeric($daysBetweenIssueAndMaturity)) {
                
                return $daysBetweenIssueAndMaturity;
            }
            $daysBetweenIssueAndMaturity *= $daysPerYear;
            $daysBetweenSettlementAndMaturity = DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                
                return $daysBetweenSettlementAndMaturity;
            }
            $daysBetweenSettlementAndMaturity *= $daysPerYear;

            return ((1 + (($daysBetweenIssueAndMaturity / $daysPerYear) * $rate) - (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate))) /
                   (($price / 100) + (($daysBetweenIssueAndSettlement / $daysPerYear) * $rate))) *
                   ($daysPerYear / $daysBetweenSettlementAndMaturity);
        }

        return Functions::VALUE();
    }
}
