<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Events;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseEvent
 * @package Cphne\PsrTests\Events
 */
class ResponseEvent extends AbstractStoppableEvent
{

    /**
     * ResponseEvent constructor.
     * @param ResponseInterface $response
     */
    public function __construct(private ResponseInterface $response)
    {
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
