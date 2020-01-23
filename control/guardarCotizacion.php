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
    $usuario = $sesion->get("id");
    $listaProductos = json_decode($_POST['listaProductos']);
    $descuento = "";
    $idCliente = $_POST['idCliente'];
    $sql ="SELECT
                tipoprecio
            FROM
                clientes
            WHERE
                id = $idCliente
            LIMIT 1";
    $resultadoCliente   = $mysqli->query($sql);
    $tipoCliente        = $resultadoCliente->fetch_assoc();
    $tipoprecio         = $tipoCliente['tipoprecio'];
    $totalVenta = number_format($_POST['t'], 2, '.', '');
    $response = array(
        "status"        => 1
    );
    $sql = "INSERT INTO cotizaciones (cliente, descuento, usuario, metododepago, sesion, totalventa, tipoprecio)
            VALUES ($idCliente, 0, $usuario, 1, $idSesion, $totalVenta, $tipoprecio)";
    if($mysqli->query($sql) == TRUE)
    {
        $venta              =   $mysqli->insert_id;
        $sql                =   "";
        $totVenta = 0;
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            $cantidad       =   $producto ->cantidad;
            $precio         =   $producto ->precioU;
            $nombre         =   $producto ->nombre;
            $tmp            =   $producto ->subTot;
            //$totVenta       = $totVenta +  $tmp;
            $sql.= "INSERT INTO detallecotizacion (cotizacion, producto, cantidad, precio, subTotal, descripcion)
                    VALUES ($venta, $id, $cantidad, $precio, $tmp, '$nombre');";
        }
        if($mysqli->multi_query($sql))
        {
            do {
                # code...
            } while ($mysqli->next_result()&&$mysqli->more_results());
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Gracias por su preferencia!";
            $response["respuesta"] .=   "</div>";
            //$reciboHTML = genReciboCotizacion($venta, $mysqli);
            //$response['recibo']     =   $reciboHTML;
            $response['idCotizacion'] = $venta;
        }
        else
        {
            $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la operaci&oacute;n.";
            $response["respuesta"] .=   "</div>";
            $response["status"] = 0;
        }
    }
    responder($response,$mysqli);
}
     ?>
