<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Linfo\OS\Darwin;
use Linfo\OS\Linux;
use Linfo\OS\Windows;
use Matriphe\Larinfo\Windows\WindowsOs;

final class ServerInfo extends LinfoEntity implements
    Arrayable,
    OperatingSystemContract
{
    /**
     * @return string
     */
    public function getOS(): string
    {
        if ($this->linfo === null) {
            return self::OS_UNKNOWN;
        }

        if ($this->linfo instanceof Darwin) {
            return self::OS_MAC;
        }

        if ($this->linfo instanceof Windows) {
            return self::OS_WINDOWS;
        }

        return $this->linfo->getOS();
    }

    /**
     * @return array
     */
    public function getDistro(): array
    {
        if ($this->linfo instanceof Darwin) {
            return $this->parseDarwinDistro($this->linfo);
        }

        if ($this->linfo instanceof Windows) {
            return $this->parseWindowsDistro($this->linfo);
        }

        if (! $this->linfo instanceof Linux) {
            return [];
        }

        $distro = $this->linfo->getDistro();
        if ($distro === false) {
            return [];
        }

        return [
            self::DISTRO_NAME => $distro[self::DISTRO_NAME] ?? '',
            self::DISTRO_VERSION => $distro[self::DISTRO_VERSION] ?? '',
        ];
    }

    /**
     * @return string
     */
    public function getDistroName(): string
    {
        return $this->getDistro()[self::DISTRO_NAME] ?? '';
    }

    /**
     * @return string
     */
    public function getDistroVersion(): string
    {
        return $this->getDistro()[self::DISTRO_VERSION] ?? '';
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

        return trim(sprintf(
            '%s %s',
            $distro[self::DISTRO_NAME],
            $distro[self::DISTRO_VERSION]
        ));
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

    /**
     * @param  Darwin   $os
     * @return string[]
     */
    private function parseDarwinDistro(Darwin $os): array
    {
        $distro = [
            self::DISTRO_NAME => self::OS_MAC,
            self::DISTRO_VERSION => '',
        ];

        $os = $os->getOS();
        preg_match('/Darwin\s+\((.+)\)/i', $os, $m);
        if (empty($m) || empty($m[1])) {
            return $distro;
        }

        preg_match('/(.+)\s+(\d+\.\d+\.\d+)/i', $m[1], $n);
        if (empty($n) || empty($n[2])) {
            $distro[self::DISTRO_VERSION] = 'X';

            return $distro;
        }

        $distro[self::DISTRO_VERSION] = $n[2];

        return $distro;
    }

    /**
     * @param  Windows $os
     * @return array
     */
    private function parseWindowsDistro(Windows $os): array
    {
        if ($os instanceof WindowsOs) {
            return $os->getDistro();
        }

        $name = $os->getOS();
        $version = str_replace(self::NAME_WINDOWS, '', $name);

        return [
            self::DISTRO_NAME => self::NAME_WINDOWS,
            self::DISTRO_VERSION => trim($version),
        ];
    }
}
