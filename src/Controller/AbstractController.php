<?php


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
     * @return string
     */
    public function render(string $templatePath, array $values): string
    {
        if (!str_starts_with(DIRECTORY_SEPARATOR, $templatePath)) {
            $templatePath = DIRECTORY_SEPARATOR . $templatePath;
        }
        $content = file_get_contents("public/html" . $templatePath . ".html");
        foreach ($values as $key => $value) {
            $replacer = sprintf('{{%s}}', $key);
            $content = str_replace($replacer, $value, $content);
        }
        return $content;
    }
}
