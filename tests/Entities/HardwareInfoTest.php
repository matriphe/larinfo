<?php

namespace Matriphe\Larinfo\Tests\Entities;

use Linfo\OS\Darwin;
use Linfo\OS\FreeBSD;
use Linfo\OS\Linux;
use Linfo\OS\Minix;
use Linfo\OS\OS;
use Linfo\OS\Windows;
use Matriphe\Larinfo\Entities\HardwareInfo;
use Mockery;

class HardwareInfoTest extends LinfoEntityTestCase
{
    /**
     * @return array[]
     */
    public function cpuData(): array
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
            'weird class returns unknown' => [
                'parser' => new class() {
                    public function getCpu(): array
                    {
                        return [
                            [
                                'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                                'MHz' => '2500',
                            ],
                            [
                                'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.70GHz',
                                'MHz' => '2700',
                            ],
                        ];
                    }
                },
                'expectedCpu' => [],
                'expectedCpuString' => '',
                'expectedCpuCount' => 0,
            ],
        ];
    }

    /**
     * @dataProvider cpuData
     * @param mixed  $parser
     * @param array  $expectedCpu
     * @param string $expectedCpuString
     * @param int    $expectedCpuCount
     */
    public function testGetCpuReturnsCorrectValues(
        $parser,
        array $expectedCpu,
        string $expectedCpuString,
        int $expectedCpuCount
    ): void {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser));
        $this->assertEquals($expectedCpu, $hardwareInfo->getCpu());
        $this->assertEquals($expectedCpuString, $hardwareInfo->getCpuString());
        $this->assertEquals($expectedCpuCount, $hardwareInfo->getCpuCount());
    }

    /**
     * @return array[]
     */
    public function modelData(): array
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
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getModel(): string
                    {
                        return 'MacBook Pro';
                    }
                },
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider modelData
     * @param mixed  $parser
     * @param string $expected
     */
    public function testGetModelReturnsCorrectValues($parser, string $expected): void
    {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $hardwareInfo->getModel());
    }

    /**
     * @return array[]
     */
    public function virtualizationData(): array
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
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getVirtualization(): array
                    {
                        return ['type' => 'guest', 'method' => 'Docker'];
                    }
                },
                'expectedVirtualization' => [],
                'expectedVirtualizationString' => '',
            ],
        ];
    }

    /**
     * @dataProvider virtualizationData
     * @param mixed  $parser
     * @param array  $expectedVirtualization
     * @param string $expectedVirtualizationString
     */
    public function testGetVirtualizationReturnsCorrectValues(
        $parser,
        array $expectedVirtualization,
        string $expectedVirtualizationString
    ): void {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser));
        $this->assertEquals($expectedVirtualization, $hardwareInfo->getVirtualization());
        $this->assertEquals($expectedVirtualizationString, $hardwareInfo->getVirtualizationString());
    }

    /**
     * @return array[]
     */
    public function memoryData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => [
                    'ram' => ['free' => 0, 'total' => 0],
                    'swap' => ['free' => 0, 'total' => 0],
                ],
            ],
            'minix returns empty' => [
                'parser' => Mockery::mock(Minix::class),
                'expected' => [
                    'ram' => ['free' => 0, 'total' => 0],
                    'swap' => ['free' => 0, 'total' => 0],
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
                    'ram' => ['free' => 4000000, 'total' => 8000000],
                    'swap' => ['free' => 0, 'total' => 0],
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
                    'ram' => ['free' => 4000000, 'total' => 8000000],
                    'swap' => ['free' => 2000000, 'total' => 4000000],
                ],
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getRam(): array
                    {
                        return [
                            'type' => 'Physical',
                            'total' => 8000000,
                            'free' => 4000000,
                            'swapTotal' => 4000000,
                            'swapFree' => 2000000,
                            'swapInfo' => [],
                        ];
                    }
                },
                'expected' => [
                    'ram' => ['free' => 0, 'total' => 0],
                    'swap' => ['free' => 0, 'total' => 0],
                ],
            ],
        ];
    }

    /**
     * @dataProvider memoryData
     * @param mixed $parser
     * @param array $expected
     */
    public function testGetMemoryReturnsCorrectValues($parser, array $expected): void
    {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $hardwareInfo->getMemory());
    }

    /**
     * @return array[]
     */
    public function diskData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => ['free' => 0, 'total' => 0],
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
                'expected' => ['free' => 6000000, 'total' => 12000000],
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
                'expected' => ['free' => 4000000, 'total' => 8000000],
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getMounts(): array
                    {
                        return [
                            [
                                'devtype' => 'Fixed drive',
                                'size' => 8000000,
                                'free' => 4000000,
                            ],
                        ];
                    }
                },
                'expected' => ['free' => 0, 'total' => 0],
            ],
        ];
    }

    /**
     * @dataProvider diskData
     * @param mixed $parser
     * @param array $expected
     */
    public function testGetDiskReturnsCorrectValues($parser, array $expected): void
    {
        $hardwareInfo = new HardwareInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $hardwareInfo->getDisk());
    }

    /**
     * @return array[]
     */
    public function arrayData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => [
                    'cpu' => '',
                    'cpu_count' => 0,
                    'model' => '',
                    'virtualization' => '',
                    'ram' => ['total' => 0, 'free' => 0],
                    'swap' => ['total' => 0, 'free' => 0],
                    'disk' => ['total' => 0, 'free' => 0],
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
                    'ram' => ['total' => 8000000, 'free' => 4000000],
                    'swap' => ['total' => 4000000, 'free' => 2000000],
                    'disk' => ['total' => 8000000, 'free' => 4000000],
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
                    'ram' => ['total' => 8000000, 'free' => 4000000],
                    'swap' => ['total' => 4000000, 'free' => 2000000],
                    'disk' => ['total' => 8000000, 'free' => 4000000],
                ],
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getCpu(): array
                    {
                        return [
                            [
                                'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                                'Vendor' => 'Datum Corporation',
                                'MHz' => '2500',
                                'usage_percentage' => 60,
                            ],
                        ];
                    }

                    public function getModel(): string
                    {
                        return 'MacBook Pro';
                    }

                    public function getVirtualization(): array
                    {
                        return ['type' => 'guest', 'method' => 'Docker'];
                    }

                    public function getRam(): array
                    {
                        return [
                            'type' => 'Physical',
                            'total' => 8000000,
                            'free' => 4000000,
                            'swapTotal' => 4000000,
                            'swapFree' => 2000000,
                            'swapInfo' => [],
                        ];
                    }

                    public function getMounts(): array
                    {
                        return [
                            [
                                'devtype' => 'Fixed drive',
                                'size' => 8000000,
                                'free' => 4000000,
                            ],
                        ];
                    }
                },
                'expected' => [
                    'cpu' => '',
                    'cpu_count' => 0,
                    'model' => '',
                    'virtualization' => '',
                    'ram' => ['total' => 0, 'free' => 0],
                    'swap' => ['total' => 0, 'free' => 0],
                    'disk' => ['total' => 0, 'free' => 0],
                ],
            ],
        ];
    }

    /**
     * @dataProvider arrayData
     * @param mixed $parser
     * @param array $expected
     */
    public function testToArrayReturnsCorrectValues($parser, array $expected):void
    {
        $serverInfo = new HardwareInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->toArray());
    }
}
