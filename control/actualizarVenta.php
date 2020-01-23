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
    $usuario                = $sesion->get("id");
    $listaProductos         = json_decode($_POST['listaProductos']);
    $idVenta                = $_POST['idVenta'];
    $idCliente              = $_POST['idCliente'];
    $remision               = ($_POST['remision'] == 1) ? 1 : 0;
    $ocultarPU              = ($_POST['ocultarPU'] == 1) ? 1 : 0;
    $esCredito              = ($_POST['esCredito'] == 1) ? 1 : 0;
    $response               = array(
                "status"    => 1
                    );
    // $sql                    = "SELECT esfactura FROM compras WHERE id = $idCompra LIMIT 1";
    // $resultFactura          = $mysqli->query($sql);
    // $rowFactura             = $resultFactura->fetch_assoc();
    // $esFactura_orig         = ($rowFactura['esfactura'] == 1) ? 1 : 0;
    $sql                    = "";
    $sql_cant               = "SELECT producto, cantidad, subTotal, facturable, facturado FROM detalleventa WHERE venta = $idVenta AND activo = 1";
    $res_cant               = $mysqli->query($sql_cant);
    while ($row_cant        = $res_cant->fetch_assoc())
    {
        $idProd_            = $row_cant['producto'];
        $cant_              = $row_cant['cantidad'];
        $subTot_            = $row_cant['subTotal'];
        $facturable_        = $row_cant['facturable'];
        $facturado_         = $row_cant['facturado'];

        $sql                .= "UPDATE productos SET existencia = existencia + $cant_ WHERE id = $idProd_ LIMIT 1;";
        if ($facturable_    == 1)
            $sql            .= "UPDATE productos SET existencia_factura = existencia_factura + $cant_ WHERE id = $idProd_ LIMIT 1;";
        else
            $sql            .= "UPDATE productos SET existencia_remision = existencia_remision + $cant_ WHERE id = $idProd_ LIMIT 1;";

    }
    $sum_subTotal           = 0;
    foreach ($listaProductos as $producto)
    {
        $subTotal           = $producto->subTotal;
        $sum_subTotal       += $subTotal;
    }
    $sql                    .= "UPDATE ventas
                                SET ocultarPU   = $ocultarPU,
                                    esCredito   = $esCredito,
                                    cliente     = $idCliente,
                                    remision    = $remision,
                                    totalventa  = $sum_subTotal
                                WHERE id        = $idVenta LIMIT 1;";
    $sql                    .= "UPDATE detalleventa SET activo = 0 WHERE venta = $idVenta;";
    if($mysqli->multi_query($sql))
    {
        do {
            # code...
        } while ($mysqli->next_result() && $mysqli->more_results());
    }
    else
    {
        $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> Algo est&aacute; err&oacute;neo en la Base de datos.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
        responder($response,$mysqli);
    }
    $sql_det        = "";
    //var_dump($listaProductos);
    foreach ($listaProductos as $producto)
    {
        $idSubVenta         = $producto->idSubVenta;
        $codigo             = $producto->codigo;
        $idProducto         = $producto->idProducto;
        $subTotal           = $producto->subTotal;
        $precioU            = $producto->precioU;
        $cantidad           = $producto->cantidad;
        $descripcion        = $producto->descripcion;
        // $iva                = $producto->iva;
        // $ieps               = $producto->ieps;
        // $unidadSat          = $producto->unidadSat;
        // $claveUSat          = $producto->claveSat;

        //$sql_f              = "SELECT factorconversion AS fConv FROM productos WHERE id = $idProducto LIMIT 1";
        //echo $sql_f;
        // if (!$result_factor = $mysqli->query($sql_f)) {
        //     echo "Query: " . $sql_f . "\n";
        //     echo "Errno: " . $mysqli->errno . "\n";
        //     echo "Error: " . $mysqli->error . "\n";
        //     exit;
        // }
        //$result_factor      = $mysqli->query($sql_f);
        // $row_factor         = $result_factor->fetch_assoc();
        // $factor             = $row_factor['fConv'];

        $sql_exist      =   "SELECT
                                existencia_remision AS TR,
                                existencia_factura  AS TF,
                                existencia          AS TT
                            FROM productos WHERE id = $idProducto LIMIT 1";
        $res_exist      =   $mysqli->query($sql_exist);
        $row_exist      =   $res_exist->fetch_assoc();
        $TR             =   $row_exist['TR'];
        $TF             =   $row_exist['TF'];
        //$existencia_r   =   $TR + $TF;
        $TT             =   $row_exist['TT'];
        if ($TF         < $cantidad && $TF > 0)
        {
            $cantidad1  = $TF;
            $cantidad2  = $cantidad - $cantidad1;
            $sql_det       .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, nombrecorto)
                           VALUES ($idVenta, $idProducto, $cantidad2, $precioU, $subTotal, '$descripcion');";
            $sql_det       .= "UPDATE productos
                           SET existencia_remision = existencia_remision - $cantidad2,
                                existencia = existencia - $cantidad2 WHERE id = $idProducto LIMIT 1;";
            $subTotal     = $precioU * $cantidad1;
            $sql_det       .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, facturable, nombrecorto)
                           VALUES ($idVenta, $idProducto, $cantidad1, $precioU, $subTotal, 1, '$descripcion');";
            $sql_det       .= "UPDATE productos
                           SET existencia_factura = existencia_factura - $cantidad1,
                                existencia = existencia - $cantidad1 WHERE id = $idProducto LIMIT 1;";
        }
        elseif ($TF     <= 0)
        {
            $sql_det        .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, nombrecorto)
                            VALUES ($idVenta, $idProducto, $cantidad, $precioU, $subTotal, '$descripcion');";
            $sql_det        .= "UPDATE productos
                            SET existencia_remision = existencia_remision - $cantidad,
                                existencia = existencia - $cantidad WHERE id = $idProducto LIMIT 1;";
        }
        elseif ($TF     >= $cantidad)
        {
            $sql_det        .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, facturable, nombrecorto)
                            VALUES ($idVenta, $idProducto, $cantidad, $precioU, $subTotal, 1, '$descripcion');";
            $sql_det        .= "UPDATE productos
                            SET existencia_factura = existencia_factura - $cantidad,
                                existencia = existencia - $cantidad WHERE id = $idProducto LIMIT 1;";
        }

    }
    if($mysqli->multi_query($sql_det))
    {
        do {
            # code...
        } while ($mysqli->next_result() && $mysqli->more_results());
        $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> La venta ha sido modificada con éxito!";
        $response["respuesta"] .=   "   </br><a href='reporteVentas.php' class='alert-link'><i class='fa fa-reply' aria-hidden='true'></i> Regresar reporte de ventas</a>";
        $response["respuesta"] .=   "</div>";
        //$reciboHTML = genReciboCotizacion($idVenta, $mysqli);
        //$response['recibo']     =   $reciboHTML;
        //$response['idCotizacion'] = $idVenta;
    }
    else
    {

        $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la operaci&oacute;n. $mysqli->errno - $mysqli->error";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
    }
    responder($response,$mysqli);
}
     ?>
