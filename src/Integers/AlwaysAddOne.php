<?php


namespace Cphne\PsrTests\Integers;

class AlwaysAddOne
{

    public function add(int $number, int $toAdd): int
    {
        return $number + ($toAdd = 1);
    }

}
