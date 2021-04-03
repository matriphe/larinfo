<?php

namespace Matriphe\Larinfo\Windows;

use Linfo\Exceptions\FatalException;
use Linfo\OS\Windows;

class Wrapper extends Windows
{
    /**
     * @var Windows|WindowsNoComNet
     */
    private $windows;

    public function __construct($settings)
    {
        try {
            $this->windows = parent::__construct($settings);
        } catch (FatalException $e) {
            $this->windows = new WindowsNoComNet();
        }
    }

    public function getOS()
    {
        return $this->windows->getOS();
    }

    public function getDistro()
    {
        return $this->windows->getDistro();
    }

    public function getKernel()
    {
        return $this->windows->getKernel();
    }

    public function getCPUArchitecture()
    {
        return $this->windows->getCPUArchitecture();
    }

    public function getUpTime()
    {
        return $this->windows->getUpTime();
    }

    public function getCPU()
    {
        return $this->windows->getCPU();
    }

    public function getModel()
    {
        return $this->windows->getModel();
    }

    public function getRam()
    {
        return $this->windows->getRam();
    }

    public function getMounts()
    {
        return $this->windows->getMounts();
    }
}
