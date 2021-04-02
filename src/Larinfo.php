<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager as Database;
use Illuminate\Http\Request;
use Linfo\Linfo;
use Matriphe\Larinfo\Entities\DatabaseInfo;
use Matriphe\Larinfo\Entities\GeoIpInfo;
use Matriphe\Larinfo\Entities\HardwareInfo;
use Matriphe\Larinfo\Entities\IpAddressChecker;
use Matriphe\Larinfo\Entities\ServerInfo;
use Matriphe\Larinfo\Entities\SystemInfo;

class Larinfo implements LarinfoContract
{
    /**
     * @var Ipinfo
     */
    private Ipinfo $ipinfo;
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var Linfo
     */
    private Linfo $linfo;
    /**
     * @var Database
     */
    private Database $database;
    /**
     * @var IpAddressChecker
     */
    private IpAddressChecker $ipAddressChecker;

    /**
     * @param Ipinfo           $ipinfo
     * @param Request          $request
     * @param Linfo            $linfo
     * @param Database         $database
     * @param IpAddressChecker $ipAddressChecker
     */
    public function __construct(
        Ipinfo $ipinfo,
        Request $request,
        Linfo $linfo,
        Database $database,
        IpAddressChecker $ipAddressChecker
    ) {
        $this->ipinfo = $ipinfo;
        $this->request = $request;
        $this->linfo = $linfo;
        $this->database = $database;
        $this->ipAddressChecker = $ipAddressChecker;
    }

    /**
     * @param  array $connection
     * @return $this
     */
    public function setDatabaseConfig(array $connection = []): self
    {
        $this->database->addConnection($connection);

        return $this;
    }

    /**
     * @return GeoIpInfo|null
     */
    public function hostIpInfo(): ?GeoIpInfo
    {
        try {
            $serverIp = $this->getServerIpAddress();
            $ipCheck = $this->ipAddressChecker->setIpAddress($serverIp);

            return new GeoIpInfo(
                $this->ipinfo->getYourOwnIpDetails(),
                $ipCheck->isPrivate() === true ? $serverIp : ''
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getHostIpinfo(): array
    {
        $hostinfo = $this->hostIpInfo();
        if ($hostinfo === null) {
            return [];
        }

        return $hostinfo->toArray();
    }

    /**
     * @return GeoIpInfo|null
     */
    public function clientIpInfo(): ?GeoIpInfo
    {
        try {
            $clientIp = $this->request->ip();
            $ipCheck = $this->ipAddressChecker->setIpAddress($clientIp);

            return new GeoIpInfo(
                $this->ipinfo->getFullIpDetails($clientIp),
                $ipCheck->isPrivate() === true ? $clientIp : ''
            );
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function getClientIpinfo(): array
    {
        $clientInfo = $this->clientIpInfo();
        if ($clientInfo === null) {
            return [];
        }

        return $clientInfo->toArray();
    }

    /**
     * @return ServerInfo
     */
    public function serverInfoSoftware(): ServerInfo
    {
        return new ServerInfo($this->linfo);
    }

    /**
     * @return array
     */
    public function getServerInfoSoftware(): array
    {
        return $this->serverInfoSoftware()->toArray();
    }

    /**
     * @return HardwareInfo
     */
    public function serverInfoHardware(): HardwareInfo
    {
        return new HardwareInfo($this->linfo);
    }

    /**
     * @return array
     */
    public function getServerInfoHardware(): array
    {
        return $this->serverInfoHardware()->toArray();
    }

    /**
     * @return SystemInfo
     */
    public function systemInfo(): SystemInfo
    {
        return new SystemInfo($this->linfo);
    }

    /**
     * Get server uptime.
     *
     * @access public
     * @return array
     */
    public function getUptime(): array
    {
        return $this->systemInfo()->toArray();
    }

    /**
     * @return array[]
     */
    public function getServerInfo(): array
    {
        return [
            'software' => $this->getServerInfoSoftware(),
            'hardware' => $this->getServerInfoHardware(),
            'uptime' => $this->getUptime(),
        ];
    }

    /**
     * @return DatabaseInfo
     */
    public function databaseInfo(): DatabaseInfo
    {
        return new DatabaseInfo($this->database);
    }

    /**
     * @return array
     */
    public function getDatabaseInfo(): array
    {
        return $this->databaseInfo()->toArray();
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return [
            'host' => $this->getHostIpinfo(),
            'client' => $this->getClientIpinfo(),
            'server' => $this->getServerInfo(),
            'database' => $this->getDatabaseInfo(),
        ];
    }

    /**
     * @return string
     */
    private function getServerIpAddress(): string
    {
        // for IIS
        if (! empty($_SERVER['LOCAL_ADDR'])) {
            return $_SERVER['LOCAL_ADDR'];
        }

        return $_SERVER['SERVER_ADDR'] ?? '';
    }
}
