<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Controller;

/**
 * Class AbstractController
 * @package Cphne\PsrTests\Controller
 */
class AbstractController
{
    /**
     * @param string $templatePath
     * @param array $values
     * @param int $code
     * @return TemplateResponse
     */
    public function render(string $templatePath, array $values, int $code = 200): TemplateResponse
    {
        return new TemplateResponse($templatePath, $values, $code);
    }
}
