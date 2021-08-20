<?php


namespace Cphne\PsrTests\Services\Finder;


/**
 * Class ClassFinder
 * @package Cphne\PsrTests\Services\Finder
 */
class ClassFinder implements FinderInterface
{

    /**
     * @param $subject
     * @return mixed
     */
    public function find($subject): mixed
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
            if (in_array($file, [".", ".."])) {
                continue;
            }
            $path = $subject . DIRECTORY_SEPARATOR . $file;
            if (filetype($path) === "dir") {
                $fqdns = array_merge($fqdns, $this->find($path));
            } else {
                $fqdns[] = $this->getFqdn($path);
            }
        }
        return $fqdns;
    }

    /**
     * @param string $filepath
     * @return string
     */
    private function getFqdn(string $filepath): string
    {
        $content = file_get_contents($filepath);
        $tokens = \PhpToken::tokenize($content);
        $classLine = null;
        $namespaceLine = null;
        while (is_null($classLine) && ($token = array_shift($tokens)) !== false) {
            /* @var \PhpToken $token */
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
        $parts = explode(" ", $class);
        $class = $parts[1];
        $namespace = str_replace("namespace ", "", $namespace);
        $namespace = str_replace(";", "", $namespace);
        return trim($namespace) . "\\" . $class;
    }
}
