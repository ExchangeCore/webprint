<?php
namespace exchangecore\webprint\src\printers;

class Printer
{
    const UNIT_DPI = 'dpi';
    const UNIT_DPPT = 'dppt';
    const UNIT_DPCM = 'dpcm';
    const UNIT_INCHES = 'in';
    const UNIT_MILLIMETERS = 'mm';

    const INCHES_PER_POINT = 0.16666666666667;

    protected $unitsOfMeasure = [
        self::UNIT_DPI => [
            'precision' => 0,
        ],
        self::UNIT_INCHES => [
            'precision' => 3,
        ],
        self::UNIT_DPCM => [
            'precision' => 0,
        ],
        self::UNIT_MILLIMETERS => [
            'precision' => 1,
        ],
        self::UNIT_DPPT => [
            'precision' => 0,
        ]
    ];

    protected $command = [];
    protected $dpi = 203;
    protected $printWidthDpi = 0;
    protected $paperWidthDpi = 0;
    protected $paperLengthDpi = 0;

    /** @var resource $socket */
    protected $socket;

    /**
     * Opens a connection to the defined printer socket
     * @param string $host
     * @param int $port
     * @return bool|$this Returns the printer object if connected, false if the connection failed
     */
    public function connect($host, $port)
    {
        if (!$this->socket) {
            $this->socket = @pfsockopen($host, $port, $errno, $errstr);
            if (!$this->socket || $errno > 0) {
                return false;
            } else {
                return $this;
            }
        }
        return $this;
    }

    /**
     * Writes the current command stack to the printer
     * @param bool $resetCommandQueue Indicates if the current print command should be removed if the command was sent successfully
     * @return bool|$this Returns true if the command writes the same number of bytes that existed in the command stack
     */
    public function sendCommandStack($resetCommandQueue = true)
    {
        $printed = fwrite($this->socket, $this->getCommandString());

        if ($printed == strlen($this->getCommandString()) && $resetCommandQueue) {
            $this->resetStack();
        }
        return ($printed === strlen($this->getCommandString()))?($this):(false);
    }

    /**
     * Disconnects from the existing connection if one is present
     * @return bool
     */
    public function disconnect()
    {
        if($this->socket) {
            return fclose($this->socket);
        } else {
            return true; //we weren't connected anyway
        }
    }

    /**
     * Adds a printer command to the print command stack to be written
     * @param string $command
     * @return $this
     */
    public function pushCommand($command)
    {
        $this->command[] = $command;
        return $this;
    }

    /**
     * Returns a string representation of the command stack
     * @return string
     */
    public function getCommandString()
    {
        $string = '';
        foreach ($this->command AS $command) {
            $string .= $command;
        }
        return $string;
    }

    /**
     * Empties out our command stack
     * @return $this
     */
    public function resetStack()
    {
        $this->command = [];
        return $this;
    }

