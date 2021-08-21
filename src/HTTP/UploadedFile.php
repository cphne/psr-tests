<?php

namespace Cphne\PsrTests\HTTP;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFile
 * @package Cphne\PsrTests\HTTP
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * @var bool
     */
    private bool $moved = false;

    /**
     * UploadedFile constructor.
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     */
    public function __construct(
        protected StreamInterface $stream,
        protected ?int $size,
        protected int $error,
        protected ?string $clientFilename,
        protected ?string $clientMediaType
    ) {
    }

    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        if ($this->moved) {
            throw $this->getMovedException();
        }
        return $this->stream;
    }

    /**
     * @param string $targetPath
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw $this->getMovedException();
        }
        if (!is_writable(dirname($targetPath))) {
            throw new \InvalidArgumentException("Target path is not writeable.");
        }
        $factory = new Factory();
        $destStream = $factory->createStreamFromFile($targetPath, "w+");
        while (!$this->stream->eof()) {
            $destStream->write($this->stream->read(1024));
        }
        // TODO Check if move was successful!
        $this->stream->detach();
        $this->moved = true;
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * @return string|null
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * @return \RuntimeException
     */
    private function getMovedException()
    {
        return new \RuntimeException("File has been moved previously.");
    }
}
