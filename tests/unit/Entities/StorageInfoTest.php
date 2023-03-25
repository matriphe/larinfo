<?php

namespace Matriphe\Larinfo\Tests\unit\Entities;

use Matriphe\Larinfo\Converters\StorageSizeConverter;
use Matriphe\Larinfo\Entities\StorageInfo;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group entity
 */
final class StorageInfoTest extends TestCase
{
    public static function storageData(): array
    {
        return [
            [
                'total' => 3,
                'free' => 4,
                'precision' => 0,
                'useBinary' => true,
                'totalHuman' => '3 B',
                'freeHuman' => '4 B',
            ],
            [
                'total' => 123456789,
                'free' => 7890,
                'precision' => 2,
                'useBinary' => false,
                'totalHuman' => '123.46 MB',
                'freeHuman' => '7.89 KB',
            ],
        ];
    }

    /**
     * @dataProvider storageData
     * @param int    $total
     * @param int    $free
     * @param int    $precision
     * @param bool   $useBinary
     * @param string $totalHuman
     * @param string $freeHuman
     */
    public function testStorageInfoReturnsCorrectly(
        int $total,
        int $free,
        int $precision,
        bool $useBinary,
        string $totalHuman,
        string $freeHuman
    ) {
        $storage = new StorageInfo(
            $total,
            $free,
            new StorageSizeConverter(),
            $precision,
            $useBinary
        );

        $this->assertEquals($total, $storage->getTotal());
        $this->assertEquals($free, $storage->getFree());
        $this->assertEquals($totalHuman, $storage->getTotalHuman($precision, $useBinary));
        $this->assertEquals($freeHuman, $storage->getFreeHuman($precision, $useBinary));
    }
}
