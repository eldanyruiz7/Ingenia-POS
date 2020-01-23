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
    $idSesion = $_POST['idCompra'];
    $sql = "SELECT saldoinicial, retiroPorCorte FROM sesionescontrol WHERE id = $idSesion LIMIT 1";
    $resultSaldoInicial = $mysqli->query($sql);
    $rowSaldoInicial = $resultSaldoInicial->fetch_assoc();
    $diferenciaFondo = $rowSaldoInicial['saldoinicial'];// $rowSaldoInicial['retiroPorCorte'];
?>
<div class="table-responsive" style="max-height:500px !important;">
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-inbox" aria-hidden="true"></i> Fondos</h4>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Inicial: </p>
            <label>$<?php echo number_format($rowSaldoInicial['saldoinicial'],2,".",",");?></label>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Final: </p>
            <label>$<?php echo number_format($rowSaldoInicial['retiroPorCorte'],2,".",",");?></label>
        </div>
        </br>
        </br>
        </br>
    </div>
<?php
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
?>
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Ventas</h4>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Cantidad: </p>
            <label><?php echo $rowVentas['cant'];?></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Monto: </p>
            <label>$<?php echo number_format($rowVentas['suma'],2,".",",");?></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Crédito: </p>
            <label>$<?php echo number_format($creditoVenta,2,".",",");?></label>
        </div>
        </br>
        </br>
        </br>
    </div>
<?php
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
 ?>
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-cart-plus" aria-hidden="true"></i> Compras</h4>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Cantidad: </p>
            <label><?php echo $rowCompras['cant'];?></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Monto: </p>
            <label>$<?php echo number_format($rowCompras['suma'],2,".",",");?></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Cr&eacute;dito: </p>
            <label>$<?php echo number_format($creditoCompra,2,".",",");?></label>
        </div>
        </br>
        </br>
        </br>
    </div>
<?php

$sql = "SELECT
            IFNULL(SUM(retiros.monto),0) AS suma,
            COUNT(retiros.id) AS cant
        FROM retiros WHERE retiros.sesion = $idSesion AND tipo = 0";

$resultRetiros = $mysqli->query($sql);
$rowRetiros = $resultRetiros->fetch_assoc();
$sql = "SELECT
            retiros.monto AS esteMontoRetiro,
            usuarios.nombre AS nombreUsuario,
            usuarios.apellidop AS apellidopUsuario
        FROM retiros
        INNER JOIN usuarios
        ON retiros.usuario = usuarios.id
        WHERE retiros.sesion = $idSesion AND tipo = 0
        ORDER BY retiros.timestamp ASC";
$resultRetirosDetalle = $mysqli->query($sql);


 ?>
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-arrow-left" aria-hidden="true"></i> Retiros de efectivo</h4>
        <table width="100%">
<?php
$cont = 0;
while ($rowRetirosDetalle = $resultRetirosDetalle->fetch_assoc())
{
?>
            <tr>
                <td>
                    <?php echo ++$cont;?>.
                </td>
                <td>
                    <?php echo $rowRetirosDetalle['nombreUsuario']." ".$rowRetirosDetalle['apellidopUsuario'];?>
                </td>
                <td class="text-right">
                    <label>$<?php echo number_format($rowRetirosDetalle['esteMontoRetiro'],2,".",",");?></label>
                </td>
            </tr>
<?php
}
?>
        </table>
    <hr />
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Cantidad: </p>
            <label><?php echo $rowRetiros['cant'];?></label>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Total: </p>
            <label>$<?php echo number_format($rowRetiros['suma'],2,".",",");?></label>
        </div>
        </br>
        </br>
        </br>
    </div>
