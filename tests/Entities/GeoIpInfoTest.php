<?php

namespace Matriphe\Larinfo\Tests\Entities;

use DavidePastore\Ipinfo\Host;
use Matriphe\Larinfo\Entities\GeoIpInfo;
use PHPUnit\Framework\TestCase;

class GeoIpInfoTest extends TestCase
{
    /**
     * @return array
     */
    public function hostData(): array
    {
        return [
            'empty host' => [
                'host' => [],
                'expected' => [
                    'ip' => '',
                    'hostname' => '',
                    'region' => '',
                    'city' => '',
                    'country' => '',
                    'location' => '',
                    'timezone' => '',
                    'org' => '',
                    'phone' => '',
                    'postal' => '',
                ],
            ],
            'full host data' => [
                'host' => [
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'hostname' => 'indihome.telkom.co.id',
                    'ip' => '180.252.202.108',
                    'loc' => '-6.2349,106.9896',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '021-147',
                    'postal' => '17144',
                    'region' => 'West Java',
                ],
                'expected' => [
                    'ip' => '180.252.202.108',
                    'hostname' => 'indihome.telkom.co.id',
                    'region' => 'West Java',
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'location' => '-6.2349,106.9896',
                    'timezone' => '',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '021-147',
                    'postal' => '17144',
                ],
            ],
            'some host data' => [
                'host' => [
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'ip' => '180.252.202.108',
                    'loc' => '-6.2349,106.9896',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'region' => 'West Java',
                ],
                'expected' => [
                    'ip' => '180.252.202.108',
                    'hostname' => '',
                    'region' => 'West Java',
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'location' => '-6.2349,106.9896',
                    'timezone' => '',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '',
                    'postal' => '',
                ],
            ],
            'some host data with timezone' => [
                'host' => [
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'ip' => '180.252.202.108',
                    'loc' => '-6.2349,106.9896',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'region' => 'West Java',
                    'timezone' => 'Asia/Jakarta',
                ],
                'expected' => [
                    'ip' => '180.252.202.108',
                    'hostname' => '',
                    'region' => 'West Java',
                    'city' => 'Bekasi',
                    'country' => 'ID',
                    'location' => '-6.2349,106.9896',
                    'timezone' => 'Asia/Jakarta',
                    'org' => 'AS17974 PT Telekomunikasi Indonesia',
                    'phone' => '',
                    'postal' => '',
                ],
            ],
        ];
    }

    /**
     * @dataProvider hostData
     * @param array $host
     * @param array $expected
     */
    public function testItReturnsCorrectValues(array $host, array $expected): void
    {
        $host = new Host($host);
        $geoIpInfo = new GeoIpInfo($host);

        $this->assertEquals($expected, $geoIpInfo->toArray());
        $this->assertEquals($expected['ip'], $geoIpInfo->getIp());
        $this->assertEquals($expected['hostname'], $geoIpInfo->getHostname());
        $this->assertEquals($expected['region'], $geoIpInfo->getRegion());
        $this->assertEquals($expected['city'], $geoIpInfo->getCity());
        $this->assertEquals($expected['country'], $geoIpInfo->getCountry());
        $this->assertEquals($expected['location'], $geoIpInfo->getLocation());
        $this->assertEquals($expected['timezone'], $geoIpInfo->getTimezone());
        $this->assertEquals($expected['org'], $geoIpInfo->getOrg());
        $this->assertEquals($expected['phone'], $geoIpInfo->getPhone());
        $this->assertEquals($expected['postal'], $geoIpInfo->getPostal());
    }
}
