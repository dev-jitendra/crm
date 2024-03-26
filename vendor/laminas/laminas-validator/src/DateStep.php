<?php

declare(strict_types=1);

namespace Laminas\Validator;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_combine;
use function array_count_values;
use function array_map;
use function array_shift;
use function ceil;
use function date_default_timezone_get;
use function explode;
use function floor;
use function func_get_args;
use function in_array;
use function is_array;
use function max;
use function min;
use function preg_match;
use function sprintf;
use function str_starts_with;

use const PHP_INT_MAX;

class DateStep extends Date
{
    
    public const NOT_STEP = 'dateStepNotStep';

    
    public const FORMAT_DEFAULT = DateTime::ISO8601;

    
    protected $messageTemplates = [
        self::INVALID      => 'Invalid type given. String, integer, array or DateTime expected',
        self::INVALID_DATE => 'The input does not appear to be a valid date',
        self::FALSEFORMAT  => "The input does not fit the date format '%format%'",
        self::NOT_STEP     => 'The input is not a valid step',
    ];

    
    protected $baseValue = '1970-01-01T00:00:00Z';

    
    protected $step;

    
    protected $timezone;

    
    public function __construct($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (! is_array($options)) {
            $options           = func_get_args();
            $temp              = [];
            $temp['baseValue'] = array_shift($options);
            if (! empty($options)) {
                $temp['step'] = array_shift($options);
            }
            if (! empty($options)) {
                $temp['format'] = array_shift($options);
            }
            if (! empty($options)) {
                $temp['timezone'] = array_shift($options);
            }

            $options = $temp;
        }

        if (! isset($options['step'])) {
            $options['step'] = new DateInterval('P1D');
        }
        if (! isset($options['timezone'])) {
            $options['timezone'] = new DateTimeZone(date_default_timezone_get());
        }

        parent::__construct($options);
    }

    
    public function setBaseValue($baseValue)
    {
        $this->baseValue = $baseValue;
        return $this;
    }

    
    public function getBaseValue()
    {
        return $this->baseValue;
    }

    
    public function setStep(DateInterval $step)
    {
        $this->step = $step;
        return $this;
    }

    
    public function getStep()
    {
        return $this->step;
    }

    
    public function getTimezone()
    {
        return $this->timezone;
    }

    
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }

    
    protected function convertString($value, $addErrors = true)
    {
        
        if (
            str_starts_with($this->format, 'Y-\WW')
            && preg_match('/^([0-9]{4})\-W([0-9]{2})/', $value, $matches)
        ) {
            $date = new DateTime();
            $date->setISODate((int) $matches[1], (int) $matches[2]);
        } else {
            $date = DateTime::createFromFormat($this->format, $value, new DateTimeZone('UTC'));
        }

        
        
        $errors = DateTime::getLastErrors();
        if (is_array($errors) && $errors['warning_count'] > 0) {
            if ($addErrors) {
                $this->error(self::FALSEFORMAT);
            }
            return false;
        }

        return $date;
    }

    
    public function isValid($value)
    {
        if (! parent::isValid($value)) {
            return false;
        }

        $valueDate = $this->convertToDateTime($value, false); 
        $baseDate  = $this->convertToDateTime($this->baseValue, false);

        if (false === $valueDate || false === $baseDate) {
            return false;
        }

        $step = $this->getStep();

        
        
        if ($valueDate == $baseDate) {
            return true;
        }

        
        
        $intervalParts = explode('|', $step->format('%y|%m|%d|%h|%i|%s'));
        $intervalParts = array_map('intval', $intervalParts);
        $partCounts    = array_count_values($intervalParts);

        $unitKeys      = ['years', 'months', 'days', 'hours', 'minutes', 'seconds'];
        $intervalParts = array_combine($unitKeys, $intervalParts);

        
        $absoluteValueDate = new DateTime($valueDate->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));
        $absoluteBaseDate  = new DateTime($baseDate->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));

        $timeDiff  = $absoluteValueDate->diff($absoluteBaseDate, true);
        $diffParts = array_map('intval', explode('|', $timeDiff->format('%y|%m|%d|%h|%i|%s')));
        $diffParts = array_combine($unitKeys, $diffParts);

        if (5 === $partCounts[0]) {
            
            $intervalUnit = 'days';
            $stepValue    = 1;
            foreach ($intervalParts as $key => $value) {
                if (0 !== $value) {
                    $intervalUnit = $key;
                    $stepValue    = $value;
                    break;
                }
            }

            
            if (in_array($intervalUnit, ['years', 'months', 'days'])) {
                switch ($intervalUnit) {
                    case 'years':
                        if (
                            0 === $diffParts['months'] && 0 === $diffParts['days']
                            && 0 === $diffParts['hours'] && 0 === $diffParts['minutes']
                            && 0 === $diffParts['seconds']
                        ) {
                            if (($diffParts['years'] % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                    case 'months':
                        if (
                            0 === $diffParts['days'] && 0 === $diffParts['hours']
                            && 0 === $diffParts['minutes'] && 0 === $diffParts['seconds']
                        ) {
                            $months = ($diffParts['years'] * 12) + $diffParts['months'];
                            if (($months % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                    case 'days':
                        if (
                            0 === $diffParts['hours'] && 0 === $diffParts['minutes']
                            && 0 === $diffParts['seconds']
                        ) {
                            $days = (int) $timeDiff->format('%a'); 
                            if (($days % $stepValue) === 0) {
                                return true;
                            }
                        }
                        break;
                }
                $this->error(self::NOT_STEP);
                return false;
            }

            
            if (in_array($intervalUnit, ['hours', 'minutes', 'seconds'])) {
                
                if (1 === $stepValue) {
                    if (
                        'hours' === $intervalUnit
                        && 0 === $diffParts['minutes'] && 0 === $diffParts['seconds']
                    ) {
                        return true;
                    } elseif ('minutes' === $intervalUnit && 0 === $diffParts['seconds']) {
                        return true;
                    } elseif ('seconds' === $intervalUnit) {
                        return true;
                    }

                    $this->error(self::NOT_STEP);

                    return false;
                }

                
                if (
                    $baseDate->format('Y-m-d') === $valueDate->format('Y-m-d')
                    && $baseDate->format('Y-m-d') === '1970-01-01'
                ) {
                    switch ($intervalUnit) {
                        case 'hours':
                            if (0 === $diffParts['minutes'] && 0 === $diffParts['seconds']) {
                                if (($diffParts['hours'] % $stepValue) === 0) {
                                    return true;
                                }
                            }
                            break;
                        case 'minutes':
                            if (0 === $diffParts['seconds']) {
                                $minutes = ($diffParts['hours'] * 60) + $diffParts['minutes'];
                                if (($minutes % $stepValue) === 0) {
                                    return true;
                                }
                            }
                            break;
                        case 'seconds':
                            $seconds = ($diffParts['hours'] * 60 * 60)
                                       + ($diffParts['minutes'] * 60)
                                       + $diffParts['seconds'];
                            if (($seconds % $stepValue) === 0) {
                                return true;
                            }
                            break;
                    }
                    $this->error(self::NOT_STEP);
                    return false;
                }
            }
        }

        return $this->fallbackIncrementalIterationLogic($baseDate, $valueDate, $intervalParts, $diffParts, $step);
    }

    
    private function fallbackIncrementalIterationLogic(
        DateTimeInterface $baseDate,
        DateTimeInterface $valueDate,
        array $intervalParts,
        array $diffParts,
        DateInterval $step
    ): bool {
        [$minSteps, $requiredIterations] = $this->computeMinStepAndRequiredIterations($intervalParts, $diffParts);
        $minimumInterval                 = $this->computeMinimumInterval($intervalParts, $minSteps);
        $isIncrementalStepping           = $baseDate < $valueDate;

        if (! ($baseDate instanceof DateTime || $baseDate instanceof DateTimeImmutable)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Function %s requires the baseDate to be a DateTime or DateTimeImmutable instance.',
                __FUNCTION__
            ));
        }

        for ($offsetIterations = 0; $offsetIterations < $requiredIterations; $offsetIterations += 1) {
            if ($isIncrementalStepping) {
                $baseDate = $baseDate->add($minimumInterval);
            } else {
                $baseDate = $baseDate->sub($minimumInterval);
            }
        }

        while (
            ($isIncrementalStepping && $baseDate < $valueDate)
            || (! $isIncrementalStepping && $baseDate > $valueDate)
        ) {
            if ($isIncrementalStepping) {
                $baseDate = $baseDate->add($step);
            } else {
                $baseDate = $baseDate->sub($step);
            }

            
            if ($baseDate == $valueDate) {
                return true;
            }
        }

        $this->error(self::NOT_STEP);

        return false;
    }

    
    private function computeMinimumInterval(array $intervalParts, $minSteps): DateInterval
    {
        return new DateInterval(sprintf(
            'P%dY%dM%dDT%dH%dM%dS',
            $intervalParts['years'] * $minSteps,
            $intervalParts['months'] * $minSteps,
            $intervalParts['days'] * $minSteps,
            $intervalParts['hours'] * $minSteps,
            $intervalParts['minutes'] * $minSteps,
            $intervalParts['seconds'] * $minSteps
        ));
    }

    
    private function computeMinStepAndRequiredIterations(array $intervalParts, array $diffParts): array
    {
        $minSteps = $this->computeMinSteps($intervalParts, $diffParts);

        
        
        $maxInteger = min(2 ** 31, PHP_INT_MAX);
        
        $maximumInterval        = max($intervalParts);
        $requiredStepIterations = 1;

        if (($minSteps * $maximumInterval) > $maxInteger) {
            $requiredStepIterations = ceil(($minSteps * $maximumInterval) / $maxInteger);
            $minSteps               = floor($minSteps / $requiredStepIterations);
        }

        return [(int) $minSteps, $minSteps ? (int) $requiredStepIterations : 0];
    }

    
    private function computeMinSteps(array $intervalParts, array $diffParts)
    {
        $intervalMaxSeconds = $this->computeIntervalMaxSeconds($intervalParts);

        return 0 === $intervalMaxSeconds
            ? 0
            : max(floor($this->computeDiffMinSeconds($diffParts) / $intervalMaxSeconds) - 1, 0);
    }

    
    private function computeIntervalMaxSeconds(array $intervalParts): int
    {
        return ($intervalParts['years'] * 60 * 60 * 24 * 366)
            + ($intervalParts['months'] * 60 * 60 * 24 * 31)
            + ($intervalParts['days'] * 60 * 60 * 24)
            + ($intervalParts['hours'] * 60 * 60)
            + ($intervalParts['minutes'] * 60)
            + $intervalParts['seconds'];
    }

    
    private function computeDiffMinSeconds(array $diffParts): int
    {
        return ($diffParts['years'] * 60 * 60 * 24 * 365)
            + ($diffParts['months'] * 60 * 60 * 24 * 28)
            + ($diffParts['days'] * 60 * 60 * 24)
            + ($diffParts['hours'] * 60 * 60)
            + ($diffParts['minutes'] * 60)
            + $diffParts['seconds'];
    }
}
