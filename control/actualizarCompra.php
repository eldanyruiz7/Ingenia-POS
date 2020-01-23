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
    $idCompra               = $_POST['idCompra'];
    $esFactura              = ($_POST['esFactura'] == 1) ? 1 : 0;
    $esCredito              = ($_POST['esCredito'] == 1) ? 1 : 0;
    $idProveedor            = $_POST['idProveedor'];
    $response               = array(
                "status"    => 1
                    );
    $sql                    = "SELECT esfactura FROM compras WHERE id = $idCompra LIMIT 1";
    $resultFactura          = $mysqli->query($sql);
    $rowFactura             = $resultFactura->fetch_assoc();
    $esFactura_orig         = ($rowFactura['esfactura'] == 1) ? 1 : 0;
    $sql                    = "";
    $sql_cant               = "SELECT producto, cantidad, subTotal FROM detallecompra WHERE compra = $idCompra AND activo = 1";
    $res_cant               = $mysqli->query($sql_cant);
    while ($row_cant        = $res_cant->fetch_assoc())
    {
        $cant_              = $row_cant['cantidad'];
        $idProd_            = $row_cant['producto'];
        $subTot_            = $row_cant['subTotal'];

        $sql                .= "UPDATE productos SET existencia = existencia - $cant_ WHERE id = $idProd_ LIMIT 1;";
        if ($esFactura_orig == 1)
            $sql            .= "UPDATE productos SET existencia_factura = existencia_factura - $cant_ WHERE id = $idProd_ LIMIT 1;";
        else
            $sql            .= "UPDATE productos SET existencia_remision = existencia_remision - $cant_ WHERE id = $idProd_ LIMIT 1;";

    }
    $sum_subTotal           = 0;
    foreach ($listaProductos as $producto)
    {
        $subTotal           = $producto->subTotal;
        $sum_subTotal       += $subTotal;
    }
    $sql                    .= "UPDATE compras
                                SET esfactura   = $esFactura,
                                    esCredito   = $esCredito,
                                    proveedor   = $idProveedor,
                                    monto       = $sum_subTotal
                                WHERE id        = $idCompra LIMIT 1;";
    $sql                    .= "UPDATE detallecompra SET activo = 0 WHERE compra = $idCompra;";
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
        $idSubCompra        = $producto->idSubCompra;
        $codigo             = $producto->codigo;
        $idProducto         = $producto->idProducto;
        $subTotal           = $producto->subTotal;
        $precioU            = $producto->precioU;
        $cantidad           = $producto->cantidad;
        //$descripcion        = $producto->descripcion;
        $iva                = $producto->iva;
        $ieps               = $producto->ieps;
        $unidadSat          = $producto->unidadSat;
        $claveUSat          = $producto->claveSat;

        $sql_f              = "SELECT factorconversion AS fConv FROM productos WHERE id = $idProducto LIMIT 1";
        //echo $sql_f;
        if (!$result_factor = $mysqli->query($sql_f)) {
            echo "Query: " . $sql_f . "\n";
            echo "Errno: " . $mysqli->errno . "\n";
            echo "Error: " . $mysqli->error . "\n";
            exit;
        }
        //$result_factor      = $mysqli->query($sql_f);
        $row_factor         = $result_factor->fetch_assoc();
        $factor             = $row_factor['fConv'];

        $sql_det            .= "INSERT INTO detallecompra (compra, producto, cantidad, preciolista, subTotal, activo)
                                VALUES ($idCompra, $idProducto, $cantidad, $precioU, $subTotal, 1);";
        $sql_det            .= "UPDATE precios SET preciolista = $precioU WHERE producto = $idProducto LIMIT 1;";
        if ($esFactura      == 1)
        {
            $sql_det        .= "UPDATE productos SET
                                    existencia_factura      = existencia_factura + $cantidad,
                                    existencia              = existencia + $cantidad,
                                    unidadventa             = $unidadSat,
                                    claveSHCP               = '$claveUSat',
                                    IVA                     = $iva,
                                    IEPS                    = $ieps
                                WHERE id = $idProducto LIMIT 1;";
        }
        else
        {
            $sql_det        .= "UPDATE productos SET
                                    existencia_remision     = existencia_remision + $cantidad,
                                    existencia              = existencia + $cantidad,
                                    unidadventa             = $unidadSat,
                                    claveSHCP               = '$claveUSat',
                                    IVA                     = $iva,
                                    IEPS                    = $ieps
                                WHERE id = $idProducto LIMIT 1;";
        }
        $sql_det            .= "UPDATE detalleprecios
                            SET
                                utilidadXpaquete 	= 	((precioXpaquete - $precioU) * 100) / $precioU,
                                utilidadXunidad 	= 	((precioXunidad - ( $precioU / $factor ) ) * 100 / ( ($precioU / $factor) ) )
                            WHERE
                                producto = $idProducto AND tipoprecio = 1 LIMIT 1;";
        $sql_det            .= "UPDATE detalleprecios
                            SET
                                utilidadXpaquete 	= 	((precioXpaquete - $precioU) * 100) / $precioU,
                                utilidadXunidad 	= 	((precioXunidad - ( $precioU / $factor ) ) * 100 / ( ($precioU / $factor) ) )
                            WHERE
                                producto = $idProducto AND tipoprecio = 2 LIMIT 1;";
        $sql_det            .= "UPDATE detalleprecios
                            SET
                                utilidadXpaquete 	= 	((precioXpaquete - $precioU) * 100) / $precioU,
                                utilidadXunidad 	= 	((precioXunidad - ( $precioU / $factor ) ) * 100 / ( ($precioU / $factor) ) )
                            WHERE
                                producto = $idProducto AND tipoprecio = 3 LIMIT 1;";
        $sql_det            .= "UPDATE detalleprecios
                            SET
                                utilidadXpaquete 	= 	((precioXpaquete - $precioU) * 100) / $precioU,
                                utilidadXunidad 	= 	((precioXunidad - ( $precioU / $factor ) ) * 100 / ( ($precioU / $factor) ) )
                            WHERE
                                producto = $idProducto AND tipoprecio = 4 LIMIT 1;";

    }
    if($mysqli->multi_query($sql_det))
    {
        do {
            # code...
        } while ($mysqli->next_result() && $mysqli->more_results());
        $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> La compra ha sido modificada con éxito!";
        $response["respuesta"] .=   "   </br><a href='../control/genRemisionCompraPDF.php?idCompra=$idCompra' class='alert-link' target='_blank'> <i class='fa fa-external-link' aria-hidden='true'></i> Reimprimir compra No. $idCompra</a>";
        $response["respuesta"] .=   "   </br><a href='reporteCompras.php' class='alert-link'><i class='fa fa-reply' aria-hidden='true'></i> Regresar reporte de compras</a>";
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
