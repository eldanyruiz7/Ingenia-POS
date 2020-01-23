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
    //require "compraRecibo.php";
    function responder($response, $mysqli)
    {
        $response['error']  = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $usuario                = $sesion->get("id");
    $listaProductos         = json_decode($_POST['listaProductos']);
    $descuento              = "";
    $montoTotal             = $_POST['montoTotal'];
    $idProveedor            = $_POST['idProveedor'];
    $esFactura              = $_POST['fact'];
    $noDocto                = $_POST['noDocto'];
    $tipoPago               = $_POST['tipoPago'];
    $fechaExpira            = $_POST['fechaExpira'];
    $abono                  = $_POST['abono'];
    $esCredito              = 1;
    $response               = array(
                            "status"        => 1
                            );
    $sql                    = "SELECT nombres, apellidop, apellidom FROM proveedores WHERE id = $idProveedor LIMIT 1";
    if ($result             = $mysqli->query($sql))
    {
        $row_cnt            = $result->num_rows;
        if($row_cnt         == 0)
        {
            $response['status'] = 0;
            responder($response,$mysqli);
        }
    }
    $hoy                = date("Y-m-d");
    if ($tipoPago == 0)
    {
        if (is_numeric($abono) == FALSE || $abono >= $montoTotal)
        {
            $response['status'] = 0;
            $response['abono'] = 0;
            responder($response,$mysqli);
        }
        if ($fechaExpira  <= $hoy )
        {
            $response['status'] = 0;
            $response['fecha'] = 0;
            responder($response,$mysqli);
        }
    }
    else
    {
        $fechaExpira        = $hoy;
        $esCredito          = 0;
    }
    $montoTotal = round($montoTotal,2);
    $sql = "INSERT INTO compras (usuario, fechaexpira, metododepago, activo, sesion, proveedor, monto, contado, motivo, corte, esfactura, nodocumento, pagado, esCredito)
            VALUES ($usuario, '$fechaExpira', 1, 1, $idSesion, $idProveedor, $montoTotal, $tipoPago, 1, 0, $esFactura, '$noDocto', $tipoPago, $esCredito)";
    if($mysqli->query($sql) == TRUE)
    {
        $compra             =   $mysqli->insert_id;
        $sql                =   "";
        //$totCompra = 0;
        $totProd = 0;
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            $cantidad       =   $producto ->cantidad;
            $precio         =   $producto ->precioLista;
            $nombre         =   $producto ->nombre;
            $subTotal       =   $producto ->subTot;

            $unidadVenta    =   $producto ->unidadVenta;
            $claveSAT       =   $producto ->claveSAT;
            $iva            =   number_format($producto ->iva,2,".","");
            $ieps           =   number_format($producto ->ieps,2,".","");
            $totProd ++;
            $sql_           =   "SELECT factorconversion FROM productos WHERE productos.id = $id LIMIT 1";
            $result_factor  =   $mysqli->query($sql_);
            $row_factor     =   $result_factor->fetch_assoc();
            $factor         =   $row_factor['factorconversion'];
            $sql            .=  "INSERT INTO detallecompra (compra, producto, cantidad, preciolista, subTotal)
                                 VALUES ($compra, $id, $cantidad, $precio, $subTotal);";
            $sql            .= "UPDATE precios SET preciolista = $precio WHERE producto = $id LIMIT 1;";
            if ($esFactura == 1)
            {
                $sql        .= "UPDATE productos SET
                                    existencia_factura      = existencia_factura + $cantidad,
                                    existencia              = existencia + $cantidad,
                                    unidadventa             = $unidadVenta,
                                    claveSHCP               = '$claveSAT',
                                    IVA                     = $iva,
                                    IEPS                    = $ieps
                                WHERE id = $id LIMIT 1;";
            }
            else
            {
                $sql        .= "UPDATE productos SET
                                    existencia_remision     = existencia_remision + $cantidad,
                                    existencia              = existencia + $cantidad,
                                    unidadventa             = $unidadVenta,
                                    claveSHCP               = '$claveSAT',
                                    IVA                     = $iva,
                                    IEPS                    = $ieps
                                WHERE id = $id LIMIT 1;";
            }

            $sql            .= "UPDATE detalleprecios
                                SET
                                    utilidadXpaquete 	= 	((precioXpaquete - $precio) * 100) / $precio,
                                    utilidadXunidad 	= 	((precioXunidad - ( $precio / $factor ) ) * 100 / ( ($precio / $factor) ) )
                                WHERE
                                    producto = $id AND tipoprecio = 1 LIMIT 1;";
            $sql            .= "UPDATE detalleprecios
                                SET
                                    utilidadXpaquete 	= 	((precioXpaquete - $precio) * 100) / $precio,
                                    utilidadXunidad 	= 	((precioXunidad - ( $precio / $factor ) ) * 100 / ( ($precio / $factor) ) )
                                WHERE
                                    producto = $id AND tipoprecio = 2 LIMIT 1;";
            $sql            .= "UPDATE detalleprecios
                                SET
                                    utilidadXpaquete 	= 	((precioXpaquete - $precio) * 100) / $precio,
                                    utilidadXunidad 	= 	((precioXunidad - ( $precio / $factor ) ) * 100 / ( ($precio / $factor) ) )
                                WHERE
                                    producto = $id AND tipoprecio = 3 LIMIT 1;";
            $sql            .= "UPDATE detalleprecios
                                SET
                                    utilidadXpaquete 	= 	((precioXpaquete - $precio) * 100) / $precio,
                                    utilidadXunidad 	= 	((precioXunidad - ( $precio / $factor ) ) * 100 / ( ($precio / $factor) ) )
                                WHERE
                                    producto = $id AND tipoprecio = 4 LIMIT 1;";
        }
        if($mysqli->multi_query($sql))
        {
            do {
                # code...
            } while ($mysqli->next_result()&&$mysqli->more_results());
            if ($tipoPago == 0 && $abono > 0)
            {
                $sql = "INSERT INTO pagosemitidos (idcompra, monto, usuario, proveedor, corte, sesion)
                        VALUES ($compra, $abono, $usuario, $idProveedor, 0, $idSesion)";
                if($mysqli->query($sql) != TRUE)
                {
                    $response['status']= 0;
                    $response['pagos'] = 0;
                    responder($response,$mysqli);
                }
            }
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Gracias por su preferencia!";
            $response["respuesta"] .=   "</div>";
            //$reciboHTML = genReciboCompra($compra, $mysqli);
            //$response['recibo']     =   $reciboHTML;
            $response['length']     =   $totProd;
            $response['idCompra']   =   $compra;
            $response['codigo']     =  str_pad($compra, 12, "0", STR_PAD_LEFT);
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
    else
    {
        $response['status']= 0;
        responder($response,$mysqli);
    }
}
     ?>
