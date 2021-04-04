<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;

class MemoryInfo implements Arrayable
{
    /**
     * @var StorageInfo
     */
    private StorageInfo $ram;
    /**
     * @var StorageInfo
     */
    private StorageInfo $swap;

    /**
     * MemoryInfo constructor.
     * @param StorageInfo $ram
     * @param StorageInfo $swap
     */
    public function __construct(StorageInfo $ram, StorageInfo $swap)
    {
        $this->ram = $ram;
        $this->swap = $swap;
    }

    /**
     * @return StorageInfo
     */
    public function getRAM(): StorageInfo
    {
        return $this->ram;
    }

    /**
     * @return StorageInfo
     */
    public function getSWAP(): StorageInfo
    {
        return $this->swap;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'ram' => $this->ram->toArray(),
            'swap' => $this->swap->toArray(),
        ];
    }
}
