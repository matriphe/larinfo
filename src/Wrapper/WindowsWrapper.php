<?php

namespace Matriphe\Larinfo\Wrapper;

use Linfo\OS\OS;
use Linfo\OS\Windows;

class WindowsWrapper implements LinfoWrapperContract
{
    /**
     * @var Windows
     */
    private $windows;

    /**
     * @param Windows $windows
     */
    public function __construct(Windows $windows)
    {
        $this->windows = $windows;
    }

    /**
     * @return OS|null
     */
    public function getParser(): ?OS
    {
        return $this->windows;
    }
}
