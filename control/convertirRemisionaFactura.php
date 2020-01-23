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
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $idCompra = $_POST['idCompra'];
    $response = array(
        "status"        => 1
    );
    if(isset($_POST['idCompra']) == false)
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se recibi&oacute; ning&uacute;n dato.";
        $response["respuesta"] .=   "</div>";
        $response['queCompra']  =   $idCompra;
        $response['status']     =   0;
        responder($response, $mysqli);
    }
    $sql = "SELECT id, esfactura FROM compras WHERE id = $idCompra LIMIT 1";
    $resExisteCompra            = $mysqli->query($sql);
    if ($resExisteCompra->num_rows == 0)
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No existe ninguna compra con el id solicitado.";
        $response["respuesta"] .=   "</div>";
        $response['queCompra']  =   $idCompra;
        $response['status']     =   0;
        responder($response, $mysqli);
    }
    $rowExisteCompra            = $resExisteCompra->fetch_assoc();
    $esFactura                  = $rowExisteCompra['esfactura'];
    if ($esFactura == 1)
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> Esta compra ya se ha convertido con anterioridad.";
        $response["respuesta"] .=   "</div>";
        $response['queCompra']  =   $idCompra;
        $response['esFactura']  =   1;
        $response['status']     =   0;
        responder($response, $mysqli);
    }
    $sql                        = "SELECT producto, cantidad FROM detallecompra WHERE compra = $idCompra";
    $resultD                    = $mysqli->query($sql);
    $sqlD                       = "";
    while ($rowD                = $resultD->fetch_assoc())
    {
        $idProducto             = $rowD['producto'];
        $cantidad               = $rowD['cantidad'];
        $sql_                   = "SELECT existencia_remision FROM productos WHERE id = $idProducto LIMIT 1";
        $result_                = $mysqli->query($sql_);
        $row_                   = $result_->fetch_assoc();
        $exist_real             = $row_['existencia_remision'];
        if ($exist_real < $cantidad)
        {
            $sqlD              .= "UPDATE productos SET existencia_remision = existencia_remision - $exist_real WHERE id = $idProducto LIMIT 1;";
            $sqlD              .= "UPDATE productos SET existencia_factura = existencia_factura + $exist_real WHERE id = $idProducto LIMIT 1;";
            $dif                = $cantidad - $exist_real;
            $sql__ = "SELECT * FROM detalleventa WHERE cantidad = $dif AND producto = $idProducto AND facturable = 0 AND facturado = 0 LIMIT 1";
            $res__ = $mysqli->query($sql__);
            if($res__->num_rows > 0)
            {
                $row__ = $res__->fetch_assoc();
                $idDetVenta     = $row__['id'];
                $sqlD          .= "UPDATE detalleventa SET facturable = 1 WHERE id = $idDetVenta LIMIT 1;";
            }
            else
            {
                $cont = 0;
                $dif            = round($dif);
                $sql__          = "SELECT * FROM detalleventa WHERE cantidad = 1 AND producto = $idProducto AND facturable = 0 AND facturado = 0 LIMIT $dif";
                $res__          = $mysqli->query($sql__);
                $encontrados    = $res__->num_rows;
                while ($row__   = $res__->fetch_assoc())
                {
                    $cont++;
                    if ($cont   > $encontrados)
                    {
                        break;
                    }
                    $idDetVenta = $row__['id'];
                    $sqlD      .= "UPDATE detalleventa SET facturable = 1 WHERE id = $idDetVenta LIMIT 1;";
                }
            }
        }
        else
        {
            $sqlD              .= "UPDATE productos SET existencia_remision = existencia_remision - $cantidad WHERE id = $idProducto LIMIT 1;";
            $sqlD              .= "UPDATE productos SET existencia_factura = existencia_factura + $cantidad WHERE id = $idProducto LIMIT 1;";
        }
    }
    $sqlD                      .= "UPDATE compras SET esfactura = 1 WHERE id = $idCompra LIMIT 1;";
    if($mysqli->multi_query($sqlD))
    {
        do {
            # code...
        } while ($mysqli->next_result());
        $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> La compra con id:<b>$idCompra se ha convertido a factura correctamente.";
        $response["respuesta"] .=   "</div>";
        $response['queCompra']  =   $idCompra;
        responder($response,$mysqli);
    }
}
?>
