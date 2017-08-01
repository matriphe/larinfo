<?php

namespace Matriphe\Larinfo\Tests;

use DavidePastore\Ipinfo\Host;
use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionInterface;
use Linfo\Linfo;
use Linfo\OS\OS;
use Matriphe\Larinfo\Larinfo;
use Mockery;
use PDOMock;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    protected function getPdo()
    {
        return Mockery::mock(PDOMock::class, array(
            'getAttribute' => 'mysql',
        ));
    }

    protected function getConnection()
    {
        return Mockery::mock(ConnectionInterface::class, array(
            'getPdo' => $this->getPdo(),
        ));
    }

    protected function getDbManager()
    {
        return Mockery::mock(Manager::class, array(
            'getConnection' => $this->getConnection(),
        ));
    }

    protected function getRequest()
    {
        return Mockery::mock(Request::class, array(
            'getClientIp' => '112.215.171.128',
        ));
    }

    protected function getHostIpinfo()
    {
        return Mockery::mock(Host::class, array(
            'getProperties' => array(
                Ipinfo::CITY => 'Bekasi',
                Ipinfo::COUNTRY => 'ID',
                Ipinfo::HOSTNAME => '',
                Ipinfo::IP => '180.250.116.128',
                Ipinfo::LOC => '-6.2349,106.9896',
                Ipinfo::ORG => 'AS17974 PT Telekomunikasi Indonesia',
                Ipinfo::PHONE => '',
                Ipinfo::POSTAL => '',
                Ipinfo::REGION => 'West Java',
            ),
        ));
    }

    protected function getClientIpinfo()
    {
        return Mockery::mock(Host::class, array(
            'getProperties' => array(
                Ipinfo::CITY => 'Bandung',
                Ipinfo::COUNTRY => 'ID',
                Ipinfo::HOSTNAME => '',
                Ipinfo::IP => '112.215.171.128',
                Ipinfo::LOC => '-6.9039,107.6186',
                Ipinfo::ORG => 'AS24203 PT Excelcomindo Pratama (Network Access Provider)',
                Ipinfo::PHONE => '',
                Ipinfo::POSTAL => '',
                Ipinfo::REGION => 'West Java',
            ),
        ));
    }

    protected function getLinfoParser()
    {
        return Mockery::mock(OS::class, array(
            'getOS' => 'Linux',
            'getKernel' => '1.2.3',
            'getCPUArchitecture' => 'x86_64',
            'getWebService' => 'Unknown',
            'getPhpVersion' => '7.1',
            'getDistro' => '',
            'getCPU' => array(
                array(
                    'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2.50GHz',
                ),
                array(
                    'Model' => 'Intel(R) Core(TM) i5-3210M CPU @ 2GHz',
                ),
            ),
            'getModel' => 'Macbook',
            'getVirtualization' => array(
                'method' => 'Qemu/KVM',
            ),
            'getRam' => array(
                'total' => 1000000,
                'free' => 500000,
                'swapTotal' => 500000,
                'swapFree' => 250000,
            ),
            'getMounts' => array(
                array(
                    'size' => 1000000,
                    'free' => 500000,
                ),
                array(
                    'size' => 1000000,
                    'free' => 500000,
                ),
            ),
            'getUpTime' => array(
                'text' => '1 year',
                'bootedTimestamp' => 1501582800,
            ),
        ));
    }

    protected function getLinfo()
    {
        return Mockery::mock(Linfo::class, array(
            '__construct' => array(),
            'getParser' => $this->getLinfoParser(),
        ));
    }

    protected function getIpinfo()
    {
        return Mockery::mock(Ipinfo::class, array(
            'getYourOwnIpDetails' => $this->getHostIpinfo(),
            'getFullIpDetails' => $this->getClientIpinfo(),
            'getProperties' => array(),
        ));
    }

    protected function getLarinfo()
    {
        return new Larinfo(
            $this->getIpinfo(),
            $this->getRequest(),
            $this->getLinfo(),
            $this->getDbManager()
        );
    }
}
