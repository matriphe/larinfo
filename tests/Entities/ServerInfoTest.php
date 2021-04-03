<?php

namespace Matriphe\Larinfo\Tests\Entities;

use Linfo\OS\Darwin;
use Linfo\OS\Linux;
use Linfo\OS\OS;
use Linfo\OS\Windows;
use Matriphe\Larinfo\Entities\ServerInfo;
use Mockery;

/**
 * @group unit
 * @group entity
 */
final class ServerInfoTest extends LinfoEntityTestCase
{
    /**
     * @return array[]
     */
    public function osData(): array
    {
        return [
            'null returns unknown' => [
                'parser' => null,
                'expected' => 'Unknown',
            ],
            'os returns darwin' => [
                'parser' => Mockery::mock(OS::class, ['getOS' => 'Darwin']),
                'expected' => 'Darwin',
            ],
            'whatever returns whatever' => [
                'parser' => Mockery::mock(OS::class, ['getOS' => 'Matriphe']),
                'expected' => 'Matriphe',
            ],
            'weird class returns unknown' => [
                'parser' => new class() {
                    public function getOs(): string
                    {
                        return 'Awesome';
                    }
                },
                'expected' => 'Unknown',
            ],
            'darwin returns empty' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => '']),
                'expected' => 'MacOS',
            ],
            'darwin returns macos' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin']),
                'expected' => 'MacOS',
            ],
            'darwin returns macos 10.12.6 with space' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin (macOS 10.12.6 )']),
                'expected' => 'MacOS',
            ],
            'darwin returns macos 10.15.7 without space' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin (macOS 10.15.7)']),
                'expected' => 'MacOS',
            ],
            'darwin returns macos X' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin (Mac OS X)']),
                'expected' => 'MacOS',
            ],
        ];
    }

    /**
     * @dataProvider osData
     * @param mixed  $parser
     * @param string $expected
     */
    public function testGetOSReturnsCorrectValues($parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getOS());
    }

    /**
     * @return array[]
     */
    public function distroData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expectedDistro' => [],
                'expectedDistroString' => '',
                'expectedDistroName' => '',
                'expectedDistroVersion' => '',
            ],
            'darwin returns macos on empty' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => '']),
                'expectedDistro' => [
                    'name' => 'MacOS',
                    'version' => '',
                ],
                'expectedDistroString' => 'MacOS',
                'expectedDistroName' => 'MacOS',
                'expectedDistroVersion' => '',
            ],
            'darwin returns macos' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin']),
                'expectedDistro' => [
                    'name' => 'MacOS',
                    'version' => '',
                ],
                'expectedDistroString' => 'MacOS',
                'expectedDistroName' => 'MacOS',
                'expectedDistroVersion' => '',
            ],
            'darwin returns macos 10.12.6 with space' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin (macOS 10.12.6 )']),
                'expectedDistro' => [
                    'name' => 'MacOS',
                    'version' => '10.12.6',
                ],
                'expectedDistroString' => 'MacOS 10.12.6',
                'expectedDistroName' => 'MacOS',
                'expectedDistroVersion' => '10.12.6',
            ],
            'darwin returns macos 10.15.7 without space' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin (macOS 10.15.7)']),
                'expectedDistro' => [
                    'name' => 'MacOS',
                    'version' => '10.15.7',
                ],
                'expectedDistroString' => 'MacOS 10.15.7',
                'expectedDistroName' => 'MacOS',
                'expectedDistroVersion' => '10.15.7',
            ],
            'darwin returns macos X' => [
                'parser' => Mockery::mock(Darwin::class, ['getOS' => 'Darwin (Mac OS X)']),
                'expectedDistro' => [
                    'name' => 'MacOS',
                    'version' => 'X',
                ],
                'expectedDistroString' => 'MacOS X',
                'expectedDistroName' => 'MacOS',
                'expectedDistroVersion' => 'X',
            ],
            'windows returns empty' => [
                'parser' => Mockery::mock(Windows::class),
                'expectedDistro' => [],
                'expectedDistroString' => '',
                'expectedDistroName' => '',
                'expectedDistroVersion' => '',
            ],
            'linux returns empty' => [
                'parser' => Mockery::mock(Linux::class, [
                    'getDistro' => ['name' => 'Ubuntu', 'version' => '20.04'],
                ]),
                'expectedDistro' => ['name' => 'Ubuntu', 'version' => '20.04'],
                'expectedDistroString' => 'Ubuntu 20.04',
                'expectedDistroName' => 'Ubuntu',
                'expectedDistroVersion' => '20.04',
            ],
            'linux returns distro without version' => [
                'parser' => Mockery::mock(Linux::class, [
                    'getDistro' => ['name' => 'Ubuntu'],
                ]),
                'expectedDistro' => ['name' => 'Ubuntu', 'version' => ''],
                'expectedDistroString' => 'Ubuntu',
                'expectedDistroName' => 'Ubuntu',
                'expectedDistroVersion' => '',
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getDistro(): array
                    {
                        return ['name' => 'Ubuntu', 'version' => '20.04'];
                    }
                },
                'expectedDistro' => [],
                'expectedDistroString' => '',
                'expectedDistroName' => '',
                'expectedDistroVersion' => '',
            ],
        ];
    }

    /**
     * @dataProvider distroData
     * @param mixed  $parser
     * @param array  $expectedDistro
     * @param string $expectedDistroString
     * @param string $expectedDistroName
     * @param string $expectedDistroVersion
     */
    public function testGetDistroReturnsCorrectValues(
        $parser,
        array $expectedDistro,
        string $expectedDistroString,
        string $expectedDistroName,
        string $expectedDistroVersion
    ): void {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expectedDistro, $serverInfo->getDistro());
        $this->assertEquals($expectedDistroString, $serverInfo->getDistroString());
        $this->assertEquals($expectedDistroName, $serverInfo->getDistroName());
        $this->assertEquals($expectedDistroVersion, $serverInfo->getDistroVersion());
    }

    /**
     * @return array[]
     */
    public function kernelData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => '',
            ],
            'OS returns kernel version' => [
                'parser' => Mockery::mock(OS::class, ['getKernel' => '1.2.3']),
                'expected' => '1.2.3',
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getKernel(): string
                    {
                        return '1.2.3';
                    }
                },
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider kernelData
     * @param mixed  $parser
     * @param string $expected
     */
    public function testGetKernelReturnsCorrectValues($parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getKernel());
    }

    /**
     * @return array[]
     */
    public function archData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => '',
            ],
            'OS returns arch' => [
                'parser' => Mockery::mock(OS::class, ['getCPUArchitecture' => 'x86_64']),
                'expected' => 'x86_64',
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getCPUArchitecture(): string
                    {
                        return 'x86_64';
                    }
                },
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider archData
     * @param mixed  $parser
     * @param string $expected
     */
    public function testGetArchReturnsCorrectValues($parser, string $expected):void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getArch());
    }

    /**
     * @return array[]
     */
    public function webServerData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => '',
            ],
            'OS returns web server' => [
                'parser' => Mockery::mock(OS::class, ['getWebService' => 'matriphe/1.2.3']),
                'expected' => 'matriphe/1.2.3',
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getWebService(): string
                    {
                        return 'matriphe/1.2.3';
                    }
                },
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider webServerData
     * @param mixed  $parser
     * @param string $expected
     */
    public function testGetWebServerReturnsCorrectValues($parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getWebServer());
    }

    /**
     * @return array[]
     */
    public function phpVersionData(): array
    {
        return [
            'null returns empty' => [
                'parser' => null,
                'expected' => '',
            ],
            'OS returns web server' => [
                'parser' => Mockery::mock(OS::class, ['getPhpVersion' => '8.0.3']),
                'expected' => '8.0.3',
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getPhpVersion(): string
                    {
                        return '8.0.3';
                    }
                },
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider phpVersionData
     * @param mixed  $parser
     * @param string $expected
     */
    public function testGetPhpVersionReturnsCorrectValues($parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getPhpVersion());
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
                    'os' => 'Unknown',
                    'distro' => '',
                    'kernel' => '',
                    'arc' => '',
                    'webserver' => '',
                    'php' => '',
                ],
            ],
            'OS returns data' => [
                'parser' => Mockery::mock(OS::class, [
                    'getOS' => 'Matriphe',
                    'getDistro' => ['name' => 'Ubuntu', 'version' => '20.04'],
                    'getKernel' => '1.2.3',
                    'getCPUArchitecture' => '4.5.6',
                    'getWebservice' => 'matriphe/1.2.3',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'Matriphe',
                    'distro' => '',
                    'kernel' => '1.2.3',
                    'arc' => '4.5.6',
                    'webserver' => 'matriphe/1.2.3',
                    'php' => '8.0.3',
                ],
            ],
            'weird class returns empty' => [
                'parser' => new class() {
                    public function getOs(): string
                    {
                        return 'Awesome';
                    }

                    public function getDistro(): array
                    {
                        return ['name' => 'Ubuntu', 'version' => '20.04'];
                    }

                    public function getKernel(): string
                    {
                        return '1.2.3';
                    }

                    public function getCPUArchitecture(): string
                    {
                        return 'x86_64';
                    }

                    public function getWebService(): string
                    {
                        return 'matriphe/1.2.3';
                    }

                    public function getPhpVersion(): string
                    {
                        return '8.0.3';
                    }
                },
                'expected' => [
                    'os' => 'Unknown',
                    'distro' => '',
                    'kernel' => '',
                    'arc' => '',
                    'webserver' => '',
                    'php' => '',
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
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->toArray());
    }
}
