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
    /*if($validar['tipousuario'] == 2)
        header("location: /rest/orden/index.php");*/
// Aquí va el contenido de la pagina qu se mostrara en caso de que se haya iniciado sesion

    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $id = $_POST['id'];
    $nombre = $_POST['cacheNuevo'];
    $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id LIMIT 1";
    $result = $mysqli->query($sql);
    if($mysqli->affected_rows == 1)
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
        responder($response,$mysqli);
    }
}
