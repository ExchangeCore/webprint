<?php
namespace exchangecore\webprint\src\printers;

/**
 * Interface PrinterInterface
 * This is an interface of methods that should be implemented for any printer object
 * @package exchangecore\yii2\webprint\src\printers
 */
interface PrinterInterface
{

    public function connect($host, $port);
    public function disconnect();
    public function sendCommandStack($reset);
    public function setBaseReference();
    public function startPrint();
    public function endPrint();
    public function setPosition($horizontal, $vertical);

    /**
     * Sets the font size to the closest available font height to the given font point value
     * @param int $size The size of the font in points
     * @return $this
     */
    public function setFontSize($size);
    public function text($string);

} 