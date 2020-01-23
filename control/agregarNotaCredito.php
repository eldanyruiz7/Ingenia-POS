<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
require_once ("../conecta/cerrarOtrasSesiones.php");
require_once ("../conecta/usuarioLogeado.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    require ("ventaRecibo.php");
    function responder($response, $mysqli)
    {
        $response['respuesta'].=$mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $listaProductos         = json_decode($_POST['listaProductos']);
    $listaProductosCredito  = json_decode($_POST['listaProductosCredito']);
    $totalCredito           = $_POST['totalCredito'];
    $tipoCredito            = $_POST['tipoCredito'];
    $idCliente              = $_POST['idCliente'];
    $idVenta                = $_POST['idVenta'];
    $granTotal              = $_POST['granTotal'];
    $totalVenta             = $_POST['antiguoTotal'];
    $observaciones          = $_POST['observaciones'];
    $usuario                = $sesion->get('id');
    $sql = "INSERT INTO
                notacredito (
                    venta,
                    usuario,
                    tipo,
                    observaciones,
                    totalventa,
                    credito,
                    nuevototalventa,
                    cliente,
                    sesion)
            VALUES ($idVenta,
                    $usuario,
                    $tipoCredito,
                    '$observaciones',
                    $totalVenta,
                    $totalCredito,
                    $granTotal,
                    $idCliente,
                    $idSesion)";
    if($mysqli->query($sql)!= TRUE)
    {
        $response["status"] = 0;
        $response['respuesta'] = "No se pudo generar la nota de credito";
        responder($response, $mysqli);
    }
    else
    {
        $sql                = "";
        $idNota             =   $mysqli->insert_id;
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            $idProducto     =   $producto ->idProducto;
            $cantidad       =   $producto ->cantidad;
            $precio         =   $producto ->precioU;
            $subTotal       =   $producto ->subTotal;
            //$totVenta       = $totVenta +  $tmp;
            $sql.= "INSERT INTO notacreditodetalle (idsubventa, idproducto, idnotacredito, precio, cantidad, subTotal)
                    VALUES ($id, $idProducto, $idNota, $precio, $cantidad, $subTotal);";
        }
        if($mysqli->multi_query($sql) || sizeof($listaProductos) == 0)
        {
            do {
                # code...
            } while ($mysqli->next_result()&&$mysql->more_results());
            $sql                = "";
            //$idNota             =   $mysqli->insert_id;
            foreach ($listaProductosCredito as $producto_C)
            {
                $id             =   $producto_C ->id;
                $idProducto     =   $producto_C ->idProducto;
                $cantidad       =   $producto_C ->cantidad;
                $precio         =   $producto_C ->precioU;
                $subTotal       =   $producto_C ->subTotal;
                //$totVenta       = $totVenta +  $tmp;
                $sql            .=  "INSERT INTO notacreditocambio (idsubventa, idproducto, idnotacredito, precio, cantidad, subTotal)
                                    VALUES ($id, $idProducto, $idNota, $precio, $cantidad, $subTotal);";
                $sql_Aj_Inv     =   "SELECT * FROM detalleventa WHERE id = $id LIMIT 1";
                $res_Aj_Inv     =   $mysqli->query($sql_Aj_Inv);
                $row_Aj_Inv     =   $res_Aj_Inv->fetch_assoc();
                $facturable     =   $row_Aj_Inv['facturable'];
                $facturado      =   $row_Aj_Inv['facturado'];
                if ($facturable ==  1 && $facturado == 0)
                {
                    $sql        .=  "UPDATE productos
                                     SET
                                        existencia = existencia + $cantidad,
                                        existencia_factura = existencia_factura + $cantidad
                                    WHERE
                                        id = $idProducto LIMIT 1;";
                }
                elseif ($facturable == 0)
                {
                    $sql        .=  "UPDATE productos
                                     SET
                                        existencia = existencia + $cantidad,
                                        existencia_remision = existencia_remision + $cantidad
                                    WHERE
                                        id = $idProducto LIMIT 1;";
                }
                elseif ($facturable == 1 && $facturado == 1)
                {
                    $sql        .=  "UPDATE productos
                                     SET
                                        existencia = existencia + $cantidad,
                                        existencia_remision = existencia_remision + $cantidad
                                    WHERE
                                        id = $idProducto LIMIT 1;";
                }
            }
            if($mysqli->multi_query($sql))
            {
                do {
                    # code...
                } while ($mysqli->next_result()&&$mysqli->more_results());
                $sql                        =   "SELECT remision FROM ventas WHERE id = $idVenta LIMIT 1";
                $resTipoVenta               =   $mysqli->query($sql);
                $rowTipoVenta               =   $resTipoVenta->fetch_assoc();
                $tipoVenta                  =   $rowTipoVenta['remision'];
                $response["respuesta"]      =   "<div class='alert alert-success alert-dismissable'>";
                $response["respuesta"]     .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
                $response["respuesta"]     .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Nota de credito ha sido generada correctamente";
                $response["respuesta"]     .=   "</div>";
                if ($tipoVenta == 1)
                {
                    $response['remision']   =   1;
                    $response['idVenta']    =   $idVenta;
                }
                else
                {
                    $response['remision']   = 0;
                    $reciboHTML = genReciboVenta($idVenta, $mysqli);
                    $response['recibo']     =   $reciboHTML;
                    $response['codigo']     =  str_pad($idVenta, 12, "0", STR_PAD_LEFT);
                }
                $response["status"] = 1;
                responder($response, $mysqli);
            }
            else
            {
                $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
                $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
                $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se pudo generar correctamente la nota (notaCreditoCambio).";
                $response["respuesta"] .=   "</div>";
                $response["status"] = 0;
                responder($response, $mysqli);
            }
        }
        else
        {
            $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se pudo generar correctamente la nota (notaCreditodetalle).";
            $response["respuesta"] .=   "</div>";
            $response["status"] = 0;
            responder($response, $mysqli);
        }
    }
    //print_r($listaProductosCredito);
}
?>
