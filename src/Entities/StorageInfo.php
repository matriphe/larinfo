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
     * @var int
     */
    private int $precision;
    /**
     * @var bool
     */
    private bool $useBinary;

    /**
     * StorageInfo constructor.
     * @param int                  $total
     * @param int                  $free
     * @param int                  $precision
     * @param bool                 $useBinary
     * @param StorageSizeConverter $converter
     */
    public function __construct(
        int $total,
        int $free,
        StorageSizeConverter $converter,
        int $precision = 0,
        bool $useBinary = false
    ) {
        $this->total = $total;
        $this->free = $free;
        $this->converter = $converter;
        $this->precision = $precision;
        $this->useBinary = $useBinary;
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
     * @return string
     */
    public function getTotalHuman():string
    {
        return $this->converter->toHuman($this->total, $this->precision, $this->useBinary);
    }

    /**
     * @return string
     */
    public function getFreeHuman(): string
    {
        return $this->converter->toHuman($this->free, $this->precision, $this->useBinary);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'free' => $this->free,
            'human_total' => $this->getTotalHuman(),
            'human_free' => $this->getFreeHuman(),
        ];
    }
}
