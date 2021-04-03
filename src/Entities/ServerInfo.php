<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Linfo\OS\Darwin;
use Linfo\OS\Linux;

final class ServerInfo extends LinfoEntity implements Arrayable
{
    private const OS_UNKNOWN = 'Unknown';
    private const OS_MAC = 'MacOS';

    /**
     * @return string
     */
    public function getOS(): string
    {
        if ($this->linfo === null) {
            return self::OS_UNKNOWN;
        }

        $os = $this->linfo->getOS();
        if (! $this->linfo instanceof Darwin) {
            return $os;
        }

        preg_match('/Darwin\s+\((.+)\)/i', $os, $m);
        if (empty($m) || empty($m[1])) {
            return self::OS_MAC;
        }

        preg_match('/(.+)\s+(\d+\.\d+\.\d+)/i', $m[1], $n);
        if (empty($n) || empty($n[2])) {
            return trim(sprintf('%s X', self::OS_MAC));
        }

        return trim(sprintf('%s %s', self::OS_MAC, $n[2]));
    }

    /**
     * @return array
     */
    public function getDistro(): array
    {
        if (! $this->linfo instanceof Linux) {
            return [];
        }

        $distro = $this->linfo->getDistro();
        if ($distro === false) {
            return [];
        }

        return [
            'name' => $distro['name'] ?? '',
            'version' => $distro['version'] ?? '',
        ];
    }

    /**
     * @return string
     */
    public function getDistroName(): string
    {
        return $this->getDistro()['name'] ?? '';
    }

    /**
     * @return string
     */
    public function getDistroVersion(): string
    {
        return $this->getDistro()['version'] ?? '';
    }

    /**
     * @return string
     */
    public function getDistroString(): string
    {
        $distro = $this->getDistro();
        if (empty($distro)) {
            return '';
        }

        return trim(sprintf('%s %s', $distro['name'], $distro['version']));
    }

    /**
     * @return string
     */
    public function getKernel(): string
    {
        if ($this->linfo === null) {
            return '';
        }

        return trim($this->linfo->getKernel());
    }

    /**
     * @return string
     */
    public function getArch(): string
    {
        if ($this->linfo === null) {
            return '';
        }

        return $this->linfo->getCPUArchitecture();
    }

    /**
     * @return string
     */
    public function getWebServer(): string
    {
        if ($this->linfo === null) {
            return '';
        }

        return $this->linfo->getWebService();
    }

    /**
     * @return string
     */
    public function getPhpVersion(): string
    {
        if ($this->linfo === null) {
            return '';
        }

        return $this->linfo->getPhpVersion();
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'os' => $this->getOS(),
            'distro' => $this->getDistroString(),
            'kernel' => $this->getKernel(),
            'arc' => $this->getArch(),
            'webserver' => $this->getWebServer(),
            'php' => $this->getPhpVersion(),
        ];
    }
}
