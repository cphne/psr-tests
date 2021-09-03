<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ExceptionEvent
 * @package Cphne\PsrTests\Events
 */
class ExceptionEvent extends AbstractStoppableEvent
{
    private ?ResponseInterface $response;

    /**
     * ExceptionEvent constructor.
     * @param \Throwable $throwable
     */
    public function __construct(private \Throwable $throwable)
    {
    }

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface|null $response
     * @return ExceptionEvent
     */
    public function setResponse(?ResponseInterface $response): ExceptionEvent
    {
        $this->response = $response;
        return $this;
    }

}
