<?php
namespace exchangecore\webprint\src\printers;

use exchangecore\webprint\src\printers\interfaces\PrinterInterface;
use exchangecore\webprint\src\barcodes\code39;

class WebPrinter extends Printer implements PrinterInterface
{
    protected $head = '';

    protected $bodyStyle = [
        'position' => 'relative',
        'font-family' => 'Arial, Helvetica, sans-serif;'
    ];

    protected $elementStyle = [];

    protected $textStyle = [
        'position' => 'absolute',
        'margin-top' => '0',
        'margin-left' => '0',
    ];

    protected $barcodeStyle = [
        'position' => 'absolute',
        'margin-top' => '0',
        'margin-left' => '0',
        'height' => '0',
    ];

    protected function beforePrint()
    {
        if(!parent::beforePrint()) {
            return false;
        }

        $this->prependCommand("
        <head>
        <style>
        body {
            {$this->getStyleString($this->bodyStyle)}
        }
        </style>
        </head>
        <body>");

        $this->pushCommand('<script type="text/javascript">window.print();</script></body>');

        return true;
    }

    public function onProcessCommandStack($reset = true)
    {
        echo $this->getCommandString();
        return parent::onProcessCommandStack($reset);
    }


    public function setBaseReference($offsetHorizontal = 0, $offsetVertical = 0, $unitOfMeasure = self::UNIT_INCHES)
    {
        $this->bodyStyle['margin-top'] = $this->convertUnitOfMeasure($offsetVertical, $unitOfMeasure, self::UNIT_INCHES) . 'in';
        $this->bodyStyle['margin-left'] = $this->convertUnitOfMeasure($offsetHorizontal, $unitOfMeasure, self::UNIT_INCHES) . 'in';
        return $this;
    }

    public function setPosition($horizontal, $vertical, $unitOfMeasure = self::UNIT_INCHES)
    {
        $this->textStyle['margin-top'] = $this->convertUnitOfMeasure($vertical, $unitOfMeasure, self::UNIT_INCHES) . 'in';
        $this->textStyle['margin-left'] = $this->convertUnitOfMeasure($horizontal, $unitOfMeasure, self::UNIT_INCHES) . 'in';
        $this->barcodeStyle['margin-top'] = $this->convertUnitOfMeasure($vertical, $unitOfMeasure, self::UNIT_INCHES) . 'in';
        $this->barcodeStyle['margin-left'] = $this->convertUnitOfMeasure($horizontal, $unitOfMeasure, self::UNIT_INCHES) . 'in';
        return $this;
    }

    public function setCopies($quantity)
    {
        //no can do, it's up to the user to select the appropriate quantity they desire
        return $this;
    }

    public function outputText($string)
    {
        $style = $this->getStyleString($this->textStyle) . $this->getStyleString($this->elementStyle);
        $this->pushCommand('<span style="' . $style . '">' . $string .'</span>');
        return $this;
    }

    protected function getStyleString($styleArray)
    {
        $style = '';
        foreach($styleArray AS $property => $value) {
            $style .= $property . ':';
            $style .= $value;
            $style .= ';';
        }
        return $style;
    }

    public function setFontSize($points)
    {
        $this->textStyle['font-size'] = $points . 'pt';
        return $this;
    }

    public function outputCode39($value, $height, $unitOfMeasure = self::UNIT_INCHES, $narrowWidth = 1)
    {

        $barcode = new code39();
        $barcode->setBarWidth($narrowWidth);
        $style = $this->getStyleString($this->textStyle) . 'height: ' . $this->convertUnitOfMeasure($height, $unitOfMeasure, self::UNIT_INCHES) . 'in;';
        $style .= $this->getStyleString($this->elementStyle);
        $this->pushCommand('<img style="' . $style . '" src="data:image/png;base64,' . $barcode->draw($value) . '"/>');
        return $this;
    }

    public function setRotation($clockwiseRotation)
    {
        $this->elementStyle['-webkit-transform'] = 'rotate(' . $clockwiseRotation .'deg)';
        $this->elementStyle['-moz-transform'] = 'rotate(' . $clockwiseRotation .'deg)';
        $this->elementStyle['-o-transform'] = 'rotate(' . $clockwiseRotation .'deg)';
        $this->elementStyle['-ms-transform'] = 'rotate(' . $clockwiseRotation .'deg)';
        $this->elementStyle['transform'] = 'rotate(' . $clockwiseRotation .'deg)';
        $this->elementStyle['transform-origin'] = 'left top';
        return $this;
    }
} 