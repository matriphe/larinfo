<?php

namespace Matriphe\Larinfo\Tests\unit\Entities;

use Linfo\OS\Darwin;
use Linfo\OS\FreeBSD;
use Linfo\OS\Linux;
use Linfo\OS\Minix;
use Linfo\OS\OS;
use Linfo\OS\Windows;
use Matriphe\Larinfo\Converters\StorageSizeConverter;
use Matriphe\Larinfo\Entities\HardwareInfo;
use Mockery;

/**
 * @group unit
 * @group entity
 */
final class HardwareInfoTest extends LinfoEntityTestCase
{
    /**
     * @var StorageSizeConverter
     */
    private $converter;

    protected function setUp():void
    {
        parent::setUp();

        $this->converter = new StorageSizeConverter();
    }

    /**
     * @return array[]
     */
    public static function cpuData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expectedCpu' => [],
                'expectedCpuString' => '',
                'expectedCpuCount' => 0,
            ],
            'minix returns empty' => [
                'parser' => Mockery::mock(Minix::class),
                'expectedCpu' => [],
                'expectedCpuString' => '',
                'expectedCpuCount' => 0,
            ],
            'os returns full' => [
                'parser' => Mockery::mock(OS::class, [
                    'getCpu' => [
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'Vendor' => 'Datum Corporation',
                            'MHz' => '2500',
                            'usage_percentage' => 60,
                        ],
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'Vendor' => 'Datum Corporation',
                            'MHz' => '2500',
                            'usage_percentage' => 60,
                        ],
                    ],
                ]),
                'expectedCpu' => [
                    [
                        'model' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                        'vendor' => 'Datum Corporation',
                        'clock_mhz' => '2500',
                        'usage_percentage' => 60,
                    ],
                    [
                        'model' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                        'vendor' => 'Datum Corporation',
                        'clock_mhz' => '2500',
                        'usage_percentage' => 60,
                    ],
                ],
                'expectedCpuString' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                'expectedCpuCount' => 2,
            ],
            'os returns not full' => [
                'parser' => Mockery::mock(OS::class, [
                    'getCpu' => [
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'MHz' => '2500',
                        ],
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'MHz' => '2500',
                        ],
                    ],
                ]),
                'expectedCpu' => [
                    [
                        'model' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                        'vendor' => null,
                        'clock_mhz' => '2500',
                        'usage_percentage' => null,
                    ],
                    [
                        'model' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                        'vendor' => null,
                        'clock_mhz' => '2500',
                        'usage_percentage' => null,
                    ],
                ],
                'expectedCpuString' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                'expectedCpuCount' => 2,
            ],
            'os returns not full different' => [
                'parser' => Mockery::mock(OS::class, [
                    'getCpu' => [
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'MHz' => '2500',
                        ],
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.70GHz',
                            'MHz' => '2700',
                        ],
                    ],
                ]),
                'expectedCpu' => [
                    [
                        'model' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                        'vendor' => null,
                        'clock_mhz' => '2500',
                        'usage_percentage' => null,
                    ],
                    [
                        'model' => 'Intel® Core™ i5-3210M CPU @ 2.70GHz',
                        'vendor' => null,
                        'clock_mhz' => '2700',
                        'usage_percentage' => null,
                    ],
                ],
                'expectedCpuString' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz / Intel® Core™ i5-3210M CPU @ 2.70GHz',
                'expectedCpuCount' => 2,
            ],
        ];
    }

    /**
     * @dataProvider cpuData
     * @param OS|null $parser
     * @param array   $expectedCpu
     * @param string  $expectedCpuString
     * @param int     $expectedCpuCount
     */
    public function testGetCpuReturnsCorrectValues(
        ?OS $parser,
        array $expectedCpu,
        string $expectedCpuString,
        int $expectedCpuCount
    ): void {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser), $this->converter);
        $this->assertEquals($expectedCpu, $hardwareInfo->getCpu());
        $this->assertEquals($expectedCpuString, $hardwareInfo->getCpuString());
        $this->assertEquals($expectedCpuCount, $hardwareInfo->getCpuCount());
    }

    /**
     * @return array[]
     */
    public static function modelData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => '',
            ],
            'darwin returns model' => [
                'parser' => Mockery::mock(Darwin::class, ['getModel' => 'MacBook Pro']),
                'expected' => 'MacBook Pro',
            ],
            'os returns model' => [
                'parser' => Mockery::mock(OS::class),
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider modelData
     * @param OS|null $parser
     * @param string  $expected
     */
    public function testGetModelReturnsCorrectValues(?OS $parser, string $expected): void
    {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser), $this->converter);
        $this->assertEquals($expected, $hardwareInfo->getModel());
    }

    /**
     * @return array[]
     */
    public static function virtualizationData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expectedVirtualization' => [],
                'expectedVirtualizationString' => '',
            ],
            'linux returns empty' => [
                'parser' => Mockery::mock(Linux::class, ['getVirtualization' => []]),
                'expectedVirtualization' => [],
                'expectedVirtualizationString' => '',
            ],
            'freebsd returns virtualization' => [
                'parser' => Mockery::mock(FreeBSD::class, [
                    'getVirtualization' => ['type' => 'guest', 'method' => 'Docker'],
                ]),
                'expectedVirtualization' => ['type' => 'guest', 'method' => 'Docker'],
                'expectedVirtualizationString' => 'Docker',
            ],
            'os returns virtualization' => [
                'parser' => Mockery::mock(OS::class, [
                    'getVirtualization' => ['type' => 'guest', 'method' => 'Docker'],
                ]),
                'expectedVirtualization' => [],
                'expectedVirtualizationString' => '',
            ],
        ];
    }

    /**
     * @dataProvider virtualizationData
     * @param OS|null $parser
     * @param array   $expectedVirtualization
     * @param string  $expectedVirtualizationString
     */
    public function testGetVirtualizationReturnsCorrectValues(
        ?OS $parser,
        array $expectedVirtualization,
        string $expectedVirtualizationString
    ): void {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser), $this->converter);
        $this->assertEquals($expectedVirtualization, $hardwareInfo->getVirtualization());
        $this->assertEquals($expectedVirtualizationString, $hardwareInfo->getVirtualizationString());
    }

    /**
     * @return array[]
     */
    public static function memoryData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => [
                    'ram' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                    'swap' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                ],
            ],
            'minix returns empty' => [
                'parser' => Mockery::mock(Minix::class),
                'expected' => [
                    'ram' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                    'swap' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                ],
            ],
            'windows returns ram only' => [
                'parser' => Mockery::mock(Windows::class, [
                    'getRam' => [
                        'type' => 'Physical',
                        'total' => 8000000,
                        'free' => 4000000,
                    ],
                ]),
                'expected' => [
                    'ram' => [
                        'total' => 8000000,
                        'free' => 4000000,
                        'human_total' => '8 MB',
                        'human_free' => '4 MB',
                    ],
                    'swap' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                ],
            ],
            'os returns ram and swap' => [
                'parser' => Mockery::mock(OS::class, [
                    'getRam' => [
                        'type' => 'Physical',
                        'total' => 8000000,
                        'free' => 4000000,
                        'swapTotal' => 4000000,
                        'swapFree' => 2000000,
                        'swapInfo' => [],
                    ],
                ]),
                'expected' => [
                    'ram' => [
                        'total' => 8000000,
                        'free' => 4000000,
                        'human_total' => '8 MB',
                        'human_free' => '4 MB',
                    ],
                    'swap' => [
                        'total' => 4000000,
                        'free' => 2000000,
                        'human_total' => '4 MB',
                        'human_free' => '2 MB',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider memoryData
     * @param OS|null $parser
     * @param array   $expected
     */
    public function testGetMemoryReturnsCorrectValues(?OS $parser, array $expected): void
    {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser), $this->converter);
        $this->assertEquals($expected, $hardwareInfo->getMemory()->toArray());
    }

    /**
     * @return array[]
     */
    public static function diskData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => [
                    'free' => 0,
                    'total' => 0,
                    'human_total' => '0 B',
                    'human_free' => '0 B',
                ],
            ],
            'os returns disk multiple' => [
                'parser' => Mockery::mock(OS::class, [
                    'getMounts' => [
                        [
                            'devtype' => 'Removable drive',
                            'size' => 4000000,
                            'free' => 2000000,
                        ],
                        [
                            'devtype' => 'Fixed drive',
                            'size' => 8000000,
                            'free' => 4000000,
                        ],
                    ],
                ]),
                'expected' => [
                    'total' => 12000000,
                    'free' => 6000000,
                    'human_total' => '12 MB',
                    'human_free' => '6 MB',
                ],
            ],
            'os returns disk single' => [
                'parser' => Mockery::mock(OS::class, [
                    'getMounts' => [
                        [
                            'devtype' => 'Fixed drive',
                            'size' => 8000000,
                            'free' => 4000000,
                        ],
                    ],
                ]),
                'expected' => [
                    'total' => 8000000,
                    'free' => 4000000,
                    'human_total' => '8 MB',
                    'human_free' => '4 MB',
                ],
            ],
        ];
    }

    /**
     * @dataProvider diskData
     * @param OS|null $parser
     * @param array   $expected
     */
    public function testGetDiskReturnsCorrectValues(?OS $parser, array $expected): void
    {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser), $this->converter);
        $this->assertEquals($expected, $hardwareInfo->getDisk()->toArray());
    }

    /**
     * @return array[]
     */
    public static function arrayData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => [
                    'cpu' => '',
                    'cpu_count' => 0,
                    'model' => '',
                    'virtualization' => '',
                    'ram' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                    'swap' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                    'disk' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
                ],
            ],
            'OS returns data' => [
                'parser' => Mockery::mock(OS::class, [
                    'getCpu' => [
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'Vendor' => 'Datum Corporation',
                            'MHz' => '2500',
                            'usage_percentage' => 60,
                        ],
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'Vendor' => 'Datum Corporation',
                            'MHz' => '2500',
                            'usage_percentage' => 60,
                        ],
                    ],
                    'getModel' => 'MacBook Pro',
                    'getVirtualization' => ['type' => 'host', 'method' => 'Docker'],
                    'getRam' => [
                        'type' => 'Physical',
                        'total' => 8000000,
                        'free' => 4000000,
                        'swapTotal' => 4000000,
                        'swapFree' => 2000000,
                        'swapInfo' => [],
                    ],
                    'getMounts' => [
                        [
                            'devtype' => 'Fixed drive',
                            'size' => 8000000,
                            'free' => 4000000,
                        ],
                    ],
                ]),
                'expected' => [
                    'cpu' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                    'cpu_count' => 2,
                    'model' => '',
                    'virtualization' => '',
                    'ram' => [
                        'total' => 8000000,
                        'free' => 4000000,
                        'human_total' => '8 MB',
                        'human_free' => '4 MB',
                    ],
                    'swap' => [
                        'total' => 4000000,
                        'free' => 2000000,
                        'human_total' => '4 MB',
                        'human_free' => '2 MB',
                    ],
                    'disk' => [
                        'total' => 8000000,
                        'free' => 4000000,
                        'human_total' => '8 MB',
                        'human_free' => '4 MB',
                    ],
                ],
            ],
            'darwin returns data' => [
                'parser' => Mockery::mock(Darwin::class, [
                    'getCpu' => [
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'Vendor' => 'Datum Corporation',
                            'MHz' => '2500',
                            'usage_percentage' => 60,
                        ],
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'Vendor' => 'Datum Corporation',
                            'MHz' => '2500',
                            'usage_percentage' => 60,
                        ],
                    ],
                    'getModel' => 'MacBook Pro',
                    'getVirtualization' => ['type' => 'host', 'method' => 'Docker'],
                    'getRam' => [
                        'type' => 'Physical',
                        'total' => 8000000,
                        'free' => 4000000,
                        'swapTotal' => 4000000,
                        'swapFree' => 2000000,
                        'swapInfo' => [],
                    ],
                    'getMounts' => [
                        [
                            'devtype' => 'Fixed drive',
                            'size' => 8000000,
                            'free' => 4000000,
                        ],
                    ],
                ]),
                'expected' => [
                    'cpu' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz',
                    'cpu_count' => 2,
                    'model' => 'MacBook Pro',
                    'virtualization' => 'Docker',
                    'ram' => [
                        'total' => 8000000,
                        'free' => 4000000,
                        'human_total' => '8 MB',
                        'human_free' => '4 MB',
                        ],
                    'swap' => [
                        'total' => 4000000,
                        'free' => 2000000,
                        'human_total' => '4 MB',
                        'human_free' => '2 MB',
                    ],
                    'disk' => [
                        'total' => 8000000,
                        'free' => 4000000,
                        'human_total' => '8 MB',
                        'human_free' => '4 MB',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider arrayData
     * @param OS|null $parser
     * @param array   $expected
     */
    public function testToArrayReturnsCorrectValues(?OS $parser, array $expected):void
    {
        $serverInfo = new HardwareInfo($this->setLinfo($parser), $this->converter);
        $this->assertEquals($expected, $serverInfo->toArray());
    }
}
