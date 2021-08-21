<?php

namespace Cphne\PsrTests\HTTP\Factory;

use Cphne\PsrTests\HTTP\Stream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class StreamFactory.
 */
class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritDoc}
     * @deprecated use Factory instead
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = fopen('php://temp', 'w+b');
        if (!is_resource($stream)) {
            throw new \LogicException('PHP temp resource could not be opened');
        }
        fwrite($stream, $content);
        fseek($stream, 0);

        return $this->createStreamFromResource($stream);
    }

    /**
     * {@inheritDoc}
     * @deprecated use Factory instead
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        $file = fopen($filename, $mode . 'b');

        return $this->createStreamFromResource($file);
    }

    /**
     * {@inheritDoc}
     * @deprecated use Factory instead
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        if (!is_resource($resource)) {
            throw new \InvalidArgumentException('Resource is not a valid resource.');
        }

        return new Stream($resource);
    }
}
