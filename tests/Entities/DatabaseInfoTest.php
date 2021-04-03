<?php

namespace Matriphe\Larinfo\Tests\Entities;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Matriphe\Larinfo\Entities\DatabaseInfo;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group entity
 */
class DatabaseInfoTest extends TestCase
{
    /**
     * @return \string[][]
     */
    public function driverData(): array
    {
        return [
            'return mysql' => [
                'driver' => 'mysql',
                'expected' => 'MySQL',
            ],
            'return empty' => [
                'driver' => '',
                'expected' => '',
            ],
            'return empty for null' => [
                'driver' => null,
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider driverData
     * @param mixed  $driver
     * @param string $expected
     */
    public function testGetDriverReturnsCorrectValues($driver, string $expected): void
    {
        $manager = $this->mockDatabaseManager(\PDO::ATTR_DRIVER_NAME, $driver);
        $databaseInfo = new DatabaseInfo($manager);
        $this->assertEquals($expected, $databaseInfo->getDriver());
    }

    public function versionData(): array
    {
        return [
            'return mysql' => [
                'version' => '8.0',
                'expected' => '8.0',
            ],
            'return empty' => [
                'version' => '',
                'expected' => '',
            ],
            'return empty for null' => [
                'version' => null,
                'expected' => '',
            ],
        ];
    }

    /**
     * @dataProvider versionData
     * @param mixed  $version
     * @param string $expected
     */
    public function testGetVersionReturnsCorrectValues($version, string $expected): void
    {
        $manager = $this->mockDatabaseManager(\PDO::ATTR_SERVER_VERSION, $version);
        $databaseInfo = new DatabaseInfo($manager);
        $this->assertEquals($expected, $databaseInfo->getVersion());
    }

    /**
     * @param  int     $argument
     * @param  mixed   $result
     * @return Manager
     */
    private function mockDatabaseManager(int $argument, $result): Manager
    {
        $pdoMock = Mockery::mock(\PDO::class);
        $pdoMock->shouldReceive('getAttribute')
            ->with($argument)
            ->andReturn($result);

        $connectionMock = Mockery::mock(Connection::class);
        $connectionMock->shouldReceive('getPdo')
            ->andReturn($pdoMock);

        $managerMock = Mockery::mock(Manager::class);
        $managerMock->shouldReceive('getConnection')
            ->andReturn($connectionMock);

        return $managerMock;
    }
}
