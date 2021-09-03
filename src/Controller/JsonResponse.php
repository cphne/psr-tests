<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Controller;

/**
 * Class JsonResponse
 * @package Cphne\PsrTests\Controller
 */
class JsonResponse implements ControllerResponseInterface
{
    /**
     * JsonResponse constructor.
     * @param mixed $content
     * @param int $code
     */
    public function __construct(private mixed $content, private int $code = 200)
    {
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     * @throws \JsonException
     */
    public function getBody(): string
    {
        return json_encode($this->content, JSON_THROW_ON_ERROR);
    }

}
