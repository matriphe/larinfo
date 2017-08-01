<?php

namespace Matriphe\Larinfo\Tests;

class LarinfoTest extends TestCase
{
    /**
     * @test
     */
    public function testServerIpinfoReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $ipinfo = $larinfo->getHostIpinfo();

        $this->assertTrue(is_array($ipinfo));
        $this->assertSame('Bekasi', $ipinfo['city']);
        $this->assertSame('ID', $ipinfo['country']);
        $this->assertSame('', $ipinfo['hostname']);
        $this->assertSame('180.250.116.128', $ipinfo['ip']);
        $this->assertSame('-6.2349,106.9896', $ipinfo['loc']);
        $this->assertSame('AS17974 PT Telekomunikasi Indonesia', $ipinfo['org']);
        $this->assertSame('', $ipinfo['phone']);
        $this->assertSame('', $ipinfo['postal']);
        $this->assertSame('West Java', $ipinfo['region']);
    }

    /**
     * @test
     */
    public function testClientIpinfoReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $ipinfo = $larinfo->getClientIpinfo();

        $this->assertTrue(is_array($ipinfo));
        $this->assertSame('Bandung', $ipinfo['city']);
        $this->assertSame('ID', $ipinfo['country']);
        $this->assertSame('', $ipinfo['hostname']);
        $this->assertSame('112.215.171.128', $ipinfo['ip']);
        $this->assertSame('-6.9039,107.6186', $ipinfo['loc']);
        $this->assertSame('AS24203 PT Excelcomindo Pratama (Network Access Provider)', $ipinfo['org']);
        $this->assertSame('', $ipinfo['phone']);
        $this->assertSame('', $ipinfo['postal']);
        $this->assertSame('West Java', $ipinfo['region']);
    }

    /**
     * @test
     */
    public function testServerInfoSoftwareReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $serverinfo = $larinfo->getServerInfoSoftware();

        $this->assertTrue(is_array($serverinfo));
        $this->assertSame('Linux', $serverinfo['os']);
        $this->assertSame('', $serverinfo['distro']);
        $this->assertSame('1.2.3', $serverinfo['kernel']);
        $this->assertSame('x86_64', $serverinfo['arc']);
        $this->assertSame('Unknown', $serverinfo['webserver']);
        $this->assertSame('7.1', $serverinfo['php']);
    }

    /**
     * @test
     */
    public function testServerInfoHardwareReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $serverinfo = $larinfo->getServerInfoHardware();

        $this->assertTrue(is_array($serverinfo));
        $this->assertSame('Intel® Core™ i5-3210M CPU @ 2.50GHz / Intel® Core™ i5-3210M CPU @ 2GHz', $serverinfo['cpu']);
        $this->assertSame(2, $serverinfo['cpu_count']);
        $this->assertSame('Macbook', $serverinfo['model']);
        $this->assertSame('Qemu/KVM', $serverinfo['virtualization']);
        $this->assertSame(['total' => 1000000, 'free' => 500000], $serverinfo['ram']);
        $this->assertSame(['total' => 500000, 'free' => 250000], $serverinfo['swap']);
        $this->assertSame(['total' => 2000000, 'free' => 1000000], $serverinfo['disk']);
    }

    /**
     * @test
     */
    public function testUptimeReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $uptime = $larinfo->getUptime();

        $this->assertTrue(is_array($uptime));
        $this->assertSame(['uptime' => '1 year', 'booted_at' => '2017-08-01 10:20:00'], $uptime);
    }

    /**
     * @test
     */
    public function testDatabaseInfoReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $database = $larinfo->getDatabaseInfo();

        $this->assertTrue(is_array($database));
        $this->assertSame('MySQL', $database['driver']);
        $this->assertSame('mysql', $database['version']);
    }

    /**
     * @test
     */
    public function testGetInfoReturnArray()
    {
        $larinfo = $this->getLarinfo();

        $info = $larinfo->getInfo();

        $this->assertTrue(is_array($info));
        $this->assertTrue(is_array($info['host']));
        $this->assertTrue(is_array($info['client']));
        $this->assertTrue(is_array($info['server']));
        $this->assertTrue(is_array($info['server']['hardware']));
        $this->assertTrue(is_array($info['server']['software']));
        $this->assertTrue(is_array($info['server']['uptime']));
        $this->assertTrue(is_array($info['database']));
    }
}
