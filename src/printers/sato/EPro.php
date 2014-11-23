<?php
namespace exchangecore\webprint\src\printers\sato;

use exchangecore\webprint\src\printers\Printer;
use exchangecore\webprint\src\printers\PrinterInterface;

class EPro extends Printer implements PrinterInterface
{
    const STX = "\x02";
    const ETX = "\x03";
    const ESC = "\x1B";
    const ENQ = "\x05";
    const CAN = "\x18";
    const OFFLINE = "\x40";

    const FONT_SIZE_XS = "XS";

    public function startPrint()
    {
        $this->pushCommand(static::ESC . 'A');
        return $this;
    }

    public function endPrint()
    {
        $this->pushCommand(static::ESC . 'Z');
        return $this;
    }

    public function outputText($string)
    {
        $this->pushCommand(static::ESC . 'H' . str_pad($this->currentPositionHorizontal, 4, '0', STR_PAD_LEFT));
        $this->pushCommand(static::ESC . 'V' . str_pad($this->currentPositionVertical, 4, '0', STR_PAD_LEFT));
        if($this->currentFontSize > 0) {
            //use the vector font
            $this->pushCommand(static::ESC . '$A,' . $this->currentFontSize . ',' . $this->currentFontSize . ',0');
            $this->pushCommand(static::ESC . '$=' . $string);
        } else {
            //use a default font
            $this->pushCommand(static::ESC . 'XM' . $string);
        }
        return $this;
    }

    public function setBaseReference($offsetHorizontal = 0, $offsetVertical = 0, $unitOfMeasure = self::UNIT_DPI)
    {
        $offsetHorizontal = $this->convertUnitOfMeasure($offsetHorizontal, $unitOfMeasure, self::UNIT_DPI);
        $offsetHorizontal = str_pad($this->printWidthDpi - $this->paperWidthDpi + $offsetHorizontal, 4, '0', STR_PAD_LEFT);
        $offsetVertical = $this->convertUnitOfMeasure($offsetVertical, $unitOfMeasure, self::UNIT_DPI);
        $offsetVertical = str_pad(1+$offsetVertical, 4, '0', STR_PAD_LEFT);
        $this->pushCommand(static::ESC . 'A3H' . $offsetHorizontal . 'V' . $offsetVertical);
        return $this;
    }

    public function setPosition($horizontal = null, $vertical = null, $unitOfMeasure = self::UNIT_DPI)
    {
        if(!is_null($horizontal)) {
            $this->currentPositionHorizontal = $this->convertUnitOfMeasure($horizontal, $unitOfMeasure, self::UNIT_DPI);
        }
        if(!is_null($vertical)) {
            $this->currentPositionVertical = $this->convertUnitOfMeasure($vertical, $unitOfMeasure, self::UNIT_DPI);
        }
        return $this;
    }

    public function setFontSize($size)
    {
        $this->currentFontSize = $this->convertUnitOfMeasure($size * 0.01388889, self::UNIT_INCHES, self::UNIT_DPI);
        return $this;
    }

    public function setCopies($amount)
    {
        $this->pushCommand(static::ESC . 'Q' . (int) $amount);
        return $this;
    }

    public function outputCode39($value)
    {
        $this->pushCommand(static::ESC . 'H' . str_pad($this->currentPositionHorizontal, 4, '0', STR_PAD_LEFT));
        $this->pushCommand(static::ESC . 'V' . str_pad($this->currentPositionVertical, 4, '0', STR_PAD_LEFT));
        $narrowWidth = str_pad($this->barcodeNarrowWidth, 2 , '0', STR_PAD_LEFT);
        $height = str_pad($this->barcodeHeight, 3, '0', STR_PAD_LEFT);
        $this->pushCommand(static::ESC . 'B1' . $narrowWidth . $height . $value);

        return $this;
    }
}