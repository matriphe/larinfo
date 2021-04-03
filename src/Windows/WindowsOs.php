<?php

namespace Matriphe\Larinfo\Windows;

use Linfo\OS\Windows;

class WindowsOs extends Windows
{
    private const OS_WIN = 'Windows';

    /**
     * @return string
     */
    public function getOS()
    {
        return self::OS_WIN;
    }

    /**
     * @return string[]
     */
    public function getDistro()
    {
        return [
            'name' => self::OS_WIN,
            'version' => '',
        ];
    }

    /**
     * @return string
     */
    public function getKernel()
    {
        return 'Unknown';
    }

    /**
     * @return string
     */
    public function getCPUArchitecture()
    {
        return 'Unknown';
    }

    /**
     * @return array
     */
    public function getUpTime()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getCPU()
    {
        return [];
    }

    public function getModel()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getRam()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getMounts()
    {
        return [];
    }
}
