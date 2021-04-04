<?php

namespace Matriphe\Larinfo\Windows;

use Linfo\OS\Windows;
use Matriphe\Larinfo\Entities\OperatingSystemContract;

class WindowsOs extends Windows implements OperatingSystemContract
{
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
        return self::OS_WINDOWS;
    }

    /**
     * @return string[]
     */
    public function getDistro()
    {
        return [
            self::DISTRO_NAME => self::NAME_WINDOWS,
            self::DISTRO_VERSION => $this->uname->getVersion(),
        ];
    }

    /**
     * @return string
     */
    public function getKernel()
    {
        return $this->uname->getKernel();
    }

    /**
     * @return string
     */
    public function getCPUArchitecture()
    {
        return $this->uname->getMachine();
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
