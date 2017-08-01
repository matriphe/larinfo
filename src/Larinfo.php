<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Linfo\Linfo;
use Symfony\Component\HttpFoundation\Request;

class Larinfo
{
    /**
     * Define results
     *
     * @var array
     * @access protected
     */
    protected $results = array(
        'host' => array(),
        'client' => array(),
        'server' => array(
            'software' => array(),
            'hardware' => array(),
            'uptime' => array(),
        ),
    );

    /**
     * Settings for Linfo
     *
     * @var array
     * @access protected
     */
    protected $linfoSettings = array(
        'show' => array(
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
        ),
    );

    /**
     * Constructor.
     *
     * @access public
     * @param \DavidePastore\Ipinfo\Ipinfo              $ipinfo
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Linfo\Linfo                              $linfo
     */
    public function __construct(Ipinfo $ipinfo, Request $request, Linfo $linfo)
    {
        $this->ipinfo = $ipinfo;
        $this->request = $request;
        $this->linfo = $linfo;
        $this->linfo->__construct($this->linfoSettings);
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
        $this->getServerInfo();

        return $this->results;
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
    protected function getCPUString($cpus = array())
    {
        if (empty($cpus)) {
            return  '';
        }

        $cpuStrings = array();

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
    protected function getDiskSpace($mounts = array())
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
    protected function getVirtualizationString($virtualization = array())
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
    protected function getDistroString($distro = array())
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
        $ram = array(
            'total' => (int) $this->ifExists($memory['total']),
            'free' => (int) $this->ifExists($memory['free']),
        );
        $swap = array(
            'total' => (int) $this->ifExists($memory['swapTotal']),
            'free' => (int) $this->ifExists($memory['swapFree']),
        );

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
