<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Container;

/**
 * Class DependencyResolver
 * @package Cphne\PsrTests\Container
 */
class DependencyResolver implements DependencyResolverInterface
{

    /**
     * @var array
     */
    private array $queue;

    /**
     * @var array
     */
    private array $services = [];

    /**
     * @var array
     */
    private array $taggedServices = [];

    /**
     * DependencyResolver constructor.
     * @param array $fqdns
     * @param array $tags
     */
    public function __construct(
        array $fqdns,
        private array $tags
    ) {
        $this->queue = $fqdns;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function resolve(): array
    {
        while (($fqdn = array_shift($this->queue)) !== null) {
            // Container instance will not be constructed, so resolving is finished at this point
            if ($fqdn === Container::class) {
                $this->addService($fqdn);
                continue;
            }
            $dependencies = [];
            $reflection = new \ReflectionClass($fqdn);
            $interfaces = array_keys($reflection->getInterfaces());
            // Check interfaces of potential service whether service is tagged
            foreach ($this->tags as $tag => $taggedInterface) {
                $key = array_search($taggedInterface, $interfaces, true);
                // If Service is not already marked for this tag, add to list
                if (
                    $key !== false
                    && (!array_key_exists($tag, $this->taggedServices) || !in_array(
                        $fqdn,
                        $this->taggedServices[$tag],
                        true
                    ))
                ) {
                    $this->taggedServices[$tag][] = $fqdn;
                    $this->push($fqdn);
                }
            }
            $constructor = $reflection->getConstructor();
            if (!is_null($constructor)) {
                // Check constructor argument's
                foreach ($constructor->getParameters() as $parameter) {
                    $type = $parameter->getType();
                    /* @var \ReflectionNamedType $type */
                    // Builtin Types are not supported yet
                    if ($type->isBuiltin()) {
                        throw new \RuntimeException(
                            sprintf('Type %s of %s can not be wired.', $type->getName(), $fqdn)
                        );
                    }
                    // Constructor arguments are the dependencies, add them
                    $dependencies[] = $type->getName();

                    // TODO check type is object, not array for example
                    // Add dependency to queue for resolving it's dependencies
                    $this->push($type->getName());
                }
            }
            $this->addService($fqdn, $dependencies);
        }
        // Sort array so that services with the least amount of dependencies are constructed first
        uasort(
            $this->services,
            static function (array $a, array $b) {
                return count($a) <=> count($b);
            }
        );
        return $this->services;
    }

    /**
     * @return array
     */
    public function getTaggedServices(): array
    {
        return $this->taggedServices;
    }

    /**
     * Adds a resolved Service and it's dependencies to the resulting array
     *
     * @param string $fqdn
     * @param array $dependencies
     */
    private function addService(string $fqdn, array $dependencies = [])
    {
        if (!in_array($fqdn, $this->services)) {
            $this->services[$fqdn] = $dependencies;
        }
    }

    /**
     * Adds a FQDN to the queue that still needs to be resolved
     *
     * @param string $fqdn
     */
    private function push(string $fqdn): void
    {
        if (!in_array($fqdn, $this->queue, true) && !in_array($fqdn, $this->services, true)) {
            $this->queue[] = $fqdn;
            array_unshift($this->queue, $fqdn);
        }
    }
}
