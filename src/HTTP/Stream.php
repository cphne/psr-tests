<?php

namespace Cphne\PsrTests\HTTP;




use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class Stream
 * @package Cphne\PsrTests\HTTP
 */
class Stream implements StreamInterface{
    private $resource;

    private array|false $stats = false;

    /**
     * Stream constructor.
     * @param null $body
     */
    public function __construct($body = null)
    {
        $this->resource = (!is_null($body)) ? $body : fopen('php://input', 'rb');
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            $this->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $this->seek(0);
        return $this->getContents();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        fclose($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function detach()
    {
        $result = $this->resource;
        $this->resource = null;
        $this->stats = false;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if ($this->stats !== false) {
            return $this->stats["size"];
        }

        $stats = fstat($this->resource);
        if ($stats !== false) {
            $this->stats = $stats;
            return $this->getSize();
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function tell(): int
    {
        $position = ftell($this->resource);
        return ($position !== false) ? $position : throw new RuntimeException(
            "Position of Stream could not be determined"
        );
    }

    /**
     * @inheritDoc
     */
    public function eof(): bool
    {
        return feof($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function isSeekable()
    {
        $meta = stream_get_meta_data($this->resource);
        return $meta["seekable"];
    }

    /**
     * @inheritDoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new RuntimeException("Position in the Stream could not be seeked");
        }
    }

    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        if (!rewind($this->resource)) {
            throw new RuntimeException("Rewind failed!");
        }
    }

    /**
     * @inheritDoc
     */
    public function isWritable()
    {
        return is_writable($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function write($string)
    {
        return fwrite($this->resource, $string);
    }

    /**
     * @inheritDoc
     */
    public function isReadable()
    {
        return is_readable($this->resource);
    }

    /**
     * @inheritDoc
     */
    public function read($length): string
    {
        $data = "";
        while (!$this->eof() && ($buffer = fgets($this->resource, $length)) !== false) {
            $data .= $buffer;
        }
        if (!$buffer) {
            throw new RuntimeException("Error while reading data from Stream!");
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getContents(): string
    {
        $data = "";
        while (!$this->eof() && ($buffer = fgets($this->resource)) !== false) {
            $data .= $buffer;
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->resource);
        return (is_null($key)) ? $meta : $meta[$key];
    }
}
