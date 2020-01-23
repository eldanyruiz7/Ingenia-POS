<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $usuario = $sesion->get("id");
    $listaProductos = json_decode($_POST['listaProductos']);
    $descuento = "";
    $observaciones = $_POST['obs'];
    /*$idCliente = $_POST['idCliente'];
    $sql ="SELECT
                tipoprecio
            FROM
                clientes
            WHERE
                id = $idCliente
            LIMIT 1";
    $resultadoCliente   = $mysqli->query($sql);
    $tipoCliente        = $resultadoCliente->fetch_assoc();
    $tipoprecio         = $tipoCliente['tipoprecio'];*/
    $totalVenta = number_format($_POST['t'], 2, '.', '');
    $response = array(
        "status"        => 1
    );
    $arrayPreciosLista = array();
    $arrayExistencia = array();
    $montolista = 0;
    $cont = 0;
    foreach ($listaProductos as $producto)
    {
        $id                                     =   $producto ->id;
        $sql                                    =   "SELECT existencia, existencia_remision, existencia_factura, preciolista
                                                     FROM productos
                                                     INNER JOIN precios
                                                     ON productos.id = precios.producto
                                                     WHERE productos.id = $id LIMIT 1";
        $res                                    =   $mysqli->query($sql);
        $row                                    =   $res->fetch_assoc();
        $precioLista                            =   $row['preciolista'];
        $arrayExistencia[$cont]['existencia']   =   $row['existencia'];
        $arrayExistencia[$cont]['existencia_r'] =   $row['existencia_remision'];
        $arrayExistencia[$cont]['existencia_f'] =   $row['existencia_factura'];
        $arrayPreciosLista[$cont]               =   $precioLista;
        $cantidad                               =   $producto ->cantidad;
        $montolista                             +=   $cantidad * $precioLista;
        $cont++;
    }
    $sql = "INSERT INTO notadesalida (usuario, sesion, montolista, montopublico, observaciones)
            VALUES ($usuario, $idSesion, $montolista, $totalVenta, '$observaciones')";
    if($mysqli->query($sql) == TRUE)
    {
        $idNotaSal          =   $mysqli->insert_id;
        $cont = 0;
        $sql                =   "";
        foreach ($listaProductos as $producto)
        {
            $id             =   $producto ->id;
            $cantidad       =   $producto ->cantidad;
            $precio         =   $producto ->precioU;
            $nombre         =   $producto ->nombre;
            $subTotPub      =   $producto ->subTot;
            $preciolista    =   $arrayPreciosLista[$cont];
            $subTotLista    =   $cantidad * $preciolista;
            $existencia     =   $arrayExistencia[$cont]['existencia'];
            $existencia_r   =   $arrayExistencia[$cont]['existencia_r'];
            $existencia_f   =   $arrayExistencia[$cont]['existencia_f'];
            //$totVenta       = $totVenta +  $tmp;

            if ($existencia_r > $cantidad || $existencia_f <= 0)
            {
                $sql.= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico, facturable, facturado)
                VALUES ($idNotaSal, $id, $cantidad, $preciolista, $precio, $subTotLista, $subTotPub, 0, 0);";
                $sql.= "UPDATE productos SET existencia = existencia - $cantidad, existencia_remision = existencia_remision - $cantidad WHERE id = $id LIMIT 1;";
            }elseif ($existencia_r < $cantidad && $existencia_r > 0)
            {
                $cantidad_p1 = $existencia_r;
                $sql.= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico, facturable, facturado)
                VALUES ($idNotaSal, $id, $cantidad_p1, $preciolista, $precio, $subTotLista, $subTotPub, 0, 0);";
                $sql.= "UPDATE productos SET existencia = existencia - $cantidad_p1, existencia_remision = existencia_remision - $cantidad_p1 WHERE id = $id LIMIT 1;";
                if ($existencia_f > 0 && $existencia_f >= $cantidad - $cantidad_p1)
                {
                    $cantidad_p2 = $cantidad - $cantidad_p1;
                    $sql.= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico, facturable, facturado)
                    VALUES ($idNotaSal, $id, $cantidad_p2, $preciolista, $precio, $subTotLista, $subTotPub, 1, 0);";
                    $sql.= "UPDATE productos SET existencia = existencia - $cantidad_p2, existencia_factura = existencia_factura - $cantidad_p2 WHERE id = $id LIMIT 1;";
                }elseif ($existencia_f > 0 && $existencia_f <= $cantidad - $cantidad_p1)
                {
                    $cantidad_p2 = $existencia_f;
                    $sql.= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico, facturable, facturado)
                    VALUES ($idNotaSal, $id, $cantidad_p2, $preciolista, $precio, $subTotLista, $subTotPub, 1, 0);";
                    $sql.= "UPDATE productos SET existencia = existencia - $cantidad_p2, existencia_factura = existencia_factura - $cantidad_p2 WHERE id = $id LIMIT 1;";
                    $cantidad_p3 = $cantidad - $cantidad_p2 - $cantidad_p1;
                    $sql.= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico, facturable, facturado)
                    VALUES ($idNotaSal, $id, $cantidad_p3, $preciolista, $precio, $subTotLista, $subTotPub, 0, 0);";
                    $sql.= "UPDATE productos SET existencia = existencia - $cantidad_p3, existencia_remision = existencia_remision - $cantidad_p3 WHERE id = $id LIMIT 1;";
                }
            }elseif ($existencia_f >= $cantidad)
            {
                $sql.= "INSERT INTO detallenotadesalida (idnota, producto, cantidad, preciolista, preciopublico, subtotallista, subtotalpublico, facturable, facturado)
                VALUES ($idNotaSal, $id, $cantidad, $preciolista, $precio, $subTotLista, $subTotPub, 1, 0);";
                $sql.= "UPDATE productos SET existencia = existencia - $cantidad, existencia_factura = existencia_factura - $cantidad WHERE id = $id LIMIT 1;";
            }/*elseif ($existencia_f <= 0)
            {
                $sql.= "UPDATE productos SET existencia = existencia - $cantidad, existencia_remision = existencia_remision - $cantidad WHERE id = $id LIMIT 1;";
            }*/
            $cont++;
        }
        if($mysqli->multi_query($sql))
        {
            do {
                # code...
            } while ($mysqli->more_results() && $mysqli->next_result());
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Gracias por su preferencia!";
            $response["respuesta"] .=   "</div>";
            //$reciboHTML = genReciboVenta($venta, $mysqli);
            //$response['recibo']     =   $reciboHTML;
            //$response['codigo']     =  str_pad($idNotaSal, 12, "0", STR_PAD_LEFT);
            $response["idNota"]     =   $idNotaSal;
            responder($response,$mysqli);
        }
        else
        {
            $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la operaci&oacute;n.";
            $response["respuesta"] .=   "</div>";
            $response["status"] = 0;
            responder($response,$mysqli);
        }
    }
    else {
        $response['respuesta'] = $mysqli->error;
        $response["status"] = 0;
        responder($response,$mysqli);

    }
}
     ?>
