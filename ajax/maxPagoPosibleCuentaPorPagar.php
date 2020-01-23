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
    $response = array(
        "status"        => 1
    );
    $idCompra   = $_POST['idCompra'];
    $montoPago  = $_POST['montoPago'];
    if (is_numeric($montoPago) == FALSE || $montoPago <= 0)
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  Indica el monto del abono.';
        $response['respuesta'] .='</div>';
        $response['montoPago']  = 0;
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    $sql        = "SELECT
                        compras.monto AS montoCompra,
                        compras.proveedor AS idProveedor,
                        (SELECT IFNULL(SUM(pagosemitidos.monto), 0) FROM pagosemitidos WHERE idcompra = $idCompra) AS pagado
                    FROM compras
                    WHERE compras.id = $idCompra";
    $result     = $mysqli->query($sql);
    if ($result->num_rows == 0)
    {
        $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .="  No existe ning&uacute;na compra o documento con el id (<b>$idCompra</b>).";
        $response['respuesta'] .='</div>';
        $response['idCompra']   = 0;
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    $rowRes     = $result->fetch_assoc();
    $pagado     = $rowRes['pagado'];
    $montoCompra= $rowRes['montoCompra'];
    $idProveedor= $rowRes['idProveedor'];
    $saldo      = $montoCompra - $pagado;
    if ($saldo  < $montoPago)
    {
        $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .="  El monto del abono (<b>$$montoPago</b>) no puede ser mayor que el saldo (<b>$saldo</b>).";
        $response['respuesta'] .='</div>';
        $response['montoPago']  = 0;
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    $sql        = "INSERT INTO pagosemitidos
                        (idcompra, monto, usuario, proveedor, sesion)
                   VALUES
                        ($idCompra, $montoPago, $idUsuario, $idProveedor, $idSesion)";
    if($mysqli->query($sql))
    {
        $sql        = "SELECT
                            compras.monto AS montoCompra,
                            (SELECT IFNULL(SUM(pagosemitidos.monto), 0) FROM pagosemitidos WHERE idcompra = $idCompra) AS pagado
                        FROM compras
                        WHERE compras.id = $idCompra";
        $result_    = $mysqli->query($sql);
        $rowRes_    = $result_->fetch_assoc();
        $pagado     = $rowRes_['pagado'];
        $montoCompra= $rowRes_['montoCompra'];
        $saldo      = $montoCompra - $pagado;
        $sql = "SELECT
                    compras.monto           AS montoCompra,
                    (SELECT IFNULL(SUM(monto),0) FROM pagosemitidos
                    WHERE pagosemitidos.idcompra = compras.id) AS montoPagado
                    FROM compras
                    WHERE compras.pagado = 0";
        $result = $mysqli->query($sql);
        $adeudo = 0;
        while ($rowDoc = $result->fetch_assoc())
        {
            $adeudo             += $rowDoc['montoCompra'] - $rowDoc['montoPagado'];
        }
        if ($saldo  <= 0)
        {
            $sql    = "UPDATE compras SET pagado = 1 WHERE id = $idCompra LIMIT 1";
            if ($mysqli->query($sql))
            {
                $response['respuesta']  ='<div class="alert alert-success alert-dismissable">';
                $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $response['respuesta'] .="  Pago (<b>$$montoPago</b>) a la compra (<b>#$idCompra</b>) realizado exitosamente.</br>";
                $response['respuesta'] .="  <i class='fa fa-check-circle' aria-hidden='true'></i> La compra ha sido saldada y borrada de las cuentas por pagar. ";
                $response['respuesta'] .='</div>';
                $response['status']     = 1;
                $response['adeudo']     = number_format($adeudo,2,".",",");
                $response['borrar']     = $idCompra;
                responder($response,$mysqli);
            }
        }
        else
        {
            $response['respuesta']  ='<div class="alert alert-success alert-dismissable">';
            $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response['respuesta'] .="  Pago (<b>$$montoPago</b>) a la compra (<b>#$idCompra</b>) realizado exitosamente.";
            $response['respuesta'] .='</div>';
            $response['status']     = 1;
            $response['pagado']     = number_format($pagado,2,".",",");
            $response['saldo']      = number_format($saldo,2,".",",");
            $response['adeudo']     = number_format($adeudo,2,".",",");
            $response['idCompra']     = $idCompra;
            responder($response,$mysqli);
        }
    }
    else
    {
        $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .="  No se pudo realizar el pago.";
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
        responder($response,$mysqli);
    }
}
?>
