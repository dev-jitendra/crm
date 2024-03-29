<?php

namespace React\Promise;

interface PromiseInterface
{
    
    public function then(callable $onFulfilled = null, callable $onRejected = null, callable $onProgress = null);
}
