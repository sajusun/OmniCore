<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CodeGenerator
{

    private static function randomAlphabet(int $length = 4): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $result;
    }

    private static function randomNumber(int $length = 5): string
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= random_int(0, 9);
        }
        return $result;
    }

    public static function uniqueCode(string $prefix = '', int $length = 4): string
    {
        $randomString = Str::upper(Str::random($length));
        return $prefix . $randomString;
    }

    public static function customCode(int $stringLength = 4, int $numberLength = 6, mixed $seperator = null): string
    {
        if ($seperator) {
            $randomNumber = self::randomAlphabet($stringLength) . $seperator . self::randomNumber($numberLength);
            return $randomNumber;
        }
        $randomNumber = self::randomAlphabet($stringLength) . self::randomNumber($numberLength);
        return $randomNumber;
    }
}
