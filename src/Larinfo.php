<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager as Database;
use Linfo\Linfo;
use PDO;
use Symfony\Component\HttpFoundation\Request;

class Larinfo
{
    /**
     * Define results
     *
     * @var array
     * @access protected
     */
    protected $results = [
        'host' => [
            'city' => null,
            'country' => null,
            'hostname' => null,
            'ip' => null,
            'loc' => null,
            'org' => null,
            'phone' => null,
            'postal' => null,
            'region' => null,
        ],
        'client' => [
            'city' => null,
            'country' => null,
            'hostname' => null,
            'ip' => null,
            'loc' => null,
            'org' => null,
            'phone' => null,
            'postal' => null,
            'region' => null,
        ],
        'server' => [
            'software' => [
                'os' => null,
                'distro' => null,
                'kernel' => null,
                'arc' => null,
                'webserver' => null,
                'php' => null,
            ],
            'hardware' => [
                'cpu' => null,
                'cpu_count' => null,
                'model' => null,
                'virtualization' => null,
                'ram' => [
                    'total' => null,
                    'free' => null,
                ],
                'swap' => [
                    'total' => null,
                    'free' => null,
                ],
                'disk' => [
                    'total' => null,
                    'free' => null,
                ],
            ],
            'uptime' => [
                'uptime' => null,
                'booted_at' => null,
            ],
        ],
        'database' => [
            'driver' => null,
            'version' => null,
        ],
    ];

    /**
     * Settings for Linfo
     *
     * @var array
     * @access protected
     */
    protected $linfoSettings = [
        'show' => [
            'kernel' => true,
            'os' => true,
            'ram' => true,
            'mounts' => true,
            'webservice' => true,
            'phpversion' => true,
            'uptime' => true,
            'cpu' => true,
            'distro' => true,
            'model' => true,
            'virtualization' => true,

            'duplicate_mounts' => false,
            'mounts_options' => false,
        ],
    ];

    protected $databases = [
        'mysql' => 'MySQL',
        'sqlite' => 'SQLite',
        'pgsql' => 'PostgreSQL',
        'oracle' => 'Oracle',
    ];

    /**
     * Constructor.
     *
     * @access public
     * @param \DavidePastore\Ipinfo\Ipinfo              $ipinfo
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Linfo\Linfo                              $linfo
     * @param \Illuminate\Database\Capsule\Manager      $database
     */
    public function __construct(Ipinfo $ipinfo, Request $request, Linfo $linfo, Database $database)
    {
        $this->ipinfo = $ipinfo;

        $this->request = $request;

        $this->linfo = $linfo;
        $this->linfo->__construct($this->linfoSettings);

        $this->database = $database;
    }

    /**
     * Set database connection
     *
     * @access public
     * @param mixed $connection (default: [])
     */
    public function setDatabaseConfig($connection = [])
    {
        $this->database->addConnection($connection);

        return $this;
    }

    /**
     * Set token for Ipinfo if exists.
     *
     * @access public
     * @param string $token (default: null)
     * @param bool   $debug (default: false)
     */
    public function setIpinfoConfig($token = null, $debug = false)
    {
        $this->ipinfo->__construct(compact('token', 'debug'));

        return $this;
    }

    /**
     * Get Host IP info.
     *
     * @access public
     * @return arrah
     */
    public function getHostIpinfo()
    {
        $this->hostIpinfo();

        return $this->results['host'];
    }

    /**
     * Get Client IP info.
     *
     * @access public
     * @return array
     */
    public function getClientIpinfo()
    {
        $this->clientIpinfo();

        return $this->results['client'];
    }

    /**
     * Get server software info.
     *
     * @access public
     * @return array
     */
    public function getServerInfoSoftware()
    {
        $this->serverInfoSoftware();

        return $this->results['server']['software'];
    }

    /**
     * Get server hardware info.
     *
     * @access public
     * @return array
     */
    public function getServerInfoHardware()
    {
        $this->serverInfoHardware();

        return $this->results['server']['hardware'];
    }

    /**
     * Get server uptime.
     *
     * @access public
     * @return array
     */
    public function getUptime()
    {
        $this->uptime();

        return $this->results['server']['uptime'];
    }

    /**
     * Get server info.
     *
     * @access public
     * @return array
     */
    public function getServerInfo()
    {
        $this->getServerInfoSoftware();
        $this->serverInfoHardware();
        $this->uptime();

        return $this->results['server'];
    }

    public function getDatabaseInfo()
    {
        $this->databaseInfo();

        return $this->results['database'];
    }

    /**
     * Get all info.
     *
     * @access public
     * @return array
     */
    public function getInfo()
    {
        $this->hostIpinfo();
        $this->clientIpinfo();
        $this->getServerInfoSoftware();
        $this->serverInfoHardware();
        $this->uptime();
        $this->databaseInfo();

        return $this->results;
    }

