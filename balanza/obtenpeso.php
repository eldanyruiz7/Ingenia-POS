<?php
//header_remove();
error_reporting(E_ALL);
ini_set('display_errors', '1');     //displays php errors*/
//Es importante filtrar lo que recibimos para que no nos ejecuten comandos
// Let's start the class
exec("sudo /usr/bin/chmod 777 /dev/ttyS4");
include_once 'php_serial.class.php';
$serial = new PhpSerial;

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
$serial->deviceSet("/dev/ttyS4");

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
if($serial->confFlowControl("none"))

// Then we need to open it
$serial->deviceOpen();

// To write into
$serial->sendMessage("P");
usleep(6000);
$e = $serial->readPort();
$e = str_replace(" ","",$e);
$e = str_ireplace("kg","",$e);
echo $e;
$serial->deviceClose();
/*

$p = $_POST["producto"];
//echo $p;
echo rand(10,100);
*/
?>
