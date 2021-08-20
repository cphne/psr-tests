<?php

namespace Cphne\PsrTests\Container;

use Cphne\PsrTests\Cache\CacheItemPool;
use Cphne\PsrTests\Services\Outer;
use Psr\Http\Server\MiddlewareInterface;

class Container implements \Psr\Container\ContainerInterface
{
    private array $services = [];

    protected array $tagMapping = [
        ServiceCainInterface::TAG_MIDDLEWARE => MiddlewareInterface::class,
    ];

    protected array $taggedServices = [];

    public function __construct(array $services = [])
    {
        $pool = new CacheItemPool();

        foreach ($services as $possibleTaggedService) {
            $reflection = new \ReflectionClass($possibleTaggedService);
            $interfaces = array_keys($reflection->getInterfaces());
            foreach ($this->tagMapping as $tag => $taggedInterface) {
                $key = array_search($taggedInterface, $interfaces, true);
                if ($key !== false) {
                    $this->taggedServices[$tag][] = $possibleTaggedService;
                }
            }
        }

        foreach ($services as $fqdn) {
            $item = $pool->getItem($fqdn);
            if (!$item->isHit()) {
                $service = $this->wire($fqdn);
                $item->set($service);
                $pool->saveDeferred($item);
            } else {
                $service = $item->get();
            }
            $this->services[$fqdn] = $service;
        }
        $pool->commit();
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
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    private function wire(string $fqdn)
    {
        $reflection = new \ReflectionClass($fqdn);
        $test = $reflection->getConstructor();
        if ((is_null($test))) {
            $service = new $fqdn();
        } else {
            $params = $test->getParameters();
            $dependencies = [];
            foreach ($params as $param) {
                $type = $param->getType()->getName();
                $dependency = $this->wire($type);
                $this->services[$type] = $dependency;
                $dependencies[] = $dependency;
            }
            $service = new $fqdn(...$dependencies);
        }
        if (!$reflection->implementsInterface(ServiceCainInterface::class)) {
            return $service;
        }
        /* @var ServiceCainInterface $service */
        foreach ($service->getChains() as $tag => $method) {
            if (!method_exists($service, $method)) {
                throw new \RuntimeException(
                    "Can't add services. Method " . $method . " does not exist or ist not callable"
                );
            }
            foreach ($this->taggedServices[$tag] as $taggedService) {
                $service->$method($this->get($taggedService));
            }
        }
        return $service;
    }
}
