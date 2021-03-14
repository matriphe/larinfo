<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Linfo\OS\Darwin;
use Linfo\OS\FreeBSD;
use Linfo\OS\Linux;
use Linfo\OS\Minix;

final class HardwareInfo extends LinfoEntity implements Arrayable
{
    private const CPU_MODEL = 'model';
    private const CPU_VENDOR = 'vendor';
    private const CPU_CLOCK_MHZ = 'clock_mhz';
    private const CPU_USAGE_PERCENTAGE = 'usage_percentage';
    private const CPU_SEPARATOR = ' / ';
    private const MEM_RAM = 'ram';
    private const MEM_SWAP = 'swap';
    private const MEM_TOTAL = 'total';
    private const MEM_FREE = 'free';
    private const VIRTUAL_TYPE = 'type';
    private const VIRTUAL_METHOD = 'method';

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
     * @return array|array[]
     */
    public function getMemory(): array
    {
        $result = [
            self::MEM_RAM => [
                self::MEM_TOTAL => 0,
                self::MEM_FREE => 0,
            ],
            self::MEM_SWAP => [
                self::MEM_TOTAL => 0,
                self::MEM_FREE => 0,
            ],
        ];

        if ($this->linfo === null || $this->linfo instanceof Minix) {
            return $result;
        }

        $memory = $this->linfo->getRam();
        $result[self::MEM_RAM][self::MEM_TOTAL] = $memory['total'];
        $result[self::MEM_RAM][self::MEM_FREE] = $memory['free'];
        $result[self::MEM_SWAP][self::MEM_TOTAL] = $memory['swapTotal'] ?? 0;
        $result[self::MEM_SWAP][self::MEM_FREE] = $memory['swapFree'] ?? 0;

        return $result;
    }

    /**
     * @return int[]
     */
    public function getDisk(): array
    {
        $result = [
            self::MEM_TOTAL => 0,
            self::MEM_FREE => 0,
        ];

        if ($this->linfo === null) {
            return $result;
        }

        $mounts = $this->linfo->getMounts();
        if (empty($mounts)) {
            return $result;
        }

        foreach ($mounts as $mount) {
            $result[self::MEM_TOTAL] += $mount['size'];
            $result[self::MEM_FREE] += $mount['free'];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge([
            'cpu' => $this->getCpuString(),
            'cpu_count' => $this->getCpuCount(),
            'model' => $this->getModel(),
            'virtualization' => $this->getVirtualizationString(),
            'disk' => $this->getDisk(),
        ], $this->getMemory());
    }
}
