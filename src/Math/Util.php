<?php

namespace App\Math;

class Util
{
    public static function siPrefixedFormat(float $number): string
    {
        $prefixes = ["y", "z", "a", "f","p", "n", "µ", "m", "", "k", "M", "G", "T", "P", "E", "Z", "Y"];

        $log10 = (int)\log(\abs($number),10);
        if ($log10 < -27) {
            return '0.000';
        }

        if ($log10 % -3 < 0) {
            $log10 -= 3;
        }

        $log1000 = (int)\max(-8, \min($log10 / 3, 8));

        return $number / \pow(10, $log1000 * 3).$prefixes[$log1000+8];
    }
}
