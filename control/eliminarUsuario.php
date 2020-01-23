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
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: ../salir.php");
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
    $id = $_POST['id'];
    $sql = "UPDATE usuarios SET activo = 0 WHERE id = $id LIMIT 1";

    if($result = $mysqli->query($sql))
    {
        $response = array(
            "status"        => 1
        );
        responder($response,$mysqli);
    }
    else
    {
        $response = array(
            "status"        => 0
        );
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>Error</b> No se pudo eliminar el usuario";
        $response["respuesta"] .=   "</div>";
        responder($response,$mysqli);
    }
}
