<?php



namespace Symfony\Component\HttpClient\Response;

use function GuzzleHttp\Promise\promise_for;
use GuzzleHttp\Promise\PromiseInterface as GuzzlePromiseInterface;
use Http\Promise\Promise as HttplugPromiseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;


final class HttplugPromise implements HttplugPromiseInterface
{
    private $promise;

    public function __construct(GuzzlePromiseInterface $promise)
    {
        $this->promise = $promise;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null): self
    {
        return new self($this->promise->then(
            $this->wrapThenCallback($onFulfilled),
            $this->wrapThenCallback($onRejected)
        ));
    }

    public function cancel(): void
    {
        $this->promise->cancel();
    }

    
    public function getState(): string
    {
        return $this->promise->getState();
    }

    
    public function wait($unwrap = true)
    {
        $result = $this->promise->wait($unwrap);

        while ($result instanceof HttplugPromiseInterface || $result instanceof GuzzlePromiseInterface) {
            $result = $result->wait($unwrap);
        }

        return $result;
    }

    private function wrapThenCallback(?callable $callback): ?callable
    {
        if (null === $callback) {
            return null;
        }

        return static function ($value) use ($callback) {
            return promise_for($callback($value));
        };
    }
}
