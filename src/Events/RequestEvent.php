<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Events;

use Psr\Http\Message\RequestInterface;

/**
 * Class RequestEvent
 * @package Cphne\PsrTests\Events
 */
class RequestEvent extends AbstractStoppableEvent
{
    /**
     * RequestEvent constructor.
     * @param RequestInterface $request
     */
    public function __construct(private RequestInterface $request)
    {
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
