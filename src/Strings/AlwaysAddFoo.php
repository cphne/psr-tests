<?php

namespace Strings;


/**
 * Class AlwaysAddFoo
 * @package StringLib\Strings
 */
class AlwaysAddFoo
{
    public function addBar(string $str): string
    {
        return $str . " Foo";
    }
}
