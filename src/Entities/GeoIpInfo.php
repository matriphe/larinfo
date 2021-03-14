<?php

namespace Matriphe\Larinfo\Entities;

use DavidePastore\Ipinfo\Host;
use Illuminate\Contracts\Support\Arrayable;

final class GeoIpInfo implements Arrayable
{
    /**
     * @var Host
     */
    private Host $host;

    /**
     * GeoIpInfo constructor.
     * @param Host $host
     */
    public function __construct(Host $host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return trim($this->host->getIp());
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return trim($this->host->getHostname());
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return trim($this->host->getCity());
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return trim($this->host->getRegion());
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return trim($this->host->getCountry());
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return trim($this->host->getLoc());
    }

    /**
     * @return string
     */
    public function getOrg(): string
    {
        return trim($this->host->getOrg());
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return trim($this->host->getPhone());
    }

    /**
     * @return string|null
     */
    public function getPostal(): string
    {
        return trim($this->host->getPostal());
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'ip' => $this->getIp(),
            'hostname' => $this->getHostname(),
            'region' => $this->getRegion(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'location' => $this->getLocation(),
            'org' => $this->getOrg(),
            'phone' => $this->getPhone(),
            'postal' => $this->getPostal(),
        ];
    }
}
