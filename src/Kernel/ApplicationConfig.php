<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Kernel;

/**
 * Class ApplicationConfig
 * @package Cphne\PsrTests\Kernel
 */
class ApplicationConfig
{
    /**
     * @var array
     */
    private array $config;

    /**
     * ApplicationConfig constructor.
     * @throws \JsonException
     */
    public function __construct()
    {
        $path = sprintf('%s/config/application.json', getenv('project-dir'));
        $json = file_get_contents($path);
        $this->config = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return $this->config['logs'];
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return $this->config['cache'];
    }

    /**
     * @return array
     */
    public function getServiceDirs(): array
    {
        return $this->config['services']['directories'];
    }

    /**
     * @return array
     */
    public function getServiceTags(): array
    {
        return $this->config['services']['tags'];
    }
}
