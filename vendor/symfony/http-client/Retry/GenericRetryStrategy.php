<?php



namespace Symfony\Component\HttpClient\Retry;

use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class GenericRetryStrategy implements RetryStrategyInterface
{
    public const IDEMPOTENT_METHODS = ['GET', 'HEAD', 'PUT', 'DELETE', 'OPTIONS', 'TRACE'];
    public const DEFAULT_RETRY_STATUS_CODES = [
        0 => self::IDEMPOTENT_METHODS, 
        423,
        425,
        429,
        500 => self::IDEMPOTENT_METHODS,
        502,
        503,
        504 => self::IDEMPOTENT_METHODS,
        507 => self::IDEMPOTENT_METHODS,
        510 => self::IDEMPOTENT_METHODS,
    ];

    private $statusCodes;
    private $delayMs;
    private $multiplier;
    private $maxDelayMs;
    private $jitter;

    
    public function __construct(array $statusCodes = self::DEFAULT_RETRY_STATUS_CODES, int $delayMs = 1000, float $multiplier = 2.0, int $maxDelayMs = 0, float $jitter = 0.1)
    {
        $this->statusCodes = $statusCodes;

        if ($delayMs < 0) {
            throw new InvalidArgumentException(sprintf('Delay must be greater than or equal to zero: "%s" given.', $delayMs));
        }
        $this->delayMs = $delayMs;

        if ($multiplier < 1) {
            throw new InvalidArgumentException(sprintf('Multiplier must be greater than or equal to one: "%s" given.', $multiplier));
        }
        $this->multiplier = $multiplier;

        if ($maxDelayMs < 0) {
            throw new InvalidArgumentException(sprintf('Max delay must be greater than or equal to zero: "%s" given.', $maxDelayMs));
        }
        $this->maxDelayMs = $maxDelayMs;

        if ($jitter < 0 || $jitter > 1) {
            throw new InvalidArgumentException(sprintf('Jitter must be between 0 and 1: "%s" given.', $jitter));
        }
        $this->jitter = $jitter;
    }

    public function shouldRetry(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): ?bool
    {
        $statusCode = $context->getStatusCode();
        if (\in_array($statusCode, $this->statusCodes, true)) {
            return true;
        }
        if (isset($this->statusCodes[$statusCode]) && \is_array($this->statusCodes[$statusCode])) {
            return \in_array($context->getInfo('http_method'), $this->statusCodes[$statusCode], true);
        }
        if (null === $exception) {
            return false;
        }

        if (\in_array(0, $this->statusCodes, true)) {
            return true;
        }
        if (isset($this->statusCodes[0]) && \is_array($this->statusCodes[0])) {
            return \in_array($context->getInfo('http_method'), $this->statusCodes[0], true);
        }

        return false;
    }

    public function getDelay(AsyncContext $context, ?string $responseContent, ?TransportExceptionInterface $exception): int
    {
        $delay = $this->delayMs * $this->multiplier ** $context->getInfo('retry_count');

        if ($this->jitter > 0) {
            $randomness = $delay * $this->jitter;
            $delay = $delay + random_int(-$randomness, +$randomness);
        }

        if ($delay > $this->maxDelayMs && 0 !== $this->maxDelayMs) {
            return $this->maxDelayMs;
        }

        return (int) $delay;
    }
}
