<?php

namespace Matriphe\Larinfo\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use Linfo\OS\Minix;
use Matriphe\Larinfo\Wrapper\LinfoWrapperContract;

class SystemInfo extends LinfoEntity implements Arrayable
{
    private const UPTIME = 'uptime';
    private const BOOTED_AT = 'booted_at';

    /**
     * @var string[]
     */
    private array $uptime = [
        self::UPTIME => '',
        self::BOOTED_AT => '',
    ];

    /**
     * SystemInfo constructor.
     * @param LinfoWrapperContract $linfo
     */
    public function __construct(LinfoWrapperContract $linfo)
    {
        parent::__construct($linfo);

        $this->parseUptime();
    }

    /**
     * @return string
     */
    public function getUptime(): string
    {
        return $this->uptime[self::UPTIME] ?? '';
    }

    /**
     * @return string
     */
    public function getBootedAt(): string
    {
        return $this->uptime[self::BOOTED_AT];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->uptime;
    }

    private function parseUptime(): void
    {
        if ($this->linfo === null || $this->linfo instanceof Minix) {
            return;
        }

        $uptime = $this->linfo->getUpTime();
        if (empty($uptime)) {
            return;
        }

        $this->uptime[self::UPTIME] = trim($uptime['text'] ?? '');

        if ($uptime['bootedTimestamp'] === false || empty($uptime['bootedTimestamp'])) {
            return;
        }

        $bootTimestamp = Carbon::createFromTimestamp($uptime['bootedTimestamp']);

        $this->uptime[self::BOOTED_AT] = $bootTimestamp->toDateTimeString();
    }
}
