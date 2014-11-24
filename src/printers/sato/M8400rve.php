<?php
namespace exchangecore\webprint\src\printers\sato;

use exchangecore\webprint\src\printers\interfaces\NetworkPrinterInterface;

class M8400rve extends EPro implements NetworkPrinterInterface
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