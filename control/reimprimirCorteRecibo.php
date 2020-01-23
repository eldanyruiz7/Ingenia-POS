<?php
date_default_timezone_set('America/Mexico_City');
require "../conecta/bd.php";
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
require_once ("../conecta/cerrarOtrasSesiones.php");
require_once ("../conecta/usuarioLogeado.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    require "corteCajaRecibo.php";
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $id = $_POST['id'];
    //echo genReciboVenta($id, $mysqli);
    $reciboHTML = genReciboCorteCaja($id, $mysqli,$sesion);
    $response['recibo']     =   $reciboHTML;
    $response['codigo']     =  str_pad($id, 12, "0", STR_PAD_LEFT);
    responder($response,$mysqli);
}
 ?>
