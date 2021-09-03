<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Controller;

/**
 * Interface ControllerResponseInterface
 * @package Cphne\PsrTests\Controller
 */
interface ControllerResponseInterface
{
    public function getCode(): int;

    public function getBody(): string;
}
