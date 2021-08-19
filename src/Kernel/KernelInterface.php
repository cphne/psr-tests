<?php


namespace Cphne\PsrTests\Kernel;


interface KernelInterface
{
    public function boot();

    public function run();

    public function cleanup();
}
