<?php


namespace Cphne\PsrTests\HTTP;


use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Factory implements RequestFactoryInterface, ResponseFactoryInterface
{
    public function createRequest(string $method, $uri): RequestInterface
    {
        // TODO: Implement createRequest() method.
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        // TODO: Implement createResponse() method.
    }


}
