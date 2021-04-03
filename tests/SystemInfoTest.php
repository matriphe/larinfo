<?php

namespace Matriphe\Larinfo\Tests;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use Linfo\Linfo;
use Matriphe\Larinfo\Entities\IpAddressChecker;
use Matriphe\Larinfo\Larinfo;
use Mockery;
use PHPUnit\Framework\TestCase;

class SystemInfoTest extends TestCase
{
    /**
     * @var Larinfo
     */
    private $larinfo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->larinfo = $larinfo = new Larinfo(
            new Ipinfo(),
            Request::capture(),
            new Linfo(),
            Mockery::mock(Manager::class),
            new IpAddressChecker()
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group macos
     */
    public function testMacOsCatalina()
    {
        $info = $this->larinfo->serverInfoSoftware();

        $this->assertEquals('MacOS', $info->getOS());
        $this->assertEquals('MacOS', $info->getDistroName());
        $this->assertMatchesRegularExpression('/\d+\.\d+\.\d+/i', $info->getDistroVersion());
        $this->assertMatchesRegularExpression('/\d+\.\d+\.[\d\-a-z]+/i', $info->getKernel());
        $this->assertEquals('x86_64', $info->getArch());
    }

    /**
     * @group ubuntu
     */
    public function testUbuntu()
    {
        $info = $this->larinfo->serverInfoSoftware();

        $this->assertEquals('Linux', $info->getOS());
        $this->assertEquals('Ubuntu', $info->getDistroName());
        $this->assertMatchesRegularExpression('/\d+\.\d+\.[\d\-a-z]+/i', $info->getKernel());
        $this->assertEquals('x86_64', $info->getArch());
    }
}
