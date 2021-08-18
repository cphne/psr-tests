<?php

namespace Cphne\PsrTests\HTTP\Factory;

use Cphne\PsrTests\HTTP\Stream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createStream(string $content = ''): StreamInterface
    {
        // TODO check fopen
        $stream = fopen('php://temp', 'rb+');
        fwrite($stream, $content);
        fseek($stream, 0);

        return new Stream($stream);
    }

    /**
     * {@inheritDoc}
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        throw new \Exception('Not implemented yet!');
        // TODO: Implement createStreamFromFile() method.
    }

    /**
     * {@inheritDoc}
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        // TODO: Implement createStreamFromResource() method.
        throw new \Exception('Not implemented yet!');
    }
}
