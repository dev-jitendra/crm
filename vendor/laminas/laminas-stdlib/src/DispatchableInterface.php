<?php

declare(strict_types=1);

namespace Laminas\Stdlib;

interface DispatchableInterface
{
    
    public function dispatch(RequestInterface $request, ?ResponseInterface $response = null);
}