    /**
     * This method should be used to set the dots per inch for the given printer
     * @param float $measurement
     * @param string $unitOfMeasure
     * @internal param int $dotsPerInch
     * @return $this
     */
    public function setDpi($measurement, $unitOfMeasure = self::UNIT_DPI)
    {
        $this->dpi = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_DPI);
        return $this;
    }

    /**
     * This method is used to set the maximum printing width of the printer and is used in combination with the
     * paper width setting to determine the default printing offset
     * @param float $measurement
     * @param string $unitOfMeasure
     * @return $this
     */
    public function setPrintWidth($measurement, $unitOfMeasure = self::UNIT_DPI)
    {
        $this->printWidthDpi = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_DPI);
        return $this;
    }

    /**
     * This method is used to set the width of the print media being used and is used in combination with the
     * maximum printing width to determine the default printing offset
     * @param float $measurement
     * @param string $unitOfMeasure
     * @return $this
     */
    public function setPaperWidth($measurement, $unitOfMeasure = self::UNIT_DPI)
    {
        $this->paperWidthDpi = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_DPI);
        return $this;
    }

    /**
     * @param float $measurement
     * @param int $startUnit The starting unit of measure UNIT_* constant
     * @param int $resultUnit The unit of measure UNIT_* constant to convert the measurement to
     * @return float
     */
    public function convertUnitOfMeasure($measurement, $startUnit, $resultUnit)
    {
        if ($startUnit === self::UNIT_INCHES) {
            if ($resultUnit === self::UNIT_INCHES) {
                return round($measurement, $this->unitsOfMeasure[self::UNIT_INCHES]['precision']);
            } elseif ($resultUnit === self::UNIT_MILLIMETERS) {
                return round($measurement * 25.4, $this->unitsOfMeasure[self::UNIT_MILLIMETERS]['precision']);
            } elseif ($resultUnit === self::UNIT_DPI) {
                return round($this->dpi * $measurement, $this->unitsOfMeasure[self::UNIT_DPI]['precision']);
            } elseif ($resultUnit === self::UNIT_DPCM) {
                return round($this->dpi * $measurement / 0.393701, $this->unitsOfMeasure[self::UNIT_DPCM]['precision']);
            } elseif ($resultUnit === self::UNIT_DPPT) {
                return round($this->dpi * $measurement * self::INCHES_PER_POINT, $this->unitsOfMeasure[self::UNIT_DPPT]['precision']);
            }
        } elseif ($startUnit === self::UNIT_MILLIMETERS) {
            if ($resultUnit === self::UNIT_INCHES) {
                return round($measurement / 25.4, $this->unitsOfMeasure[self::UNIT_INCHES]['precision']);
            } elseif ($resultUnit === self::UNIT_MILLIMETERS) {
                return round($measurement, $this->unitsOfMeasure[self::UNIT_MILLIMETERS]['precision']);
            } elseif ($resultUnit === self::UNIT_DPI) {
                return round($this->dpi * $measurement / 0.0393701, $this->unitsOfMeasure[self::UNIT_DPI]['precision']);
            } elseif ($resultUnit === self::UNIT_DPCM) {
                return round($this->dpi * $measurement / 10, $this->unitsOfMeasure[self::UNIT_DPCM]['precision']);
            } elseif ($resultUnit === self::UNIT_DPPT) {
                return $this->convertUnitOfMeasure($measurement / 25.4, self::UNIT_INCHES, self::UNIT_DPPT);
            }
        } elseif ($startUnit === self::UNIT_DPI) {
            if ($resultUnit === self::UNIT_INCHES) {
                return round($measurement / $this->dpi, $this->unitsOfMeasure[self::UNIT_INCHES]['precision']);
            } elseif ($resultUnit === self::UNIT_MILLIMETERS) {
                return round($measurement / $this->dpi * 25.4 , $this->unitsOfMeasure[self::UNIT_MILLIMETERS]['precision']);
            } elseif ($resultUnit === self::UNIT_DPI) {
                return round($measurement, $this->unitsOfMeasure[self::UNIT_DPI]['precision']);
            } elseif ($resultUnit === self::UNIT_DPCM) {
                return round($measurement / 0.393701, $this->unitsOfMeasure[self::UNIT_DPCM]['precision']);
            } elseif ($resultUnit === self::UNIT_DPPT) {
                return $this->convertUnitOfMeasure($measurement / $this->dpi, self::UNIT_INCHES, self::UNIT_DPPT);
            }
        } elseif ($startUnit === self::UNIT_DPCM) {
            if ($resultUnit === self::UNIT_INCHES) {
                return round($measurement / $this->dpi * 2.54, $this->unitsOfMeasure[self::UNIT_INCHES]['precision']);
            } elseif ($resultUnit === self::UNIT_MILLIMETERS) {
                return round($measurement / $this->dpi * 10 , $this->unitsOfMeasure[self::UNIT_MILLIMETERS]['precision']);
            } elseif ($resultUnit === self::UNIT_DPI * 2.54) {
                return round($measurement, $this->unitsOfMeasure[self::UNIT_DPI]['precision']);
            } elseif ($resultUnit === self::UNIT_DPCM) {
                return round($measurement, $this->unitsOfMeasure[self::UNIT_DPCM]['precision']);
            } elseif ($resultUnit === self::UNIT_DPPT) {
                return $this->convertUnitOfMeasure($measurement / $this->dpi * 2.54, self::UNIT_INCHES, self::UNIT_DPPT);
            }
        }
        return $measurement;
    }
} 