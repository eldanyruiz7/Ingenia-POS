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
    $tipoUsr = $sesion->get("tipousuario");
    $pagina = 7;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Corte de caja</title>
    <!-- Bootstrap Core CSS -->
    <link href="../startbootstrap/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="../startbootstrap/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../startbootstrap/dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Morris Charts CSS -->
    <link href="../startbootstrap/vendor/morrisjs/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="../startbootstrap/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .navbar-static-top
        {
            z-index: 100;
        }
        tr.success .inputCantidad
        {
            background-color: #d0e9c6;
        }
        #dataTable_filter
        {
            text-align: right;
        }
        .calc
        {
            text-align: right;
            margin-bottom: 6px;
        }
        .calcDisabled
        {
            text-align: right;
            margin-bottom: 6px;
        }
        .labelCalc
        {
            text-align: right;
            margin-top: 6px;
        }
        .inputCorte, .inputCorteRetiro
        {
            text-align: right;
        }
    </style>
</head>
<body>
    <div id="recibo" style="display:none">
    </div>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"> <i class="fa fa-desktop" aria-hidden="true"></i> Corte de caja</h1>
                </div>
                <div id="divRespuesta" class="col-lg-6 col-md-6 col-xs-6">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-11 col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <b><i class="fa fa-money" aria-hidden="true"></i> Informaci&oacute;n del corte de caja</b>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 list-group-item">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 list-group-item" style="text-align:center;margin-bottom:6px">
                                    <label>Nombre:</label> <?php echo $sesion->get("nombre")." ".$sesion->get("apellidop"); ?>
                                </div>
                                <div class="table">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="text-align:center;width:25%"></th>
                                                <th style="text-align:center;width:25%"><label>Contado</label></th>
                                                <th style="text-align:center;width:25%"><label><?php echo ($tipoUsr == 1) ? "Calculado" : "&nbsp;";?></label></th>
                                                <th style="text-align:center;width:25%"><label><?php echo ($tipoUsr == 1) ? "Diferencia" : "&nbsp;";?></label></th>
                                            </tr>
                                        </thead>
                                        <?php
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
                                                FROM retiros WHERE retiros.sesion = $idSesion AND retiros.tipo = 0";

                                        $resultRetiros = $mysqli->query($sql);
                                        $rowRetiros = $resultRetiros->fetch_assoc();

                                        $sql = "SELECT
                                                    IFNULL(SUM(retiros.monto),0) AS suma,
                                                    COUNT(retiros.id) AS cant
                                                FROM retiros WHERE retiros.sesion = $idSesion AND retiros.tipo = 1";

                                        $resultIngresos = $mysqli->query($sql);
                                        $rowIngresos = $resultIngresos->fetch_assoc();


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
                                        $totalVenta += $rowIngresos['suma'];
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

                                        //$balance = $totalVenta;
                                        $ventaEfectivo = $totalVenta - $ventaCheque - $ventaTarjeta - $ventaVales;
                                         ?>
                                        <tbody>
                                            <tr>
                                                <td style="padding-right:0px"> Efectivo</td>
                                                <td>
                                                    <div class="form-group input-group" style="margin-bottom:0px">
                                                        <a class="input-group-addon mostrarCalc" id="calcEfectivo" name="inputContadoEfectivoCorte" style="cursor:pointer"><i class="fa fa-calculator" aria-hidden="true"></i></a>
                                                        <input type="number" id="inputContadoEfectivoCorte" value="0" class="form-control inputCorte inputContado">
                                                    </div>
                                                </td>
                                        <?php
                                            if($tipoUsr == 1)
                                            {
                                        ?>
                                                <td><input type="number" id="inputCalculadoEfectivo" value="<?php echo number_format($ventaEfectivo, 2, '.', '') ?>" class="form-control inputCorte"></td>
                                                <td><input type="number" id="inputDiferenciaEfectivo" value="0" class="form-control inputCorte"></td>
                                        <?php
                                            }
                                            else
                                            {
                                        ?>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                        <?php
                                            }
                                        ?>
                                            </tr>
                                            <tr>
                                                <td>Cheque</td>
                                                <td><span><input type="number" id="inputContadoCheque" value="0" class="form-control inputCorte inputContado"></span></td>
                                        <?php
                                            if($tipoUsr == 1)
                                            {
                                        ?>
                                                <td><input type="number" id="inputCalculadoCheque" value="<?php echo number_format($ventaCheque, 2, '.', '') ?>" class="form-control inputCorte"></td>
                                                <td><input type="number" id="inputDiferenciaCheque" value="0"class="form-control inputCorte inputDiferencia"></td>
                                        <?php
                                            }
                                        ?>
                                            </tr>
                                            <tr>
                                                <td>Vales</td>
                                                <td><span><input type="number" id="inputContadoVales" value="0" class="form-control inputCorte inputContado"></span></td>
                                        <?php
                                            if($tipoUsr == 1)
                                            {
                                        ?>
                                                <td><input type="number" id="inputCalculadoVales" value="<?php echo number_format($ventaVales,2 , '.', '') ?>" class="form-control inputCorte"></td>
                                                <td><input type="number" id="inputDiferenciaVales" value="0" class="form-control inputCorte inputDiferencia"></td>
                                        <?php
                                            }
                                        ?>
                                            </tr>
                                            <tr>
                                                <td>Tarjeta</td>
                                                <td><span><input type="number" id="inputContadoTarjeta" value="0" class="form-control inputCorte inputContado"></span></td>
                                        <?php
                                            if($tipoUsr == 1)
                                            {
                                        ?>
                                                <td><input type="number" id="inputCalculadoTarjeta" value="<?php echo number_format($ventaTarjeta,2 , '.', '') ?>" class="form-control inputCorte"></td>
                                                <td><input type="number" id="inputDiferenciaTarjeta" value="0" class="form-control inputCorte inputDiferencia"></td>
                                        <?php
                                            }
                                        ?>
                                            </tr>
                                            <tr>
                                                <td><label>Total</label></td>
                                                <td><span><input type="number" id="inputContadoTotal" value="0" class="form-control inputCorte"></span></td>
                                        <?php
                                            if($tipoUsr == 1)
                                            {
                                        ?>
                                                <td><input type="number" id="inputCalculadoTotal" value="<?php echo number_format($totalVenta,2 , '.', '')?>" class="form-control inputCorte"></td>
                                                <td><input type="number" id="inputDiferenciaTotal" value="0" class="form-control inputCorte"></td>
                                        <?php
                                            }
                                        ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 list-group-item">
                                <div class="col-lg-12 list-group-item" style="margin-bottom:10px;">
                                    <label>Fondo de caja final</label>
                                </div>
                                <div class="table">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="padding-right:0px">Efectivo</td>
                                                <td>
                                                    <div class="form-group input-group" style="margin-bottom:0px">
                                                        <a class="input-group-addon mostrarCalc" name="inputContadoEfectivoRetiro" style="cursor:pointer;"><i class="fa fa-calculator" aria-hidden="true"></i></a>
                                                        <input type="number" id="inputContadoEfectivoRetiro" value="<?php echo number_format($rowSaldoInicial['saldoinicial'],2,".","");?>" class="form-control inputCorteRetiro">
                                                    </div>
                                                </td>


                                            </tr>
                                            <tr>
                                                <td>Cheque</td>
                                                <td><input value="0" min="0" type="number" class="form-control inputCorteRetiro"></td>

                                            </tr>
                                            <tr>
                                                <td>Vales</td>
                                                <td><input value="0" min="0" type="number" class="form-control inputCorteRetiro"></td>

                                            </tr>
                                            <tr>
                                                <td>Tarjeta</td>
                                                <td><input value="0" min="0" type="number" class="form-control inputCorteRetiro"></td>

                                            </tr>
                                            <tr>
                                                <td><label>Total</label></td>
                                                <td><input value="0" min="0" type="number" class="form-control" id="inputDiferenciaTotalCorte" style="text-align:right"></td>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="col-lg-6 col-lg-offset-3" style="text-align:center">
                                        <button type="button" id="btnGuardar" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../startbootstrap/vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Morris Charts JavaScript -->
    <script src="../startbootstrap/vendor/raphael/raphael.min.js"></script>
    <script src="../startbootstrap/vendor/morrisjs/morris.min.js"></script>
    <script src="../startbootstrap/data/morris-data.js"></script>
    <!-- DataTables Javascript -->
    <script src="../startbootstrap/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-responsive/dataTables.responsive.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../startbootstrap/dist/js/sb-admin-2.js"></script>
    <script src="../startbootstrap/vendor/jsBarcode/JsBarcode.all.min.js"></script>
    <script src="../startbootstrap/vendor/printThis/printThis.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    function calcularTotalCalc()
    {
        s = 0;
        $(".calcDisabled").each(function()
        {
            s = s + parseFloat($(this).val());
        });
        $("#calcTotal").val(s.toFixed(2));
    }
    function calcularContadoTotal()
    {
        s       = 0;
        dif     = 0;
        difTotal  = parseFloat(0);
        $(".inputContado").each(function()
        {
            x = parseFloat($(this).val());
            s = s + x;
            calc = parseFloat($(this).parent().parent().next().find("input").val());
            dif = parseFloat(x) - parseFloat(calc);
            difTotal = parseFloat(difTotal) + parseFloat(dif);
            input = $(this).parent().parent().next().next().find("input");
            input.val(dif.toFixed(2));
        });
        $("#inputDiferenciaTotal").val(difTotal.toFixed(2));
        $("#inputContadoTotal").val(s.toFixed(2));
    }
    function calcularContadoTotalRetiro()
    {
        tot = 0;
        $(".inputCorteRetiro").each(function()
        {
            tot += parseFloat($(this).val());
        });
        $("#inputDiferenciaTotalCorte").val(tot.toFixed(2));
    }
    $(document).ready(function()
    {
        $(".mostrarCalc").click(function()
        {
            $(".calc").val("0");
            $(".calcDisabled").val("0");
            $("#calcTotal").val("0");
            name = this.name;
            $("#dialog-contador-efectivo").data('name',name).dialog("open");
        });
        $(document).on("keyup change",".calc",function()
        {
            denominacion = $(this).attr("name");
            cantidad = $(this).val();
            subTotal = denominacion * cantidad;
            $(this).parent().next().next().find(".calcDisabled").val(subTotal.toFixed(2));
            calcularTotalCalc();
        });
        $(document).on("keyup change",".inputContado",function()
        {
            if($(this).val().length == 0)
                $(this).val("0");
            calcularContadoTotal();
        });
        $(document).on("keyup change",".inputCorteRetiro",function()
        {
            if($(this).val().length == 0)
                $(this).val("0");
            calcularContadoTotalRetiro();
        });
        $( "#dialog-realizar-corte" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
            modal: true,
            autoOpen: false,
            position: { my: 'top', at: 'top+1' },
            show:
            {
                effect: "slide",
                duration: 300,
                direction: 'up'
            },
            buttons:
            [
                {
                    text: "Aceptar",
                    id: "btnRealizarCorte",
                    click: function()
                    {
                        dialogo = $(this);
                        efectivo = $("#inputContadoEfectivoCorte").val();
                        cheque = $("#inputContadoCheque").val();
                        vales = $("#inputContadoVales").val();
                        tarjeta = $("#inputContadoTarjeta").val();
                        retiro = $("#inputDiferenciaTotalCorte").val();
                        btnRealizarCorte = $("#btnRealizarCorte");
                        btnCancelarCorte = $("#btnCancelarCorte");
                        btnRealizarCorte.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                        btnRealizarCorte.prop("disabled", true);
                        btnCancelarCorte.prop("disabled", true);
                        dialogo = $(this);
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/corteDeCajaTurno.php",
                            data: {efectivo:efectivo,cheque:cheque,vales:vales,tarjeta:tarjeta,retiro:retiro}
                        })
                        .done(function(p)
                        {
                            if(p.error == 1)
                            {
                                dialogo.dialog( "close" );
                                $("#divRespuesta").html(p.respuesta);
                            }
                            else
                            {
                                $('#recibo').html(p.recibo).promise().done(function()
                                {
                                    //your callback logic / code here
                                    JsBarcode("#code_svg",p.codigo,
                                    {
                                        width:2,
                                        height:35,
                                        fontSize:12,
                                        margin:1
                                    });
                                    $('#recibo').printThis();
                                    setTimeout(function()
                                    {
                                        //dialogo.dialog( "close" );
                                        window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/salir.php");
                                    }, 2250);
                                });
                            }
                        })
                        .fail(function()
                        {
                            alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                            dialogo.dialog( "close" );
                            btnRealizarCorte.html('Aceptar');
                            btnRealizarCorte.prop("disabled", false);
                            btnCancelarCorte.prop("disabled", false);
                        })
                        .always(function(p)
                        {
                            console.log(p);
                        });

                    }
                },
                {
                    text: "Cancelar",
                    id: "btnCancelarCorte",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $( "#dialog-contador-efectivo" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: 550,
            modal: true,
            autoOpen: false,
            show:
            {
                effect: "scale",
                duration: 250
            },
            open: function()
            {
            },
            close: function()
            {
                dialogoAbierto = 0;
            },
            buttons:
            [
                {
                    text: "Aceptar",
                    'id': "btnAgregarEfectivoCalc",
                    click: function()
                    {
                        ipt = $( "#dialog-contador-efectivo" ).data('name');
                        calcTotal = parseFloat($("#calcTotal").val());
                        $("#"+ipt).val(calcTotal.toFixed(2));
                        if(ipt == "inputContadoEfectivoCorte")
                        {
                            diferencia = parseFloat(calcTotal) - parseFloat($("#inputCalculadoEfectivo").val());
                            calcularContadoTotal();
                            $("#inputDiferenciaEfectivo").val(diferencia.toFixed(2));
                        }
                        else
                        {
                            calcularContadoTotalRetiro();
                        }
                        $("#"+ipt).focus();
                        $( this ).dialog( "close" );
                    }
                },
                {
                    text: "Cancelar",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $("input.calc").focus(function()
        {
            this.select();
        });
        $("input.calc").keydown(function(e)
        {
            if(e.keyCode == 40 || e.keyCode == 13)
            {
                e.preventDefault();
                if($(this).attr('name') == '0.1')
                {
                    $("#btnAgregarEfectivoCalc").click();
                    return false;
                }
                $(this).parent().next().next().next().find("input.calc").focus();
            }
            if(e.keyCode == 38)
            {
                e.preventDefault();
                $(this).parent().prev().prev().prev().find("input.calc").focus();
            }
        });
        $("#btnGuardar").click(function()
        {
            $( "#dialog-realizar-corte" ).dialog("open");
        });
        calcularContadoTotalRetiro();
        $("#calcEfectivo").click();
    });


    </script>
</body>

</html>
<div id="dialog-realizar-corte" title="Realizar corte">
    <p>
        <i class="fa fa-pull-left fa-exclamation-triangle fa-2x" aria-hidden="true"></i> ¿Est&aacute;s seguro que deseas realizar el corte de caja? <br> Se cerrar&aacute; la sesi&oacute;n al pulsar Aceptar
    </p>
</div>
<div id="dialog-contador-efectivo" title="Contador de contado">
    <div class="col-lg-12" style="text-align:right">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 list-group-item" style="text-align:center;margin-bottom:6px">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <label>CANTIDAD</label>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <label>DENOMINACI&Oacute;N</label>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                <label>TOTAL</label>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="1000" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 1,000.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="500" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 500.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="200" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 200.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="100" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 100.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="50" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 50.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="20" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 20.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="10" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 10.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="5" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 5.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="2" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 2.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="1" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 1.00</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="0.5" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 0.50</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name=".2" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 0.20</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" name="0.1" min="0" class="form-control calc" value="0">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 labelCalc">
            <label class="">x $ 0.10</label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" min="0" class="form-control calcDisabled" value="0.00" disabled="disabled">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 col-lg-offset-4 col-md-offset-4 col-sm-offset-4 col-xs-offset-4 labelCalc">
            <label style="color:blue">TOTAL-></label>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <input type="number" class="form-control" value="0.00" disabled="disabled" style="color:blue;font-weight:bold;text-align:right" id="calcTotal">
        </div>
    </div>

</div>
<?php
}
?>
