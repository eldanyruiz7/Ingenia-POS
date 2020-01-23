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
require ("retiroEfectivoRecibo.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    $monto = $_POST['inputMontoRetirar'];
    $obs = $_POST['inputObsRetirar'];
    $xArs = $_POST['xArs'];
    $status = 1;
    $url = 'retiroEfectivo.php';
    $idCajero = $idUsuario;
    $response = array(
        "retiro"    => $monto,
        'obs'       => $obs,
        'status'    => $status,
        'url'       => $url
    );
    function responder($response, $mysqli)
    {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }

    //$idSesion = $_POST['idCompra'];
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

            FROM notacredito WHERE notacredito.sesion = $idSesion";

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


    $balance = $totalVenta;
    if (!is_numeric($monto) || strlen($monto) == 0 || $monto <= 1)
    {
        $response['numerico'] = 0;
        $response['status'] = 0;
        $response['respuesta'] =   "<div class='alert alert-danger alert-dismissable'>";
        $response['respuesta'].=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response['respuesta'].=   "   <i class='fa fa-exclamation-circle' aria-hidden='true'></i> Completa los campos correctamente.";
        $response['respuesta'].=   "</div>";
        responder($response, $mysqli);
    }
    if($balance < $monto)
    {
        $response['numerico'] = 0;
        $response['status'] = 0;
        $response['respuesta'] =   "<div class='alert alert-danger alert-dismissable'>";
        $response['respuesta'].=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response['respuesta'].=   "   <i class='fa fa-exclamation-circle' aria-hidden='true'></i> No hay fondos suficientes, ingresa un monto menor para retirar.";
        $response['respuesta'].=   "</div>";
        responder($response, $mysqli);
    }
    $sql = "INSERT INTO retiros (sesion, usuario, cajero, monto, observaciones) VALUES ($idSesion, $xArs, $idCajero, $monto, '$obs')";
    if($mysqli->query($sql) != TRUE)
    {
        $response['status'] = 0;
        $response['respuesta'] =   "<div class='alert alert-danger alert-dismissable'>";
        $response['respuesta'].=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response['respuesta'].=   "   <i class='fa fa-exclamation-circle' aria-hidden='true'></i> Algo sali&oacute; mal. Parece ser un error de base de datos. Contacta con el Administrador del sistema.";
        $response['respuesta'].=   "</div>";
        responder($response, $mysqli);
    }
    else
    {
        $idRecibo               = $mysqli->insert_id;
        $reciboHTML             = genReciboRetiro($idRecibo,$mysqli);
        $response['recibo']     = $reciboHTML;
        responder($response, $mysqli);
    }

}

 ?>
