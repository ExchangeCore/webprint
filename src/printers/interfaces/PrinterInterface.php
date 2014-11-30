<?php
namespace exchangecore\webprint\src\printers\interfaces;

/**
 * Interface PrinterInterface
 * This is an interface of methods that should be implemented for any printer object
 * @package exchangecore\yii2\webprint\src\printers
 */
interface PrinterInterface
{
    /**
     * Adds a command which sets the margin for the upper left corner of the print range
     * @param $offsetHorizontal
     * @param $offsetVertical
     * @param $unitOfMeasure
     * @return $this
     */
    public function setBaseReference($offsetHorizontal, $offsetVertical, $unitOfMeasure);

    /**
     * @param int $amount The number of copies to print
     * @return $this
     */
    public function setCopies($amount);

    /**
     * @param float $horizontal The number of $unitOfMeasure to vertically offset output
     * @param float $vertical The number of $unitOfMeasure to vertically offset output
     * @param string $unitOfMeasure The unit of measurement to use when setting the position, available options of self::UNIT_* constants
     * @return $this
     */
    public function setPosition($horizontal, $vertical, $unitOfMeasure);

    /**
     * Sets the font size for any text printed after this command to the specified points
     * @param int $points The size of the font in points
     * @return $this
     */
    public function setFontSize($points);

    /**
     * Adds a command to the stack telling the printer to print the string provided
     * @param string $string
     * @return $this
     */
    public function outputText($string);

    /**
     * Adds a command to the stack telling the printer to print a code 39 barcode
     * @param string $value The value to be printed
     * @param float $height The height of the barcode using $unitOfMeasure
     * @param string $unitOfMeasure The unit of measure to be used for the height, available options of self::UNIT_* constants
     * @param int $narrowWidth Indicates the width of the "narrow" lines in the barcode
     * @return $this
     */
    public function outputCode39($value, $height, $unitOfMeasure, $narrowWidth = 1);

    /**
     * Sends the command output to the appropriate location, this should not be modified instead modify the onProcessCommandStack function
     * @param bool $reset When true, the command stack will be cleared after processing, when false the stack will remain
     * @return bool Returns true if the stack was processed successfully, returns false if there was an error
     */
    public function processCommandStack($reset);

    /**
     * Returns a string representation of the current command stack
     * @return string
     */
    public function getCommandString();

    /**
     * Clears the current command stack
     * @return $this
     */
    public function resetStack();

    /**
     * Sets the rotation of proceeding output in degrees, pivot point should be the upper left corner of the output
     *
     * Note: If the printer cannot support rotating to the specified degrees, it should rotate to it's nearest degree floored
     * @param int $degreesClockwise
     * @return $this
     */
    public function setRotation($degreesClockwise);

} 