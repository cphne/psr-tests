<?php

namespace Cphne\PsrTests\HTTP;

use Cphne\PsrTests\Exceptions\NotImplementedException;
use Cphne\PsrTests\Services\Deserializer\Deserializer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{

    protected array $attributes = [];

    protected array $cookieParams = [];

    protected ?array $queryParams = null;

    protected array $uploadedFiles = [];

    protected null|array|object $parsedBody = null;

    public function __construct(
        string $method,
        UriInterface $uri,
        StreamInterface $body,
        array $headers = [],
        string $protocolVersion = '1.1',
        protected array $serverParams = []
    ) {
        parent::__construct($method, $uri, $body, $headers, $protocolVersion);
        $this->parsedBody = $this->parseBody();
    }

    public function getServerParams()
    {
        return $this->serverParams;
    }

    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    public function withCookieParams(array $cookies)
    {
        $new = clone $this;
        $new->cookieParams = $cookies;
        return $new;
    }

    public function getQueryParams()
    {
        if (!is_null($this->queryParams)) {
            return $this->queryParams;
        }
        $params = [];
        parse_str($this->getUri()->getQuery(), $params);
        return $params;
    }

    public function withQueryParams(array $query)
    {
        $new = clone $this;
        $new->queryParams = $query;
        return $new;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $new = clone $this;
        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data)
    {
        if (!is_array($data) && !is_object($data) && !is_null($data)) {
            throw new \InvalidArgumentException("Data must be deserialized. Type of either null, array or object");
        }
        $new = clone $this;
        $new->parsedBody = $data;
        return $new;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    public function withAttribute($name, $value)
    {
        $new = clone $this;
        $new->attributes[$name] = $value;
        return $new;
    }

    public function withoutAttribute($name)
    {
        $new = clone $this;
        if (array_key_exists($name, $new->attributes)) {
            unset($new->attributes[$name]);
        }
        return $new;
    }

    protected function parseBody()
    {
        $deserializer = new Deserializer();
        $contentType = $this->getHeader("content-type");
        if (empty($contentType)) {
            return;
        }
        $parsedBody = null;
        $contentType = array_shift($contentType); // TODO Try all content Types?
        $body = (string)$this->getBody();
        switch ($contentType) {
            case "application/json":
                $parsedBody = $deserializer->json($body);
                break;
            case "application/x-www-form-urlencoded":
            case "multipart/form-data":
                if ($this->getMethod() === "POST") {
                    $parsedBody = $_POST;
                } else {
                    $parsedBody = $deserializer->url($body);
                }
                break;
            default:
                throw new NotImplementedException();
        }
        return $parsedBody;
    }
}
