<?php

/*
*
* Endeos, Working for You
* blog.endeos.com
*
*/

require_once('PHPMailerAutoload.php');


$mail = new PHPMailer(true);

$mail->SMTPDebug    = 2;

$mail->IsSMTP();
$mail->Host = 'smtp.gmail.com';   /*Servidor SMTP*/
$mail->SMTPSecure = 'tls';   /*Protocolo SSL o TLS*/
$mail->Port = 587;   /*Puerto de conexión al servidor SMTP*/
//$mail->SMTPAuth = true;   /*Para habilitar o deshabilitar la autenticación*/
$mail->Username = 'gamb2006@gmail.com';   /*Usuario, normalmente el correo electrónico*/
$mail->Password = 'dvrselvsclpssasF';   /*Tu contraseña*/
$mail->SetFrom ('gamb2006@gmail.com','Nombre');   /*Correo electrónico que estamos autenticando*/
$mail->FromName = 'Ejemplo';   /*Puedes poner tu nombre, el de tu empresa, nombre de tu web, etc.*/
$mail->CharSet = 'UTF-8';   /*Codificación del mensaje*/

?>
