<?php
/*error_reporting(0);
ini_set('display_errors', '0');
*/
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
/**
 * This is a demo script for the functions of the PHP ESC/POS print driver,
 * Escpos.php.
 *
 * Most printers implement only a subset of the functionality of the driver, so
 * will not render this output correctly in all cases.
 *
 * @author Michael Billington <michael.billington@gmail.com>
 */
require __DIR__ . '/../autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
//use Mike42\Escpos\EscposImage;
$arrayCocina = [];
$arrayBar = [];
function dividirPedidos($listaPlatillos, $idOrden, $mesa, $mesero)
{
    foreach($listaPlatillos as $platillo)
    {
        $barococina = $platillo->barococina;
        $descLarga  = $platillo->descLarga;
        $mOrden     = $platillo->mOrden;
        $obs        = $platillo->obs;
        if($barococina == 1)
        {
            $arrayCocina[] = array(
            'descLarga' => $descLarga,
            'mOrden'    => $mOrden,
            'obs' =>  $obs
            );
        }
        else
        {
            $arrayBar[] = array(
            'descLarga' => $descLarga,
            'mOrden'    => $mOrden,
            'obs' =>  $obs
            );
        }
    }
    if(sizeof($arrayCocina) > 0)
        imprimirCocina($arrayCocina, $idOrden, $mesa, $mesero);
    if(sizeof($arrayBar) > 0)
        imprimirBar($arrayBar, $idOrden, $mesa, $mesero);
}
function imprimirCocina($arrayCocina, $idOrden, $mesa, $mesero)
{
    $connector = new WindowsPrintConnector("EPSONCOCINA");
    $printer = new Printer($connector);
    try
    {
        /* Initialize */
        $printer -> initialize();

        /* Text */
        //para observaciones $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
        $printer -> text("Comanda COCINA \n");
        $printer -> text("Mesa: ".$mesa." \n");
        $printer -> selectPrintMode();
        $printer -> text("ID Servicio: ".$idOrden." \n");
        $fechaHora = date('d-m-Y / H:i:s');
        $printer -> text("Fecha / Hora: ".$fechaHora."\n");
        $printer -> text("Mesero: ".$mesero." \n");
        $printer -> feed(1);
        $printer -> text("-------------------------------------- \n");
        $printer -> feed(2);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        for ($x=0; $x < sizeof($arrayCocina) ; $x++)
        {
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $descLarga = $arrayCocina[$x]['descLarga'];
            if($arrayCocina[$x]['mOrden'] != 1)
                $cant = '1';
            else
                $cant = '1/2';
            $printer -> text($cant." ".$descLarga."\n");
            $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
            $obs = $arrayCocina[$x]['obs'];
            $printer -> text($obs."\n");
            $printer -> feed(1);
        }
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("-------------------------------------- \n");
        $printer -> feed(1);
        $printer -> setJustification(); // Reset
        $printer -> selectPrintMode(); // Reset
        $printer -> cut();
        $printer -> close();
    } catch (Exception $e)
    {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }
}
function imprimirBar($arrayBar, $idOrden, $mesa, $mesero)
{
    $connector = new WindowsPrintConnector("EPSONBAR");
    $printer = new Printer($connector);
    try
    {
        /* Initialize */
        $printer -> initialize();

        /* Text */
        //para observaciones $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
        $printer -> text("Comanda BAR \n");
        $printer -> text("Mesa: ".$mesa." \n");
        $printer -> selectPrintMode();
        $printer -> text("ID Servicio: ".$idOrden." \n");
        $fechaHora = date('d-m-Y / H:i:s');
        $printer -> text("Fecha / Hora: ".$fechaHora."\n");
        $printer -> text("Mesero: ".$mesero." \n");
        $printer -> feed(1);
        $printer -> text("-------------------------------------- \n");
        $printer -> feed(2);
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        for ($x=0; $x < sizeof($arrayBar) ; $x++)
        {
            $printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
            $descLarga = $arrayBar[$x]['descLarga'];
            if($arrayBar[$x]['mOrden'] != 1)
                $cant = '1';
            else
                $cant = '1/2';
            $printer -> text($cant." ".$descLarga."\n");
            $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
            $obs = $arrayBar[$x]['obs'];
            $printer -> text($obs."\n");
            $printer -> feed(1);
        }
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> text("-------------------------------------- \n");
        $printer -> feed(1);
        $printer -> setJustification(); // Reset
        $printer -> selectPrintMode(); // Reset
        $printer -> cut();
        $printer -> close();
    } catch (Exception $e)
    {
        echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
    }
}/*
/* Line feeds
$printer -> text("ABC");
$printer -> feed(7);
$printer -> text("DEF");
$printer -> feedReverse(3);
$printer -> text("GHI");
$printer -> feed();
$printer -> cut();

/* Font modes
$modes = array(
    Printer::MODE_FONT_B,
    Printer::MODE_EMPHASIZED,
    Printer::MODE_DOUBLE_HEIGHT,
    Printer::MODE_DOUBLE_WIDTH,
    Printer::MODE_UNDERLINE);
for ($i = 0; $i < pow(2, count($modes)); $i++) {
    $bits = str_pad(decbin($i), count($modes), "0", STR_PAD_LEFT);
    $mode = 0;
    for ($j = 0; $j < strlen($bits); $j++) {
        if (substr($bits, $j, 1) == "1") {
            $mode |= $modes[$j];
        }
    }
    $printer -> selectPrintMode($mode);
    $printer -> text("ABCDEFGHIJabcdefghijk\n");
}
$printer -> selectPrintMode(); // Reset
$printer -> cut();

/* Underline
for ($i = 0; $i < 3; $i++) {
    $printer -> setUnderline($i);
    $printer -> text("The quick brown fox jumps over the lazy dog\n");
}
$printer -> setUnderline(0); // Reset
$printer -> cut();

/* Cuts
$printer -> text("Partial cut\n(not available on all printers)\n");
$printer -> cut(Printer::CUT_PARTIAL);
$printer -> text("Full cut\n");
$printer -> cut(Printer::CUT_FULL);

/* Emphasis
for ($i = 0; $i < 2; $i++) {
    $printer -> setEmphasis($i == 1);
    $printer -> text("The quick brown fox jumps over the lazy dog\n");
}
$printer -> setEmphasis(false); // Reset
$printer -> cut();

/* Double-strike (looks basically the same as emphasis)
for ($i = 0; $i < 2; $i++) {
    $printer -> setDoubleStrike($i == 1);
    $printer -> text("The quick brown fox jumps over the lazy dog\n");
}
$printer -> setDoubleStrike(false);
$printer -> cut();

/* Fonts (many printers do not have a 'Font C')
$fonts = array(
    Printer::FONT_A,
    Printer::FONT_B,
    Printer::FONT_C);
for ($i = 0; $i < count($fonts); $i++) {
    $printer -> setFont($fonts[$i]);
    $printer -> text("The quick brown fox jumps over the lazy dog\n");
}
$printer -> setFont(); // Reset
$printer -> cut();

/* Justification
$justification = array(
    Printer::JUSTIFY_LEFT,
    Printer::JUSTIFY_CENTER,
    Printer::JUSTIFY_RIGHT);
for ($i = 0; $i < count($justification); $i++) {
    //$printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> setJustification($justification[$i]);
    $printer -> text("A man a plan a canal panama\n");
}
$printer -> setJustification(); // Reset
$printer -> cut();

/* Barcodes - see barcode.php for more detail
$printer -> setBarcodeHeight(80);
$printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
$printer -> barcode("9876");
$printer -> feed();
$printer -> cut();

/* Graphics - this demo will not work on some non-Epson printers
try {
    $logo = EscposImage::load("resources/escpos-php.png", false);
    $imgModes = array(
        Printer::IMG_DEFAULT,
        Printer::IMG_DOUBLE_WIDTH,
        Printer::IMG_DOUBLE_HEIGHT,
        Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT
    );
    foreach ($imgModes as $mode) {
        $printer -> graphics($logo, $mode);
    }
} catch (Exception $e) {
    /* Images not supported on your PHP, or image file not found
    $printer -> text($e -> getMessage() . "\n");
}
$printer -> cut();

/* Bit image
try {
    $logo = EscposImage::load("resources/escpos-php.png", false);
    $imgModes = array(
        Printer::IMG_DEFAULT,
        Printer::IMG_DOUBLE_WIDTH,
        Printer::IMG_DOUBLE_HEIGHT,
        Printer::IMG_DOUBLE_WIDTH | Printer::IMG_DOUBLE_HEIGHT
    );
    foreach ($imgModes as $mode) {
        $printer -> bitImage($logo, $mode);
    }
} catch (Exception $e) {
    /* Images not supported on your PHP, or image file not found
    $printer -> text($e -> getMessage() . "\n");
}
$printer -> cut();

/* QR Code - see also the more in-depth demo at qr-code.php
$testStr = "Testing 123";
$models = array(
    Printer::QR_MODEL_1 => "QR Model 1",
    Printer::QR_MODEL_2 => "QR Model 2 (default)",
    Printer::QR_MICRO => "Micro QR code\n(not supported on all printers)");
foreach ($models as $model => $name) {
    $printer -> qrCode($testStr, Printer::QR_ECLEVEL_L, 3, $model);
    $printer -> text("$name\n");
    $printer -> feed();
}
$printer -> cut();

/* Pulse
$printer -> pulse();

/* Always close the printer! On some PrintConnectors, no actual
 * data is sent until the printer is closed. */
