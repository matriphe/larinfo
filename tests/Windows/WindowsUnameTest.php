<?php

namespace Matriphe\Larinfo\Tests\Windows;

use Matriphe\Larinfo\Windows\WindowsUname;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class WindowsUnameTest extends TestCase
{
    /**
     * @return \string[][]
     */
    public static function kernelData(): array
    {
        return [
            [
                'release' => '10.0',
                'version' => 'build 10240 (Windows 10)',
                'kernel' => '10.0.10240',
            ],
            [
                'release' => '10.0',
                'version' => 'build 10240',
                'kernel' => '10.0.10240',
            ],
            [
                'release' => '10.0',
                'version' => 'build nothing',
                'kernel' => '10.0',
            ],
        ];
    }

    /**
     * @dataProvider kernelData
     * @param string $release
     * @param string $version
     * @param string $kernel
     */
    public function testGetKernel(string $release, string $version, string $kernel)
    {
        $uname = Mockery::mock(WindowsUname::class, [
            'getRelease' => $release,
            'getVersion' => $version,
        ])->makePartial();

        $this->assertEquals($kernel, $uname->getKernel());
    }
}
