## Purpose

The purpose of this library is to allow a common PHP api which will allow printing directly to print devices or outputting
the same content to other interfaces, such as your web browser. 

## Supported Printers

* Web Browser
* Sato E/Pro Network Printers

## Features
              
* Text Output
    * Font Size 
* Code 39 Barcode
    * Barcode Height
    * Bar Widths

## Sample Usage

### Web Browser Sample

```
<?php
$printer = new exchangecore\webprint\src\printers\WebPrinter();
$printer
    ->setBaseReference(0.25, 0.25, $printer::UNIT_INCHES)
    ->setFontSize(20)
    ->outputText('HELLO WORLD')
    ->setPosition(0, 0.25, $printer::UNIT_INCHES)
    ->outputCode39('*123456789*', 16, $printer::UNIT_POINT, 4)
    ->processCommandStack();
```

### Network Printer Sample
                              
```
<?php
$printer = new exchangecore\webprint\src\printers\sato\M8400rve();
$printer                       
    ->setPaperWidth(4, $printer::UNIT_INCHES)
    ->setBaseReference(0.25, 0.25, $printer::UNIT_INCHES)
    ->setFontSize(20)
    ->outputText('HELLO WORLD')
    ->setPosition(0, 0.25, $printer::UNIT_INCHES)
    ->outputCode39('*123456789*', 16, $printer::UNIT_POINT, 4)
    ->setCopies(3);
    
if($printer->connect('10.1.0.49')){
    if($printer->processCommandStack(false)) {
        echo 'Printed Successfully';    
    } else {
        echo 'Failed to print';
    }
    $printer->disconnect();
} else {
    echo 'Failed to connect';
};
```