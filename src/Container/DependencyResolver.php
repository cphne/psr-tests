<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Container;

/**
 * Class DependencyResolver
 * @package Cphne\PsrTests\Container
 */
class DependencyResolver
{

    private array $queue;

    private array $services = [];

    private array $taggedServices = [];

    public function __construct(
        array $fqdns,
        private array $tags
    ) {
        $this->queue = $fqdns;
    }

    public function resolve()
    {
        $runs = 0;
        while (($fqdn = array_shift($this->queue)) !== null && $runs < 50) {
            $runs++;
            if ($fqdn === Container::class) {
                $this->addService($fqdn);
                continue;
            }
            $dependencies = [];
            $reflection = new \ReflectionClass($fqdn);
            $interfaces = array_keys($reflection->getInterfaces());
            foreach ($this->tags as $tag => $taggedInterface) {
                $key = array_search($taggedInterface, $interfaces, true);
                if (
                    $key !== false
                    && (!array_key_exists($tag, $this->taggedServices) || !in_array(
                            $fqdn,
                            $this->taggedServices[$tag],
                            true
                        ))
                ) {
                    $this->taggedServices[$tag][] = $fqdn;
                    # $dependencies[] = $fqdn;
                    $this->push($fqdn);
                }
            }
            $constructor = $reflection->getConstructor();
            if (!is_null($constructor)) {
                foreach ($constructor->getParameters() as $parameter) {
                    $type = $parameter->getType();
                    /* @var \ReflectionNamedType $type */
                    if ($type->isBuiltin()) {
                        throw new \RuntimeException(
                            sprintf('Type %s of %s can not be wired.', $type->getName(), $fqdn)
                        );
                    }
                    $dependencies[] = $type->getName();

                    // TODO check type is object, not array for example
                    $this->push($type->getName());
                }
            }
            $this->addService($fqdn, $dependencies);
        }
        if ($runs === 49) {
            throw new \RuntimeException("fuck");
        }
        uasort(
            $this->services,
            static function (array $a, array $b) {
                return count($a) <=> count($b);
            }
        );
        return $this->services;
    }

    public function getTaggedServices(): array
    {
        return $this->taggedServices;
    }

    public function resolve2()
    {
        while (($fqdn = array_shift($this->queue)) !== null) {
            if ($fqdn === Container::class) {
                $this->addService($fqdn);
                continue;
            }
            $reflection = new \ReflectionClass($fqdn);
            $interfaces = array_keys($reflection->getInterfaces());
            foreach ($this->tags as $tag => $taggedInterface) {
                $key = array_search($taggedInterface, $interfaces, true);
                if ($key !== false) {
                    $this->taggedServices[$tag][] = $fqdn;
                    $this->push($fqdn);
                }
            }
            $constructor = $reflection->getConstructor();
            if (!is_null($constructor)) {
                foreach ($constructor->getParameters() as $parameter) {
                    $type = $parameter->getType();
                    /* @var \ReflectionNamedType $type */
                    if ($type->isBuiltin()) {
                        throw new \RuntimeException(
                            sprintf('Type %s of %s can not be wired.', $type->getName(), $fqdn)
                        );
                    }
                    // TODO check type is object, not array for example
                    $this->push($type->getName());
                }
            }
            $this->addService($fqdn);
        }
        return $this->services;
    }

    private function addService(string $fqdn, array $dependencies = [])
    {
        if (!in_array($fqdn, $this->services)) {
            $this->services[$fqdn] = $dependencies;
        }
    }

    private function push(string $fqdn): void
    {
        if (!in_array($fqdn, $this->queue, true) && !in_array($fqdn, $this->services, true)) {
            $this->queue[] = $fqdn;
            array_unshift($this->queue, $fqdn);
        }
    }
}
