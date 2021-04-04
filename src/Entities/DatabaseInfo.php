<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Capsule\Manager;
use PDO;

class DatabaseInfo implements Arrayable
{
    private const DATABASE = [
        'mysql' => 'MySQL',
        'sqlite' => 'SQLite',
        'pgsql' => 'PostgreSQL',
        'oracle' => 'Oracle',
        'sqlsrv' => 'Microsoft SQL Server',
    ];

    /**
     * @var Manager
     */
    private Manager $database;

    /**
     * DatabaseInfo constructor.
     * @param Manager $database
     */
    public function __construct(Manager $database)
    {
        $this->database = $database;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        $driver = $this->getPdoAttribute(PDO::ATTR_DRIVER_NAME);

        return self::DATABASE[$driver] ?? '';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return trim($this->getPdoAttribute(PDO::ATTR_SERVER_VERSION));
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'driver' => $this->getDriver(),
            'version' => $this->getVersion(),
        ];
    }

    /**
     * @param  int    $attr
     * @return string
     */
    private function getPdoAttribute(int $attr): string
    {
        return (string) $this->database
            ->getConnection()
            ->getPdo()
            ->getAttribute($attr);
    }
}
