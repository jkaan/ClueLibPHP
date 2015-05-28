<?php

namespace Clue;

/**
 * Class Clue
 * @package Clue
 */
class Clue {

    /**
     * @var string Host address of the Clue server.
     */
    private $_host;

    /**
     * @var int Port the Clue server listens to.
     */
    private $_port;

    /**
     * @var string Regular expression to validate the host name.
     */
    private $_ipRegex;

    /**
     * Constructor for the Clue Integration Library.
     *
     * @param string $host Hostname of the Clue server. Either IP or URL. No protocol should be specified.
     * @param int $port Port number the Clue server listens to.
     * @throws \Exception When the specified host is not a valid IP or URL.
     */
    public function __construct($host, $port = 80) {
        $this->_ipRegex = '/^(((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})|([a-zA-Z0-9\-_]+\.)*[a-zA-Z0-9\-_]+\.[a-z]{2,14})$/';

        if(preg_match($this->_ipRegex, $host) !== 1) {
            throw new \Exception("Host must be a valid IP address or base URL");
        }

        $this->_host = $host;
        $this->_port = $port;
    }

    /**
     * Check if the server is reachable and return the latency.
     *
     * @return int The latency in milliseconds or -1 if the server can't be found or the request has timed out.
     */
    public function ping() {
        $errno = null;
        $errstr = null;
        $end = microtime(true);
        $check = fSockOpen($this->_host, $this->_port, $errno, $errstr, 10);
        if (!$check) {
            return -1;
        }
        $start = microtime(true);
        return round((($start - $end) * 1000), 0);
    }

    /**
     * Execute a clue.
     *
     * This function depends on the availability of shell_exec to work asynchronously.
     * If shell_exec is not available the async parameter should be set to false. The
     * request may block script execution in that case.
     *
     * @param string $clueName Name of the Clue that will be executed.
     * @param bool $async Whether or not the request should be asynchronous.
     */
    public function execute($clueName, $async = true) {
        if($async) {
            $url = escapeshellarg('https://' . $this->_host . '' . $this->_port . '/api/clue/' . $clueName . '&');
            shell_exec('php src/Clue/ClueCurler.php "' . $url . '" 2>/dev/null &');
        } else {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://" . $this->_host . "/api/clue/" . $clueName,
                CURLOPT_RETURNTRANSFER => 0,
                CURLOPT_FAILONERROR => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_POST => true
            ]);
            curl_exec($ch);
        }
    }

    /**
     * Get the host.
     *
     * @return string The current host.
     */
    public function getHost() {
        return $this->_host;
    }

    /**
     * Set the host.
     *
     * @param string $host The new value for the host. Either IP or URL.
     * @throws \Exception When the specified host is not a valid IP or URL.
     */
    public function setHost($host) {
        if(preg_match($this->_ipRegex, $host) !== 1) {
            throw new \Exception("Host must be a valid IP address or base URL");
        }
        $this->_host = $host;
    }

    /**
     * Get the port.
     *
     * @return int The current port number.
     */
    public function getPort(){
        return $this->_port;
    }

    /**
     * Set the port number
     *
     * @param int $port The new value for the port number.
     */
    public function setPort($port = 80) {
        $this->_port = $port;
    }

}