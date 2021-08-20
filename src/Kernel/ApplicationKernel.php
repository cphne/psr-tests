<?php

namespace Cphne\PsrTests\Kernel;

use Cphne\PsrTests\Cache\CacheItemPool;
use Cphne\PsrTests\Container\Container;
use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Server\RequestHandler;
use Cphne\PsrTests\Services\Finder\ClassFinder;
use ErrorException;
use Psr\Container\ContainerInterface;

/**
 * Class ApplicationKernel
 * @package Cphne\PsrTests\Kernel
 */
class ApplicationKernel implements KernelInterface
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * @throws ErrorException
     */
    public function boot()
    {
        set_error_handler(
            static function ($errno, $errstr, $errfile, $errline) {
                // error was suppressed with the @-operator
                if (0 === error_reporting()) {
                    return false;
                }

                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            }
        );


        ob_start();
        $finder = new ClassFinder();
        $fqdns = $finder->find('Server');
        $pool = new CacheItemPool();
        $pool->clear();
        $this->container = new Container($fqdns);
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $factory = new Factory();
        $request = $factory->createServerRequestFromGlobals();
        $handler = $this->container->get(RequestHandler::class);
        return $handler->handle($request);
    }

    /**
     *
     */
    public function cleanup()
    {
        ob_end_flush();
        // TODO: Implement cleanup() method.
    }
}
