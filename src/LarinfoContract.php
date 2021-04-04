<?php

namespace Matriphe\Larinfo;

use Matriphe\Larinfo\Entities\DatabaseInfo;
use Matriphe\Larinfo\Entities\GeoIpInfo;
use Matriphe\Larinfo\Entities\HardwareInfo;
use Matriphe\Larinfo\Entities\ServerInfo;
use Matriphe\Larinfo\Entities\SystemInfo;

interface LarinfoContract
{
    /**
     * Set database connection.
     * @param  array           $connection
     * @return LarinfoContract
     */
    public function setDatabaseConfig(array $connection = []): self;

    /**
     * @return GeoIpInfo|null
     */
    public function hostIpInfo(): ?GeoIpInfo;

    /**
     * Get Host IP info.
     * @return array
     */
    public function getHostIpinfo(): array;

    /**
     * @return GeoIpInfo|null
     */
    public function clientIpInfo(): ?GeoIpInfo;

    /**
     * Get Client IP info.
     * @return array
     */
    public function getClientIpinfo(): array;

    /**
     * @return ServerInfo
     */
    public function serverInfoSoftware(): ServerInfo;

    /**
     * Get server software info.
     * @return array
     */
    public function getServerInfoSoftware(): array;

    /**
     * @return HardwareInfo
     */
    public function serverInfoHardware(): HardwareInfo;

    /**
     * Get server hardware info.
     * @return array
     */
    public function getServerInfoHardware(): array;

    /**
     * @return SystemInfo
     */
    public function systemInfo(): SystemInfo;

    /**
     * Get server uptime.
     * @return array
     */
    public function getUptime(): array;

    /**
     * Get server info.
     * @return array
     */
    public function getServerInfo(): array;

    /**
     * @return DatabaseInfo
     */
    public function databaseInfo(): DatabaseInfo;

    /**
     * Get database info.
     * @return array
     */
    public function getDatabaseInfo(): array;

    /**
     * Get all info.
     * @return array
     */
    public function getInfo(): array;
}
