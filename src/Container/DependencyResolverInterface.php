<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Container;

/**
 * Interface DependencyResolverInterface
 * @package Cphne\PsrTests\Container
 */
interface DependencyResolverInterface
{
    /**
     * DependencyResolverInterface constructor.
     * @param array $fqdns
     * @param array $tags
     */
    public function __construct(array $fqdns, array $tags);


    /**
     * @return array
     */
    public function resolve(): array;

    /**
     * @return array
     */
    public function getTaggedServices(): array;
}
