<?php

declare(strict_types=1);

namespace Cphne\PsrTests\Services\Finder;

/**
 * Class ClassFinder
 * @package Cphne\PsrTests\Services\Finder
 */
class ClassFinder implements FinderInterface
{

    /**
     * @param $subject
     * @return array
     */
    public function find($subject): array
    {
        if (!str_starts_with($subject, DIRECTORY_SEPARATOR)) {
            // TODO alternative to $_SERVER
            $subject = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "src" . DIRECTORY_SEPARATOR . $subject;
        }
        if (!is_dir($subject)) {
            throw new \InvalidArgumentException(sprintf("%s is not a directory.", $subject));
        }
        if (!is_readable($subject)) {
            throw new \RuntimeException("Can't read directory to search");
        }
        $dh = opendir($subject);
        $fqdns = [];
        while (($file = readdir($dh)) !== false) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $path = $subject . DIRECTORY_SEPARATOR . $file;
            if (filetype($path) === "dir") {
                $fqdns = array_merge($fqdns, $this->find($path));
            } else {
                $fqdn = $this->getFqdn($path);
                if (is_null($fqdn)) {
                    continue;
                }
                $fqdns[] = $fqdn;
            }
        }
        return $fqdns;
    }

    /**
     * @param string $filepath
     * @return string|null
     */
    private function getFqdn(string $filepath): ?string
    {
        $content = file_get_contents($filepath);
        $tokens = \PhpToken::tokenize($content);
        $classLine = null;
        $namespaceLine = null;
        while (is_null($classLine) && ($token = array_shift($tokens)) !== null) {
            /* @var \PhpToken $token */
            if ($token->is(T_INTERFACE) || $token->is(T_ABSTRACT) || $token->is(T_TRAIT)) {
                return null;
            }

            if ($token->is(T_NAMESPACE)) {
                $namespaceLine = $token->line;
            }
            if ($token->is(T_CLASS)) {
                $classLine = $token->line;
            }
        }
        $splFile = new \SplFileObject($filepath);
        $class = null;
        $namespace = null;
        while (is_null($class) && !$splFile->eof() && ($line = $splFile->current()) !== false) {
            if ($splFile->key() === $namespaceLine - 1) {
                $namespace = $splFile->current();
            }
            if ($splFile->key() === $classLine - 1) {
                $class = $splFile->current();
            }
            $splFile->next();
        }
        $parts = explode(' ', $class);
        $class = $parts[1];
        $namespace = str_replace(['namespace ', ';'], '', $namespace);
        return trim($namespace) . "\\" . $class;
    }
}
