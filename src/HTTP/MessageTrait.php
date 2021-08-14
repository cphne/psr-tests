<?php


namespace Cphne\PsrTests\HTTP;


use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{

    protected array $server;

    protected string $protocolVersion;

    protected array $headers;

    protected StreamInterface $body;

    /**
     * @inheritDoc
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
    {
        return array_key_exists($name, $this->headers); // TODO Case-insensitive header field name
    }

    /**
     * @inheritDoc
     */
    public function getHeader($name)
    {
        return ($this->hasHeader($name)) ? $this->headers[$name] : []; // TODO Case-insensitive header field name
    }

    /**
     * @inheritDoc
     */
    public function getHeaderLine($name): string
    {
        return ($this->hasHeader($name)) ? implode(
            ",",
            $this->getHeader($name)
        ) : ""; // TODO Case-insensitive header field name
    }

    /**
     * @inheritDoc
     */
    public function withHeader($name, $value): static
    {
        $headers = $this->headers;
        $headers[$name] = $value;
        return $this->newInstanceWithHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function withAddedHeader($name, $value): static
    {
        $headers = $this->headers;
        if (!array_key_exists($name, $headers)) {
            $headers[$name] = [$value];
        } else {
            $headers[$name][] = $value;
        }
        return $this->newInstanceWithHeaders($headers);
    }

    /**
     * @inheritDoc
     */
    public function withoutHeader($name): static
    {
        $headers = $this->headers;
        if (array_key_exists($name, $headers)) {
            unset($headers["name"]);
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
        return new static($this->server, $this->headers, $body);
    }

    /**
     * @param string $protocolVersion
     */
    protected function setProtocolVersion(string $protocolVersion): void
    {
        if (str_starts_with($protocolVersion, "HTTP/")) {
            $this->protocolVersion = substr($protocolVersion, -3);
        } else {
            $this->protocolVersion = $protocolVersion;
        }
    }

    protected function setBody(StreamInterface|string|null $body = null) {
        if ($body instanceof StreamInterface) {
            $this->body = $body;
        } elseif (is_scalar($body)) {
            // TODO check fopen
            $stream = fopen('php://temp', 'rb+');
            if ($body !== '') {
                fwrite($stream, (string)$body);
                fseek($stream, 0);
            }
            $this->body = new Stream($stream);
        } else {
            $this->body = new Stream();
        }
    }

    protected function newInstanceWithHeaders(array $headers) {
        $new = clone $this;
        $new->headers = $headers;
        return $new;
    }

}
