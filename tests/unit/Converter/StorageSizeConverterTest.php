<?php

namespace Matriphe\Larinfo\Tests\unit\Converter;

use Matriphe\Larinfo\Converters\StorageSizeConverter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group converter
 */
final class StorageSizeConverterTest extends TestCase
{
    /**
     * @return array
     */
    public static function data(): array
    {
        return [
            ['num' => 0, 'precision' => 0, 'useBinary' => true, 'human' => '0 B'],
            ['num' => 0, 'precision' => 0, 'useBinary' => false, 'human' => '0 B'],
            ['num' => 5, 'precision' => 0, 'useBinary' => true, 'human' => '5 B'],
            ['num' => 5, 'precision' => 0, 'useBinary' => false, 'human' => '5 B'],
            ['num' => 1024, 'precision' => 0, 'useBinary' => true, 'human' => '1 KiB'],
            ['num' => 1000, 'precision' => 0, 'useBinary' => false, 'human' => '1 KB'],
            ['num' => 1500, 'precision' => 0, 'useBinary' => true, 'human' => '1 KiB'],
            ['num' => 1500, 'precision' => 0, 'useBinary' => false, 'human' => '2 KB'],
            ['num' => 1500, 'precision' => 1, 'useBinary' => true, 'human' => '1.5 KiB'],
            ['num' => 1500, 'precision' => 1, 'useBinary' => false, 'human' => '1.5 KB'],
            ['num' => 1500000000, 'precision' => 1, 'useBinary' => true, 'human' => '1.4 GiB'],
            ['num' => 1500000000, 'precision' => 1, 'useBinary' => false, 'human' => '1.5 GB'],
            ['num' => 1690000000, 'precision' => 1, 'useBinary' => true, 'human' => '1.6 GiB'],
            ['num' => 1690000000, 'precision' => 1, 'useBinary' => false, 'human' => '1.7 GB'],
            ['num' => 1690000000, 'precision' => 2, 'useBinary' => true, 'human' => '1.57 GiB'],
            ['num' => 1690000000, 'precision' => 2, 'useBinary' => false, 'human' => '1.69 GB'],
        ];
    }

    /**
     * @dataProvider data
     * @param int    $num
     * @param int    $precision
     * @param bool   $useBinary
     * @param string $human
     */
    public function testToHuman(int $num, int $precision, bool $useBinary, string $human)
    {
        $converter = new StorageSizeConverter();

        $this->assertEquals($human, $converter->toHuman($num, $precision, $useBinary));
    }

    /**
     * @dataProvider data
     * @param int    $num
     * @param int    $precision
     * @param bool   $useBinary
     * @param string $human
     */
    public function testHuman(int $num, int $precision, bool $useBinary, string $human)
    {
        $this->assertEquals($human, StorageSizeConverter::human($num, $precision, $useBinary));
    }
}
