<?php

namespace AngelosKanatsos\XM\PHP\Core;

final class RandomGenerator
{
    private const SYMBOL_POOL = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$!*-=()%";
    
    private function makeSeed(int $i = 1): int
    {
        list($usec, $sec) = explode(' ', microtime());
        return $sec + $usec + $i * 1000000;
    }

    public static function secureStr(int $length = 12): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    public static function simpleStr(int $length = 12): string
    {        
        $charactersLength = strlen(self::SYMBOL_POOL);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            srand($this->makeSeed($i));
            $newRandomCharacter = self::SYMBOL_POOL[rand(0, $charactersLength - 1)];
            if ($i > 0 && strpos($randomString, $newRandomCharacter) !== false) {
                --$i;
            } else {
                $randomString .= $newRandomCharacter;
            }
        }
        return $randomString;
    }

    public static function positiveInt(int $max = 1000000): int
    {
        return random_int(0, $max);
    }

    public static function negativeInt(int $min = -1000000): int
    {
        return random_int($min, -1);
    }
}
