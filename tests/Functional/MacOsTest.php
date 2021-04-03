<?php

namespace Matriphe\Larinfo\Tests\Functional;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use Linfo\Linfo;
use Matriphe\Larinfo\Entities\IpAddressChecker;
use Matriphe\Larinfo\Larinfo;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @group macos
 */
class MacOsTest extends TestCase
{
    public function testSystemInfo()
    {
        $larinfo = new Larinfo(
            new Ipinfo(),
            Request::capture(),
            new Linfo(),
            Mockery::mock(Manager::class),
            new IpAddressChecker()
        );

        $this->assertEquals([
            'os' => 'MacOS 10.15.7',
            'distro' => '',
            'kernel' => '19.6.0',
            'arc' => 'x86_64',
            'webserver' => 'Unknown',
            'php' => '8.0.3',
        ], $larinfo->getServerInfoSoftware());
    }
}
