<?php

namespace Matriphe\Larinfo\Windows;

/**
 * Class WindowsUname is wrapper for php_uname for Windows.
 * @package Matriphe\Larinfo\Windows
 */
class WindowsUname
{
    /**
     * @return string
     */
    public function getAll():string
    {
        return php_uname();
    }

    /**
     * @return string
     */
    public function getOperatingSystem(): string
    {
        return php_uname('s');
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return php_uname('n');
    }

    /**
     * @return string
     */
    public function getRelease(): string
    {
        return php_uname('r');
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return php_uname('v');
    }

    /**
     * @return string
     */
    public function getMachine(): string
    {
        return php_uname('m');
    }

    /**
     * @return string
     */
    public function getKernel(): string
    {
        preg_match('/^build\s+(\d+).*$/i', $this->getVersion(), $m);
        $r = $this->getRelease();
        if (! empty($m[1])) {
            return trim(sprintf('%s.%s', $r, $m[1]));
        }

        return $r;
    }
}
