<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Controller;

/**
 * Class TemplateResponse
 * @package Cphne\PsrTests\Controller
 */
class TemplateResponse implements ControllerResponseInterface
{

    public function __construct(private string $template, private array $parameters, private int $code)
    {
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    public function getBody(): string
    {
        if (!str_starts_with(DIRECTORY_SEPARATOR, $this->template)) {
            $this->template = DIRECTORY_SEPARATOR . $this->template;
        }
        $content = file_get_contents('public/html' . $this->template . '.html');
        foreach ($this->parameters as $key => $value) {
            $replacer = sprintf('{{%s}}', $key);
            $content = str_replace($replacer, $value, $content);
        }
        return $content;
    }
}
