<?php
namespace exchangecore\webprint\src\printers\sato;

use exchangecore\webprint\src\printers\PrinterInterface;

class M8400rve extends EPro implements PrinterInterface
{

    public function cutLabel()
    {
        $this->pushCommand(static::ESC . '-A');
    }

    public function cutJob()
    {
        $this->pushCommand(static::ESC . '-a');
    }
} 