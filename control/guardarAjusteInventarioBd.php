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
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    require "compraRecibo.php";
    $usuario                =   $sesion->get("id");
    if(isset($_POST['arrayJSON']))
        $listaProductos     =   json_decode($_POST['arrayJSON']);
    if($_POST['imprimirCompra'] == 1)
        $imprimirCompra     =   1;
    else
        $imprimirCompra     =   0;
    if($_POST['imprimirNotaS']  == 1)
        $imprimirNotaS      =   1;
    else
        $imprimirNotaS      =   0;
    $sqlDetalleCompra       =   '';
    $sqlDetalleNotaSal      =   '';
    $response               =   array(
        "status"            =>  1,
        "respuesta"         =>  '');
    $arrayDiferencias       =   array();
    $arrayPreciosLista      =   array();
    $arrayPreciosPublico    =   array();
    $fechaExpira            =   date('Y/m/d', time());
    $cont                   =   0;
    $hayNotaS               =   0;
    $hayCompra              =   0;
    $montoCompra            =   0;
    $montoNotaS             =   0;
    $ventaFactura           =   0;
    $idCompra               =   "NULL";
    $idNotaSal              =   "NULL";

    $observacionesNotaS     =   'Mensaje del sistema: <<Nota de salida por ajuste de inventario>>';
    //Determinar si hay valores negativos y positivos
    if(isset($_POST['arrayJSON']))
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            $sql_v          =  "SELECT existencia, preciolista, precioXunidad
                                FROM productos
                                INNER JOIN precios
                                ON productos.id = precios.producto
                                INNER JOIN detalleprecios
                                ON productos.id = detalleprecios.producto
                                WHERE productos.id = $id AND tipoprecio = 1 LIMIT 1";
            $res_ex         =   $mysqli->query($sql_v);
            $row_ex         =   $res_ex->fetch_assoc();
            $existenciasis  =   $row_ex['existencia'];
            $precioU        =   $arrayPreciosLista[$cont]   = $row_ex['preciolista'];
            $precioPublico  =   $arrayPreciosPublico[$cont] = $row_ex['precioXunidad'];
            $existenciafis  =   $producto                   ->cantidaddespues;
            $diferencia     =   $existenciafis              - $existenciasis;
            $arrayDiferencias[$cont]                        = $diferencia;
            if($diferencia  >   0)
            {
                $montoCompra+=  $precioU * $diferencia;
                $hayCompra  =   1;
            }
            if($diferencia  <   0)
            {
                $montoNotaS+=   $precioPublico * abs($diferencia);
                $hayNotaS   =   1;
            }
            $cont++;
        }
    if ($hayCompra          ==   1)
    {
        $sql_v              =  "INSERT INTO compras (usuario, fechaexpira, metododepago, activo, sesion, proveedor, monto, motivo, nodocumento)
                                VALUES ($usuario, '$fechaExpira', 1, 1, $idSesion, 1, $montoCompra, 2, 0)";
        if($res_v           =   $mysqli->query($sql_v))
            $idCompra       =   $mysqli->insert_id;
        else
        {
            $response["respuesta"] .=   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la Compra.";
            $response["respuesta"] .=   "</div>";
            $response["status"]     = 0;
            responder($response,$mysqli);
        }
    }
    else
        $imprimirCompra     = 0;
    if ($hayNotaS           ==   1)
    {
        $sql_n              = "INSERT INTO notadesalida (usuario, sesion, montolista, montopublico, observaciones)
                               VALUES ($usuario, $idSesion, 0, $montoNotaS, '$observacionesNotaS')";
        if($res_n           =   $mysqli->query($sql_n))
            $idNotaSal      =  $mysqli->insert_id;
        else
        {
            $response["respuesta"] .=   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la Nota de Salida.";
            $response["respuesta"] .=   "</div>";
            $response["status"]     = 0;
            responder($response,$mysqli);
        }
    }
    else
        $imprimirNotaS  = 0;
    $cont               =   0;
    if(isset($_POST['arrayJSON']))
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            if ($arrayDiferencias[$cont] > 0)
            {
                $cantidad   =   $arrayDiferencias[$cont];
                $precioU    =   $arrayPreciosLista[$cont];
                $subTotal   =   $cantidad * $precioU;
                $sqlDetalleCompra   .= "INSERT INTO detallecompra (compra, producto, cantidad, preciolista, subTotal)
                                        VALUES ($idCompra, $id, $cantidad, $precioU, $subTotal);";
                $sqlDetalleCompra   .= "UPDATE productos
                                        SET existencia = existencia + $cantidad,
                                            existencia_remision = existencia_remision + $cantidad
                                        WHERE id = $id LIMIT 1;";
            }
            if ($arrayDiferencias[$cont] < 0)
            {
                $cantidad   =   abs($arrayDiferencias[$cont]);
                $precioP    =   $arrayPreciosPublico[$cont];
                $subTotal   =   $cantidad * $precioP;
                $sqlDetalleNotaSal .= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico)
                                        VALUES ($idNotaSal, $id, $cantidad, 0, $precioP, 0, $subTotal);";
                $sql_exist  =   "SELECT
                                 existencia_remision AS TR,
                                 existencia_factura  AS TF,
                                 existencia          AS TT
                                 FROM productos WHERE id = $id LIMIT 1";
                $res_exist  =   $mysqli->query($sql_exist);
                $row_exist  =   $res_exist->fetch_assoc();
                $TR         =   $row_exist['TR'];
                $TF         =   $row_exist['TF'];
                $existencia_r=   $TR + $TF;
                $TT         =   $row_exist['TT'];
                $sql_fact       =   "SELECT
                                        IFNULL(SUM(cantidad), 0) AS totfact
                                    FROM detalleventa
                                    WHERE producto = $id
                                    AND facturable = 1
                                    AND facturado = 0";
                $res_fact       =   $mysqli->query($sql_fact);
                $row_fact       =   $res_fact->fetch_assoc();
                $tot_fact       =   $row_fact['totfact'];
                $TF_            =   $TF - $tot_fact;
                if ($TF_ >= $cantidad)
                {

                    $sqlDetalleNotaSal.= "UPDATE productos
                                          SET
                                          existencia_factura = existencia_factura - $cantidad,
                                          existencia = existencia - $cantidad
                                          WHERE id = $id LIMIT 1;";
                    $sql = "INSERT INTO ventas (cliente, usuario, metododepago, sesion, totalventa, tipoprecio, pagacon, credito, adeudo, remision, corte)
                            VALUES (1, $usuario, 1, $idSesion, $subTotal, 1, $subTotal, 0, 0, 1, 1)";
                    if($mysqli->query($sql) == TRUE)
                    {
                        $venta         =   $mysqli->insert_id;
                        $sql           = "INSERT INTO detalleventa (venta, producto, cantidad, precio, subTotal, facturable)
                                            VALUES ($venta, $id, $cantidad, $precioP, $subTotal, 1);";
                        $mysqli->query($sql);
                    }

                }
                else
                {
                    $sqlDetalleNotaSal.= "UPDATE productos
                                          SET
                                          existencia_remision = existencia_remision - $cantidad,
                                          existencia = existencia - $cantidad
                                          WHERE id = $id LIMIT 1;";
                }
            }
            $cont++;
        }
    if ($hayCompra     ==   1)
    {
        if($mysqli->multi_query($sqlDetalleCompra))
        {
            do {
                # code...
            } while ($mysqli->next_result());
        }
        else
        {
            $response["respuesta"] .=   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; el detalle de compra.";
            $response["respuesta"] .=   "</div>";
            $response["status"]     = 0;
            responder($response,$mysqli);
        }
    }

    if ($hayNotaS      ==   1)
    {
        if($mysqli->multi_query($sqlDetalleNotaSal))
        {
            do {
                # code...
            } while ($mysqli->next_result());
        }
        else
        {
            $response["respuesta"] .=   "<div class='alert alert-danger alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; el detalle de nota de salida.";
            $response["respuesta"] .=   "</div>";
            $response["status"]     = 0;
            responder($response,$mysqli);
        }
    }
    if ($hayNotaS == 0 && $hayCompra == 0)
    {
        $response["respuesta"] .=   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> Las existencias f&iacute;sicas y de sistema no pueden ser iguales y la lista no puede estar vac&iacute;a. </br>No se guard&oacute nada.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
        responder($response,$mysqli);
    }
    else
    {
        $sql_ajuste_inv         =   "INSERT INTO ajustesinventario (usuario, activo, sesion, idCompra, idNotaSal)
                                     VALUES ($idUsuario, 1, $idSesion, $idCompra, $idNotaSal)";
        if($result              =   $mysqli->query($sql_ajuste_inv))
        {
            $response['idAjuste']=   $mysqli->insert_id;
            $response['ajuste'] =   1;
        }

    }
    if ($hayCompra == 1 && $imprimirCompra == 1)
    {
        $reciboHTML = genReciboCompra($idCompra, $mysqli);
        $response['recibo']     =   $reciboHTML;
        //$response['length']     =   $totProd;
        $response['codigo']     =  str_pad($idCompra, 12, "0", STR_PAD_LEFT);
        $response['hayCompra']  =   1; //Validar por ajax si se generó compra
    }
    if ($hayNotaS == 1 && $imprimirNotaS == 1)
    {
        $response["idNota"]     =   $idNotaSal;
        $response['hayNotaS']   =   1; //Validar por ajax si se generó nota de salida
    }
    responder($response,$mysqli);
}
     ?>
