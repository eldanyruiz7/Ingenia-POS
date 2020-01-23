<?php
/* Change to the correct path if you copy this example! */
require __DIR__ . '/../../autoload.php';
//composer require mike42/escpos-php;
//use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\CupsPrintConnector;

try {

    $connector = new CupsPrintConnector("epsonm105series");
    //$connector = new FilePrintConnector("usb://EPSON/M105%20Series?serial=53324E593030323687");
    /* Print a "Hello world" receipt" */
    $printer = new Printer($connector);
    $printer -> initialize();
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("SUPER DON ALEX \n");
    //$printer -> feed();
    $printer -> text("GENERAR NOTA DE SALIDA");
    $printer -> text("\n");
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
    $printer -> text("ID   NOMBRE           P UNIT.       CANT.    SUBTOT.\n");
    $printer -> feed();
    $printer -> text("-------------------------------------- \n");
    $printer -> feed();
    $printer -> selectPrintMode(Printer::MODE_FONT_A);
    $printer -> setJustification(); // Reset
    $printer -> text("003    Higienico Suavel 500hj   33.20          5.000   166.000\n");
    //$printer -> feed();
    $printer -> text("003    Higienico Suavel 500hj   33.20          5.000   166.000\n");
    //$printer -> feed();
    $printer -> text("003    Higienico Suavel 500hj   33.20          5.000   166.000\n");
    //$printer -> feed();
    $printer -> text("003    Higienico Suavel 500hj   33.20          5.000   166.000\n");
    //$printer -> feed();
    $printer -> text("003    Higienico Suavel 500hj   33.20          5.000   166.000\n");
    //$printer -> feed();
    $printer -> text("-------------------------------------- \n");
    $printer -> text("Comanda COCINA \n");
    $printer -> text("Tabulador:  \t tabulador");
    $printer -> text("\n");
    $printer -> text("TabuladorV:  \v tabulador");
    //$printer -> feed();
    //$printer -> selectPrintMode();
    $printer -> text("\n");
    $printer -> text("ID Servicio: \n");
    $fechaHora = date('d-m-Y / H:i:s');
    $printer -> text("Fecha / Hora: \n");
    $printer -> text("Mesero:  \n");
    //$printer -> feed(1);
    $printer -> text("-------------------------------------- \n");
    //$printer -> feed(42);
    $printer -> text("Mesero:  \n");
    //$printer -> cut();
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> text("\n");
    $printer -> setJustification(); // Reset
    $printer -> selectPrintMode(); // Reset
    $printer -> cut();
    /* Close printer */
    $printer -> close();
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}
