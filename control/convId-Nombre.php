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
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    function responder($response, $mysqli)
    {
        $response['respuesta']      = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    function query($idProducto, $mysqli, $response)
    {
        $query                      = "SELECT id, nombrelargo FROM productos WHERE activo = 1 AND id = $idProducto LIMIT 1";
        $result                     = mysqli_query($mysqli, $query);
        $row                        = $result->fetch_assoc();
        $response['id']             = $row['id'];
        $response['nombreLargo']    = $row['nombrelargo'];
        responder($response, $mysqli);
    }
    $response                       = array(
        "status"                    => 1 );
    $idProducto                     = $_POST["idProducto"];
    $direccion                      = $_POST["direccion"];
    if (($idProducto <= 0          || is_numeric($idProducto) == FALSE ) && $direccion == 1)
    {
        $query                      = "SELECT MIN(id) AS idMin FROM productos WHERE activo = 1";
        $result                     = mysqli_query($mysqli, $query);
        $row                        = $result->fetch_assoc();
        $idProducto                 = $row['idMin'];
        query($idProducto, $mysqli, $response);
    }
    elseif (($idProducto <= 0      || is_numeric($idProducto) == FALSE ) && $direccion == 0)
    {
        $query                      = "SELECT MAX(id) AS idMax FROM productos WHERE activo = 1";
        $result                     = mysqli_query($mysqli, $query);
        $row                        = $result->fetch_assoc();
        $idProducto                 = $row['idMax'];
        query($idProducto, $mysqli, $response);
    }

    $query                          = "SELECT MAX(id) AS idMax FROM productos WHERE activo = 1";
    $result                         = mysqli_query($mysqli, $query);
    $row                            = $result->fetch_assoc();
    $idMaxProducto                  = $row['idMax'];

    $query                          = "SELECT MIN(id) AS idMin FROM productos WHERE activo = 1";
    $result                         = mysqli_query($mysqli, $query);
    $row                            = $result->fetch_assoc();
    $idMinProducto                  = $row['idMin'];
    if ($idMaxProducto              == $idProducto && $direccion == 1)
    {
        $idProducto                 = $idMinProducto;
        query($idProducto, $mysqli, $response);
    }
    elseif ($idMinProducto          == $idProducto && $direccion == 0)
    {
        $idProducto                 = $idMaxProducto;
        query($idProducto, $mysqli, $response);
    }
    if ($direccion                  == 1)
        $query                          = "SELECT id, nombrelargo FROM productos WHERE activo = 1 AND id > $idProducto ORDER BY id ASC";
    else
        $query                          = "SELECT id, nombrelargo FROM productos WHERE activo = 1 AND id < $idProducto ORDER BY id DESC";

    //echo $query;
    $result                         = $mysqli->query($query);
    while($row                      = $result->fetch_assoc())
    {
        if ($row['id']              == $idProducto)
            continue;
        $response['id']         = $row['id'];
        $response['nombreLargo']= $row['nombrelargo'];
        responder($response, $mysqli);
    }
    // $query                          = "SELECT id, nombrelargo FROM productos WHERE activo = 1 AND id = $idProducto LIMIT 1";
    // $result                         = mysqli_query($mysqli, $query);
    // if(mysqli_num_rows($result)     > 0)
    // {
    //     $row                        = $result->fetch_assoc();
    //     $response['nombreLargo']    = $row['nombrelargo'];
    //     $response['id']             = $row['id'];
    // }
    // else
    // {
    //     $query                      = "SELECT id, nombrelargo FROM productos WHERE activo = 1 AND id = 1 LIMIT 1";
    //     $result                     = mysqli_query($mysqli, $query);
    //     $row                        = $result->fetch_assoc();
    //     $response['nombreLargo']    = $row['nombrelargo'];
    //     $response['id']             = $row['id'];
    // }
}
?>
