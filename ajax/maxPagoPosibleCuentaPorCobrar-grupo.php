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
                "status"                => 1
                );
    //$idVenta              = $_POST['idVenta'];
    $montoPago_POST                     = $_POST['montoPago'];
    $coleccionVentas                    = json_decode($_POST['coleccionWarningJSON']);
    if (is_numeric($montoPago_POST)     == FALSE || $montoPago_POST <= 0)
    {
        $response['respuesta']          = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta']          .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta']          .='  Indica el monto del abono.';
        $response['respuesta']          .='</div>';
        $response['montoPago']          = 0;
        $response['status']             = 0;
        responder($response,$mysqli);
    }
    $pagado                         = 0;
    $montoVenta                         = 0;
    $cont                               = 0;
    $saldo                              = 0;
    //var_dump($coleccionVentas);
    foreach ($coleccionVentas as $idVenta)
    {
        $idVenta_F                      = $idVenta->idVenta;

        $sql        = "SELECT
                            ventas.totalventa   AS totalVenta,
                            ventas.cliente      AS idCliente,
                            clientes.rsocial    AS nombreCliente,
                            (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta_F) AS pagado
                        FROM ventas
                        INNER JOIN clientes
                        ON clientes.id  = ventas.cliente
                        WHERE ventas.id = $idVenta_F";
        $result                         = $mysqli->query($sql);
        if ($result->num_rows           == 0)
        {
            $response['respuesta']      ='<div class="alert alert-danger alert-dismissable">';
            $response['respuesta']      .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response['respuesta']      .="  Una o más ventas ya no están disponibles: (<b>$idVenta</b>).";
            $response['respuesta']      .='</div>';
            $response['idCompra']       = 0;
            $response['status']         = 0;
            responder($response,$mysqli);
        }
        $rowRes                         = $result->fetch_assoc();
        $pagado                         += $rowRes['pagado'];
        $montoVenta                     += $rowRes['totalVenta'];
        $rSocial                        = $rowRes['nombreCliente'];
        $saldo                          += $rowRes['totalVenta'] - $rowRes['pagado'];
        if (++$cont                     == 1)
            $idCliente_ant              = $rowRes['idCliente'];
        else
            if ($idCliente_ant          != $rowRes['idCliente'])
            {
                $response['respuesta']  ='<div class="alert alert-danger alert-dismissable">';
                $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $response['respuesta'] .="  Las ventas seleccionadas deben pertenecer al mismo cliente.";
                $response['respuesta'] .='</div>';
                $response['mismoCliente']= 0;
                $response['status']     = 0;
                responder($response,$mysqli);
            }
    }
    if ($saldo  < $montoPago_POST)
    {
        $response['respuesta']          ='<div class="alert alert-danger alert-dismissable">';
        $response['respuesta']          .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta']          .="  El monto del abono (<b>$$montoPago_POST</b>) no puede ser mayor que el saldo (<b>$saldo</b>).";
        $response['respuesta']          .='</div>';
        $response['montoPago']          = 0;
        $response['status']             = 0;
        responder($response,$mysqli);
    }
    $sql_insert                         = "";
    $saldo_restar                       = $montoPago_POST;
    $cont                               = 0;
    $banderaPrimerPago                  = 1;
    $response['actualizar']             = 0;
    $response['borrar']                 = 0;
    foreach ($coleccionVentas as $idVenta)
    {
        $idVenta_F                      = $idVenta->idVenta;
        $sql                            = " SELECT
                                                ventas.totalventa AS totalVenta,
                                            (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta_F) AS pagado
                                            FROM ventas
                                            WHERE ventas.id = $idVenta_F";
        $result_                        = $mysqli->query($sql);
        $rowRes_                        = $result_->fetch_assoc();
        $pagado                         = $rowRes_['pagado'];
        $montoVenta                     = $rowRes_['totalVenta'];
        $esteSaldo                      = $montoVenta - $pagado;
        if ($saldo_restar               >= $esteSaldo)
        {
            $montoPago                  = $esteSaldo;
            $sql_insert                 .= "UPDATE ventas SET pagado = 1 WHERE id = $idVenta_F LIMIT 1;";
            $response['rowBorrar'][$cont]= $idVenta_F;
            $response['borrar']         = 1;
            $cont++;
        }
        else
        {
            $montoPago                  = $saldo_restar;
            $response['rowActualizar']  = $idVenta_F;
            $response['actualizar']     = 1;
            $response['nuevoPagado']    = $pagado + $montoPago;
            $response['nuevoSaldo']     = number_format($montoVenta - $response['nuevoPagado'],2,".",",");
            $response['nuevoPagado']    = number_format($response['nuevoPagado'],2,".",",");
        }
        if ($banderaPrimerPago == 1)
        {
            $sql_primer_p               = " INSERT INTO pagosrecibidos
                                                (idventa, monto, usuario, cliente, sesion)
                                            VALUES
                                                ($idVenta_F, $montoPago, $idUsuario, $idCliente_ant, $idSesion);";
            $resultPrimerPago           = $mysqli->query($sql_primer_p);
            $idPrimer_pago              = $mysqli->insert_id;
            $sql_insert                 .= "UPDATE pagosrecibidos SET idPagoRelacion = $idPrimer_pago WHERE id = $idPrimer_pago LIMIT 1;";
            $banderaPrimerPago          = 0;
        }
        else
        {
            $sql_insert                 .= "INSERT INTO pagosrecibidos
                                                (idventa, idPagoRelacion, monto, usuario, cliente, sesion)
                                            VALUES
                                                ($idVenta_F, $idPrimer_pago, $montoPago, $idUsuario, $idCliente_ant, $idSesion);";
        }
        $saldo_restar                   = $saldo_restar - $montoPago;
        if ($saldo_restar               <= 0)
        {
            break;
        }
    }
    if($mysqli->multi_query($sql_insert))
    {
        do {
            # code...
        } while ($mysqli->more_results() && $mysqli->next_result());
        $sql = "SELECT
                    ventas.totalventa           AS totalVenta,
                    (SELECT IFNULL(SUM(monto),0) FROM pagosrecibidos
                    WHERE pagosrecibidos.idventa = ventas.id) AS montoPagado
                    FROM ventas
                    WHERE ventas.pagado = 0";
        $result = $mysqli->query($sql);
        $adeudo = 0;
        while ($rowDoc = $result->fetch_assoc())
        {
            $adeudo                     += $rowDoc['totalVenta'] - $rowDoc['montoPagado'];
        }
        $response['respuesta']          ='<div class="alert alert-success alert-dismissable">';
        $response['respuesta']          .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta']          .="  Monto (<b>$$montoPago_POST</b>) al cliente: <b>$rSocial</b> realizado correctamente.</br>";
        $response['respuesta']          .="  <i class='fa fa-check-circle' aria-hidden='true'></i> El pago ha sido ingresado exitosamente. ";
        $response['respuesta']          .='</div>';
        $response['status']             = 1;
        $response['saldo']              = $saldo;
        $response['sql']                = $sql_insert;
        $response['adeudo']             = "$".number_format($adeudo,2,".",",");
        responder($response,$mysqli);
    }
}
?>
