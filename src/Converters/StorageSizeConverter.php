<?php

namespace Matriphe\Larinfo\Converters;

class StorageSizeConverter
{
    private const UNIT_DEC = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    private const UNIT_BIN = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];

    /**
     * Converts binary or decimal to human readable.
     * @link https://ourcodeworld.com/articles/read/718/converting-bytes-to-human-readable-values-kb-mb-gb-tb-pb-eb-zb-yb-with-php
     * @param  int    $num
     * @param  int    $precision
     * @param  bool   $useBinary
     * @return string
     */
    public function toHuman(int $num, int $precision = 0, bool $useBinary = false): string
    {
        if ($num === 0) {
            return sprintf('%s B', number_format(0, $precision));
        }

        $divider = 1024; // use binary
        $units = self::UNIT_BIN;
        if (! $useBinary) {
            $divider = 1000; // use decimal
            $units = self::UNIT_DEC;
        }

        $i = floor(log($num) / log($divider));
        $val = $num / pow($divider, $i) * 1;
        $val = number_format($val, $precision);

        return sprintf('%s %s', $val, $units[$i]);
    }

    /**
     * Static function shortcut.
     * @param  int    $num
     * @param  int    $precision
     * @param  bool   $useBinary
     * @return string
     */
    public static function human(int $num, int $precision, bool $useBinary = true): string
    {
        return (new self())->toHuman($num, $precision, $useBinary);
    }
}
