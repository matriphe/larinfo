<?php

namespace Matriphe\Larinfo\Tests\Entities;

use Linfo\OS\OS;
use Matriphe\Larinfo\Entities\SystemInfo;
use Mockery;

/**
 * @group unit
 * @group entity
 */
final class SystemInfoTest extends LinfoEntityTestCase
{
    /**
     * @return array[]
     */
    public function uptimeData(): array
    {
        return [
            'null returns unknown' => [
                'parser' => null,
                'expected' => ['uptime' => '', 'booted_at' => ''],
            ],
            'os returns uptime' => [
                'parser' => Mockery::mock(OS::class, [
                    'getUpTime' => [
                        'text' => '4 days, 8 hours, 38 seconds',
                        'bootedTimestamp' => 1615761646,
                    ],
                ]),
                'expected' => [
                    'uptime' => '4 days, 8 hours, 38 seconds',
                    'booted_at' => '2021-03-14 22:40:46',
                ],
            ],
            'os returns false boot time' => [
                'parser' => Mockery::mock(OS::class, [
                    'getUpTime' => [
                        'text' => '4 days, 8 hours, 38 seconds',
                        'bootedTimestamp' => false,
                    ],
                ]),
                'expected' => [
                    'uptime' => '4 days, 8 hours, 38 seconds',
                    'booted_at' => '',
                ],
            ],
            'weird class returns unknown' => [
                'parser' => new class() {
                    public function getUpTime(): array
                    {
                        return [
                            'text' => '4 days, 8 hours, 38 seconds',
                            'bootedTimestamp' => 1615761646,
                        ];
                    }
                },
                'expected' => ['uptime' => '', 'booted_at' => ''],
            ],
        ];
    }

    /**
     * @dataProvider uptimeData
     * @param $parser
     * @param array $expected
     */
    public function testGetUptimeReturnsCorrectValues($parser, array $expected): void
    {
        $systemInfo = new SystemInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $systemInfo->toArray());
        $this->assertEquals($expected['uptime'], $systemInfo->getUptime());
        $this->assertEquals($expected['booted_at'], $systemInfo->getBootedAt());
    }
}
