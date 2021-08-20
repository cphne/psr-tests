<?php


namespace Cphne\PsrTests\Server\Middleware;


use Cphne\PsrTests\Logger\StdoutLogger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Security
 * @package Cphne\PsrTests\Server\Middleware
 */
class Security implements MiddlewareInterface
{

    /**
     * Security constructor.
     * @param StdoutLogger $logger
     */
    public function __construct(private StdoutLogger $logger)
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->info("Security pass.");
        return $handler->getResponse();
    }


}
