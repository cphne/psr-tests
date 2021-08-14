<?php


namespace Cphne\PsrTests\HTTP;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{

    use MessageTrait;

    protected string $reasonPhrase;

    public function __construct(
        StreamInterface|string|null $body,
        protected int $statusCode = 200,
        protected array $headers = [],
        ?string $reasonPhrase = null,
        protected string $protocolVersion = "1.1"
    )
    {
        $this->setBody($body);
        $this->reasonPhrase = $reasonPhrase ?? "This is a TODO item"; // TODO Check default phrase
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        // TODO: Implement withStatus() method.
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
       return $this->reasonPhrase;
    }

    public function send() {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name.': '.$value, true, $this->statusCode);
        }
        echo $this->body->getContents();
    }

}
