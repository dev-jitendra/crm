<?php declare(strict_types=1);



namespace Monolog\Formatter;

use MongoDB\BSON\Type;
use MongoDB\BSON\UTCDateTime;
use Monolog\Utils;
use Monolog\LogRecord;


class MongoDBFormatter implements FormatterInterface
{
    private bool $exceptionTraceAsString;
    private int $maxNestingLevel;
    private bool $isLegacyMongoExt;

    
    public function __construct(int $maxNestingLevel = 3, bool $exceptionTraceAsString = true)
    {
        $this->maxNestingLevel = max($maxNestingLevel, 0);
        $this->exceptionTraceAsString = $exceptionTraceAsString;

        $this->isLegacyMongoExt = extension_loaded('mongodb') && version_compare((string) phpversion('mongodb'), '1.1.9', '<=');
    }

    
    public function format(LogRecord $record): array
    {
        
        $res = $this->formatArray($record->toArray());

        return $res;
    }

    
    public function formatBatch(array $records): array
    {
        $formatted = [];
        foreach ($records as $key => $record) {
            $formatted[$key] = $this->format($record);
        }

        return $formatted;
    }

    
    protected function formatArray(array $array, int $nestingLevel = 0)
    {
        if ($this->maxNestingLevel > 0 && $nestingLevel > $this->maxNestingLevel) {
            return '[...]';
        }

        foreach ($array as $name => $value) {
            if ($value instanceof \DateTimeInterface) {
                $array[$name] = $this->formatDate($value, $nestingLevel + 1);
            } elseif ($value instanceof \Throwable) {
                $array[$name] = $this->formatException($value, $nestingLevel + 1);
            } elseif (is_array($value)) {
                $array[$name] = $this->formatArray($value, $nestingLevel + 1);
            } elseif (is_object($value) && !$value instanceof Type) {
                $array[$name] = $this->formatObject($value, $nestingLevel + 1);
            }
        }

        return $array;
    }

    
    protected function formatObject($value, int $nestingLevel)
    {
        $objectVars = get_object_vars($value);
        $objectVars['class'] = Utils::getClass($value);

        return $this->formatArray($objectVars, $nestingLevel);
    }

    
    protected function formatException(\Throwable $exception, int $nestingLevel)
    {
        $formattedException = [
            'class' => Utils::getClass($exception),
            'message' => $exception->getMessage(),
            'code' => (int) $exception->getCode(),
            'file' => $exception->getFile() . ':' . $exception->getLine(),
        ];

        if ($this->exceptionTraceAsString === true) {
            $formattedException['trace'] = $exception->getTraceAsString();
        } else {
            $formattedException['trace'] = $exception->getTrace();
        }

        return $this->formatArray($formattedException, $nestingLevel);
    }

    protected function formatDate(\DateTimeInterface $value, int $nestingLevel): UTCDateTime
    {
        if ($this->isLegacyMongoExt) {
            return $this->legacyGetMongoDbDateTime($value);
        }

        return $this->getMongoDbDateTime($value);
    }

    private function getMongoDbDateTime(\DateTimeInterface $value): UTCDateTime
    {
        return new UTCDateTime((int) floor(((float) $value->format('U.u')) * 1000));
    }

    
    private function legacyGetMongoDbDateTime(\DateTimeInterface $value): UTCDateTime
    {
        $milliseconds = floor(((float) $value->format('U.u')) * 1000);

        $milliseconds = (PHP_INT_SIZE == 8) 
            ? (int) $milliseconds
            : (string) $milliseconds;

        return new UTCDateTime($milliseconds);
    }
}
