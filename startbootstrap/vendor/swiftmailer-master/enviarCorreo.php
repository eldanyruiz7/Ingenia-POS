<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once 'lib/swift_required.php'; //verifica que la ruta sea la correcta
//datos de configuración:
//require_once '/path/to/vendor/autoload.php';

// Create the Transport
$transport = (new Swift_SmtpTransport('smtp.gmail.com', 25))
  ->setUsername('gamb2006@gmail.com')
  ->setPassword('dvrselvsclpssasF')
;

// Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

// Create a message
$message = (new Swift_Message('Wonderful Subject'))
  ->setFrom(['gamb2006@gmail.com' => 'Pruebas'])
  ->setTo(['abarrotesalagua@gmail.com' => 'A name'])
  ->setBody('Here is the message itself')
  ;

// Send the message
$result = $mailer->send($message);
?>
