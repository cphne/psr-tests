<?php

namespace Cphne\PsrTests\HTTP;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Factory.
 */
class Factory implements RequestFactoryInterface, ResponseFactoryInterface
{
    /**
     * Create a new request.
     *
     * @param string              $method the HTTP method associated with the request
     * @param string|UriInterface $uri    The URI associated with the request. If
     *                                    the value is a string, the factory MUST create a UriInterface
     *                                    instance based on it.
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        // TODO: Implement createRequest() method.
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        // TODO: Implement createResponse() method.
    }
}
