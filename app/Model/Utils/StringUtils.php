<?php
declare(strict_types=1);

namespace App\Model\Utils;

use Nette\Utils\Strings;

class StringUtils
{
    public static function clean(string $string): string
    {
        return lcfirst(Strings::webalize($string, "a-zA-Z0-9", false));
    }
}