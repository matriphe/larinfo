<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Matriphe\Larinfo\Converters\StorageSizeConverter;

class StorageInfo implements Arrayable
{
    /**
     * @var int
     */
    private int $total;
    /**
     * @var int
     */
    private int $free;
    /**
     * @var StorageSizeConverter
     */
    private StorageSizeConverter $converter;

    /**
     * StorageInfo constructor.
     * @param int                  $total
     * @param int                  $free
     * @param StorageSizeConverter $converter
     */
    public function __construct(
        int $total,
        int $free,
        StorageSizeConverter $converter
    ) {
        $this->total = $total;
        $this->free = $free;
        $this->converter = $converter;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getFree(): int
    {
        return $this->free;
    }

    /**
     * @param  int    $precision
     * @param  bool   $useBinary
     * @return string
     */
    public function getTotalHuman(int $precision = 0, bool $useBinary = true):string
    {
        return $this->converter->toHuman($this->total, $precision, $useBinary);
    }

    /**
     * @param  int    $precision
     * @param  bool   $useBinary
     * @return string
     */
    public function getFreeHuman(int $precision = 0, bool $useBinary = true): string
    {
        return $this->converter->toHuman($this->free, $precision, $useBinary);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'free' => $this->free,
        ];
    }
}
