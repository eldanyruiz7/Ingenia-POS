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
        $response['error']  = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $usuario                = $sesion->get("id");
    $listaProductos         = json_decode($_POST['listaProductos']);
    $descuento              = "";
    $idCliente              = $_POST['idCliente'];
    $sql                    ="  SELECT tipoprecio FROM clientes WHERE id = $idCliente LIMIT 1";
    $resultadoCliente       = $mysqli->query($sql);
    $tipoCliente            = $resultadoCliente->fetch_assoc();
    $tipoprecio             = $tipoCliente['tipoprecio'];
    $totalVenta             = $_POST['montoTotal'];
    $pagaCon                = $_POST['pagaCon'];
    $cambio                 = $_POST['inputCambio'];
    $remision               = ($_POST['remision'] == 1) ? 1 : 0;
    $imprimir               = ($_POST["chkImprimir"] == 1) ? 1 : 0;
    $ocultarPU              = ($_POST["chkOcultarPU"] == 1) ? 1 : 0;
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
    //$totalVenta         = number_format($_POST['t'], 2, '.', '');
    $response               = array(
        "status"            => 1
    );
    $sql = "INSERT INTO ventas (cliente, usuario, metododepago, tipoprecio, sesion, totalventa, pagacon, pagado, esCredito, remision, ocultarPU)
            VALUES ($idCliente, $usuario, 1, $tipoprecio, $idSesion, $totalVenta, $pagaCon, $pagado, $esCredito, $remision, $ocultarPU)";
    if($mysqli->query($sql) == TRUE)
    {
        $venta              =   $mysqli->insert_id;
        $sql                =   "";
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            $cantidad       =   $producto ->cantidad;
            $precioU        =   $producto ->precioU;
            $nombre         =   $producto ->nombre;
            $subTot         =   $producto ->subTot;
            $nombreProducto =   $producto ->nombre;
            $sql_exist      =   "SELECT
                                    existencia_remision AS TR,
                                    existencia_factura  AS TF,
                                    existencia          AS TT
                                FROM productos WHERE id = $id LIMIT 1";
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
                $subTot     = $precioU * $cantidad2;
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
            } while ($mysqli->next_result()&&$mysqli->more_results());
            if ($chkAbonos  == 1 && $abono > 0)
            {
                $sql        = "INSERT INTO pagosrecibidos (idventa, monto, usuario, cliente, corte, sesion)
                                VALUES ($venta, $abono, $usuario, $idCliente, 0, $idSesion)";
                $mysqli->query($sql);
            }
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Gracias por su preferencia!";
            $response["respuesta"] .=   "</div>";
            $response["idVenta"]    =   $venta;
            $response["remision"]   =   $remision;
            $response['imprimir']   =   $imprimir;
            $reciboHTML             = genReciboVenta($venta, $mysqli);
            $response['recibo']     =   $reciboHTML;
            $response['codigo']     =  str_pad($venta, 12, "0", STR_PAD_LEFT);
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
    else {
        $response["status"] = 0;
    }
    responder($response,$mysqli);
}
     ?>
