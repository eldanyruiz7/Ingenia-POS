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
    $request = $_POST["nombre"];
    $query = "SELECT id FROM productos WHERE activo = 1 AND nombrelargo = '$request' LIMIT 1";
    $result = mysqli_query($mysqli, $query);
    //$data = array();
    if(mysqli_num_rows($result) > 0)
    {
        $row = $result->fetch_assoc();
            echo $row['id'];
    }
}
?>
