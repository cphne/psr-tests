<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Services\Finder;

/**
 * Interface FinderInterface
 * @package Cphne\PsrTests\Services\Finder
 */
interface FinderInterface
{

    /**
     * @param $subject
     * @return mixed
     */
    public function find($subject): mixed;

}
