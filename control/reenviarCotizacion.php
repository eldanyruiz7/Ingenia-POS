<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
require_once ("../conecta/cerrarOtrasSesiones.php");
require_once ("../conecta/usuarioLogeado.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../startbootstrap/vendor/PHPMailer-master/src/Exception.php';
require_once '../startbootstrap/vendor/PHPMailer-master/src/PHPMailer.php';
require_once '../startbootstrap/vendor/PHPMailer-master/src/SMTP.php';
require_once '../startbootstrap/vendor/fpdf/code128.php';
require_once '../startbootstrap/vendor/fpdf/qrcode/qrcode.class.php';
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
    $eMail_         = $_POST['eMail'];
    $idCotizacion_  = $_POST['idCotizacion'];
    $enviar_    = 1;
    require "genRemisionCotizacionPDF.php";
    $sql        = "SELECT * FROM cotizaciones WHERE id = $idCotizacion_ LIMIT 1";
    $result     = $mysqli->query($sql);
    if ($result->num_rows < 1)
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  No se encuentra la cotizaci&oacute;n con ID. '.$idCotizacion_.'.';
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    $sql_p              = "SELECT passEmail, hostEmail, portEmail, smtpSecureEmail FROM configuracion WHERE id = 1 LIMIT 1";
    $result             = $mysqli->query($sql_p);
    $rowPass            = $result->fetch_assoc();
    $passEmail          = $rowPass['passEmail'];
    $hostEmail          = $rowPass['hostEmail'];
    $portEmail          = $rowPass['portEmail'];
    $smtpSecureEmail    = $rowPass['smtpSecureEmail'];
    // $row_fact   = $result->fetch_assoc();
    // $xml        = $row_fact['xml'];
    // $idRelacion = $row_fact['idRelacion'];
    // $tipoCFDI   = $row_fact['tipoCFDI'];
    // if ($tipoCFDI == 'P')
    // {
    //     $sql        = "SELECT folioFiscal FROM facturas WHERE id = $idRelacion LIMIT 1";
    //     $res_rel    = $mysqli->query($sql);
    //     $row_rel    = $res_rel->fetch_assoc();
    //     $UUID_dr    = $row_rel['folioFiscal'];
    // }
    // $archivoXML     = fopen("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml", "w");
    // if($archivoXML == false)
    // {
    //     $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
    //     $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    //     $response['respuesta'] .='  Error de escritura, no se puede procesar el XML. </b>Puede que el disco est&eacute; lleno o protegido contra escritura';
    //     $response['respuesta'] .='</div>';
    //     $response['status']     = 0;
    //     responder($response,$mysqli);
    // }
    // fwrite($archivoXML, $xml);
    // fclose($archivoXML);
    $msg                = "<p>Env&iacute;o de su cotizaci&oacute;n.</p> <p> Esperamos su compra.</p> </b><p>Gracias por su preferencia!</p>";
    $subject            = utf8_decode('Envío de cotización '.$sesion->get("nombreComercio"));
    $mail               = new PHPMailer();
    $mail->isSMTP();
    //Set the hostname of the mail server
    $mail->Host         = $hostEmail;
    $mail->Port         = $portEmail;
    $mail->SMTPSecure   = $smtpSecureEmail;
    $mail->SMTPAuth     = true;

    $mail->Username     = $sesion->get("emailComercio");

    $mail->Password     = $passEmail;

    $mail->From         = $sesion->get("emailComercio");
    $mail->FromName     = $sesion->get("nombreComercio"); //A RELLENAR Nombre a mostrar del remitente.

    $mail->addAddress($eMail);

    $mail->Subject      = $subject;


    $mail->IsHTML(true); // El correo se envía como HTML
    $mail->Body         = $msg;
    $mail->addAttachment("../ws/XML/".str_pad($idCotizacion_, 13, "0", STR_PAD_LEFT).".pdf");
    //Attach an image file
    // if ($opciones == 1)
    // {
    //     $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml");
    //     $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".pdf");
    // }
    // elseif ($opciones == 2)
    // {
    // }
    // elseif ($opciones == 3)
    // {
    //     $mail->addAttachment("XML/".str_pad($idFactura, 13, "0", STR_PAD_LEFT).".xml");
    // }

    //send the message, check for errors
    if (!$mail->send())
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  No se pudo enviar, comprueba tu conexi&oacute; a internet y la configuraci&oacute;n de E-mail en men&uacute; Configuraci&oacute;n e int&eacute;ntalo nuevamente.';
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
    unlink("../ws/XML/".str_pad($idCotizacion_, 13, "0", STR_PAD_LEFT).".pdf");
    responder($response,$mysqli);
}





?>
