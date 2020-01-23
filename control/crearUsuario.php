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
    $nombre     = $_POST['nombre'];
    $apellidop  = $_POST['apellidop'];
    $apellidom  = $_POST['apellidom'];
    $nick       = $_POST['nick'];
    $celular    = $_POST['celular'];
    $email      = $_POST['email'];
    $tipo       = $_POST['tipo'];
    $cntrsn     = $_POST['pwd'];
    if($tipo != 1)
        $tipo = 2;
    $sql        = "INSERT INTO usuarios (nombre, apellidop, apellidom, nick, celular, email, cntrsn, tipousuario) VALUES ('$nombre', '$apellidop', '$apellidom', '$nick', '$celular', '$email', '$cntrsn', $tipo)";
    if($result  = $mysqli->query($sql))
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
        $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>Error</b> No se pudo crear el usuario. Cons&uacute;ltalo con el Administrador del Sistema.";
        $response["respuesta"] .=   "</div>";
        responder($response,$mysqli);
    }
}
