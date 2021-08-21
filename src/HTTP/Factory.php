<?php

namespace Cphne\PsrTests\HTTP;

use Cphne\PsrTests\HTTP\Factory\StreamFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Factory.
 */
class Factory implements
    UriFactoryInterface,
    ResponseFactoryInterface,
    RequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    ServerRequestFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        $data = parse_url($uri);

        return new Uri(
            $data['scheme'] ?? '',
            $data['user'] ?? null,
            $data['pass'] ?? null,
            $data['host'] ?? '',
            $data['port'] ?? null,
            $data['path'] ?? '',
            $data['query'] ?? null,
            $data['fragment'] ?? null
        );
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $factory = new StreamFactory();

        return new Response(
            $factory->createStream(),
            $code,
            [],
            $reasonPhrase,
        );
    }

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
        $factory = new StreamFactory();
        if (is_string($uri)) {
            $uri = $this->createUri($uri);
        } elseif (!$uri instanceof UriInterface) {
            throw new \InvalidArgumentException('$uri must be either one of string or ' . UriInterface::class);
        }

        return new Request(
            $method,
            $uri,
            $factory->createStreamFromResource(fopen('php://input', 'rb'))
        );
    }

    public function createStream(string $content = ''): StreamInterface
    {
        $factory = new StreamFactory();

        return $factory->createStream($content);
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $factory = new StreamFactory();

        return $factory->createStreamFromFile($filename, $mode);
    }

    /**
     * @param resource $resource
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        $factory = new StreamFactory();

        return $factory->createStreamFromResource($resource);
    }

    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface {
        if (!$stream->isReadable()) {
            throw new \InvalidArgumentException('Stream of UploadedFile must be readable!');
        }
        $size ??= $stream->getSize();

        return new UploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        $uri = ($uri instanceof UriInterface) ? $uri : $this->createUri($uri);

        return new ServerRequest(
            $method,
            $uri,
            $this->createStream(''),
            $serverParams
        );
    }

    public function createServerRequestFromGlobals()
    {
        return $this->createServerRequest(
            $_SERVER['REQUEST_METHOD'],
            Uri::fromServer($_SERVER),
            $_SERVER
        );
    }
}
