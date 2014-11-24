<?php
namespace exchangecore\webprint\src\printers;

class Printer
{
    const UNIT_INCHES = 'in';
    const UNIT_POINT = 'pt';
    const UNIT_CENTIMETERS = 'cm';
    const UNIT_MILLIMETERS = 'mm';

    const INCHES_PER_POINT = 0.16666666666667;

    protected function unitsOfMeasure()
    {
        return [
            self::UNIT_INCHES => [
                'precision' => 3,
            ],
            self::UNIT_CENTIMETERS => [
                'precision' => 2,
            ],
            self::UNIT_MILLIMETERS => [
                'precision' => 1,
            ],
            self::UNIT_POINT => [
                'precision' => 1,
            ]
        ];
    }

    protected $command = [];

    /**
     * @var float $printWidth
     *  The maximum width the printer can print
     */
    protected $printWidth = 0;
    protected $paperWidth = 0;

    /** @var int Current Horizontal Position in inches */
    protected $currentPositionHorizontal = 0.002;
    /** @var int Current Vertical Position in inches */
    protected $currentPositionVertical = 0.0002;
    /** @var int Current Font Size in points */
    protected $currentFontSize = 0;

    /** Start of Events */

    /**
     * An event that is fired before any print commands are processed, always call parent::beforePrint() in this function
     * @return bool returns true if printing should continue, false if there was a problem that should prevent printing
     */
    protected function beforePrint()
    {
        return true;
    }

    /**
     * An event that is fired after the print commands are processed, always call parent::afterPrint() in this function
     */
    protected function afterPrint()
    {
        //just a shell
    }

    protected function onProcessCommandStack($resetCommandQueue = true)
    {
        if ($resetCommandQueue) {
            $this->resetStack();
        }
        return true;
    }

    /** Command Storage and processing */

    public function processCommandStack($resetCommandQueue)
    {
        if($this->beforePrint()) {
            $processed = $this->onProcessCommandStack($resetCommandQueue);
            $this->afterPrint();
            return $processed;
        } else {
            return false;
        }
    }

    protected function prependCommand($command)
    {
        array_unshift($this->command, $command);
        return $this;
    }

    protected function pushCommand($command)
    {
        $this->command[] = $command;
        return $this;
    }

    public function getCommandString()
    {
        $string = '';
        foreach ($this->command AS $command) {
            $string .= $command;
        }
        return $string;
    }

    public function resetStack()
    {
        $this->command = [];
        return $this;
    }

    /** Printer Commands */

    /**
     * This method is used to set the maximum printing width of the printer and is used in combination with the
     * paper width setting to determine the default printing offset
     * @param float $measurement
     * @param string $unitOfMeasure
     * @return $this
     */
    public function setPrintWidth($measurement, $unitOfMeasure = self::UNIT_INCHES)
    {
        $this->printWidth = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_INCHES);
        return $this;
    }

    /**
     * This method is used to set the width of the print media being used and is used in combination with the
     * maximum printing width to determine the default printing offset
     * @param float $measurement
     * @param string $unitOfMeasure
     * @return $this
     */
    public function setPaperWidth($measurement, $unitOfMeasure = self::UNIT_INCHES)
    {
        $this->paperWidth = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_INCHES);
        return $this;
    }

    public function setBarcodeNarrowWidth($measurement, $unitOfMeasure = self::UNIT_INCHES)
    {
        $this->barcodeNarrowWidth = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_INCHES);
        return $this;
    }

    public function setBarcodeHeight($measurement, $unitOfMeasure = self::UNIT_INCHES)
    {
        $this->barcodeHeight = $this->convertUnitOfMeasure($measurement, $unitOfMeasure, self::UNIT_INCHES);
        return $this;
    }

    /**
     * Helper function for converting one unit of measure to another
     * @param float $measurement
     * @param int $startUnit The starting unit of measure UNIT_* constant
     * @param int $resultUnit The unit of measure UNIT_* constant to convert the measurement to
     * @param bool $round Round to the precision specified for the resulting unit of measure
     * @return float
     */
    public function convertUnitOfMeasure($measurement, $startUnit, $resultUnit, $round = true)
    {
        if ($startUnit === self::UNIT_INCHES) {
            switch($resultUnit) {
                case self::UNIT_POINT:
                    $measurement *= 72;
                    break;
                case self::UNIT_MILLIMETERS:
                    $measurement *= 25.4;
                    break;
                case self::UNIT_CENTIMETERS:
                    $measurement *= 2.54;
                    break;
            }
        } elseif ($startUnit === self::UNIT_POINT) {
            $measurement = $this->convertUnitOfMeasure($measurement / 72, self::UNIT_INCHES, $resultUnit, $round);
        } elseif ($startUnit === self::UNIT_CENTIMETERS) {
            $measurement = $this->convertUnitOfMeasure($measurement / 2.54, self::UNIT_INCHES, $resultUnit, $round);
        } elseif ($startUnit === self::UNIT_MILLIMETERS) {
            $measurement = $this->convertUnitOfMeasure($measurement / 25.4, self::UNIT_INCHES, $resultUnit, $round);
        }

        if($round) {
            return round($measurement, $this->unitsOfMeasure()[$resultUnit]['precision']);
        } else {
            return $measurement;
        }
    }
} 