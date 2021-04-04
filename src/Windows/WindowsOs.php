<?php

namespace Matriphe\Larinfo\Windows;

use Linfo\OS\Windows;

class WindowsOs extends Windows
{
    private const OS_WIN = 'Windows';
    private const NAME_WIN = 'Microsoft Windows';

    /**
     * @var WindowsUname
     */
    private WindowsUname $uname;

    /**
     * @param array        $settings
     * @param WindowsUname $uname
     */
    public function __construct(array $settings, WindowsUname $uname)
    {
        $this->uname = $uname;
    }

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
            'name' => self::NAME_WIN,
            'version' => trim(implode(' ', [
                php_uname('r'), // release
                php_uname('v'), // version
            ])),
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
        return php_uname('m'); // machine name
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
