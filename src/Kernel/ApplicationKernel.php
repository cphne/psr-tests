<?php

namespace Cphne\PsrTests\Kernel;

use Cphne\PsrTests\HTTP\Factory;
use Cphne\PsrTests\Server\RequestHandler;

class ApplicationKernel implements KernelInterface
{
    public function boot()
    {
        // TODO: Implement boot() method.
    }

    public function run()
    {
        $factory = new Factory();
        $request = $factory->createServerRequestFromGlobals();
        $handler = new RequestHandler();

        return $handler->handle($request);
    }

    public function cleanup()
    {
        // TODO: Implement cleanup() method.
    }
}