<?php

    $sql = "SELECT
                IFNULL(SUM(retiros.monto),0) AS suma,
                COUNT(retiros.id) AS cant
            FROM retiros WHERE retiros.sesion = $idSesion AND tipo = 1";

    $resultIngresos = $mysqli->query($sql);
    $rowIngresos = $resultIngresos->fetch_assoc();
    $sql = "SELECT
                retiros.monto AS esteMontoRetiro,
                usuarios.nombre AS nombreUsuario,
                usuarios.apellidop AS apellidopUsuario
            FROM retiros
            INNER JOIN usuarios
            ON retiros.usuario = usuarios.id
            WHERE retiros.sesion = $idSesion AND tipo = 1
            ORDER BY retiros.timestamp ASC";
    $resultIngresosDetalle = $mysqli->query($sql);


     ?>
        <div class="well well-sm" style="padding-bottom:0px">
            <h4 class="text-center"><i class="fa fa-arrow-right" aria-hidden="true"></i> Ingreso de efectivo</h4>
            <table width="100%">
    <?php
    $cont = 0;
    while ($rowIngresosDetalle = $resultIngresosDetalle->fetch_assoc())
    {
    ?>
                <tr>
                    <td>
                        <?php echo ++$cont;?>.
                    </td>
                    <td>
                        <?php echo $rowIngresosDetalle['nombreUsuario']." ".$rowIngresosDetalle['apellidopUsuario'];?>
                    </td>
                    <td class="text-right">
                        <label>$<?php echo number_format($rowIngresosDetalle['esteMontoRetiro'],2,".",",");?></label>
                    </td>
                </tr>
    <?php
    }
    ?>
            </table>
        <hr />
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
                <p>Cantidad: </p>
                <label><?php echo $rowIngresos['cant'];?></label>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
                <p>Total: </p>
                <label>$<?php echo number_format($rowIngresos['suma'],2,".",",");?></label>
            </div>
            </br>
            </br>
            </br>
        </div>
<?php
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

 ?>
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-certificate" aria-hidden="true"></i> Notas de crédito</h4>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Devs: </p>
            <label><?php echo $rowNotaCred['devolucion'];?></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Camb: </p>
            <label><?php echo $rowNotaCred['cambio'];?></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 text-center">
            <p>Total: </p>
            <p><label>$<?php echo number_format($rowNotaCred['suma'],2,".",",");?></label></p>
        </div>
        </br>
        </br>
        </br>
    </div>
<?php
$sql = "SELECT
            IFNULL(SUM(notadesalida.montopublico),0) AS suma,
            COUNT(notadesalida.id) AS cant
            FROM notadesalida
            WHERE notadesalida.sesion = $idSesion";

$resultNotaSal = $mysqli->query($sql);
$rowNotaSal = $resultNotaSal->fetch_assoc();

 ?>
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Notas de salida</h4>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Cantidad: </p>
            <label><?php echo $rowNotaSal['cant'];?></label>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Total: </p>
            <label>$<?php echo number_format($rowNotaSal['suma'],2,".",",");?></label>
        </div>
        </br>
        </br>
        </br>
    </div>
<?php
$sql = "SELECT
            IFNULL(SUM(pagosrecibidos.monto),0) AS suma,
            COUNT(pagosrecibidos.id) AS cant
            FROM pagosrecibidos
            WHERE pagosrecibidos.sesion = $idSesion";

$resultPagosRec = $mysqli->query($sql);
$rowPagosRec = $resultPagosRec->fetch_assoc();
$sql = "SELECT
            pagosrecibidos.monto AS esteMontoRec,
            clientes.rsocial AS nombreCliente,
            metodosdepago.nombre AS metodoPago
        FROM pagosrecibidos
        INNER JOIN clientes
        ON pagosrecibidos.cliente = clientes.id
        INNER JOIN metodosdepago
        ON pagosrecibidos.metodoPago = metodosdepago.id
        WHERE pagosrecibidos.sesion = $idSesion
        ORDER BY pagosrecibidos.fechahora ASC";
