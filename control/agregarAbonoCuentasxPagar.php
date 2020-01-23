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
    if(isset($_POST['idProducto']))
    {
        $idProducto = $_POST['idProducto'];
        $sql = "SELECT nombre, nombrelargo, existencia
                FROM productos
                INNER JOIN unidadesventa ON productos.unidadventa = unidadesventa.id
                WHERE productos.id = $idProducto LIMIT 1";
    }
    if(isset($_POST['codBarras']))
    {
        $codBarras = $_POST['codBarras'];
        $sql = "SELECT nombre, nombrelargo, existencia, productos.id
                FROM productos
                INNER JOIN unidadesventa ON productos.unidadventa = unidadesventa.id
                WHERE productos.codigo = $codBarras OR productos.codigo2 = $codBarras LIMIT 1";
    }
    $response = array(
        "nombre"    =>1
    );

    if($resultado = $mysqli->query($sql))
    {
        if($resultado->num_rows > 0)
        {
            $row = $resultado->fetch_assoc();
            $nombreLargo = $row['nombrelargo'];
            $existencia = number_format($row['existencia'],3,".",",");
            $unidadVenta = $row['nombre'];
            $response['html'] = "<div class='col-lg-12'>";
            $response['html'].= "   <label><h3>$nombreLargo</h3></label>";
            $response['html'].= "</div>";
            $response['html'].= "<div class='col-lg-12'>";
            $response['html'].= "   <label>Existencias en sistema:</label>";
            $response['html'].= "   <div class='form-group input-group'>";
            $response['html'].= "       <input id='inputCantidadSistema' type='number' class='form-control' disabled value='$existencia'>";
            $response['html'].= "       <span class='input-group-addon'>$unidadVenta</span>";
            $response['html'].= "   </div>";
            $response['html'].= "</div>";
            $response['html'].= "<div class='col-lg-12'>";
            $response['html'].= "   <label>Existencias en f&iacute;sico:</label>";
            $response['html'].= "   <div class='form-group input-group'>";
            $response['html'].= "       <input id='inputCantidadFisico' type='number' class='form-control'>";
            $response['html'].= "       <span class='input-group-addon'>$unidadVenta</span>";
            $response['html'].= "   </div>";
            $response['html'].= "</div>";
            $response['html'].= "<div class='col-lg-12'>";
            $response['html'].= "   <div class='form-group'>";
            $response['html'].= "       <label>Diferencia:</label>";
            $response['html'].= "       <input id='inputCantidadDiferencia' type='number' class='form-control' disabled value='$existencia'>";
            $response['html'].= "   </div>";
            $response['html'].= "</div>";
            if(isset($_POST['codBarras']))
                $response['id']   = $row['id'];
            $response["status"] = 1;
            responder($response, $mysqli);
        }
        else
        {
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>Error.</b> No se encontr&oacute; el producto en la Base de Datos. ';
            $response["respuesta"].='</div>';
            $response["status"] = 0;
            responder($response, $mysqli);
        }
    }
    else
    {
        $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
        $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>Error.</b> Ocurri&oacute; un error inesperado. Consulta al Administrador del Sistema.';
        $response["respuesta"].='</div>';
        $response["status"] = 0;
        responder($response, $mysqli);
    }
}
?>
