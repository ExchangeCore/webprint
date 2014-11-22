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
    }

    public function endPrint()
    {
        $this->pushCommand(static::ESC . 'Q' . chr('1'));
        $this->pushCommand(static::ESC . 'Z');
    }

    public function text($string)
    {
        //todo
        $this->pushCommand('');
    }

    public function setBaseReference($offsetHorizontal = 0, $offsetVertical = 0, $unitOfMeasure = self::UNIT_DPI)
    {
        $offsetHorizontal = $this->convertUnitOfMeasure($offsetHorizontal, $unitOfMeasure, self::UNIT_DPI);
        $offsetHorizontal = str_pad($this->printWidthDpi - $this->paperWidthDpi - $offsetHorizontal, 4, '0', STR_PAD_LEFT);
        $offsetVertical = $this->convertUnitOfMeasure($offsetVertical, $unitOfMeasure, self::UNIT_DPI);
        $offsetVertical = str_pad(1-$offsetVertical, 4, '0', STR_PAD_LEFT);
        $this->pushCommand(static::ESC . 'H' . $offsetHorizontal . 'V' . $offsetVertical);
    }

    public function setPosition($horizontal, $vertical)
    {
        //todo
    }

    public function setFontSize($font)
    {
        //todo
    }
}