<?php

namespace Matriphe\Larinfo;

interface LarinfoContract
{
    public function setDatabaseConfig($connection = []);

    /**
     * Set token for Ipinfo if exists.
     *
     * @access public
     * @param string $token (default: null)
     * @param bool   $debug (default: false)
     */
    public function setIpinfoConfig($token = null, $debug = false);

    /**
     * Get Host IP info.
     *
     * @access public
     * @return arrah
     */
    public function getHostIpinfo();

    /**
     * Get Client IP info.
     *
     * @access public
     * @return array
     */
    public function getClientIpinfo();

    /**
     * Get server software info.
     *
     * @access public
     * @return array
     */
    public function getServerInfoSoftware();

    /**
     * Get server hardware info.
     *
     * @access public
     * @return array
     */
    public function getServerInfoHardware();

    /**
     * Get server uptime.
     *
     * @access public
     * @return array
     */
    public function getUptime();

    /**
     * Get server info.
     *
     * @access public
     * @return array
     */
    public function getServerInfo();

    public function getDatabaseInfo();

    /**
     * Get all info.
     *
     * @access public
     * @return array
     */
    public function getInfo();
}
