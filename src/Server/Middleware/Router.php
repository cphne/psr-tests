<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Server\Middleware;

use Cphne\PsrTests\Attributes\Router\Route;
use Cphne\PsrTests\Controller\ControllerResponseInterface;
use Cphne\PsrTests\Exceptions\HTTP\NotFoundException;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Logger\StdoutLogger;
use Cphne\PsrTests\Services\Finder\ClassFinder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class Router
 * @package Cphne\PsrTests\Server\Middleware
 */
class Router extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * @var array
     */
    private array $routes = [];

    /**
     * Router constructor.
     * @param StdoutLogger $logger
     * @param ClassFinder $finder
     * @param Factory $factory
     */
    public function __construct(private StdoutLogger $logger, private ClassFinder $finder, private Factory $factory)
    {
        $this->controller = $this->finder->find("Controller");
        $this->getRoutes();
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->info("Router pass.");
        $uri = $request->getUri();
        if (!array_key_exists($uri->getPath(), $this->routes)) {
            throw new NotFoundException();
        }
        $meta = $this->routes[$uri->getPath()];
        $controller = new $meta["controller"]();
        $method = $meta["method"];
        $controllerResponse = $controller->$method();
        /* @var $controllerResponse ControllerResponseInterface */
        return $this->response
            ->withStatus($controllerResponse->getCode())
            ->withBody($this->factory->createStream($controllerResponse->getBody()));
    }

    private function getRoutes()
    {
        foreach ($this->controller as $controllerFqdn) {
            try {
                $reflection = new \ReflectionClass($controllerFqdn);
            } catch (\ReflectionException $exception) {
                $this->logger->error($exception->getMessage());
                continue;
            }

            foreach ($reflection->getMethods() as $reflectionMethod) {
                foreach ($reflectionMethod->getAttributes() as $reflectionAttribute) {
                    $attribute = $reflectionAttribute->newInstance();
                    if ($attribute instanceof Route) {
                        $this->routes[$attribute->getRoute()] = [
                            "controller" => $controllerFqdn,
                            "method" => $reflectionMethod->getName()
                        ];
                    }
                }
            }
        }
    }
}
