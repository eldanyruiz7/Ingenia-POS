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
require ("retiroEfectivoRecibo.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    $monto = $_POST['inputMontoRetirar'];
    $obs = $_POST['inputObsRetirar'];
    $xArs = $_POST['xArs'];
    $status = 1;
    $url = 'ingresoEfectivo.php';
    $idCajero = $idUsuario;
    $response = array(
        "retiro"    => $monto,
        'obs'       => $obs,
        'status'    => $status,
        'url'       => $url
    );
    function responder($response, $mysqli)
    {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    if (!is_numeric($monto) || strlen($monto) == 0 || $monto <= 0)
    {
        $response['numerico'] = 0;
        $response['status'] = 0;
        $response['respuesta'] =   "<div class='alert alert-danger alert-dismissable'>";
        $response['respuesta'].=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response['respuesta'].=   "   <i class='fa fa-exclamation-circle' aria-hidden='true'></i> Completa los campos correctamente.";
        $response['respuesta'].=   "</div>";
        responder($response, $mysqli);
    }
    $sql = "INSERT INTO retiros (sesion, usuario, cajero, monto, observaciones, tipo) VALUES ($idSesion, $xArs, $idCajero, $monto, '$obs', 1)";
    if($mysqli->query($sql) != TRUE)
    {
        $response['status'] = 0;
        $response['respuesta'] =   "<div class='alert alert-danger alert-dismissable'>";
        $response['respuesta'].=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response['respuesta'].=   "   <i class='fa fa-exclamation-circle' aria-hidden='true'></i> Algo sali&oacute; mal. Parece ser un error de base de datos. Contacta con el Administrador del sistema.";
        $response['respuesta'].=   "</div>";
        responder($response, $mysqli);
    }
    else
    {
        $idRecibo               = $mysqli->insert_id;
        $reciboHTML             = genReciboRetiro($idRecibo,$mysqli);
        $response['recibo']     = $reciboHTML;
        responder($response, $mysqli);
    }

}

 ?>
