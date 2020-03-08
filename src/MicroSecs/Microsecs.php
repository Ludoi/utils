<?php
declare(strict_types=1);
/*
   Copyright (C) 2020 Luděk Bednarz

   Project: utils
   Author:  Luděk Bednarz
*/


namespace Ludoi\Microsecs;


use DateTimeImmutable;
use Exception;

class Microsecs
{
    /**
     *
     */
    public const FORMAT_HHMMSS = 'H:i:s';
    /**
     *
     */
    public const PRECISION_SECONDS = 1000;
    /**
     *
     */
    public const PRECISION_SECONDS10 = 100;

    /**
     * @param int|null $microsecs
     * @param bool $display_micro
     * @return string
     */
    public static function microsecsToTime(?int $microsecs, bool $display_micro = false): string 
    {
        if (!is_null($microsecs)) {
            $sss = floor($microsecs % 1000 / 100);
            $secs = floor($microsecs / 1000);
            $ss = ($secs % 60);
            $mm = floor($secs / 60) % 60;
            $hh = floor($secs / 3600);
            if ($display_micro) {
                return sprintf('%d:%02d:%02d.%01d', $hh, $mm, $ss, $sss);
            } else {
                return sprintf('%d:%02d:%02d', $hh, $mm, $ss);
            }
        } else {
            return '-';
        }
    }

    /**
     * @param int|null $microsecs
     * @param string $format
     * @return string
     */
    public static function microsecsToDate(?int $microsecs, string $format): string
    {
        if (!is_null($microsecs)) {
            $secs = floor($microsecs / 1000);
            try {
                $date = new DateTimeImmutable();
                $date->setTimestamp($secs);
                return $date->format($format);
            } catch (Exception $exception) {
                return '-';
            }
        } else {
            return '-';
        }
    }

    /**
     * @param int|null $microsecs
     * @param int $precision
     * @return int|null
     */
    public static function roundTo(?int $microsecs, int $precision = self::PRECISION_SECONDS): ?int
    {
        if ($precision > 0 && !is_null($microsecs)) return intval(round($microsecs / $precision) * $precision);
        return null;
    }
}