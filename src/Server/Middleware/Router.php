<?php


namespace Cphne\PsrTests\Server\Middleware;


use Cphne\PsrTests\Attributes\Router\Route;
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
class Router implements MiddlewareInterface
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
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->logger->info("Router pass.");
        $uri = $request->getUri();
        if (!array_key_exists($uri->getPath(), $this->routes)) {
            $response = $handler->getResponse()
                ->withStatus(404)
                ->withBody($this->factory->createStream("<h1>404</h1>"))
                ->setProcessingFinished(true);
            return $response;
        }
        $meta = $this->routes[$uri->getPath()];
        $controller = new $meta["controller"]();
        $method = $meta["method"];
        $content = $controller->$method();
        $response = $handler->getResponse()
            ->withStatus(200)
            ->withBody($this->factory->createStream($content))
            ->setProcessingFinished(true);
        return $response;
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
