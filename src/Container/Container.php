<?php

namespace Cphne\PsrTests\Container;

use Cphne\PsrTests\Services\Outer;

class Container implements \Psr\Container\ContainerInterface
{
    private array $services = [];

    public function __construct(array $services = [])
    {
        foreach ($services as $fqdn) {
            $service = $this->wire($fqdn);
            $this->services[$fqdn] = $service;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id)
    {
        if (!array_key_exists($id, $this->services)) {
            throw new NotFoundException('Service with id ' . $id . ' could not be found.');
        }

        try {
            return $this->services[$id];
        } catch (\Throwable $th) {
            throw new ContainerException('Error while retrieving service.', 500, $th);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id)
    {
        return array_key_exists($id, $this->services);
    }

    private function wire(string $fqdn)
    {
        $reflection = new \ReflectionClass($fqdn);
        $test = $reflection->getConstructor();
        if ((is_null($test))) {
            return new $fqdn();
        }
        $params = $test->getParameters();
        $services = [];
        foreach ($params as $param) {
            $type = $param->getType()->getName();
            $service = $this->wire($type);
            $this->services[$type] = $service;
            $services[] = $service;
        }

        return new $fqdn(...$services);
    }
}
