<?php

namespace Cphne\PsrTests\Integers;

/**
 * Class AlwaysAddOne.
 */
class AlwaysAddOne
{
    public function add(int $number, int $toAdd): int
    {
        return $number + ($toAdd = 1);
    }
}
