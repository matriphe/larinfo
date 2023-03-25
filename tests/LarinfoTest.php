<?php

namespace Matriphe\Larinfo\Tests;

use DavidePastore\Ipinfo\Host;
use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Linfo\Linfo;
use Linfo\OS\Darwin;
use Linfo\OS\Linux;
use Linfo\OS\Minix;
use Linfo\OS\OS;
use Linfo\OS\Windows;
use Matriphe\Larinfo\Converters\StorageSizeConverter;
use Matriphe\Larinfo\Entities\DatabaseInfo;
use Matriphe\Larinfo\Entities\GeoIpInfo;
use Matriphe\Larinfo\Entities\HardwareInfo;
use Matriphe\Larinfo\Entities\IpAddressChecker;
use Matriphe\Larinfo\Entities\ServerInfo;
use Matriphe\Larinfo\Entities\SystemInfo;
use Matriphe\Larinfo\Larinfo;
use Matriphe\Larinfo\Windows\WindowsOs;
use Matriphe\Larinfo\Wrapper\LinfoWrapperContract;
use Mockery;

/**
 * @group unit
 */
final class LarinfoTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Ipinfo|Mockery\MockInterface
     */
    private $ipinfo;
    /**
     * @var Request|Mockery\MockInterface
     */
    private $request;
    /**
     * @var Linfo|Mockery\MockInterface
     */
    private $linfo;
    /**
     * @var Manager|Mockery\MockInterface
     */
    private $database;
    /**
     * @var IpAddressChecker
     */
    private $ipAddressChecker;
    /**
     * @var StorageSizeConverter
     */
    private $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipinfo = Mockery::mock(Ipinfo::class);
        $this->request = Mockery::mock(Request::class);
        $this->linfo = Mockery::mock(LinfoWrapperContract::class);
        $this->database = Mockery::mock(Manager::class);
        $this->ipAddressChecker = new IpAddressChecker();
        $this->converter = new StorageSizeConverter();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @return array[]
     */
    public static function hostData(): array
    {
        return [
            [
                'serverAddress' => '',
                'hostdata' => [
                    Ipinfo::CITY => 'Bekasi',
                    Ipinfo::COUNTRY => 'ID',
                    Ipinfo::HOSTNAME => '',
                    Ipinfo::IP => '180.250.116.128',
                    Ipinfo::LOC => '-6.2349,106.9896',
                    Ipinfo::ORG => 'AS17974 PT Telekomunikasi Indonesia',
                    Ipinfo::PHONE => '',
                    Ipinfo::POSTAL => '',
                    Ipinfo::REGION => 'West Java',
                ],
                'expected' => [
                    'hostname' => '',
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'ip' => '180.250.116.128',
                    'ip_private' => '',
                    'location' => '-6.2349,106.9896',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '',
                    'postal' => '',
                    'region' => 'West Java',
                    'timezone' => '',
                ],
            ],
            [
                'serverAddress' => '10.10.1.1',
                'hostdata' => [
                    Ipinfo::CITY => 'Bekasi',
                    Ipinfo::COUNTRY => 'ID',
                    Ipinfo::HOSTNAME => 'bekasi.telkom.co.id',
                    Ipinfo::IP => '180.250.116.128',
                    Ipinfo::LOC => '-6.2349,106.9896',
                    Ipinfo::ORG => 'AS17974 PT Telekomunikasi Indonesia',
                    Ipinfo::PHONE => '',
                    Ipinfo::POSTAL => '',
                    Ipinfo::REGION => 'West Java',
                    'timezone' => 'Asia/Jakarta',
                ],
                'expected' => [
                    'hostname' => 'bekasi.telkom.co.id',
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'ip' => '180.250.116.128',
                    'ip_private' => '10.10.1.1',
                    'location' => '-6.2349,106.9896',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '',
                    'postal' => '',
                    'region' => 'West Java',
                    'timezone' => 'Asia/Jakarta',
                ],
            ],
            [
                'serverAddress' => '180.250.116.128',
                'hostdata' => [
                    Ipinfo::CITY => 'Bekasi',
                    Ipinfo::COUNTRY => 'ID',
                    Ipinfo::HOSTNAME => 'bekasi.telkom.co.id',
                    Ipinfo::IP => '180.250.116.128',
                    Ipinfo::LOC => '-6.2349,106.9896',
                    Ipinfo::ORG => 'AS17974 PT Telekomunikasi Indonesia',
                    Ipinfo::PHONE => '',
                    Ipinfo::POSTAL => '',
                    Ipinfo::REGION => 'West Java',
                ],
                'expected' => [
                    'hostname' => 'bekasi.telkom.co.id',
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'ip' => '180.250.116.128',
                    'ip_private' => '',
                    'location' => '-6.2349,106.9896',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '',
                    'postal' => '',
                    'region' => 'West Java',
                    'timezone' => '',
                ],
            ],
        ];
    }

    /**
     * @dataProvider hostData
     * @param string $serverAddress
     * @param array  $hostdata
     * @param array  $expected
     */
    public function testHostInfoWithLocalAddr(
        string $serverAddress,
        array $hostdata,
        array $expected
    ) {
        $this->ipinfo->shouldReceive('getYourOwnIpDetails')
            ->andReturn(new Host($hostdata));

        $this->request->shouldReceive('server')
            ->with('LOCAL_ADDR')
            ->andReturn($serverAddress);

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(GeoIpInfo::class, $larinfo->hostIpInfo());
        $this->assertEquals(
            $expected,
            $larinfo->getHostIpinfo()
        );
    }

    /**
     * @dataProvider hostData
     * @param string $serverAddress
     * @param array  $hostdata
     * @param array  $expected
     */
    public function testHostInfoWithServerAddr(
        string $serverAddress,
        array $hostdata,
        array $expected
    ) {
        $this->ipinfo->shouldReceive('getYourOwnIpDetails')
            ->andReturn(new Host($hostdata));

        $this->request->shouldReceive('server')
            ->with('LOCAL_ADDR')
            ->andReturnNull();
        $this->request->shouldReceive('server')
            ->with('SERVER_ADDR')
            ->andReturn($serverAddress);

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(GeoIpInfo::class, $larinfo->hostIpInfo());
        $this->assertEquals(
            $expected,
            $larinfo->getHostIpinfo()
        );
    }

    /**
     * @dataProvider hostData
     * @param string $serverAddress
     * @param array  $hostdata
     * @param array  $expected
     */
    public function testClientInfo(
        string $serverAddress,
        array $hostdata,
        array $expected
    ) {
        $this->ipinfo->shouldReceive('getFullIpDetails')
            ->with($serverAddress)
            ->andReturn(new Host($hostdata));

        $this->request->shouldReceive('ip')
            ->andReturn($serverAddress);

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(GeoIpInfo::class, $larinfo->clientIpInfo());
        $this->assertEquals(
            $expected,
            $larinfo->getClientIpinfo()
        );
    }

    /**
     * @return array[]
     */
    public static function softwareData(): array
    {
        return [
            'unknown' => [
                'os' => null,
                'expected' => [
                    'os' => 'Unknown',
                    'distro' => '',
                    'kernel' => '',
                    'arc' => '',
                    'webserver' => '',
                    'php' => '',
                ],
            ],
            'macOS 10.15.4' => [
                'os' => Mockery::mock(Darwin::class, [
                    'getOS' => 'Darwin (macOS 10.15.4)',
                    'getKernel' => '19.6.0',
                    'getCPUArchitecture' => 'x86_64',
                    'getWebService' => 'nginx/1.19.8',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'MacOS',
                    'distro' => 'MacOS 10.15.4',
                    'kernel' => '19.6.0',
                    'arc' => 'x86_64',
                    'webserver' => 'nginx/1.19.8',
                    'php' => '8.0.3',
                ],
            ],
            'macOS' => [
                'os' => Mockery::mock(Darwin::class, [
                    'getOS' => 'Darwin',
                    'getKernel' => '19.6.0',
                    'getCPUArchitecture' => 'x86_64',
                    'getWebService' => 'nginx/1.19.8',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'MacOS',
                    'distro' => 'MacOS',
                    'kernel' => '19.6.0',
                    'arc' => 'x86_64',
                    'webserver' => 'nginx/1.19.8',
                    'php' => '8.0.3',
                ],
            ],
            'macOS X' => [
                'os' => Mockery::mock(Darwin::class, [
                    'getOS' => 'Darwin (Mac OS X)',
                    'getKernel' => '19.6.0',
                    'getCPUArchitecture' => 'x86_64',
                    'getWebService' => 'nginx/1.19.8',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'MacOS',
                    'distro' => 'MacOS X',
                    'kernel' => '19.6.0',
                    'arc' => 'x86_64',
                    'webserver' => 'nginx/1.19.8',
                    'php' => '8.0.3',
                ],
            ],
            'windows' => [
                'os' => Mockery::mock(Windows::class, [
                    'getOS' => 'Microsoft Windows 10 Enterprise Evaluation',
                    'getKernel' => '19.6.0',
                    'getCPUArchitecture' => 'x64',
                    'getWebService' => 'nginx/1.19.8',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'Windows',
                    'distro' => 'Microsoft Windows 10 Enterprise Evaluation',
                    'kernel' => '19.6.0',
                    'arc' => 'x64',
                    'webserver' => 'nginx/1.19.8',
                    'php' => '8.0.3',
                ],
            ],
            'windows os' => [
                'os' => Mockery::mock(WindowsOs::class, [
                    'getDistro' => [
                        'name' => 'Microsoft Windows',
                        'version' => '10.0',
                    ],
                    'getKernel' => '19.6.0',
                    'getCPUArchitecture' => 'AMD64',
                    'getWebService' => 'nginx/1.19.8',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'Windows',
                    'distro' => 'Microsoft Windows 10.0',
                    'kernel' => '19.6.0',
                    'arc' => 'AMD64',
                    'webserver' => 'nginx/1.19.8',
                    'php' => '8.0.3',
                ],
            ],
            'ubuntu' => [
                'os' => Mockery::mock(Linux::class, [
                    'getOS' => 'Linux',
                    'getKernel' => '19.6.0',
                    'getDistro' => [
                        'name' => 'Ubuntu',
                        'version' => '20.04.2 LTS',
                    ],
                    'getCPUArchitecture' => 'x86_64',
                    'getWebService' => 'nginx/1.19.8',
                    'getPhpVersion' => '8.0.3',
                ]),
                'expected' => [
                    'os' => 'Linux',
                    'distro' => 'Ubuntu 20.04.2 LTS',
                    'kernel' => '19.6.0',
                    'arc' => 'x86_64',
                    'webserver' => 'nginx/1.19.8',
                    'php' => '8.0.3',
                ],
            ],
        ];
    }

    /**
     * @dataProvider softwareData
     * @param OS|null $os
     * @param array   $expected
     */
    public function testServerSoftwareInfo(
        ?OS $os,
        array $expected
    ) {
        $this->linfo->shouldReceive('getParser')
            ->andReturn($os);

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(ServerInfo::class, $larinfo->serverInfoSoftware());
        $this->assertEquals(
            $expected,
            $larinfo->getServerInfoSoftware()
        );
    }

    /**
     * @return array[]
     */
    public static function hardwareData(): array
    {
        return [
            'unknown' => [
                'os' => null,
                'expected' => [
                    'cpu' => '',
                    'cpu_count' => 0,
                    'model' => '',
                    'virtualization' => '',
                    'disk' => [
                        'total' => 0,
                        'free' => 0,
                        'human_total' => '0 B',
                        'human_free' => '0 B',
                    ],
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
            'minix' => [
                'os' => Mockery::mock(Minix::class, [
                    'getMounts' => [
                        [
                            'size' => 1000,
                            'free' => 500,
                        ],
                    ],
                ]),
                'expected' => [
                    'cpu' => '',
                    'cpu_count' => 0,
                    'model' => '',
                    'virtualization' => '',
                    'disk' => [
                        'total' => 1000,
                        'free' => 500,
                        'human_total' => '1 KB',
                        'human_free' => '500 B',
                    ],
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
            'mac' => [
                'os' => Mockery::mock(Darwin::class, [
                    'getCPU' => [
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                            'MHz' => '2500',
                        ],
                        [
                            'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.70GHz',
                            'MHz' => '2700',
                        ],
                    ],
                    'getModel' => 'MacBook Pro',
                    'getVirtualization' => [],
                    'getMounts' => [
                        [
                            'size' => 1000,
                            'free' => 500,
                        ],
                    ],
                    'getRam' => [
                        'total' => 1500,
                        'free' => 500,
                        'swapTotal' => 500,
                        'swapFree' => 100,
                    ],
                ]),
                'expected' => [
                    'cpu' => 'Intel® Core™ i5-3210M CPU @ 2.50GHz / Intel® Core™ i5-3210M CPU @ 2.70GHz',
                    'cpu_count' => 2,
                    'model' => 'MacBook Pro',
                    'virtualization' => '',
                    'disk' => [
                        'total' => 1000,
                        'free' => 500,
                        'human_total' => '1 KB',
                        'human_free' => '500 B',
                    ],
                    'ram' => [
                        'total' => 1500,
                        'free' => 500,
                        'human_total' => '2 KB',
                        'human_free' => '500 B',
                    ],
                    'swap' => [
                        'total' => 500,
                        'free' => 100,
                        'human_total' => '500 B',
                        'human_free' => '100 B',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider hardwareData
     * @param OS|null $os
     * @param array   $expected
     */
    public function testServerHardwareInfo(
        ?OS $os,
        array $expected
    ) {
        $this->linfo->shouldReceive('getParser')
            ->andReturn($os);

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(HardwareInfo::class, $larinfo->serverInfoHardware());
        $this->assertEquals(
            $expected,
            $larinfo->getServerInfoHardware()
        );
    }

    /**
     * @return array[]
     */
    public static function systemData(): array
    {
        return [
            'unknown' => [
                'os' => null,
                'expected' => [
                    'uptime' => '',
                    'booted_at' => '',
                ],
            ],
            'mac' => [
                'os' => Mockery::mock(Darwin::class, [
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
        ];
    }

    /**
     * @dataProvider systemData
     * @param OS|null $os
     * @param array   $expected
     */
    public function testSystemInfoUptime(
        ?OS $os,
        array $expected
    ) {
        $this->linfo->shouldReceive('getParser')
            ->andReturn($os);

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(SystemInfo::class, $larinfo->systemInfo());
        $this->assertEquals(
            $expected,
            $larinfo->getUptime()
        );
    }

    public function testDatabaseInfo()
    {
        $pdo = Mockery::mock(\PDO::class);
        $pdo->shouldReceive('getAttribute')
            ->with(\PDO::ATTR_DRIVER_NAME)
            ->andReturn('mysql');
        $pdo->shouldReceive('getAttribute')
            ->with(\PDO::ATTR_SERVER_VERSION)
            ->andReturn('5.7.18');

        $this->database->shouldReceive('getConnection')
            ->andReturn(
                Mockery::mock(Connection::class, [
                    'getPdo' => $pdo,
                ])
            );

        $larinfo = $this->larinfo();

        $this->assertInstanceOf(DatabaseInfo::class, $larinfo->databaseInfo());
        $this->assertEquals(
            [
                'driver' => 'MySQL',
                'version' => '5.7.18',
            ],
            $larinfo->getDatabaseInfo()
        );
    }

    /**
     * @return Larinfo
     */
    private function larinfo(): Larinfo
    {
        return new Larinfo(
            $this->ipinfo,
            $this->request,
            $this->linfo,
            $this->database,
            $this->ipAddressChecker,
            $this->converter,
            0,
            false
        );
    }
}
