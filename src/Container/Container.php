<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Container;

use Cphne\PsrTests\Cache\CacheItemPool;
use Psr\Cache\InvalidArgumentException;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Class Container
 * @package Cphne\PsrTests\Container
 */
class Container implements \Psr\Container\ContainerInterface
{
    /**
     * @var array
     */
    private array $services = [];

    /**
     * @var array|string[]
     */
    protected array $tagMapping = [
        ServiceCainInterface::TAG_MIDDLEWARE => MiddlewareInterface::class,
        ServiceCainInterface::TAG_LISTENER_PROVIDER => ListenerProviderInterface::class
    ];

    /**
     * @var array
     */
    protected array $taggedServices = [];

    /**
     * Container constructor.
     * @param array $services
     * @throws InvalidArgumentException
     * @throws \ReflectionException
     */
    public function __construct(array $services = [])
    {
        // Resolve dependencies of passed service-fqdns
        $resolver = new DependencyResolver($services, $this->tagMapping);
        $serviceTree = $resolver->resolve();
        $pool = new CacheItemPool();
        // Check the cache if Service was already constructed, otherwise resolve
        foreach ($serviceTree as $fqdn => $dependencies) {
            $item = $pool->getItem($fqdn);
            if (!$item->isHit()) {
                $service = $this->wire($fqdn);
                $item->set($service);
                $pool->saveDeferred($item);
            } else {
                $service = $item->get();
            }
            $this->services[$fqdn] = $service;
            // Check if service is tagged
            foreach ($this->tagMapping as $tag => $interface) {
                if ($service instanceof $interface) {
                    $this->taggedServices[$tag][] = $service;
                }
            }
        }
        $pool->commit();
        // Resolve ServiceChains, add dependencies
        foreach ($this->services as $service) {
            if ($service instanceof ServiceCainInterface) {
                foreach ($service->getChains() as $tag => $method) {
                    if (!method_exists($service, $method)) {
                        throw new \RuntimeException(
                            "Can't add services. Method " . $method . ' does not exist or ist not callable'
                        );
                    }
                    foreach ($this->taggedServices[$tag] as $taggedService) {
                        $service->$method($taggedService);
                    }
                }
            }
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
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    /**
     * @param string $fqdn
     * @return mixed
     * @throws \ReflectionException
     */
    private function wire(string $fqdn): mixed
    {
        if ($fqdn === __CLASS__) {
            return $this;
        }
        $reflection = new \ReflectionClass($fqdn);
        $constructor = $reflection->getConstructor();
        // If there is no constructor, no dependencies
        if ((is_null($constructor))) {
            $service = new $fqdn();
        } else {
            $params = $constructor->getParameters();
            $dependencies = [];
            // Autowire dependencies, construct them
            foreach ($params as $param) {
                $type = $param->getType()->getName();
                if ($type === __CLASS__) {
                    $dependency = $this;
                } else {
                    $dependency = $this->wire($type);
                }
                $this->services[$type] = $dependency;
                $dependencies[] = $dependency;
            }
            // Construct service with all its constructed dependencies
            $service = new $fqdn(...$dependencies);
        }

        return $service;
    }
}
