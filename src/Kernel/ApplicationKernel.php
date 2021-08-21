<?php

declare(strict_types=1);

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

    private ApplicationConfig $config;

    /**
     * ApplicationKernel constructor.
     */
    public function __construct()
    {
        $dotenv = new DotEnv('.env');
        $dotenv->load();

        $this->config = new ApplicationConfig();
    }


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
        $fqdns = [];
        foreach ($this->config->getServiceDirs() as $dir) {
            if ($dir === "EventDispatcher") {
                $fqdns[] = array_reverse($finder->find($dir));
            } else {
                $fqdns[] = $finder->find($dir);
            }
        }
        $pool = new CacheItemPool();
        $pool->clear();
        $this->container = new Container(array_merge([], ...$fqdns));
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
