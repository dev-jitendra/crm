<?php



namespace Symfony\Component\HttpClient\Internal;

use Symfony\Component\HttpClient\Response\CurlResponse;


final class PushedResponse
{
    public $response;

    
    public $requestHeaders;

    public $parentOptions = [];

    public $handle;

    public function __construct(CurlResponse $response, array $requestHeaders, array $parentOptions, $handle)
    {
        $this->response = $response;
        $this->requestHeaders = $requestHeaders;
        $this->parentOptions = $parentOptions;
        $this->handle = $handle;
    }
}
