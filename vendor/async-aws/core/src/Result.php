<?php

declare(strict_types=1);

namespace AsyncAws\Core;

use AsyncAws\Core\Exception\Http\HttpException;
use AsyncAws\Core\Exception\Http\NetworkException;


class Result
{
    
    protected $awsClient;

    
    protected $input;

    
    private $initialized = false;

    
    private $response;

    
    private $prefetchResults = [];

    public function __construct(Response $response, ?AbstractApi $awsClient = null, ?object $request = null)
    {
        $this->response = $response;
        $this->awsClient = $awsClient;
        $this->input = $request;
    }

    public function __destruct()
    {
        while (!empty($this->prefetchResults)) {
            array_shift($this->prefetchResults)->cancel();
        }
    }

    
    final public function resolve(?float $timeout = null): bool
    {
        return $this->response->resolve($timeout);
    }

    
    final public static function wait(iterable $results, ?float $timeout = null, bool $downloadBody = false): iterable
    {
        $resultMap = [];
        $responses = [];
        foreach ($results as $index => $result) {
            $responses[$index] = $result->response;
            $resultMap[$index] = $result;
        }

        foreach (Response::wait($responses, $timeout, $downloadBody) as $index => $response) {
            yield $index => $resultMap[$index];
        }
    }

    
    final public function info(): array
    {
        return $this->response->info();
    }

    final public function cancel(): void
    {
        $this->response->cancel();
    }

    final protected function registerPrefetch(self $result): void
    {
        $this->prefetchResults[spl_object_id($result)] = $result;
    }

    final protected function unregisterPrefetch(self $result): void
    {
        unset($this->prefetchResults[spl_object_id($result)]);
    }

    final protected function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->resolve();
        $this->initialized = true;
        $this->populateResult($this->response);
    }

    protected function populateResult(Response $response): void
    {
    }
}
