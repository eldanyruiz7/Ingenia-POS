<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
$usuario = $sesion->get("nick");
if( $usuario == false )
{
    header("Location: /pventa_std/pages/login.php");
}
else
{
    function responder($response, $mysqli)
    {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $proveedor = $_POST['idProveedor'];
    $sql = "UPDATE proveedores
            SET activo = 0
            WHERE id = $proveedor
            LIMIT 1";
    $response = array(
        "status"        => 1,
        "queProveedor"    => $proveedor
    );
    if($mysqli->query($sql) === TRUE)
    {
        if ($mysqli->affected_rows ==1)
        {
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Proveedor con id: <b>$proveedor</b> ha sido borrado correctamente.";
            $response["respuesta"] .=   "</div>";
            responder($response,$mysqli);
        }
        else
        {
            $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> Ning&uacute;n registro borrado.";
            $response["respuesta"] .=   "</div>";
            $response["status"] = 0;
            responder($response,$mysqli);
        }
    }
    else
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se pudo eliminar. Consulta con el administrador del sistema.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
        responder($response,$mysqli);
    }
}
?>
