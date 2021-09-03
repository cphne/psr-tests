<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Server\Middleware;

use Psr\Http\Message\ResponseInterface;

/**
 * Class AbstractMiddleware
 * @package Cphne\PsrTests\Server\Middleware
 */
abstract class AbstractMiddleware
{

    protected ?ResponseInterface $response;

    /**
     * @param ResponseInterface|null $response
     * @return AbstractMiddleware
     */
    public function setResponse(?ResponseInterface $response): AbstractMiddleware
    {
        $this->response = $response;
        return $this;
    }
}
