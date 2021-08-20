<?php

namespace Cphne\PsrTests\HTTP;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class Response
 * @package Cphne\PsrTests\HTTP
 */
class Response extends Message implements ResponseInterface
{
    protected string $reasonPhrase;

    protected bool $processingFinished = false;

    protected array $phrases = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    public function __construct(
        StreamInterface $body,
        protected int $statusCode = 200,
        protected array $headers = [],
        ?string $reasonPhrase = null,
        string $protocolVersion = "1.1"
    ) {
        parent::__construct($body, $this->headers, $protocolVersion);
        $this->reasonPhrase = empty($reasonPhrase) ? $this->phrases[$this->statusCode] : $reasonPhrase;
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
        $new = clone $this;
        $new->statusCode = $code;
        $new->reasonPhrase = empty($reasonPhrase) ? $this->phrases[$code] : $reasonPhrase;
        return $new;
    }

    /**
     * @inheritDoc
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . implode(", ", $value), true, $this->statusCode);
        }
        echo $this->body->getContents();
    }

    /**
     * @return bool
     */
    public function isProcessingFinished(): bool
    {
        return $this->processingFinished;
    }

    /**
     * @param bool $processingFinished
     * @return Response
     */
    public function setProcessingFinished(bool $processingFinished): Response
    {
        $this->processingFinished = $processingFinished;
        return $this;
    }


}
