<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Server;

use Cphne\PsrTests\Container\ServiceCainInterface;
use Cphne\PsrTests\EventDispatcher\EventDispatcher;
use Cphne\PsrTests\Events\ExceptionEvent;
use Cphne\PsrTests\Events\RequestEvent;
use Cphne\PsrTests\Events\ResponseEvent;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Logger\StdoutLogger;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class RequestHandler.
 */
class RequestHandler implements RequestHandlerInterface, ServiceCainInterface
{

    private array $middleware = [];

    private bool $resolved = false;

    public function __construct(
        private Factory $factory,
        private StdoutLogger $logger,
        private EventDispatcher $dispatcher,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->dispatcher->dispatch(new RequestEvent($request));
        $response = $this->factory->createResponse();
        try {
            while (($middleware = array_shift($this->middleware)) !== null) {
                /* @var MiddlewareInterface $middleware */
                $middleware->setResponse($response);
                $response = $middleware->process($request, $this);
            }
        } catch (\Throwable $exception) {
            $event = new ExceptionEvent($exception);
            $event->setResponse($response);
            $this->dispatcher->dispatch($event);
            $response = $event->getResponse();
        }
        if (!$this->resolved) {
            $this->dispatcher->dispatch(new ResponseEvent($response));
            $this->sendResponse($response);
            $this->resolved = true;
        }
        return $response;
    }


    #[ArrayShape([self::TAG_MIDDLEWARE => 'string'])] public function getChains(): array
    {
        return [self::TAG_MIDDLEWARE => 'addMiddleware'];
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function addMiddleware(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;
    }


    private function sendResponse(ResponseInterface $response): bool
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $value) {
            header($name . ': ' . implode(', ', $value), true, $response->getStatusCode());
        }
        echo (string)$response->getBody();

        return true;
    }
}
