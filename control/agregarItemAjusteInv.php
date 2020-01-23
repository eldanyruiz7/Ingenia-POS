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
        //$response['respuesta'].=$mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    //$esteCliente = json_decode($_POST['arrayCliente']);
    $response = array(
        "status"    =>1
    );
    $idProducto = $_POST['idProducto'];
    $sql = "SELECT
                productos.id AS idProducto,
                productos.codigo AS codigo,
                productos.codigo2 AS codigo2,
                productos.nombrelargo AS nombreProducto,
                productos.existencia AS existencia
            FROM productos
            WHERE productos.id = $idProducto AND productos.activo = 1 LIMIT 1";
    if($resultado = $mysqli->query($sql))
    {
        if($resultado->num_rows > 0)
        {
            $rowProducto = $resultado->fetch_assoc();
            $response['idProducto']     = $rowProducto['idProducto'];
            if(strlen($rowProducto['codigo'])>0)
                $response['codigo']     = $rowProducto['codigo'];
            else
                $response['codigo']     = $rowProducto['codigo2'];
            $response['nombreProducto'] = $rowProducto['nombreProducto'];
            $response['existencia']     = $rowProducto['existencia'];
            $response["status"] = 1;
            responder($response, $mysqli);
        }
        else
        {
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se encontr&oacute; ning&uacute;n registro.</b> Int&eacute;ntalo con otro producto. ';
            $response["respuesta"].='</div>';
            $response["status"] = 0;
            responder($response, $mysqli);
        }
    }
    else
    {
        $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
        $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo agregar.</b> Int&eacute;ntalo nuevamente. ';
        $response["respuesta"].='</div>';
        $response["status"] = 0;
        responder($response, $mysqli);
    }
}
?>
