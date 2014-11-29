<?php
namespace exchangecore\webprint\src\printers;

/**
 * Class NetworkPrinter
 * A base class for printers that leverage a TCP/IP connection
 */
class NetworkPrinter extends Printer
{

    const DOTS_PER_INCH = 'dpi';
    const DOTS_PER_CENTIMETER = 'dpcm';

    /**
     * @var int $dpi
     * The dots per inch that the printer prints
     */
    protected $dpi = 0;

    protected $defaultPort = 1;
    protected $socket;

    protected function unitsOfMeasure()
    {
        return array_merge(
            parent::unitsOfMeasure(),
            [
                self::DOTS_PER_INCH => [
                    'precision' => 0,
                ],
                self::DOTS_PER_CENTIMETER => [
                    'precision' => 0,
                ],
            ]
        );
    }

    public function connect($host)
    {
        if (!$this->socket) {
            $this->socket = @pfsockopen($host, $this->defaultPort, $errno, $errstr);
            if (!$this->socket || $errno > 0) {
                return false;
            } else {
                return $this;
            }
        }
        return $this;
    }

    public function disconnect()
    {
        if ($this->socket) {
            return fclose($this->socket);
        } else {
            return true;
        }
    }

    protected function onProcessCommandStack($resetCommandQueue = true)
    {
        $printed = fwrite($this->socket, $this->getCommandString());

        if ($printed === strlen($this->getCommandString())) {
            return parent::onProcessCommandStack($resetCommandQueue);
        } else {
            return false;
        }
    }

    /**
     * This method should be used to set the dots per inch for the given printer
     * Note: You can easily convert from dots per centimeter using the convertUnitOfMeasure function
     * @param int $dpi
     * @return $this
     */
    public function setDpi($dpi)
    {
        $this->dpi = $dpi;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function convertUnitOfMeasure($measurement, $startUnit, $resultUnit, $round = true)
    {
        if($startUnit === self::DOTS_PER_INCH) {
            switch($resultUnit) {
                case self::DOTS_PER_CENTIMETER:
                    $measurement *= 2.54;
                    break;
            }
        } elseif($startUnit === self::DOTS_PER_CENTIMETER) {
            switch($resultUnit) {
                case self::DOTS_PER_INCH:
                    $measurement /= 2.54;
                    break;
            }
        } elseif($startUnit === self::UNIT_INCHES) {
            switch($resultUnit) {
                case self::DOTS_PER_INCH:
                    $measurement *= $this->dpi;
                    break;
                case self::DOTS_PER_CENTIMETER:
                    $measurement *= 2.54 * $this->dpi;
                    break;
            }
        }

        return parent::convertUnitOfMeasure($measurement, $startUnit, $resultUnit, $round);
    }

} 