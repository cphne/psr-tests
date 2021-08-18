<?php


namespace Cphne\PsrTests\HTTP;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;


/**
 * Class Message
 * @package Cphne\PsrTests\HTTP
 */
class Message implements MessageInterface
{
    public const SERVER_PROTOCOL_VERSION = "SERVER_PROTOCOL";

    protected array $headers;

    /**
     * Message constructor.
     * @param StreamInterface $body
     * @param array $headers
     * @param string $protocolVersion
     */
    public function __construct(
        protected StreamInterface $body,
        array $headers = [],
        protected string $protocolVersion = "1.1"
    ) {
        $this->setHeaders($headers);
    }


    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritDoc
     */
    public function withProtocolVersion($version): static
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return ($this->headers !== false) ? $this->headers : [];
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function hasHeader($name): bool
    {// TODO optimize
        $lowerKeys = array_change_key_case($this->headers, CASE_LOWER);
        return array_key_exists(strtolower($name), $lowerKeys);
    }

    /**
     * @inheritDoc
     */
    #[Pure] public function getHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return [];
        } // TODO optimize
        $lowerKeys = array_change_key_case($this->headers, CASE_LOWER);
        return $lowerKeys[strtolower($name)];
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        return ($this->hasHeader($name)) ? implode(
            ", ",
            $this->getHeader($name)
        ) : ""; // TODO Case-insensitive header field name
    }

    /**
     * @inheritDoc
     */
    public function withHeader(
        $name,
        $value
    ): static // TODO \InvalidArgumentException for invalid header names or values.
    {
        $key = $this->getHeaderKey($name);
        $key ?? throw new InvalidArgumentException("Header " . $name . " does not exist in original array");
        $headers[$key] = $value;
        return $this->newInstanceWithHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value): static
    {
        $key = $this->getHeaderKey($name) ?? $name;
        $headers = $this->headers;
        if (is_array($value)) {
            $headers[$key] = array_merge($headers[$key] ?? [], $value);
        } else {
            $headers[$key] = array_merge($headers[$key] ?? [], explode(", ", $value));
        }
        return $this->newInstanceWithHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name): static
    {
        $headers = $this->headers;
        $key = $this->getHeaderKey($name);
        if (!is_null($key)) {
            unset($headers[$key]);
        }
        return $this->newInstanceWithHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function withBody(StreamInterface $body): static
    {
        $new = clone $this;
        $new->body = $body;
        return $new;
    }


    protected function setHeaders(array $headers)
    {
        $this->headers = [];
        foreach ($headers as $key => $value) {
            $this->headers[$key] = (is_array($value)) ? $value : explode(", ", $value);
        }
    }

    protected function newInstanceWithHeaders(array $headers)
    {
        $new = clone $this;
        $new->setHeaders($headers);
        return $new;
    }

    #[Pure] protected function getHeaderKey(string $name): ?string
    {
        $keys = array_keys($this->headers);
        foreach ($keys as $key) {
            if (strtolower($key) === strtolower($name)) {
                return $key;
            }
        }
        return null;
    }
}
