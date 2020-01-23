<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require ('../conecta/bd.php');
require ("../conecta/sesion.class.php");
$sesion = new sesion();
require ("../conecta/cerrarOtrasSesiones.php");
require ("../conecta/usuarioLogeado.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    //require ("../control/cotizacionRecibo.php");
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $usuario        = $sesion->get("id");
    $listaProductos = json_decode($_POST['listaProductos']);
    $idCotizacion   = $_POST['idCotizacion'];
    $idCliente      = $_POST['idCliente'];
    $response = array(
        "status"        => 1
    );
    $sql            = "UPDATE cotizaciones SET cliente = $idCliente WHERE id = $idCotizacion LIMIT 1";
    $mysqli->query($sql);
    $sql            = "UPDATE detallecotizacion SET activo = 0 WHERE cotizacion = $idCotizacion";
    $mysqli->query($sql);
    $sum_subTotal   = 0;
    $sql_det        = "";
    foreach ($listaProductos as $producto)
    {
        $idSubCotizacion    = $producto->idSubCotizacion;
        $cantidad           = $producto->cantidad;
        $codigo             = $producto->codigo;
        $idProducto         = $producto->idProducto;
        $precioU            = $producto->precioU;
        $subTotal           = $producto->subTotal;
        $descripcion        = $producto->descripcion;

            $sql_det        .= "INSERT INTO detallecotizacion (cotizacion, producto, cantidad, precio, subTotal, descripcion)
                                VALUES ($idCotizacion, $idProducto, $cantidad, $precioU, $subTotal, '$descripcion');";
        $sum_subTotal       += $subTotal;
    }
    $sql_det                .= "UPDATE cotizaciones SET totalventa = $sum_subTotal WHERE cotizaciones.id = $idCotizacion LIMIT 1;";
    if($mysqli->multi_query($sql_det))
    {
        do {
            # code...
        } while ($mysqli->next_result());
        $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> La cotizaci&oacute;n ha sido modificada con éxito!";
        $response["respuesta"] .=   "   </br><a href='../control/genRemisionCotizacionPDF.php?idCotizacion=$idCotizacion' class='alert-link' target='_blank'> <i class='fa fa-external-link' aria-hidden='true'></i> Reimprimir cotizaci&oacute;n No. $idCotizacion</a>";
        $response["respuesta"] .=   "   </br><a href='reporteCotizaciones.php' class='alert-link'><i class='fa fa-reply' aria-hidden='true'></i> Regresar a lista de cotizaciones</a>";
        $response["respuesta"] .=   "</div>";
        //$reciboHTML = genReciboCotizacion($venta, $mysqli);
        //$response['recibo']     =   $reciboHTML;
        //$response['idCotizacion'] = $venta;
    }
    else
    {
        $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la operaci&oacute;n.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
    }
    responder($response,$mysqli);
}
     ?>
