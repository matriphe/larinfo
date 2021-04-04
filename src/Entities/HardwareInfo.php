<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Linfo\OS\Darwin;
use Linfo\OS\FreeBSD;
use Linfo\OS\Linux;
use Linfo\OS\Minix;
use Matriphe\Larinfo\Converters\StorageSizeConverter;
use Matriphe\Larinfo\Wrapper\LinfoWrapperContract;

final class HardwareInfo extends LinfoEntity implements Arrayable
{
    private const CPU_MODEL = 'model';
    private const CPU_VENDOR = 'vendor';
    private const CPU_CLOCK_MHZ = 'clock_mhz';
    private const CPU_USAGE_PERCENTAGE = 'usage_percentage';
    private const CPU_SEPARATOR = ' / ';
    private const VIRTUAL_TYPE = 'type';
    private const VIRTUAL_METHOD = 'method';

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
     * @param LinfoWrapperContract $linfo
     * @param StorageSizeConverter $converter
     * @param int                  $precision
     * @param bool                 $useBinary
     */
    public function __construct(
        LinfoWrapperContract $linfo,
        StorageSizeConverter $converter,
        int $precision = 0,
        bool $useBinary = false
    ) {
        parent::__construct($linfo);

        $this->converter = $converter;
        $this->precision = $precision;
        $this->useBinary = $useBinary;
    }

    /**
     * @return array
     */
    public function getCpu(): array
    {
        $results = [];
        if ($this->linfo === null || $this->linfo instanceof Minix) {
            return $results;
        }

        foreach ($this->linfo->getCPU() as $cpu) {
            $model = str_replace('(R)', '®', $cpu['Model']);
            $model = str_replace('(TM)', '™', $model);

            $results[] = [
                self::CPU_MODEL => $model,
                self::CPU_VENDOR => $cpu['Vendor'] ?? null,
                self::CPU_CLOCK_MHZ => $cpu['MHz'],
                self::CPU_USAGE_PERCENTAGE => $cpu['usage_percentage'] ?? null,
            ];
        }

        return $results;
    }

    /**
     * @return string
     */
    public function getCpuString(): string
    {
        $cpu = $this->getCpu();
        if (empty($cpu)) {
            return '';
        }

        $cpuModels = array_map(function ($m) {
            return $m[self::CPU_MODEL];
        }, $cpu);

        return implode(self::CPU_SEPARATOR, array_unique($cpuModels));
    }

    /**
     * @return int
     */
    public function getCpuCount(): int
    {
        return count($this->getCpu());
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        if ($this->linfo instanceof Darwin) {
            return $this->linfo->getModel();
        }

        return '';
    }

    /**
     * @return array
     */
    public function getVirtualization(): array
    {
        if (
            ! $this->linfo instanceof Linux
            && ! $this->linfo instanceof FreeBSD
            && ! $this->linfo instanceof Darwin
        ) {
            return [];
        }

        $virtualization = $this->linfo->getVirtualization();
        if (empty($virtualization)) {
            return [];
        }

        return [
            self::VIRTUAL_TYPE => $virtualization['type'],
            self::VIRTUAL_METHOD => $virtualization['method'],
        ];
    }

    /**
     * @return string
     */
    public function getVirtualizationString(): string
    {
        $virtualization = $this->getVirtualization();
        if (! empty($virtualization[self::VIRTUAL_METHOD])) {
            return $virtualization[self::VIRTUAL_METHOD];
        }

        return '';
    }

    /**
     * @return MemoryInfo
     */
    public function getMemory(): MemoryInfo
    {
        if ($this->linfo === null || $this->linfo instanceof Minix) {
            return new MemoryInfo(
                new StorageInfo(
                    0,
                    0,
                    $this->converter,
                    $this->precision,
                    $this->useBinary
                ),
                new StorageInfo(
                    0,
                    0,
                    $this->converter,
                    $this->precision,
                    $this->useBinary
                ),
            );
        }

        $memory = $this->linfo->getRam();

        return new MemoryInfo(
            new StorageInfo(
                $memory['total'] ?? 0,
                $memory['free'] ?? 0,
                $this->converter,
                $this->precision,
                $this->useBinary
            ),
            new StorageInfo(
                $memory['swapTotal'] ?? 0,
                $memory['swapFree'] ?? 0,
                $this->converter,
                $this->precision,
                $this->useBinary
            )
        );
    }

    /**
     * @return StorageInfo
     */
    public function getDisk(): StorageInfo
    {
        $total = 0;
        $free = 0;
        if ($this->linfo === null) {
            return new StorageInfo(
                $total,
                $free,
                $this->converter,
                $this->precision,
                $this->useBinary
            );
        }

        $mounts = $this->linfo->getMounts();
        if (empty($mounts)) {
            return new StorageInfo(
                $total,
                $free,
                $this->converter,
                $this->precision,
                $this->useBinary
            );
        }

        foreach ($mounts as $mount) {
            $total += $mount['size'];
            $free += $mount['free'];
        }

        return new StorageInfo(
            $total,
            $free,
            $this->converter,
            $this->precision,
            $this->useBinary
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'cpu' => $this->getCpuString(),
                'cpu_count' => $this->getCpuCount(),
                'model' => $this->getModel(),
                'virtualization' => $this->getVirtualizationString(),
                'disk' => $this->getDisk()->toArray(),
            ],
            $this->getMemory()->toArray()
        );
    }
}
