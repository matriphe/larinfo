<?php

namespace Matriphe\Larinfo\Tests\unit\Entities;

use Linfo\OS\Darwin;
use Linfo\OS\Linux;
use Linfo\OS\OS;
use Linfo\OS\Windows;
use Matriphe\Larinfo\Entities\ServerInfo;
use Matriphe\Larinfo\Tests\LinfoEntityTestCase;
use Matriphe\Larinfo\Windows\WindowsOs;
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
    public static function osData(): array
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
            'windows returns windows' => [
                'parser' => Mockery::mock(Windows::class),
                'expected' => 'Windows',
            ],
            'windows os returns windows' => [
                'parser' => Mockery::mock(WindowsOs::class),
                'expected' => 'Windows',
            ],
        ];
    }

    /**
     * @dataProvider osData
     * @param OS|null $parser
     * @param string  $expected
     */
    public function testGetOSReturnsCorrectValues(?OS $parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getOS());
    }

    /**
     * @return array[]
     */
    public static function distroData(): array
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
            'windows returns empty' => [
                'parser' => Mockery::mock(Windows::class, [
                    'getOS' => '',
                ]),
                'expectedDistro' => [
                    'name' => 'Microsoft Windows',
                    'version' => '',
                ],
                'expectedDistroString' => 'Microsoft Windows',
                'expectedDistroName' => 'Microsoft Windows',
                'expectedDistroVersion' => '',
            ],
            'windows returns windows' => [
                'parser' => Mockery::mock(Windows::class, [
                    'getOS' => 'Microsoft Windows 10 Enterprise Evaluation',
                ]),
                'expectedDistro' => [
                    'name' => 'Microsoft Windows',
                    'version' => '10 Enterprise Evaluation',
                ],
                'expectedDistroString' => 'Microsoft Windows 10 Enterprise Evaluation',
                'expectedDistroName' => 'Microsoft Windows',
                'expectedDistroVersion' => '10 Enterprise Evaluation',
            ],
            'windows os returns windows' => [
                'parser' => Mockery::mock(WindowsOs::class, [
                    'getDistro' => ['name' => 'Microsoft Windows', 'version' => '10'],
                ]),
                'expectedDistro' => ['name' => 'Microsoft Windows', 'version' => '10'],
                'expectedDistroString' => 'Microsoft Windows 10',
                'expectedDistroName' => 'Microsoft Windows',
                'expectedDistroVersion' => '10',
            ],
        ];
    }

    /**
     * @dataProvider distroData
     * @param OS|null $parser
     * @param array   $expectedDistro
     * @param string  $expectedDistroString
     * @param string  $expectedDistroName
     * @param string  $expectedDistroVersion
     */
    public function testGetDistroReturnsCorrectValues(
        ?OS $parser,
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
    public static function kernelData(): array
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
        ];
    }

    /**
     * @dataProvider kernelData
     * @param OS|null $parser
     * @param string  $expected
     */
    public function testGetKernelReturnsCorrectValues(?OS $parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getKernel());
    }

    /**
     * @return array[]
     */
    public static function archData(): array
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
        ];
    }

    /**
     * @dataProvider archData
     * @param OS|null $parser
     * @param string  $expected
     */
    public function testGetArchReturnsCorrectValues(?OS $parser, string $expected):void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getArch());
    }

    /**
     * @return array[]
     */
    public static function webServerData(): array
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
        ];
    }

    /**
     * @dataProvider webServerData
     * @param OS|null $parser
     * @param string  $expected
     */
    public function testGetWebServerReturnsCorrectValues(?OS $parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getWebServer());
    }

    /**
     * @return array[]
     */
    public static function phpVersionData(): array
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
        ];
    }

    /**
     * @dataProvider phpVersionData
     * @param OS|null $parser
     * @param string  $expected
     */
    public function testGetPhpVersionReturnsCorrectValues(?OS $parser, string $expected): void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->getPhpVersion());
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
        ];
    }

    /**
     * @dataProvider arrayData
     * @param OS|null $parser
     * @param array   $expected
     */
    public function testToArrayReturnsCorrectValues(?OS $parser, array $expected):void
    {
        $serverInfo = new ServerInfo($this->setLinfo($parser));
        $this->assertEquals($expected, $serverInfo->toArray());
    }
}
