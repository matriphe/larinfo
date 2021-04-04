<?php

namespace Matriphe\Larinfo\Commands;

use Illuminate\Console\Command;
use Matriphe\Larinfo\LarinfoContract;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

class LarinfoCommand extends Command
{
    private const NAME = 'Larinfo';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larinfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View your system information';

    /**
     * @var LarinfoContract
     */
    private $larinfo;

    /**
     * Create a new command instance.
     * @param LarinfoContract $larinfo
     */
    public function __construct(LarinfoContract $larinfo)
    {
        parent::__construct();

        $this->larinfo = $larinfo;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->output->title(self::NAME);

        $systemInformation = $this->larinfo->serverInfoSoftware();
        $uptime = $this->larinfo->systemInfo();
        $hostInformation = $this->larinfo->hostIpInfo();
        $hardwareInfo = $this->larinfo->serverInfoHardware();
        $databaseInfo = $this->larinfo->databaseInfo();

        $data = [
            'Application' => [
                ['PHP version', $systemInformation->getPhpVersion()],
                ['Laravel version', app()->version()],
            ],
            'Database' => [
                ['Engine', $databaseInfo->getDriver()],
                ['Version', $databaseInfo->getVersion()],
            ],
            'Operating System' => [
                ['Type', $systemInformation->getOS()],
                ['Name', $systemInformation->getDistroString()],
                ['Architecture', $systemInformation->getArch()],
                ['Kernel Version', $systemInformation->getKernel()],
            ],
            'Uptime' => [
                ['Uptime', $uptime->getUptime()],
                ['First Boot', $uptime->getBootedAt()],
            ],
            'Server' => [
                ['IP Address', $hostInformation->getIp()],
                ['Private IP Address', $hostInformation->getPrivateIp()],
                ['Hostname', $hostInformation->getHostname()],
                ['Provider', $hostInformation->getOrg()],
                ['City', $hostInformation->getCity()],
                ['Region', $hostInformation->getRegion()],
                ['Country', $hostInformation->getCountry()],
            ],
            'Timezone' => [
                ['Application', config('app.timezone')],
                ['Server Location', $hostInformation->getTimezone()],
            ],
            'Hardware' => [
                ['Model', $hardwareInfo->getModel()],
                ['CPU count', $hardwareInfo->getCpuCount()],
                ['CPU', $hardwareInfo->getCpuString()],
            ],
            'RAM' => [
                ['Total', $hardwareInfo->getMemory()->getRAM()->getTotalHuman()],
                ['Free', $hardwareInfo->getMemory()->getRAM()->getFreeHuman()],
            ],
            'SWAP' => [
                ['Total', $hardwareInfo->getMemory()->getSWAP()->getTotalHuman()],
                ['Free', $hardwareInfo->getMemory()->getSWAP()->getFreeHuman()],
            ],
            'Disk Space' => [
                ['Total', $hardwareInfo->getDisk()->getTotalHuman()],
                ['Free', $hardwareInfo->getDisk()->getFreeHuman()],
            ],
        ];

        $this->renderTable($data);

        return 0;
    }

    /**
     * @param array $data
     * @param int   $colspan
     */
    private function renderTable(array $data, int $colspan = 2)
    {
        $header = [];
        $rows = [];
        $i = 0;
        foreach ($data as $title => $r) {
            if ($i == 0) {
                $header = [new TableCell($title, ['colspan' => $colspan])];
                $rows = array_merge($rows, $r);
                $i++;
                continue;
            }

            $rows[] = new TableSeparator();
            $rows[] = [new TableCell(
                sprintf('<info>%s</info>', $title),
                ['colspan' => $colspan]
            )];
            $rows[] = new TableSeparator();

            $rows = array_merge($rows, $r);
        }

        $this->table($header, $rows);
    }
}
