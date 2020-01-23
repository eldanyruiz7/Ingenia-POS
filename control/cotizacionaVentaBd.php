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
    require ("../control/ventaRecibo.php");
    function responder($response, $mysqli)
    {
        //$response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $idCotizacion = $_POST['idCotizacion'];
    $response = array(
        "status"        => 1
    );
    $sql                = "SELECT * FROM cotizaciones WHERE id = $idCotizacion AND idVenta IS NULL AND cancelada = 0 LIMIT 1";
    $resultCotizacion   = $mysqli->query($sql);
    $row_cnt            = $resultCotizacion->num_rows;
    if($row_cnt == 0)
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Ya existe una venta para esta cotizaci&oacute;n o la cotizaci&oacute;n est&aacute; cancelada.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
        responder($response,$mysqli);

    }
    $rowCotizacion          = $resultCotizacion->fetch_assoc();
    $remision               = ($_POST['remision'] == 1) ? 1 : 0;
    $imprimir               = ($_POST["chkImprimir"] == 1) ? 1 : 0;
    $ocultarPU              = ($_POST["chkOcultarPU"] == 1) ? 1 : 0;
    $pagaCon                = $_POST['pagaCon'];
    $cambio                 = $_POST['inputCambio'];
    //$ventaCtrl              = $rowCotizacion['idVenta'];
    $idCliente              = $_POST['idCliente'];
    $sql                    ="  SELECT tipoprecio FROM clientes WHERE id = $idCliente LIMIT 1";
    $resultadoCliente       = $mysqli->query($sql);
    $tipoCliente            = $resultadoCliente->fetch_assoc();
    $tipoPrecio             = $tipoCliente['tipoprecio'];
    $totalVenta             = $rowCotizacion['totalventa'];
    $esCredito              = $chkAbonos = ($_POST["chkAbonos"] == 1) ? 1 : 0;
    if ($chkAbonos          == 1)
    {
        $abono              = $_POST['inputAbono'];
        $pagado             = 0;
    }
    else
    {
        $abono              = 0;
        $pagado             = 1;
    }
    //$totalVenta             = $_POST['montoTotal'];
    //$idCliente          = $rowCotizacion['cliente'];
    $usuario                = $sesion->get("id");
    $metododepago           = $rowCotizacion['metododepago'];
    //$tipoPrecio           = $rowCotizacion['cliente'];
    $descuento              = 0;
    $sql = "INSERT INTO ventas (cliente, descuento, usuario, metododepago, tipoprecio, sesion, totalventa, pagacon, pagado, remision, ocultarPU)
            VALUES ($idCliente, $descuento, $usuario, $metododepago, $tipoPrecio, $idSesion, $totalVenta, $pagaCon, $pagado, $remision, $ocultarPU)";
    if($mysqli->query($sql) == TRUE)
    {
        $venta              =   $mysqli->insert_id;
        //$totVenta = 0;
        $sql = "SELECT * FROM detallecotizacion WHERE detallecotizacion.cotizacion = $idCotizacion AND detallecotizacion.activo = 1";
        $resultCotizacion_d = $mysqli->query($sql);
        $sql                =   "";
        while ($rowCotizacion_d = $resultCotizacion_d->fetch_assoc())
        {
            $id             =  $rowCotizacion_d['producto'];
            $cantidad       =  $rowCotizacion_d['cantidad'];
            $precioU        =  $rowCotizacion_d['precio'];
            $subTot         =  $rowCotizacion_d['subTotal'];
            $sql_exist      =   "SELECT
                                    existencia_remision AS TR,
                                    existencia_factura  AS TF,
                                    existencia          AS TT,
                                    nombrecorto         AS nombreCorto
                                FROM productos WHERE id = $id LIMIT 1";
            $res_exist      =   $mysqli->query($sql_exist);
            $row_exist      =   $res_exist->fetch_assoc();
            $TR             =   $row_exist['TR'];
            $TF             =   $row_exist['TF'];
            //$existencia_r   =   $TR + $TF;
            $TT             =   $row_exist['TT'];
            $nombreProducto =   $row_exist['nombreCorto'];
            if ($TF         < $cantidad && $TF > 0)
            {
                $cantidad1  = $TF;
                $cantidad2  = $cantidad - $cantidad1;
                $sql       .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, nombrecorto)
                               VALUES ($venta, $id, $cantidad2, $precioU, $subTot, '$nombreProducto');";
                $sql       .= "UPDATE productos
                               SET existencia_remision = existencia_remision - $cantidad2,
                                    existencia = existencia - $cantidad2 WHERE id = $id LIMIT 1;";
                $subTot     = $precioU * $cantidad1;
                $sql       .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, facturable, nombrecorto)
                               VALUES ($venta, $id, $cantidad1, $precioU, $subTot, 1, '$nombreProducto');";
                $sql       .= "UPDATE productos
                               SET existencia_factura = existencia_factura - $cantidad1,
                                    existencia = existencia - $cantidad1 WHERE id = $id LIMIT 1;";
            }
            elseif ($TF     <= 0)
            {
                $sql        .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, nombrecorto)
                                VALUES ($venta, $id, $cantidad, $precioU, $subTot, '$nombreProducto');";
                $sql        .= "UPDATE productos
                                SET existencia_remision = existencia_remision - $cantidad,
                                    existencia = existencia - $cantidad WHERE id = $id LIMIT 1;";
            }
            elseif ($TF     >= $cantidad)
            {
                $sql        .= "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, facturable, nombrecorto)
                                VALUES ($venta, $id, $cantidad, $precioU, $subTot, 1, '$nombreProducto');";
                $sql        .= "UPDATE productos
                                SET existencia_factura = existencia_factura - $cantidad,
                                    existencia = existencia - $cantidad WHERE id = $id LIMIT 1;";
            }
        }
        if($mysqli->multi_query($sql))
        {
            do {
                # code...
            } while ($mysqli->next_result());
            if ($chkAbonos  == 1 && $abono > 0)
            {
                $sql        = "INSERT INTO pagosrecibidos (idventa, monto, usuario, cliente, corte, sesion)
                                VALUES ($venta, $abono, $usuario, $idCliente, 0, $idSesion)";
                $mysqli->query($sql);
            }
                $sql = "UPDATE cotizaciones SET idVenta = $venta WHERE id = $idCotizacion";
                if($mysqli->query($sql)=== TRUE)
                {
                    $response["respuesta"]          =    "<div class='alert alert-success alert-dismissable'>";
                    $response["respuesta"]          .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
                    $response["respuesta"]          .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Gracias por su preferencia!";
                    $response["respuesta"]          .=   "</div>";
                    $response["respuesta"]          .=   "</div>";
                    $response["idVenta"]            =   $venta;
                    $response['remision']           =   $remision;
                    $response['imprimir']           =   $imprimir;
                    if($remision                    == 0)
                    {
                        $reciboHTML                 = genReciboVenta($venta, $mysqli);
                        $response['recibo']         =   $reciboHTML;
                        $response['codigo']         =  str_pad($venta, 12, "0", STR_PAD_LEFT);
                    }
                    else
                    {
                        $response['idCotizacion']   =   $idCotizacion;
                    }
                }

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
