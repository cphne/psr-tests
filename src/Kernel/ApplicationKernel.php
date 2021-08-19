<?php

namespace Cphne\PsrTests\Kernel;

use Cphne\PsrTests\Container\Container;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Server\RequestHandler;
use Psr\Container\ContainerInterface;

class ApplicationKernel implements KernelInterface
{
    private ContainerInterface $container;
    public function boot()
    {
        $services = [RequestHandler::class];
        $this->container = new Container($services);
    }

    public function run()
    {
        $factory = new Factory();
        $request = $factory->createServerRequestFromGlobals();
        $handler = $this->container->get(RequestHandler::class);
        return $handler->handle($request);
    }

    public function cleanup()
    {
        // TODO: Implement cleanup() method.
    }
}
