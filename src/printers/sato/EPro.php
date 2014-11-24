<?php
namespace exchangecore\webprint\src\printers\sato;

use exchangecore\webprint\src\printers\NetworkPrinter;
use exchangecore\webprint\src\printers\interfaces\NetworkPrinterInterface;

class EPro extends NetworkPrinter implements NetworkPrinterInterface
{
    const STX = "\x02";
    const ETX = "\x03";
    const ESC = "\x1B";
    const ENQ = "\x05";
    const CAN = "\x18";
    const OFFLINE = "\x40";

    const FONT_SIZE_XS = "XS";

    protected $printWidth = 4.1;
    protected $paperWidth = 4;
    protected $dpi = 203;

    protected $defaultPort = 1024;

    protected function beforePrint()
    {
        if(!parent::beforePrint()) {
            return false;
        }

        $this->prependCommand(static::ESC . 'A');
        $this->pushCommand(static::ESC . 'Z');

        return true;
    }

    public function outputText($string)
    {
        $currentPositionHorizontal = $this->convertUnitOfMeasure($this->currentPositionHorizontal, self::UNIT_INCHES, self::DOTS_PER_INCH);
        $currentPositionVertical = $this->convertUnitOfMeasure($this->currentPositionVertical, self::UNIT_INCHES, self::DOTS_PER_INCH);
        $this->pushCommand(static::ESC . 'H' . str_pad($currentPositionHorizontal, 4, '0', STR_PAD_LEFT));
        $this->pushCommand(static::ESC . 'V' . str_pad($currentPositionVertical, 4, '0', STR_PAD_LEFT));
        if($this->currentFontSize > 0) {
            $fontSize = $this->convertUnitOfMeasure($this->currentFontSize, self::UNIT_POINT, self::DOTS_PER_INCH);
            //use the vector font
            $this->pushCommand(static::ESC . '$A,' . $fontSize . ',' . $fontSize . ',0');
            $this->pushCommand(static::ESC . '$=' . $string);
        } else {
            //use a default font
            $this->pushCommand(static::ESC . 'XM' . $string);
        }
        return $this;
    }

    public function setBaseReference($offsetHorizontal = 0, $offsetVertical = 0, $unitOfMeasure = self::UNIT_INCHES)
    {
        $printWidth = $this->convertUnitOfMeasure($this->printWidth, self::UNIT_INCHES, self::DOTS_PER_INCH);
        $paperWidth = $this->convertUnitOfMeasure($this->paperWidth, self::UNIT_INCHES, self::DOTS_PER_INCH);
        $offsetHorizontal = $this->convertUnitOfMeasure($offsetHorizontal, $unitOfMeasure, self::DOTS_PER_INCH);
        $offsetHorizontal = str_pad($printWidth - $paperWidth + $offsetHorizontal, 4, '0', STR_PAD_LEFT);
        $offsetVertical = $this->convertUnitOfMeasure($offsetVertical, $unitOfMeasure, self::DOTS_PER_INCH);
        $offsetVertical = str_pad(1+$offsetVertical, 4, '0', STR_PAD_LEFT);
        $this->pushCommand(static::ESC . 'A3H' . $offsetHorizontal . 'V' . $offsetVertical);
        return $this;
    }

    public function setPosition($horizontal = null, $vertical = null, $unitOfMeasure = self::UNIT_INCHES)
    {
        if(!is_null($horizontal)) {
            $this->currentPositionHorizontal = $this->convertUnitOfMeasure($horizontal, $unitOfMeasure, self::DOTS_PER_INCH);
        }
        if(!is_null($vertical)) {
            $this->currentPositionVertical = $this->convertUnitOfMeasure($vertical, $unitOfMeasure, self::DOTS_PER_INCH);
        }
        return $this;
    }

    public function setFontSize($size)
    {
        $this->currentFontSize = $size;
        return $this;
    }

    public function setCopies($amount)
    {
        $this->pushCommand(static::ESC . 'Q' . (int) $amount);
        return $this;
    }

    public function outputCode39($value, $height, $unitOfMeasure = self::UNIT_INCHES, $narrowWidth = 1)
    {
        $this->pushCommand(static::ESC . 'H' . str_pad($this->currentPositionHorizontal, 4, '0', STR_PAD_LEFT));
        $this->pushCommand(static::ESC . 'V' . str_pad($this->currentPositionVertical, 4, '0', STR_PAD_LEFT));
        $narrowWidth = str_pad($narrowWidth, 2 , '0', STR_PAD_LEFT);
        $height = str_pad($this->convertUnitOfMeasure($height, $unitOfMeasure, self::DOTS_PER_INCH), 3, '0', STR_PAD_LEFT);
        $this->pushCommand(static::ESC . 'B1' . $narrowWidth . $height . $value);

        return $this;
    }
}