$resultListPagosRec = $mysqli->query($sql);

 ?>
        <div class="well well-sm" style="padding-bottom:0px">
            <h4 class="text-center"><i class="fa fa-money" aria-hidden="true"></i> Pagos de clientes</h4>
            <table width="100%">
<?php
    $cont = 0;
    while ($rowListPagosRec = $resultListPagosRec->fetch_assoc())
    {
?>
            <tr>
                <td>
                    <?php echo ++$cont;?>.
                </td>
                <td>
                    <?php echo $rowListPagosRec['nombreCliente'];?>
                </td>
                <td>
                    <?php echo $rowListPagosRec['metodoPago'];?>
                </td>
                <td class="text-right">
                    <label>$<?php echo number_format($rowListPagosRec['esteMontoRec'],2,".",",");?></label>
                </td>
            </tr>
<?php
    }
?>
        </table>
        <hr />
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
                <p>Cantidad: </p>
                <p><label><?php echo $rowPagosRec['cant'];?></label></p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
                <p>Total: </p>
                <p><label>$<?php echo number_format($rowPagosRec['suma'],2,".",",");?></label></p>
            </div>
            </br>
            </br>
            </br>
        </div>
        <?php
$sql = "SELECT
            IFNULL(SUM(pagosemitidos.monto),0) AS suma,
            COUNT(pagosemitidos.id) AS cant
            FROM pagosemitidos
            WHERE pagosemitidos.sesion = $idSesion";

$resultPagosEm = $mysqli->query($sql);
$rowPagosEm = $resultPagosEm->fetch_assoc();
$sql = "SELECT
            pagosemitidos.monto AS esteMontoEm,
            proveedores.rsocial AS nombreProveedor
        FROM pagosemitidos
        INNER JOIN proveedores
        ON pagosemitidos.proveedor = proveedores.id
        WHERE pagosemitidos.sesion = $idSesion
        ORDER BY pagosemitidos.fechahora ASC";
$resultListPagosEm = $mysqli->query($sql);
 ?>
    <div class="well well-sm" style="padding-bottom:0px">
        <h4 class="text-center"><i class="fa fa-usd" aria-hidden="true"></i> Pagos a proveedores</h4>
        <table width="100%">
<?php
$cont = 0;
while ($rowListPagosEm = $resultListPagosEm->fetch_assoc())
{
?>
        <tr>
            <td>
                <?php echo ++$cont;?>.
            </td>
            <td>
                <?php echo $rowListPagosEm['nombreProveedor'];?>
            </td>
            <td class="text-right">
                <label>$<?php echo number_format($rowListPagosEm['esteMontoEm'],2,".",",");?></label>
            </td>
        </tr>
<?php
}
?>
    </table>
    <hr />
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Cantidad: </p>
            <label><?php echo $rowPagosEm['cant'];?></label>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center">
            <p>Total: </p>
            <label>$<?php echo number_format($rowPagosEm['suma'],2,".",",");?></label>
        </div>
        </br>
        </br>
        </br>
    </div>
    <div class="col-lg-12">
    </br>
<?php
$totalVenta  = $efectivoVentas - $efectivoCompras; //$rowCompras['suma'];
$totalVenta -= $rowRetiros['suma'];
$totalVenta += $rowIngresos['suma'];
$totalVenta -= $rowNotaCred['suma'];
$totalVenta += $rowPagosRec['suma'];
$totalVenta -= $rowPagosEm['suma'];
$totalVenta += $diferenciaFondo;
$label = ($totalVenta >= 0) ? "label-success" : "label-danger";
 ?>
    <div class="list-group-item text-center">
        <h4>TOTAL VENTA DEL TURNO #<?php echo $idSesion;?>:</h4> <h3><span class="label <?php echo $label;?>">$<?php echo number_format($totalVenta,2,".",",");?></span></h3>
    </div>
</div>


</div>
<!-- /.table-responsive -->
<?php
//    responder($response,$mysqli);
}
?>
