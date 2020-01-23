<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require ('../conecta/bd.php');
require ("../conecta/sesion.class.php");
$sesion = new sesion();
require ("../conecta/cerrarOtrasSesiones.php");
require ("../conecta/usuarioLogeado.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../startbootstrap/vendor/PHPMailer-master/src/Exception.php';
require '../startbootstrap/vendor/PHPMailer-master/src/PHPMailer.php';
require '../startbootstrap/vendor/PHPMailer-master/src/SMTP.php';
require '../startbootstrap/vendor/fpdf/code128.php';
require '../startbootstrap/vendor/fpdf/qrcode/qrcode.class.php';
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $response = array(
        "status"        => 1
    );
    $eMail      = $_POST['eMail'];
    $idFactura  = $_POST['idFactura'];
    $opciones   = $_POST['opciones'];
    $sql        = "SELECT xml, tipoCFDI, idRelacion FROM facturas WHERE id = $idFactura LIMIT 1";
    $result     = $mysqli->query($sql);
    if ($result->num_rows < 1)
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  No se encuentra la factura con ID. '.$idFactura.'.';
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    $row_fact   = $result->fetch_assoc();
    $xml        = $row_fact['xml'];
    $idRelacion = $row_fact['idRelacion'];
    $tipoCFDI   = $row_fact['tipoCFDI'];
    if ($tipoCFDI == 'P')
    {
        $sql        = "SELECT folioFiscal FROM facturas WHERE id = $idRelacion LIMIT 1";
        $res_rel    = $mysqli->query($sql);
        $row_rel    = $res_rel->fetch_assoc();
        $UUID_dr    = $row_rel['folioFiscal'];
    }
    $archivoXML     = fopen("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml", "w");
    if($archivoXML == false)
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  Error de escritura, no se puede procesar el XML. </b>Puede que el disco est&eacute; lleno o protegido contra escritura';
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    fwrite($archivoXML, $xml);
    fclose($archivoXML);
    if ($tipoCFDI == 'P')
    {
        require 'genComplementoPDF.php';
        genComplemento($idFactura,$mysqli,0);
        $msg        = "<p>Envío del Complemento de Pago.</p> <p> Folio fiscal documento relacionado: <b>$UUID_dr</b></p><p>Id Factura: <b>$idFactura</b></p><p>Gracias por su preferencia!</p>";
        $subject    = utf8_decode('Envío del complemento del pago!');
    }
    else
    {
        require 'genFacturaPDF.php';
        genFactura($idFactura,$mysqli,0);
        $msg        = "<p>Envío del comprobante fiscal digital.</p></br> <p>Gracias por su preferencia!</p>";
        $subject    = 'Su factura, gracias!';
    }
    $mail = new PHPMailer();
    $mail->isSMTP();
    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;

    $mail->Username = "gamb2006@gmail.com";

    $mail->Password = "dvrselvsclpssasF";

    $mail->From='gamb2006@gmail.com';
    $mail->FromName = "SUPER DON ALEX"; //A RELLENAR Nombre a mostrar del remitente.

    $mail->addAddress($eMail);

    $mail->Subject = $subject;


    $mail->IsHTML(true); // El correo se envía como HTML
    $mail->Body    = $msg;
    //Attach an image file
    if ($opciones == 1)
    {
        $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml");
        $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".pdf");
    }
    elseif ($opciones == 2)
    {
        $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".pdf");
    }
    elseif ($opciones == 3)
    {
        $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml");
    }

    //send the message, check for errors
    if (!$mail->send())
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  No se pudo enviar, comprueba tu conexi&oacute; a internet e int&eacute;ntalo nuevamente.';
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
    } else
    {
        $response['respuesta']  = '<div class="alert alert-success alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  <i class="fa fa-check-circle" aria-hidden="true"></i> E-mail enviado correctamente.';
        $response['respuesta'] .='</div>';
        $response['status']     = 1;
    }
    unlink("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml");
    unlink("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".pdf");
    responder($response,$mysqli);
}





?>
