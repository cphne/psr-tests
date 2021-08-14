<?php

namespace Cphne\PsrTests\HTTP;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{

    public const SERVER_REQUEST_URI = "REQUEST_URI";
    public const SERVER_REQUEST_METHOD = "REQUEST_METHOD";

    protected UriInterface $uri;

    public function __construct(array $server = [], array $headers = [], StreamInterface|string|null $body = null, UriInterface $uri = null)
    {
        $this->uri = $uri ?? Uri::fromServer($server);
        parent::__construct($server, $headers, $body);
    }

    public function getRequestTarget()
    {
        return $this->server[self::SERVER_REQUEST_URI] ?? "/";
    }

    public function withRequestTarget($requestTarget)
    {
        $server = $this->server;
        $server[self::SERVER_REQUEST_URI] = $requestTarget;
        return new static($server, $this->headers, $this->body);
    }

    public function getMethod()
    {
        return $this->server[self::SERVER_REQUEST_METHOD];
    }

    public function withMethod($method)
    {
        $server = $this->server;
        $server[self::SERVER_REQUEST_METHOD] = $method;
        return new static($server, $this->headers, clone $this->body);
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if(($preserveHost && !empty($this->getUri()->getHost())) || empty($uri->getHost())) {
            $uri = $uri->withHost($this->getUri()->getHost());
        }
        return new static($this->server, $this->headers, $this->body, $uri);
    }

}
