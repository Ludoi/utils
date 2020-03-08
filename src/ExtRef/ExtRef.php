<?php
declare(strict_types=1);
/*
   Util class for check number in external reference number

   Copyright (C) 2020 Luděk Bednarz

   Project: utils
   Author:  Luděk Bednarz
*/


namespace Ludoi\ExtRef;


class ExtRef
{
    /**
     * @param int $value
     * @return bool
     */
    public static function isValid(int $value): bool
    {
        $shortValue = (int)$value / 10;
        $newValue = self::getCheckNumber($shortValue);
        return ($value == $newValue);
    }

    /**
     * @param int $value
     * @return int
     */
    public static function getCheckNumber(int $value): int
    {
        $result = 0;
        $new = abs($value);
        $pos = 0;
        $sum = 0;
        $factor = 1;
        do {
            $pos++;
            $digit = $new % 10;
            if ($pos % 2 == 1) {
                $check = 2 * $digit;
                $digitSum = self::getSumOfDigits($check);
            } else {
                $digitSum = $digit;
            }
            $sum += $digitSum;
            $new = (int)($new - $digit) / 10;
            $result = $factor * $digit + $result;
            $factor *= 10;
        } while ($new > 0);
        $mod = $sum % 10;
        if ($mod == 0) {
            $result = 10 * $result;
        } else {
            $result = 10 * $result + 10 - $mod;
        }
        return $result;
    }

    /**
     * @param int $number
     * @return int
     */
    private static function getSumOfDigits(int $number): int
    {
        $sum = 0;
        $new = abs($number);
        do {
            $digit = $new % 10;
            $sum += $digit;
            $new = (int)($new - $digit) / 10;
        } while ($new > 0);
        return $sum;
    }

    /**
     * @param int $one
     * @param int $two
     * @return int
     */
    public static function compare(int $one, int $two): int
    {
        $diff = abs($one - $two);
        return self::getSumOfDigits($diff);
    }
}