    /**
     * Get database info.
     *
     * @access protected
     */
    protected function databaseInfo()
    {
        $pdo = $this->database->getConnection()->getPdo();

        $version = $this->ifExists($pdo->getAttribute(PDO::ATTR_SERVER_VERSION));

        $driver = $this->ifExists($pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
        $driver = $this->ifExists((! empty($this->databases[$driver]) ? $this->databases[$driver] : null));

        $this->results['database'] = compact('driver', 'version');

        return $this;
    }

    /**
     * Parse host info.
     *
     * @access protected
     */
    protected function hostIpinfo()
    {
        $ipinfo = $this->ipinfo->getYourOwnIpDetails()->getProperties();
        $this->results['host'] = $ipinfo;

        return $this;
    }

    /**
     * Parse client info.
     *
     * @access protected
     */
    protected function clientIpinfo()
    {
        $ip = $this->request->getClientIp();

        $ipinfo = $this->ipinfo->getFullIpDetails($ip)->getProperties();
        $this->results['client'] = $ipinfo;

        return $this;
    }

    /**
     * Get CPU string.
     *
     * @access protected
     * @param  array  $cpus (default: array())
     * @return string
     */
    protected function getCPUString($cpus = [])
    {
        if (empty($cpus)) {
            return  '';
        }

        $cpuStrings = [];

        foreach ($cpus as $cpu) {
            $model = $cpu['Model'];
            $model = str_replace('(R)', '®', $model);
            $model = str_replace('(TM)', '™', $model);
            array_push($cpuStrings, $model);
        }

        $cpuStrings = array_unique($cpuStrings);

        return trim(implode(' / ', $cpuStrings));
    }

    /**
     * Get disk space string.
     *
     * @access protected
     * @param  array  $mounts (default: array())
     * @return string
     */
    protected function getDiskSpace($mounts = [])
    {
        $total = $free = 0;

        if (empty($mounts)) {
            return compact('total', 'free');
        }

        foreach ($mounts as $mount) {
            $total += $mount['size'];
            $free += $mount['free'];
        }

        return compact('total', 'free');
    }

    /**
     * Get virtualization string.
     *
     * @access protected
     * @param  array  $virtualization (default: array())
     * @return string
     */
    protected function getVirtualizationString($virtualization = [])
    {
        if (! empty($virtualization['method'])) {
            return $virtualization['method'];
        }

        return '';
    }

    /**
     * Get Distro string.
     *
     * @access protected
     * @param  array  $distro (default: array())
     * @return string
     */
    protected function getDistroString($distro = [])
    {
        if (! empty($distro)) {
            return implode(' ', array_values($distro));
        }

        return '';
    }

    /**
     * Parse server software.
     *
     * @access protected
     */
    protected function serverInfoSoftware()
    {
        $linfo = $this->linfo->getParser();

        $os = $this->ifExists($linfo->getOS());
        $kernel = $this->ifExists($linfo->getKernel());
        $arc = $this->ifExists($linfo->getCPUArchitecture());
        $webserver = $this->ifExists($linfo->getWebService());
        $php = $this->ifExists($linfo->getPhpVersion());

        $distro = '';
        if (method_exists($linfo, 'getDistro')) {
            $distro = $this->getDistroString(
                $this->ifExists($linfo->getDistro())
            );
        }

        $this->results['server']['software'] = compact(
            'os', 'distro', 'kernel', 'arc', 'webserver', 'php'
        );

        return $this;
    }

    /**
     * Parse server hardware.
     *
     * @access protected
     */
    protected function serverInfoHardware()
    {
        $linfo = $this->linfo->getParser();

        $CPUs = $this->ifExists($linfo->getCPU());
        $cpu = $this->getCPUString($CPUs);
        $cpu_count = count($CPUs);

        $model = $this->ifExists($linfo->getModel());
        $virtualization = $this->getVirtualizationString(
            $this->ifExists($linfo->getVirtualization())
        );

        $memory = $this->ifExists($linfo->getRam());
        $ram = [
            'total' => (int) $this->ifExists($memory['total']),
            'free' => (int) $this->ifExists($memory['free']),
        ];
        $swap = [
            'total' => (int) $this->ifExists($memory['swapTotal']),
            'free' => (int) $this->ifExists($memory['swapFree']),
        ];

        $disk = $this->getDiskSpace($linfo->getMounts());

        $this->results['server']['hardware'] = compact(
            'cpu', 'cpu_count', 'model', 'virtualization', 'ram', 'swap', 'disk'
        );

        return $this;
    }

    /**
     * Parse uptime.
     *
     * @access protected
     */
    protected function uptime()
    {
        $linfo = $this->linfo->getParser();

        $uptime = $booted_at = null;

        $systemUptime = $this->ifExists($linfo->getUpTime());

        if (! empty($systemUptime['text'])) {
            $uptime = $systemUptime['text'];
        }

        if (! empty($systemUptime['bootedTimestamp'])) {
            $booted_at = date('Y-m-d H:i:s', $systemUptime['bootedTimestamp']);
        }

        $this->results['server']['uptime'] = compact(
            'uptime', 'booted_at'
        );

        return $this;
    }

    /**
     * Check if object exists or return null.
     *
     * @access private
     * @param  mixed $object
     * @return mixed
     */
    private function ifExists($object)
    {
        return ! empty($object) ? $object : null;
    }
}
