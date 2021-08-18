<?php

namespace Cphne\PsrTests\HTTP;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request.
 */
class Request extends Message implements RequestInterface
{

    protected ?string $requestTarget = null;

    public function __construct(
        protected string $method,
        private UriInterface $uri,
        StreamInterface $body,
        array $headers = [],
        string $protocolVersion = '1.1'
    ) {
        parent::__construct($body, $headers, $protocolVersion);
    }

    public function getRequestTarget()
    {
        if (!is_null($this->requestTarget)) {
            return $this->requestTarget;
        }
        $target = $this->uri->getPath();
        if ($target === '') {
            $target = '/';
        }
        if ($this->uri->getQuery() !== '') {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    public function withRequestTarget($requestTarget)
    {
        $new = clone $this;
        $new->requestTarget = $requestTarget;
        return $new;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    public function withMethod($method)
    {
        $new = clone $this;
        $new->method = $method;
        return $new;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        if (($preserveHost && !empty($this->getUri()->getHost())) || empty($uri->getHost())) {
            $uri = $uri->withHost($this->getUri()->getHost());
        }
        $new = clone $this;
        $new->uri = $uri;
        return $new;
    }
}
