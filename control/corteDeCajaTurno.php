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
require ("../control/corteCajaRecibo.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    function responder($response, $mysqli)
    {
        $response['respuesta']=$mysqli->error;
        //print_r($response);
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $response = array("error"   =>1);
    $efectivo   = $_POST["efectivo"];
    $cheque     = $_POST["cheque"];
    $vales      = $_POST["vales"];
    $tarjeta    = $_POST["tarjeta"];
    $retiro     = $_POST["retiro"];

    $sql = "SELECT saldoinicial, retiroPorCorte FROM sesionescontrol WHERE id = $idSesion LIMIT 1";
    $resultSaldoInicial = $mysqli->query($sql);
    $rowSaldoInicial = $resultSaldoInicial->fetch_assoc();
    $diferenciaFondo = $rowSaldoInicial['saldoinicial'] - $rowSaldoInicial['retiroPorCorte'];
    $saldoInicial = $rowSaldoInicial['saldoinicial'];
    $saldoFinal = $rowSaldoInicial['retiroPorCorte'];
    $sql = "SELECT
                IFNULL(SUM(ventas.totalventa),0) AS suma,
                COUNT(ventas.id) AS cant,
                (SELECT
                    IFNULL(SUM(pagosrecibidos.monto), 0)
                FROM pagosrecibidos
                WHERE pagosrecibidos.sesion = $idSesion) AS montoPagosRecibidos,
                (SELECT
                    IFNULL(SUM(ventas.totalventa),0)
                   FROM ventas
                WHERE ventas.sesion = $idSesion AND ventas.esCredito = 1) AS montoVentasCredito
            FROM ventas WHERE ventas.sesion = $idSesion";

    $resultVentas = $mysqli->query($sql);
    $rowVentas = $resultVentas->fetch_assoc();
    $sql = "SELECT
                ventas.id AS idVentaSesion,
                pagosrecibidos.monto AS montoPagosSesion
            FROM ventas
            INNER JOIN pagosrecibidos
            ON ventas.id = pagosrecibidos.idventa
            WHERE ventas.sesion = $idSesion AND ventas.esCredito = 1";
    $resultVentasDet = $mysqli->query($sql);
    $montoPagosSesion = 0;
    while ($rowVentasDet = $resultVentasDet->fetch_assoc())
    {
        $montoPagosSesion+= $rowVentasDet['montoPagosSesion'];
    }
    $creditoVenta = $rowVentas['montoVentasCredito'] - $montoPagosSesion;
    $efectivoVentas = $rowVentas['suma'] - $rowVentas['montoVentasCredito'];//$montoEmitidosSesion;
    //$creditoVenta = $rowVentas['montoVentasCredito'] - $montoPagosSesion;
    $sql = "SELECT
                IFNULL(SUM(compras.monto),0) AS suma,
                COUNT(compras.id) AS cant,
                (SELECT
                    IFNULL(SUM(compras.monto),0)
                   FROM compras
                WHERE compras.sesion = $idSesion AND compras.esCredito = 1) AS montoComprasCredito
                FROM compras WHERE compras.sesion = $idSesion";

    $resultCompras = $mysqli->query($sql);
    $rowCompras = $resultCompras->fetch_assoc();

    $sql = "SELECT
                compras.id AS idCompraSesion,
                pagosemitidos.monto AS montoEmitidosSesion
            FROM compras
            INNER JOIN pagosemitidos
            ON compras.id = pagosemitidos.idcompra
            WHERE compras.sesion = $idSesion AND compras.esCredito = 1";
    $resultComprasDet = $mysqli->query($sql);
    $montoEmitidosSesion = 0;
    while ($rowComprasDet = $resultComprasDet->fetch_assoc())
    {
        $montoEmitidosSesion+= $rowComprasDet['montoEmitidosSesion'];
    }
    $creditoCompra = $rowCompras['montoComprasCredito'] - $montoEmitidosSesion;
    $efectivoCompras = $rowCompras['suma'] - $rowCompras['montoComprasCredito'];//$montoEmitidosSesion;
    $sql = "SELECT
                IFNULL(SUM(retiros.monto),0) AS suma,
                COUNT(retiros.id) AS cant
            FROM retiros WHERE retiros.sesion = $idSesion";

    $resultRetiros = $mysqli->query($sql);
    $rowRetiros = $resultRetiros->fetch_assoc();
    $sql = "SELECT
                IFNULL(SUM(notacredito.credito),0) AS suma,
                (SELECT
                    COUNT(notacredito.tipo)
                FROM notacredito
                WHERE notacredito.tipo = 1
                AND sesion = $idSesion) AS cambio,
                (SELECT
                    COUNT(notacredito.tipo)
                FROM notacredito
                WHERE notacredito.tipo = 2
                AND sesion = $idSesion) AS devolucion

            FROM notacredito WHERE notacredito.sesion = $idSesion AND notacredito.tipo = 2";

    $resultNotaCred = $mysqli->query($sql);
    $rowNotaCred = $resultNotaCred->fetch_assoc();
    $sql = "SELECT
                IFNULL(SUM(notadesalida.montopublico),0) AS suma,
                COUNT(notadesalida.id) AS cant
                FROM notadesalida
                WHERE notadesalida.sesion = $idSesion";

    $resultNotaSal = $mysqli->query($sql);
    $rowNotaSal = $resultNotaSal->fetch_assoc();
    $sql = "SELECT
                IFNULL(SUM(pagosrecibidos.monto),0) AS suma,
                COUNT(pagosrecibidos.id) AS cant
                FROM pagosrecibidos
                WHERE pagosrecibidos.sesion = $idSesion";

    $resultPagosRec = $mysqli->query($sql);
    $rowPagosRec = $resultPagosRec->fetch_assoc();
    $resultListPagosRec = $mysqli->query($sql);
    $sql = "SELECT
                IFNULL(SUM(pagosemitidos.monto),0) AS suma,
                COUNT(pagosemitidos.id) AS cant
                FROM pagosemitidos
                WHERE pagosemitidos.sesion = $idSesion";

    $resultPagosEm = $mysqli->query($sql);
    $rowPagosEm = $resultPagosEm->fetch_assoc();

    $totalVenta  = $efectivoVentas - $efectivoCompras; //$rowCompras['suma'];
    $totalVenta -= $rowRetiros['suma'];
    $totalVenta -= $rowNotaCred['suma'];
    $totalVenta += $rowPagosRec['suma'];
    $totalVenta -= $rowPagosEm['suma'];
    $totalVenta += $diferenciaFondo;

    $sql = "SELECT
                pagosrecibidos.monto AS esteMontoRec,
                metodosdepago.id AS idMetodoPago
            FROM pagosrecibidos
            INNER JOIN metodosdepago
            ON pagosrecibidos.metodoPago = metodosdepago.id
            WHERE pagosrecibidos.sesion = $idSesion
            ORDER BY pagosrecibidos.fechahora ASC";
    $resultListPagosRec = $mysqli->query($sql);
    $ventaCheque = 0;
    $ventaVales = 0;
    $ventaTarjeta = 0;
    while ($rowListPagosRec = $resultListPagosRec->fetch_assoc())
    {
        $idMetodoPago = $rowListPagosRec['idMetodoPago'];
        switch ($idMetodoPago)
        {
            case 2:
                $ventaCheque += $rowListPagosRec['esteMontoRec'];
                break;
            case 3:
            case 4:
            case 5:
            case 6:
            case 18:
            case 19:
                $ventaTarjeta += $rowListPagosRec['esteMontoRec'];
                break;
            case 7:
                $ventaVales += $rowListPagosRec['esteMontoRec'];
                break;
        }
    }

    $ventaEfectivo = $totalVenta - $ventaCheque - $ventaTarjeta - $ventaVales;
    /*$sql = "UPDATE ventas SET corte = 1 WHERE sesion = $idSesion AND usuario = $idUsuario";
    $mysqli->query($sql);*/
/*    $sql = "SELECT * FROM ventas WHERE usuario = $idUsuario AND corte = 0";
    $result = $mysqli->query($sql);
    $ventaEfectivo = 0;
    $ventaCheque = 0;
    $ventaVales = 0;
    $ventaTarjeta = 0;
    while($fila = $result->fetch_assoc())
    {
        $metodoPago = $fila["metododepago"];
        $estaVenta  = $fila["totalventa"];
        switch ($metodoPago)
        {
            case 1:
                $ventaEfectivo  += $estaVenta;
                break;
            case 2:
                $ventaCheque    += $estaVenta;
                break;
            case 3:
                $ventaVales     += $estaVenta;
                break;
            case 4:
                $ventaTarjeta   += $estaVenta;
                break;
        }

    }*/
    $fechaLogout    = date("Y-m-d H:i:s");
    $sql = "UPDATE sesionescontrol
            SET timestampsalida         = '$fechaLogout',
                retiroPorCorte          = $retiro,
                saldoCalculadoEfectivo  = $ventaEfectivo,
                saldoCalculadoCheque    = $ventaCheque,
                saldoCalculadoVales     = $ventaVales,
                saldoCalculadoTarjeta   = $ventaTarjeta,
                saldoDeclaradoEfectivo  = $efectivo,
                saldoDeclaradoCheque    = $cheque,
                saldoDeclaradoVales     = $vales,
                saldoDeclaradoTarjeta   = $tarjeta,
                estado                  = 2
            WHERE id = $idSesion
            AND usuario = $idUsuario
            LIMIT 1";
    if($mysqli->query($sql) === TRUE)
    {

            $sql = "UPDATE retiros SET corte = 1 WHERE sesion = $idSesion;";

            $sql .= "UPDATE compras SET corte = 1 WHERE sesion = $idSesion;";

            $sql .= "UPDATE ventas SET corte = 1 WHERE sesion = $idSesion;";

            $sql .= "UPDATE pagosrecibidos SET corte = 1 WHERE sesion = $idSesion;";

            $sql .= "UPDATE pagosemitidos SET corte = 1 WHERE sesion = $idSesion;";

            $sql .= "UPDATE notacredito SET corte = 1 WHERE sesion = $idSesion;";

            $sql .= "UPDATE notadesalida SET corte = 1 WHERE sesion = $idSesion;";
            if($mysqli->multi_query($sql))
            {
                do {
                    # code...
                } while ($mysqli->next_result());
            }
            $reciboHTML = genReciboCorteCaja($idSesion, $mysqli);
            $response['error']      = 0;
            $response['recibo']     =   $reciboHTML;
            $response['codigo']     =  str_pad($idSesion, 12, "0", STR_PAD_LEFT);
    }
    else
    {
        $response['error'] = 1;
        $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
        $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>Error</b> No se pudo completar la operaci&oacute;n.';
        $response["respuesta"].='</div>';
        responder($response, $mysqli);
    }
    responder($response, $mysqli);
    //$esteProducto = json_decode($_POST['arrayProducto']);

            //unlink($_SERVER["SERVER_ROOT"].$directorio.$nombreArchivo);
}
?>
