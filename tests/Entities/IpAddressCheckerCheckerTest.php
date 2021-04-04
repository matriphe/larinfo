<?php

namespace Matriphe\Larinfo\Tests\Entities;

use Matriphe\Larinfo\Entities\IpAddressChecker;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group entity
 */
final class IpAddressCheckerCheckerTest extends TestCase
{
    /**
     * @return array
     */
    public function ipAddressData(): array
    {
        return [
            [
                'ip' => '127.0.0.1',
                'isValid' => true,
                'isPrivate' => true,
            ],
            [
                'ip' => '192.168.1.123',
                'isValid' => true,
                'isPrivate' => true,
            ],
            [
                'ip' => '192.168.178.1',
                'isValid' => true,
                'isPrivate' => true,
            ],
            [
                'ip' => '172.16.10.10',
                'isValid' => true,
                'isPrivate' => true,
            ],
            [
                'ip' => '1.1.1.1',
                'isValid' => true,
                'isPrivate' => false,
            ],
            [
                'ip' => '255.255.255.255',
                'isValid' => true,
                'isPrivate' => true,
            ],
            [
                'ip' => 'localhost',
                'isValid' => false,
                'isPrivate' => null,
            ],
            [
                'ip' => 'localhost',
                'isValid' => false,
                'isPrivate' => null,
            ],
        ];
    }

    /**
     * @dataProvider ipAddressData
     * @param string    $ip
     * @param bool      $isValid
     * @param bool|null $isPrivate
     */
    public function testIpAddressCheckerWithConstructor(string $ip, bool $isValid, ?bool $isPrivate)
    {
        $ipAddress = new IpAddressChecker($ip);

        $this->assertEquals($isValid, $ipAddress->isValid());
        $this->assertEquals($isPrivate, $ipAddress->isPrivate());
    }

    /**
     * @dataProvider ipAddressData
     * @param string    $ip
     * @param bool      $isValid
     * @param bool|null $isPrivate
     */
    public function testIpAddressCheckerWithSetter(string $ip, bool $isValid, ?bool $isPrivate)
    {
        $ipAddress = new IpAddressChecker();
        $ipAddress->setIpAddress($ip);

        $this->assertEquals($isValid, $ipAddress->isValid());
        $this->assertEquals($isPrivate, $ipAddress->isPrivate());
    }
}
