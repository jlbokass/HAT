<?php


namespace App\Services;


class StringToUppercase
{
    /**
     * @param string $string
     * @return string
     */
    public function stringToUppercase(string $string): string
    {
        return mb_strtoupper($string);
    }
}