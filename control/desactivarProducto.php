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
    $producto = $_POST['idProducto'];
    $sql = "UPDATE productos
            SET pausado = 1
            WHERE id = $producto
            LIMIT 1";
    $response = array(
        "status"        => 1,
        "queProducto"    => $producto
    );
    if($mysqli->query($sql) === TRUE)
    {
        if ($mysqli->affected_rows ==1)
        {
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Producto con id: <b>$producto</b> ha sido desactivado correctamente.";
            $response["respuesta"] .=   "</div>";
            $response["btn"]        =   '<div class="btn-group pull-left">';
            $response["btn"]       .=   '   <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
            $response["btn"]       .=   '       <i class="fa fa-bars" aria-hidden="true"></i>';
            $response["btn"]       .=   '   </button>';
            $response["btn"]       .=   '   <ul class="dropdown-menu slidedown">';
            $response["btn"]       .=   '       <li>';
            $response["btn"]       .=   "           <a class='aModificar' name='$producto'>";
            $response["btn"]       .=   '               <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar';
            $response["btn"]       .=   '           </a>';
            $response["btn"]       .=   '       </li>';
            $response["btn"]       .=   '       <li>';
            $response["btn"]       .=   "           <a class='aActivar' name='$producto'>";
            $response["btn"]       .=   "               <i class='fa fa-hand-o-up' aria-hidden='true'></i> Activar";
            $response["btn"]       .=   "           </a>";
            $response["btn"]       .=   "       </li>";
            $response["btn"]       .=   '       <li>';
            $response["btn"]       .=   "            <a class='aEliminar' name='$producto'>";
            $response["btn"]       .=   '                <i class="fa fa-times-circle" aria-hidden="true"></i> Eliminar';
            $response["btn"]       .=   '            </a>';
            $response["btn"]       .=   '        </li>';
            $response["btn"]       .=   '    </ul>';
            $response["btn"]       .=   '</div>';
            $response['estatus']    = 'Inactivo';
            responder($response,$mysqli);
        }
        else
        {
            $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> Ning&uacute;n registro desactivado.";
            $response["respuesta"] .=   "</div>";
            $response["status"] = 0;
            responder($response,$mysqli);
        }
    }
    else
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se pudo desactivar. Consulta con el administrador del sistema.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
        responder($response,$mysqli);
    }
}
?>
