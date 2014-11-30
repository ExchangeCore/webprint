<?php
namespace exchangecore\webprint\src\printers\interfaces;

interface NetworkPrinterInterface extends PrinterInterface
{

    /**
     * Creates a connection to the network printer
     * @param string $host
     * @return $this|bool Returns $this if connected successfully, returns false if unable to connect
     */
    public function connect($host);

    /**
     * Disconnects from the current network connection
     * @return bool
     */
    public function disconnect();

} 