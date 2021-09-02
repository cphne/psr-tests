<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Kernel;

/**
 * Class DotEnv
 * @package Cphne\PsrTests\Kernel
 */
class DotEnv
{
    /**
     * @var \SplFileObject
     */
    private \SplFileObject $file;

    /**
     * DotEnv constructor.
     * @param string $path Path to .env file
     */
    public function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist.', $path));
        }
        $this->file = new \SplFileObject($path);
    }

    public function load(): void
    {
        if (!$this->file->isReadable()) {
            throw new \RuntimeException(sprintf('%s is not readable. Check permissions.', $this->file->getPath()));
        }
        while (!$this->file->eof() && $this->file->current()) {
            if (!empty($this->file->current()) && !str_starts_with(trim($this->file->current()), '#')) {
                putenv(trim($this->file->current()));
            }
            $this->file->next();
        }
    }
}
