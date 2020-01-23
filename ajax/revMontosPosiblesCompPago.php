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
    $idFactura      = $_POST['idFactura'];
    $sql            = "SELECT * FROM facturas WHERE id = $idFactura LIMIT 1";
    $result         = $mysqli->query($sql);
    $row_fact       = $result->fetch_assoc();
    //$idRelacion     = $row_fact['idRelacion'];
    if ($row_fact['metodoPago'] != 'PPD')
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> El m&eacute;todo de pago del CFDI no es diferido o en parcialidades (PPD).';
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    if ($row_fact['tipoCFDI'] != 'I')
    {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No es un tipo v&aacute;lido de CFDI. (El CFDI debe ser de tipo "<b>Ingreso</b>").';
        $response['respuesta'] .='</div>';
        $response['status']     = 0;
        responder($response,$mysqli);
    }
    $sql_montos                 = "SELECT
                                        IFNULL(SUM(montoPago),0)    AS montoPagado,
                                        COUNT(id)                   AS contPagosEmitidos
                                    FROM facturas WHERE idRelacion = $idFactura";
    $res_montos                 = $mysqli->query($sql_montos);
    $row_montos                 = $res_montos->fetch_assoc();
    $response['montoPagado']    = number_format($row_montos['montoPagado'],2,".",",");
    $response['contPagos']      = $row_montos['contPagosEmitidos'];
    $response['montoFactura']   = number_format($row_fact['total'],2,".",",");
    $response['saldo']          = number_format($row_fact['total'] - $row_montos['montoPagado'],2,".",",");
    $response['status']         = ($row_fact["pagado"] == 0) ? 1 : 0;
    responder($response,$mysqli);
//     $sql        = "SELECT
//                         ventas.totalventa AS totalVenta,
//                         ventas.cliente AS idCliente,
//                         (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta) AS pagado
//                     FROM ventas
//                     WHERE ventas.id = $idVenta";
//     $result     = $mysqli->query($sql);
//     if ($result->num_rows == 0)
//     {
//         $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
//         $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
//         $response['respuesta'] .="  No existe ning&uacute;na venta asociada al id: (<b>$idVenta</b>).";
//         $response['respuesta'] .='</div>';
//         $response['idCompra']   = 0;
//         $response['status']     = 0;
//         responder($response,$mysqli);
//     }
//     $rowRes     = $result->fetch_assoc();
//     $pagado     = $rowRes['pagado'];
//     $montoVenta = $rowRes['totalVenta'];
//     $idCliente  = $rowRes['idCliente'];
//     $saldo      = number_format($montoVenta - $pagado,2,".","");
//     if ($saldo  < $montoPago)
//     {
//         $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
//         $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
//         $response['respuesta'] .="  El monto del abono (<b>$$montoPago</b>) no puede ser mayor que el saldo (<b>$saldo</b>).";
//         $response['respuesta'] .='</div>';
//         $response['montoPago']  = 0;
//         $response['status']     = 0;
//         responder($response,$mysqli);
//     }
//     $sql        = "INSERT INTO pagosrecibidos
//                         (idventa, monto, usuario, cliente, sesion)
//                    VALUES
//                         ($idVenta, $montoPago, $idUsuario, $idCliente, $idSesion)";
//     if($mysqli->query($sql))
//     {
//         $sql        = "SELECT
//                             ventas.totalventa AS totalVenta,
//                             (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta) AS pagado
//                         FROM ventas
//                         WHERE ventas.id = $idVenta";
//         $result_    = $mysqli->query($sql);
//         $rowRes_    = $result_->fetch_assoc();
//         $pagado     = $rowRes_['pagado'];
//         $montoVenta = $rowRes_['totalVenta'];
//         $saldo      = $montoVenta - $pagado;
//         $sql = "SELECT
//                     ventas.totalventa           AS totalVenta,
//                     (SELECT IFNULL(SUM(monto),0) FROM pagosrecibidos
//                     WHERE pagosrecibidos.idventa = ventas.id) AS montoPagado
//                     FROM ventas
//                     WHERE ventas.pagado = 0";
//         $result = $mysqli->query($sql);
//         $adeudo = 0;
//         while ($rowDoc = $result->fetch_assoc())
//         {
//             $adeudo             += $rowDoc['totalVenta'] - $rowDoc['montoPagado'];
//         }
//         if ($saldo  <= 0)
//         {
//             $sql    = "UPDATE ventas SET pagado = 1 WHERE id = $idVenta LIMIT 1";
//             if ($mysqli->query($sql))
//             {
//                 $response['respuesta']  ='<div class="alert alert-success alert-dismissable">';
//                 $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
//                 $response['respuesta'] .="  Pago (<b>$$montoPago</b>) a la venta (<b>#$idVenta</b>) realizado exitosamente.</br>";
//                 $response['respuesta'] .="  <i class='fa fa-check-circle' aria-hidden='true'></i> La venta ha sido saldada y borrada de las cuentas por pagar. ";
//                 $response['respuesta'] .='</div>';
//                 $response['status']     = 1;
//                 $response['adeudo']     = number_format($adeudo,2,".",",");
//                 $response['borrar']     = $idVenta;
//                 responder($response,$mysqli);
//             }
//         }
//         else
//         {
//             $response['respuesta']  ='<div class="alert alert-success alert-dismissable">';
//             $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
//             $response['respuesta'] .="  Pago (<b>$$montoPago</b>) a la compra (<b>#$idVenta</b>) realizado exitosamente.";
//             $response['respuesta'] .='</div>';
//             $response['status']     = 1;
//             $response['pagado']     = number_format($pagado,2,".",",");
//             $response['saldo']      = number_format($saldo,2,".",",");
//             $response['adeudo']     = number_format($adeudo,2,".",",");
//             $response['idVenta']     = $idVenta;
//             responder($response,$mysqli);
//         }
//     }
//     else
//     {
//         $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
//         $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
//         $response['respuesta'] .="  No se pudo realizar el pago.";
//         $response['respuesta'] .='</div>';
//         $response['status']     = 0;
//         responder($response,$mysqli);
//     }
 }
?>
