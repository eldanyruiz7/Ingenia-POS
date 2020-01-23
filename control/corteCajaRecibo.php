<?php
function genReciboCorteCaja($idRecibo,$mysqli)
{
    $sql                    = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig           = $mysqli->query($sql);
    $rowConfig              = $resultConfig->fetch_assoc();
    $sql                    = "SELECT * FROM sesionescontrol WHERE id = $idRecibo LIMIT 1";
    $result                 = $mysqli->query($sql);
    $fila                   = $result->fetch_assoc();
    //$idSesion               = $fila['id'];
    $idSesion = $idRecibo;
    /*$usuario                = $fila['usuario'];
    $timestamEntrada        = $fila['timestampentrada'];
    $timestamSalida         = $fila['timestampsalida'];
    $saldoInicial           = $fila['saldoinicial'];
    $retiroPorCorte         = $fila['retiroPorCorte'];
    $saldoCalculadoEfectivo = $fila['saldoCalculadoEfectivo'];
    $saldoCalculadoCheque   = $fila['saldoCalculadoCheque'];
    $saldoCalculadoVales    = $fila['saldoCalculadoVales'];
    $saldoCalculadoTarjeta  = $fila['saldoCalculadoTarjeta'];
    $saldoDeclaradoEfectivo = $fila['saldoDeclaradoEfectivo'];
    $saldoDeclaradoCheque   = $fila['saldoDeclaradoCheque'];
    $saldoDeclaradoVales    = $fila['saldoDeclaradoVales'];
    $saldoDeclaradoTarjeta  = $fila['saldoDeclaradoTarjeta'];*/
    //$idSesion = $_POST['idCompra'];
    $sql = "SELECT * FROM sesionescontrol WHERE id = $idSesion LIMIT 1";
    $resultSaldoInicial = $mysqli->query($sql);
    $rowSaldoInicial = $resultSaldoInicial->fetch_assoc();

    $idCajero               = $rowSaldoInicial['usuario'];
    $sql                    = "SELECT nombre, apellidop, apellidom FROM usuarios WHERE id = $idCajero LIMIT 1";
    $resultCaj              = $mysqli->query($sql);
    $filaCaj                = $resultCaj->fetch_assoc();
    $nombreCajero           = $filaCaj['nombre']." ".$filaCaj['apellidop']." ".$filaCaj['apellidom'];
    $fecha                  = date('d/m/Y', time());
    $hora                   = date('H:i:s', time());
    $fechaInicio            = date('d/m/Y',strtotime($rowSaldoInicial['timestampentrada']));
    $horaInicio             = date('H:i:s',strtotime($rowSaldoInicial['timestampentrada']));
    $fechaFin               = date('d/m/Y',strtotime($rowSaldoInicial['timestampsalida']));
    $horaFin                = date('H:i:s',strtotime($rowSaldoInicial['timestampsalida']));
    $recibo =  " <table width='100%'>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'><h3>";
    $recibo .=          $rowConfig['nombreComercio'];
    $recibo .=          "</h3></td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'><b>COMPROBANTE CORTE DE CAJA</b></td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'>CAJERO: $nombreCajero</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'>CORTE # <b> ".$rowSaldoInicial['id']."</b></td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .=  " <table width='100%'>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Fecha de entrada:</td>";
    $recibo .= "        <td style='text-align:center'>$fechaInicio</td>";
    $recibo .= "        <td style='text-align:center'>$horaInicio</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Fecha de salida:</td>";
    $recibo .= "        <td style='text-align:center'>$fechaFin</td>";
    $recibo .= "        <td style='text-align:center'>$horaFin</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Fecha de impresión:</td>";
    $recibo .= "        <td style='text-align:center'>$fecha</td>";
    $recibo .= "        <td style='text-align:center'>$hora</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center' colspan=3><svg id='code_svg'></svg></td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .= "___________________________________";

    $diferenciaFondo = $rowSaldoInicial['saldoinicial'];
    $saldoInicial = $rowSaldoInicial['saldoinicial'];
    $saldoFinal = $rowSaldoInicial['retiroPorCorte'];
    $sql = "SELECT
                IFNULL(SUM(ventas.totalventa),0) AS suma,
                (SELECT COUNT(ventas.id)
                FROM ventas
                WHERE ventas.sesion = $idSesion) AS cant,
                (SELECT
                    IFNULL(SUM(ventas.totalventa),0)
                   FROM ventas
                WHERE ventas.sesion = $idSesion) AS montoVentas,
                (SELECT
                IFNULL(SUM(ventas.totalventa),0)
                FROM ventas
                WHERE ventas.sesion = $idSesion AND ventas.esCredito = 1) AS montoVentasCredito
            FROM ventas WHERE ventas.sesion = $idSesion AND ventas.esCredito = 0";

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
    $creditoVenta = $rowVentas['montoVentas'] - $montoPagosSesion;
    $efectivoVentas = $rowVentas['suma'] - $creditoVenta;//$rowVentas['montoVentasCredito'];//$montoEmitidosSesion;
    //$creditoVenta = $rowVentas['montoVentasCredito'] - $montoPagosSesion;
    $recibo .=  "<table width='100%'>";
    $recibo .= "    <tr>";
    $recibo .="        <td style='text-align:center' colspan='2'>";
    $recibo .="            <b>FONDO DE CAJA</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Inicial:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "$".number_format($saldoInicial,2,".",",");
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Final:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "$".number_format($saldoFinal,2,".",",");
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Diferencia:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($saldoInicial - $saldoFinal,2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
    $recibo .= "___________________________________";
    $recibo .=  "<table width='100%'>";
    $recibo .= "    <tr>";
    $recibo .="        <td style='text-align:center' colspan='2'>";
    $recibo .="            <b>VENTAS</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Cantidad:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            $rowVentas['cant'];
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Total:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "$".number_format($rowVentas['montoVentas'],2,".",",");
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Crédito:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "$".number_format($rowVentas['montoVentasCredito'],2,".",",");
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Efectivo:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($rowVentas['suma'],2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";



    $recibo .="</table>";
    $recibo .= "___________________________________";
$sql = "SELECT
            IFNULL(SUM(compras.monto),0) AS suma,
            (SELECT COUNT(compras.id)
            FROM compras
            WHERE compras.sesion = $idSesion) AS cant,
            (SELECT
                IFNULL(SUM(compras.monto),0)
               FROM compras
            WHERE compras.sesion = $idSesion) AS montoCompras,
            (SELECT
            IFNULL(SUM(compras.monto),0)
            FROM compras
            WHERE compras.sesion = $idSesion AND compras.esCredito = 1) AS montoComprasCredito
        FROM compras WHERE compras.sesion = $idSesion AND compras.esCredito = 0";
/*$sql = "SELECT
            IFNULL(SUM(compras.monto),0) AS suma,
            (SELECT COUNT(compras.id)
            FROM compras
            WHERE compras.sesion = $idSesion) AS cant,
            (SELECT
                IFNULL(SUM(compras.monto),0)
               FROM compras
            WHERE compras.sesion = $idSesion AND compras.esCredito = 1) AS montoComprasCredito
            FROM compras WHERE compras.sesion = $idSesion";
*/
$resultCompras = $mysqli->query($sql);
$rowCompras = $resultCompras->fetch_assoc();

$sql = "SELECT
            compras.id AS idCompraSesion,
            compras.proveedor AS idProveedor,
            compras.monto AS esteMontoCompra,
            compras.contado AS contado,
            proveedores.rsocial AS nombreProveedor,
            (SELECT IFNULL(SUM(pagosemitidos.monto),0)
            FROM pagosemitidos WHERE pagosemitidos.idcompra = idCompraSesion AND pagosemitidos.sesion = $idSesion) AS sumaPagos
        FROM compras
        INNER JOIN proveedores
        ON compras.proveedor = proveedores.id
        WHERE compras.sesion = $idSesion";
$resultComprasDet = $mysqli->query($sql);


    $recibo .="<table width='100%'>";
    $recibo .="     <tr>";
    $recibo .="         <td style='text-align:center' colspan='4'>";
    $recibo .="             <b>COMPRAS</b>";
    $recibo .="         </td>";
    $recibo .="     </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td style='text-align:left'>";
    $recibo .="            <b>Tipo</b>";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="             <b>Total</b>";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="            <b>Abonado</b>";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="             <b>Saldo</b>";
    $recibo .="         </td>";
    $recibo .="     </tr>";
    $montoEmitidosSesion = 0;
    $cont = 0;
    $totCompras = 0;
    $totCredCompras = 0;
    $totalAbonadoCompras = 0;
    while ($rowComprasDet = $resultComprasDet->fetch_assoc())
    {
        $cont++;
        $recibo .="     <tr>";
        $recibo .="         <td>";
        $recibo .="            ".$cont;
        $recibo .="         </td>";
        $recibo .="         <td colspan='3' style='text-align:left'>";
        $recibo .="             ".$rowComprasDet['nombreProveedor'];
        $recibo .="         </td>";
        $recibo .="     </tr>";
        $recibo .="     <tr>";
        $recibo .="         <td style='text-align:left'>";
        $recibo .=            ($rowComprasDet['contado'] == 1) ? "Contado" : "Crédito";
        $recibo .="         </td>";
        $recibo .="         <td style='text-align:right'>";
        $totCompras += $rowComprasDet['esteMontoCompra'];
        $recibo .="             $".number_format($rowComprasDet['esteMontoCompra'],2,".",",");
        $recibo .="         </td>";
        $recibo .="         <td style='text-align:right'>";
        $totCredCompras += $rowComprasDet['sumaPagos'];
        $recibo .="            $".number_format($rowComprasDet['sumaPagos'],2,".",",");
        $recibo .="         </td>";
        $s = ($rowComprasDet['contado'] == 1) ? 0 :$rowComprasDet['esteMontoCompra'] - $rowComprasDet['sumaPagos'];
        $totalAbonadoCompras += $s;
        $recibo .="         <td style='text-align:right'>";
        $recibo .=             "$".number_format($s,2,".",",");
        $recibo .="         </td>";
        $recibo .="     </tr>";
        //$montoEmitidosSesion+= $rowComprasDet['montoEmitidosSesion'];
    }
    $creditoCompra = $rowCompras['montoComprasCredito'] - $montoEmitidosSesion;
    $efectivoCompras = $rowCompras['suma'] - $rowCompras['montoComprasCredito'];//$montoEmitidosSesion;

    $recibo .="     <tr>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="             Cantidad:";
    $recibo .="         </td>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .=             $rowCompras['cant'];
    $recibo .="         </td>";
    $recibo .="     </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="             Total:";
    $recibo .="         </td>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .=             "$".number_format($rowCompras['montoCompras'],2,".",",");
    $recibo .="         </td>";
    $recibo .="     </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="             Credito:";
    $recibo .="         </td>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .=             "$".number_format($rowCompras['montoComprasCredito'],2,".",",");
    $recibo .="         </td>";
    $recibo .="     </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $recibo .="             Pagado:";
    $recibo .="         </td>";
    $recibo .="         <td>";
    $recibo .="            &nbsp;";
    $recibo .="         </td>";
    $recibo .="         <td style='text-align:right'>";
    $totalPagadoCompras = $totCompras - $totalAbonadoCompras;
    $recibo .=             "<b>$".number_format($rowCompras['suma'],2,".",",")."</b>";
    $recibo .="         </td>";
    $recibo .="     </tr>";
    $recibo .="</table>";
    $recibo .= "___________________________________";
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


    $recibo .="<table width='100%'>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center' colspan='4'>";
    $recibo .="            <b>RETIROS</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";

$cont = 0;
while ($rowRetirosDetalle = $resultRetirosDetalle->fetch_assoc())
{
    $cont++;

    $recibo .="    <tr>";
    $recibo .="        <td>";
    $recibo .=             $cont;
    $recibo .="        </td>";
    $recibo .="        <td colspan=2>";
    $recibo .=             $rowRetirosDetalle['nombreUsuario']." ".$rowRetirosDetalle['apellidopUsuario'];
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            $".number_format($rowRetirosDetalle['esteMontoRetiro'],2,".",",");
    $recibo .="        </td>";
    $recibo .="    </tr>";
}
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            Cantidad:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>".$rowRetiros['cant']."</b>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            Total:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($rowRetiros['suma'],2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
    $recibo .= "___________________________________";
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


        $recibo .="<table width='100%'>";
        $recibo .="    <tr>";
        $recibo .="        <td style='text-align:center' colspan='4'>";
        $recibo .="            <b>INGRESOS</b>";
        $recibo .="        </td>";
        $recibo .="    </tr>";

    $cont = 0;
    while ($rowIngresosDetalle = $resultIngresosDetalle->fetch_assoc())
    {
        $cont++;

        $recibo .="    <tr>";
        $recibo .="        <td>";
        $recibo .=             $cont;
        $recibo .="        </td>";
        $recibo .="        <td colspan=2>";
        $recibo .=             $rowIngresosDetalle['nombreUsuario']." ".$rowIngresosDetalle['apellidopUsuario'];
        $recibo .="        </td>";
        $recibo .="        <td style='text-align:right'>";
        $recibo .="            $".number_format($rowIngresosDetalle['esteMontoRetiro'],2,".",",");
        $recibo .="        </td>";
        $recibo .="    </tr>";
    }
        $recibo .="    <tr>";
        $recibo .="        <td style='text-align:center'>";
        $recibo .="            Cantidad:";
        $recibo .="        </td>";
        $recibo .="        <td style='text-align:right'>";
        $recibo .=            "<b>".$rowIngresos['cant']."</b>";
        $recibo .="        </td>";
        $recibo .="        <td style='text-align:center'>";
        $recibo .="            Total:";
        $recibo .="        </td>";
        $recibo .="        <td style='text-align:right'>";
        $recibo .=            "<b>$".number_format($rowIngresos['suma'],2,".",",")."</b>";
        $recibo .="        </td>";
        $recibo .="    </tr>";
        $recibo .="</table>";
        $recibo .= "___________________________________";
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

    $recibo .="<table width='100%'>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center' colspan='2'>";
    $recibo .="            <b>NOTAS DE CRÉDITO</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Devoluciones:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            $rowNotaCred['devolucion'];
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Cambios:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            $rowNotaCred['cambio'];
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            Total:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($rowNotaCred['suma'],2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
    $recibo .= "___________________________________";
$sql = "SELECT
            IFNULL(SUM(notadesalida.montopublico),0) AS suma,
            COUNT(notadesalida.id) AS cant
            FROM notadesalida
            WHERE notadesalida.sesion = $idSesion";

$resultNotaSal = $mysqli->query($sql);
$rowNotaSal = $resultNotaSal->fetch_assoc();

    $recibo .="<table width='100%'>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center' colspan='2'>";
    $recibo .="            <b>NOTAS DE SALIDA</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Cantidad:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            $rowNotaSal['cant'];
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            Total:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($rowNotaSal['suma'],2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
    $recibo .= "___________________________________";
$sql = "SELECT
            IFNULL(SUM(pagosrecibidos.monto),0) AS suma,
            COUNT(pagosrecibidos.id) AS cant
            FROM pagosrecibidos
            WHERE pagosrecibidos.sesion = $idSesion";

$resultPagosRec = $mysqli->query($sql);
$rowPagosRec = $resultPagosRec->fetch_assoc();
$sql = "SELECT
            pagosrecibidos.id AS idPago,
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

    $recibo .="<table width='100%'>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center' colspan='4'>";
    $recibo .="            <b>PAGOS DE CLIENTES</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";

    $cont = 0;
    while ($rowListPagosRec = $resultListPagosRec->fetch_assoc())
    {
        $cont++;
        $recibo .="    <tr>";
        $recibo .="        <td style='width=5%';>";
        $recibo .=            $cont;
        $recibo .="        </td>";
        $recibo .="        <td>";
        $recibo .=            $rowListPagosRec['nombreCliente'];
        $recibo .="        </td>";
        $recibo .="        <td style='text-align:center'>";
        $recibo .=            $rowListPagosRec['metodoPago'];
        $recibo .="        </td>";
        $recibo .="        <td style='text-align:right'>";
        $recibo .="             $".number_format($rowListPagosRec['esteMontoRec'],2,".",",");
        $recibo .="        </td>";
        $recibo .="    </tr>";
    }
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center;width=5%;'>";
    $recibo .="            Cant:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .=            "<b>".$rowPagosRec['cant']."</b>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            Total:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($rowPagosRec['suma'],2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
    $recibo .= "___________________________________";
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

    $recibo .="<table width='100%'>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center' colspan='4'>";
    $recibo .="            <b>PAGOS A PROVEEDORES</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
$cont = 0;
while ($rowListPagosEm = $resultListPagosEm->fetch_assoc())
{
    $cont++;
    $recibo .="    <tr>";
    $recibo .="        <td>";
    $recibo .=            $cont;
    $recibo .="        </td>";
    $recibo .="        <td colspan='2'>";
    $recibo .=             $rowListPagosEm['nombreProveedor'];
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <label>$".number_format($rowListPagosEm['esteMontoEm'],2,".",",")."</label>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
}

    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            Cant:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .=            "<b>".$rowPagosEm['cant']."</b>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            Total:";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .=            "<b>$".number_format($rowPagosEm['suma'],2,".",",")."</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
$totalVenta  = $rowVentas['suma']; //$rowCompras['suma'];
$totalVenta -= $rowCompras['suma'];
$totalVenta -= $rowRetiros['suma'];
$totalVenta += $rowIngresos['suma'];
$totalVenta -= $rowNotaCred['suma'];
$totalVenta += $rowPagosRec['suma'];
$totalVenta -= $rowPagosEm['suma'];
$totalVenta += $diferenciaFondo;
    $recibo .= "___________________________________";
    $recibo .="<table width='100%'>";
    $recibo .="    <tr>";
    $recibo .="        <td colspan='3' style='text-align:center'>";
    $recibo .="            <b>RESUMEN</b>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>FONDO INICIAL:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            &nbsp;";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($diferenciaFondo,2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>VENTAS:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            +";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowVentas['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>PAGOS DE CLIENTES:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            +";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowPagosRec['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            &nbsp;";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            =";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($diferenciaFondo + $rowVentas['suma'] + $rowPagosRec['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>COMPRAS:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            -";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowCompras['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>RETIROS:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            -";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowRetiros['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>INGRESOS:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            -";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowIngresos['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>NOTAS DE CRÉDITO:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="            -";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowNotaCred['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'>PAGOS A PROVEEDORES:</h5>";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:center'>";
    $recibo .="           -";
    $recibo .="        </td>";
    $recibo .="        <td style='text-align:right'>";
    $recibo .="            <h5 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($rowPagosEm['suma'],2,".",",")."</b></h5>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="    <tr>";
    $recibo .="        <td style='text-align:left'>";
    $recibo .="            <h3 style='margin-top:0px;margin-bottom:0px;'>ENTREGAR:</h3>";
    $recibo .="        </td>";
    $recibo .="        <td colspan='2' style='text-align:right'>";
    $recibo .="            <h3 style='margin-top:0px;margin-bottom:0px;'><b>$".number_format($totalVenta,2,".",",")."</b></h3>";
    $recibo .="        </td>";
    $recibo .="    </tr>";
    $recibo .="</table>";
    $recibo .="</br>";
    $recibo .=".";

    return $recibo;
}
 ?>
