<?php
/*    error_reporting(E_ALL);
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
        function validarFecha($date, $format)
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }
        $consultar = 1;
   // Aquí va el contenido de la pagina qu se mostrara en caso de que se haya iniciado sesion
?>
<!DOCTYPE html>
<html lang="en">
<?php $ipserver = $_SERVER['SERVER_ADDR']; ?>
<?php require_once '../conecta/bd.php';?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Reporte de facturas emitidas</title>
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
        .dataTables_filter
        {
            text-align: right;
        }
        .floatDetalle
        {
            position: fixed;
            top: 10px;
        }
        .dialog-oculto
        {
            display: none;
        }
        .aFacturar
        {
            cursor: pointer;
        }
        .inactive
        {
            background-color: #ececec;
            color: #b9b9b9;
        }
        /*.inputSAT, .inputIva, .inputIeps, .inputDescuento
        {
            text-align: right;
            -moz-appearance:textfield;
            border-style: none;
            background-color: transparent;
        }
        .inputSAT, .inputIva, .inputIeps, .inputDescuento::-webkit-inner-spin-button
        {
            -webkit-appearance: none;
            margin: 0;
            border-style: none;
            background-color: transparent;
            width: 55px;
        }
        .inputIva, .inputIeps, .inputDescuento
        {
            width: 55px;
        }*/
    </style>
</head>
<body>
    <div id="recibo" style="display:none"></div>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
                    <h1 class="page-header"> <i class="fa fa-rocket" aria-hidden="true"></i> Reporte de facturas emitidas</h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
<?php
        if(isset($_POST['selectCliente']) == false)
            $idCliente = 0;
        else
            $idCliente  = $_POST['selectCliente'];
        if ($idCliente == 0)
            $consultarXCliente = "";
        else
            $consultarXCliente = "AND facturas.idReceptor = $idCliente";
        if (isset($_POST['fechaInicio']) && isset($_POST['fechaFin']))
        {
            if(validarFecha($_POST['fechaInicio'], 'Y-m-d') && validarFecha($_POST['fechaFin'], 'Y-m-d') && $_POST['fechaInicio'] <= $_POST['fechaFin'])
            {
                $fechaInicio    = $_POST['fechaInicio'];
                $fechaFin       = $_POST['fechaFin'];
            }
            else
            {
                $text = ($_POST['fechaInicio'] > $_POST['fechaFin']) ? "La fecha de inicio no puede ser mayor que la fecha fin." : "No es un rango de fecha v&aacute;lida.";
?>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="divRespuesta" style="margin-left:15px">
                    <div class="alert alert-danger alert-dismissable" style="margin-right:15px">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        <?php echo $text; ?>
                    </div>
                </div>
<?php
                $consultar = 0;
                $fechaInicio = $_POST['fechaInicio'];
                $fechaFin = $_POST['fechaFin'];
            }
        }
        else
        {
            $fechaInicio = date('Y-m-01');
            $fechaFin = date('Y-m-d');
        }

 ?>
            </div>
            <div class="row">
                <form role="form" method="POST">
                    <div class="col-lg-12">
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    &nbsp;
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Fecha Inicio</label>
                                        <input type="date" required value="<?php echo $fechaInicio;?>" class="form-control" placeholder="Enter text" id="fechaInicio" name="fechaInicio">
                                    </div>
                                </div>
                                <div class="panel-footer" style="padding:15px 10px 15px 10px">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    &nbsp;
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input type="date" required value="<?php echo $fechaFin;?>" class="form-control" placeholder="Enter text" id="fechaFin" name="fechaFin">
                                    </div>
                                </div>
                                <div class="panel-footer" style="padding:15px 10px 15px 10px">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4" style="padding-right:0px">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    &nbsp;
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            Exportar <i class="fa fa-chevron-down"></i>
                                        </button>
                                        <ul class="dropdown-menu slidedown">
                                            <li>
                                                <a href="#" id="menu-exportExcel"> <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel </a>
                                            </li>
                                            <li>
                                                <a href="#" id="menu-exportPDF"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                            <label>Mostrar por cliente</label>
                                            <select class="form-control" id="selectCliente" name="selectCliente">
                                                <option value="0">Todos</option>
    <?php
        $sql = "SELECT id, rsocial FROM clientes ORDER BY rsocial ASC";
        $res_cli = $mysqli->query($sql);
        while ($row_cli = $res_cli->fetch_assoc())
        {
            $id_cli = $row_cli['id'];
            $rs_cli = $row_cli['rsocial'];
            if($idCliente == $id_cli)
                echo "<option selected value='$id_cli'>$rs_cli</option>";
            else
                echo "<option value='$id_cli'>$rs_cli</option>";
        }

     ?>
                                            </select>
                                        </div>
                                </div>
                                <div class="panel-footer" style="text-align:right">
                                    <button id="btnCalcular" type="submit" class="btn btn-primary btn-sm"><i class="fa fa-calculator" aria-hidden="true"></i> Calcular</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                <form id="formReporte" target="_blank" method="post" action="../control/reporteVenta.php">
                    <button type="submit" id="btn-exportPDF" class="btn btn-danger btn-sm" style="display:none"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exportar</button>
                    <input type="hidden" id="r" name="r">
                    <input type="hidden" id="fi" name="fi">
                    <input type="hidden" id="ff" name="ff">
                </form>
                <form id="formReporteXLS" target="_blank" method="post" action="../control/reporteVenta.xlsx.php">
                    <button type="submit" id="btn-exportXLS" class="btn btn-success btn-sm" style="display:none"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar</button>
                    <input type="hidden" id="rx" name="rx">
                    <input type="hidden" id="fix" name="fix">
                    <input type="hidden" id="ffx" name="ffx">
                </form>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding-right:0px">
                <div class="panel panel-default">
                    <div class="panel-heading" style="padding-bottom:18px">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Lista de los CFDI emitidos

                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <table id="example" class="display compact table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>No.</th>
                                        <th>M&eacute;todo</th>
                                        <th>Status</th>
                                        <th>Id Venta(s) Rel</th>
                                        <th>Fecha</th>
                                        <th>Nombre Receptor</th>
                                        <th>RFC Receptor</th>
                                        <th>Factur&oacute;</th>
                                        <th>IVA</th>
                                        <th>IEPS</th>
                                        <th>SUB TOT</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
            <?php
                $facturable = 0;
                if ($consultar == 1)
                {
                    $fechaInicioSQL = $fechaInicio." 00:00:00";
                    $fechaFinSQL = $fechaFin." 23:59:59";
                    $sql = "SELECT
                                facturas.id                 AS id,
                                facturas.rfcEmisor          AS rfcEmisor,
                                facturas.rfcReceptor        AS rfcReceptor,
                                facturas.razonReceptor      AS razonReceptor,
                                facturas.regimenReceptor    AS regimenReceptor,
                                facturas.calleReceptor      AS calleReceptor,
                                facturas.coloniaReceptor    AS coloniaReceptor,
                                facturas.municipioReceptor  AS municipioReceptor,
                                facturas.numeroExtReceptor  AS numeroExtReceptor,
                                facturas.numeroIntReceptor  AS numeroIntReceptor,
                                facturas.poblacionReceptor  AS poblacionReceptor,
                                facturas.entidadReceptor    AS entidadReceptor,
                                facturas.cpReceptor         AS cpReceptor,
                                facturas.idReceptor         AS idReceptor,
                                facturas.emailReceptor      AS emailReceptor,
                                facturas.fechaCertificacion AS fechaCertificacion,
                                facturas.folioFiscal        AS folioFiscal,
                                facturas.metodoPago         AS metodoPago,
                                facturas.usuario            AS usuario,
                                facturas.totalIVA           AS iva,
                                facturas.totalIEPS          AS ieps,
                                facturas.subTotal           AS subTotal,
                                facturas.total              AS total,
                                facturas.pagado             AS pagado,
                                facturas.idVentasRelacion   AS idVentasRelacion,
                                usuarios.nombre             AS nombreUsuario,
                                usuarios.apellidop          AS apellidopUsuario
                            FROM facturas
                            INNER JOIN usuarios
                            ON facturas.usuario = usuarios.id
                            WHERE       (facturas.timestamp BETWEEN '$fechaInicioSQL' AND '$fechaFinSQL') $consultarXCliente AND facturas.tipoCFDI = 'I'
                            ORDER BY    facturas.id ASC";
                    if ($resultadoVentas    = $mysqli->query($sql))
                    {
                        $totalFilas         = $resultadoVentas->num_rows;
                        $total_IVA          = 0;
                        $total_IEPS         = 0;
                        $total_subTotal     = 0;
                        $total_total        = 0;
                        while ($rowVenta    = $resultadoVentas->fetch_assoc())
                        {
                            // $estePagado             = ($rowVenta['esCredito'] == 0) ? $rowVenta['totalVenta_'] : $rowVenta['montoPagado'];
                            // $esteCredito            = ($rowVenta['esCredito'] == 0) ? 0 : $rowVenta['totalVenta_'] - $rowVenta['montoPagado'];
                            /*$estaVenta              = $rowVenta['totalVenta_'];
                            $totPagado              += $estePagado;
                            $totCredito             += $esteCredito;
                            $totVenta               += $rowVenta['totalVenta_'];
                            /*$fechaVenta             = date('d-m-Y',strtotime($rowVenta['fechaHora']));
                            $horaVenta              = date('H:i:s',strtotime($rowVenta['fechaHora']));*/
                            // $nombreCompCliente      = $rowVenta['nombreCliente'];
                            // $nombreCompCajero       = $rowVenta['nombreCajero']." ".$rowVenta['apellidopCajero'];
                            $metodoPago_desc= $rowVenta['metodoPago'];
            ?>

                                <tr class="rowReporte" name="<?php echo $rowVenta['id'];?>" email-receptor="<?php echo $rowVenta['emailReceptor'];?>" razon-receptor="<?php echo $rowVenta['razonReceptor'];?>" metodo-pago="<?php echo $metodoPago_desc?>">
                                    <td>
                                        <div class="btn-group pull-left">
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </button>
                                            <ul class="dropdown-menu slidedown">
                                            <!-- <li>
                                                <a class="aReimprimirTkt" name="<?php //echo $rowVenta['id'];?>">
                                                    <i class="fa fa-print" aria-hidden="true"></i> Reimprimir
                                                </a>
                                            </li> -->
                                            <li>
                                                <a class="aCancelar" name="<?php echo $rowVenta['idVenta_'];?>">
                                                    <i class="fa fa-times-circle" aria-hidden="true"></i> Cancelar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <?php echo $rowVenta['id'];?>
                                </td>
                                <td>
            <?php
                    $metodo_f = ($rowVenta['metodoPago'] == 'PUE') ? '<span class="label label-success"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> PUE</span>' : '<span class="label label-primary"><i class="fa fa-calendar-times-o" aria-hidden="true"></i> PPD</span>';
                    echo $metodo_f;
            ?>
                                </td>
                                <td class="text-center">
                                    <?php echo ($rowVenta['pagado'] == 1) ? '<span class="label label-default">Pagada <i class="fa fa-check" aria-hidden="true"></i></span>' : '<span class="label label-danger">No pagada <i class="fa fa-times" aria-hidden="true"></i></span>'; ?>
                                </td>
                                <td>
                                    <?php echo $rowVenta['idVentasRelacion'];?>
                                </td>
                                <td class="text-center">
                                    <?php echo $rowVenta['fechaCertificacion']; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $rowVenta['razonReceptor']; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $rowVenta['rfcReceptor']; ?>
                                </td>
                                <td class="text-left">
                                    <?php echo $rowVenta['nombreUsuario']." ".$rowVenta['apellidopUsuario']; ?>
                                </td>
                                <td class="text-right">
                                    <?php echo "$".number_format($rowVenta['iva'],2,".",","); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo "$".number_format($rowVenta['ieps'],2,".",","); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo "$".number_format($rowVenta['subTotal'],2,".",","); ?>
                                </td>
                                <td class="text-right">
                                    <?php echo "$".number_format($rowVenta['total'],2,".",","); ?>
                                </td>
                            </tr>
                <?php
                        $total_IVA      += $rowVenta['iva'];
                        $total_IEPS     += $rowVenta['ieps'];
                        $total_subTotal += $rowVenta['subTotal'];
                        $total_total    += $rowVenta['total'];
                        }
                    }
                }
                ?>
                                </tbody>
                            </table>
                            <div class="col-lg-3 col-lg-offset-9  col-md-3 col-md-offset-9 col-sm-4 col-sm-offset-8" style="margin-bottom:15px;padding-right:0px">
                                <table class="pull-right table table-striped table-bordered table-hover table-condensed">
                                    <tr>
                                        <td class="text-center" colspan="2">
                                                TOTALES
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">
                                            IVA
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($total_IVA,2,".",","); ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">
                                            IEPS
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($total_IEPS,2,".",","); ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">
                                            Sub Total:
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($total_subTotal,2,".",","); ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">
                                            Total:
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($total_total,2,".",","); ?></label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>

        <!-- /#page-wrapper -->
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding-right:0px" >
            <div class="panel panel-default" id="panelDetalle">
                <div class="panel-heading" style="padding-bottom:18px">
                    <i class="fa fa-info-circle" aria-hidden="true"></i> Vista previa
                        <div class="pull-right">
                            <div class="btn-group tooltip-demo">
                                <button id="aComplemento" type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Agregar Compl. Pago"><i class="fa fa-plus-square" aria-hidden="true"></i></button>
                                <button id="aReenviar" type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Reenviar CFDI"><i class="fa fa-share-square-o" aria-hidden="true"></i></button>
                            </div>
                        </div>
                </div>
                <div class="panel-body" id="divDetalle">
                    <iframe width="100%" height="500px" id="vp_facturaPDF"></iframe>
                </div>
            </div>
            <!-- /.col-lg-8 (nested) -->
        </div>
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
    <script src="../control/custom-js/dialogosFactura.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    widthFlotante = $("#panelDetalle").width();
        $(document).ready(function()
        {
            // tooltip demo
            $('.tooltip-demo').tooltip({
                selector: "[data-toggle=tooltip]",
                container: "body"
            });
            var table = $('#example').DataTable(
             {
                "lengthMenu": [[10, 20, 50, 100, -1],[10, 20, 50, 100, "Todos"]],
                "language":
                {
                     "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
                 },
                 responsive: true,
                 "order": [[ 1, "desc" ]],
                 "initComplete": function(settings, json)
                 {
                     $("#example > tbody > tr:nth-child(1)").click();
                    //#example > tbody > tr:nth-child(1)
                 }
            });
            $("#fechaInicio,#fechaFin,#selectCliente").change(function()
            {
                $("#btnCalcular").click();
            });
            $(".rowReporte").click(function()
            {
                $(".rowReporte").removeClass("info");
                $(this).addClass("info");
                idVenta = $(this).attr("name");
                $("#vp_facturaPDF").attr('src','../ws/gen_vpreviaFacturaPDF.php?id='+idVenta+'&vista=1');
            });
            $("#aReenviar").click(function()
            {
                $(".rowReporte").each(function()
                {
                    if ($(this).hasClass("info"))
                    {
                        idFactura       = $(this).attr("name");
                        emailReceptor   = $(this).attr("email-receptor");
                        razonReceptor   = $(this).attr("razon-receptor");
                        $( "#dialog-reenviar-cfdi" ).data('idFactura',idFactura).data('emailReceptor',emailReceptor).data('razonReceptor',razonReceptor).dialog("open");
                    }
                });
            });
            $("#aComplemento").click(function()
            {
                $(".rowReporte").each(function()
                {
                    if ($(this).hasClass("info") && $(this).attr("metodo-pago") != 'PUE')
                    {
                        idFactura       = $(this).attr("name");
                        emailReceptor   = $(this).attr("email-receptor");
                        razonReceptor   = $(this).attr("razon-receptor");
                        $.ajax(
                        {
                            method: "POST",
                            url:"../ajax/revMontosPosiblesCompPago.php",
                            data: {idFactura:idFactura}
                        })
                        .done(function(p)
                        {
                            if (p.status == 1)
                            {
                                montoPagado     = p.montoPagado;
                                contPagos       = p.contPagos;
                                montoFactura    = p.montoFactura;
                                saldo           = p.saldo;
                                $( "#dialog-agregar-comp" )
                                    .data('idFactura',      idFactura)
                                    .data('emailReceptor',  emailReceptor)
                                    .data('razonReceptor',  razonReceptor)
                                    .data('montoPagado',    montoPagado)
                                    .data('contPagos',      contPagos)
                                    .data('montoFactura',   montoFactura)
                                    .data('saldo',          saldo)
                                    .dialog("open");
                            }
                            console.log(p);
                        })
                        .always(function(p)
                        {
                            console.log(p);
                        });
                    }
                });
            });
            $("#divRevFactura").on("keyup change",".inputSAT,.inputIva,.inputIeps,.inputDescuento",reCalcularFactura);
            $("#divRevFactura").on("focus",".inputSAT,.inputIva,.inputIeps,.inputDescuento",function()
            {
                $(this).select();
            });
            $("#divRevFactura").on("focusout",".inputIva,.inputIeps,.inputDescuento",function()
            {
                val = $(this).val();
                if (isNaN(val) || val.length == 0)
                {
                    val = 0;
                }
                val = parseFloat(val);
                if (val > 0)
                {
                    val = val.toFixed(2);
                }
                $(this).val(val);
                reCalcularFactura();
            });
            $( "#dialog-reenviar-cfdi" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 450,
                modal: true,
                autoOpen: false,
                closeOnEscape: true,
                //position:['top', "middle"],
                position: { my: 'top', at: 'top+1' },
                //position: "center",
                show:
                {
                    effect: "slide",
                    duration: 300,
                    direction: 'up'
                },
                open: function()
                {
                    idFactura       = $( "#dialog-reenviar-cfdi" ).data('idFactura');
                    emailReceptor   = $( "#dialog-reenviar-cfdi" ).data('emailReceptor');
                    razonReceptor   = $( "#dialog-reenviar-cfdi" ).data('razonReceptor');
                    $('#inputEmail').val(emailReceptor);
                    $("#pIdFactura").text(idFactura);
                    $("#pRazonReceptor").text(razonReceptor);
                },
                close: function()
                {
                    $("#divRespuestaModal").empty();
                },
                buttons:
                [
                    {
                        text: "Reenviar",
                        id: "btnReenviarCfdi",
                        click: function()
                        {
                            eMail       = $('#inputEmail').val();
                            idFactura   = $("#dialog-reenviar-cfdi" ).data('idFactura');
                            opciones    = $("input:radio[name=optionsRadios]:checked").val();
                            $("#btnReenviarCfdi").attr("disabled", true);
                            $("#btnCancelarReenviarCfdi").attr("disabled", true);
                            $("#btnReenviarCfdi").html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i> Enviando...');
                            $('#inputEmail').attr("disabled", true);

                            $.ajax(
                            {
                                method: "POST",
                                url: "../ws/reenviarCFDI.php",
                                data: {idFactura:idFactura,eMail:eMail,opciones:opciones}
                            })
                            .done(function(e)
                            {
                                $("#btnReenviarCfdi").attr("disabled", false);
                                $("#btnCancelarReenviarCfdi").attr("disabled", false);
                                $("#btnReenviarCfdi").html('Reenviar');
                                $('#inputEmail').attr("disabled", false);
                                $("#divRespuestaModal").html(e.respuesta);
                            });
                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarReenviarCfdi",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-agregar-comp" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                autoOpen: false,
                closeOnEscape: true,
                //position:['top', "middle"],
                position: { my: 'top', at: 'top+1' },
                //position: "center",
                show:
                {
                    effect: "slide",
                    duration: 300,
                    direction: 'up'
                },
                open: function()
                {
                    idFactura       = $( "#dialog-agregar-comp" ).data('idFactura');
                    emailReceptor   = $( "#dialog-agregar-comp" ).data('emailReceptor');
                    razonReceptor   = $( "#dialog-agregar-comp" ).data('razonReceptor');
                    montoPagado     = $( "#dialog-agregar-comp" ).data('montoPagado');
                    contPagos       = $( "#dialog-agregar-comp" ).data('contPagos');
                    montoFactura    = $( "#dialog-agregar-comp" ).data('montoFactura');
                    saldo           = $( "#dialog-agregar-comp" ).data('saldo');
                    $('#inputEmail-comp').val(emailReceptor);
                    $("#pIdFactura-comp").text(idFactura);
                    $("#pRazonReceptor-comp").text(razonReceptor);
                    $("#pMontoFactura-comp").text("$"+montoFactura);
                    $("#pMontoPagado-comp").text("$"+montoPagado);
                    $("#pPagosEmitidos-comp").text(contPagos);
                    $("#pSaldo-comp").text("$"+saldo);
                },
                close: function()
                {
                    $("#divRespuestaModal-comp").empty();
                },
                buttons:
                [
                    {
                        text: "Facturar",
                        id: "btnReenviarCfdi-comp",
                        click: function()
                        {
                            enviarA         = $('#inputEmail-comp').val();
                            idFactura       = $("#dialog-agregar-comp" ).data('idFactura');
                            montoEmitir     = $("#inputMonto-comp").val();
                            formaPago       = $("#selectFormaPago-comp").find('option:selected').attr("name");
                            //opciones    = $("input:radio[name=optionsRadios]:checked").val();
                            $("#ifram").css("visibility","visible");
                            $("#ifram").attr('src','../ws/gen_complemento_pago.php?idFactura='+idFactura+'&enviarA='+enviarA+'&enviar=1&formaPago='+formaPago+'&montoEmitir='+montoEmitir);
                            //$('iframe').attr('src', $('iframe').attr('src'));
                            $("#btnReenviarCfdi-comp").attr("disabled", true);
                            $("#btnCancelarReenviarCfdi-comp").attr("disabled", true);
                            $("#btnReenviarCfdi-comp").html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i> Facturando...');
                            $('#inputEmail').attr("disabled", true);

                            $("#btnReenviarCfdi-comp").attr("disabled", false);
                            $("#btnCancelarReenviarCfdi-comp").attr("disabled", false);
                            $("#btnReenviarCfdi-comp").html('Facturar');
                            $('#inputEmail-comp').attr("disabled", false);
                            $("#divRespuestaModal").html(e.respuesta);

                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarReenviarCfdi",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $(document).on( 'scroll', function()
            {
                //
                var sv = $(this).scrollTop();
                console.log('Event Fired: '+sv);
                panel = $("#panelDetalle");
                if (sv >= 371)
                {
                    if (panel.hasClass("floatDetalle") == false)
                    {
                        panel.addClass("floatDetalle");
                        panel.width(widthFlotante);
                    }
                }
                else
                {
                    if (panel.hasClass("floatDetalle") == true)
                    {
                        panel.removeClass("floatDetalle");
                    }
                }
            });
            $('#menu-exportExcel').on('click', function()
            {
                $('#btn-exportXLS').click();
            });
            $("#menu-exportPDF").click(function()
            {
                $('#btn-exportPDF').click();
            });
            $(".aReimprimirTkt").click(function()
            {
                nombre = $(this).attr('name');
                $.ajax(
                {
                    method: "POST",
                    url:"../control/reimprimirVentaRecibo.php",
                    data: {id:nombre}
                })
                .done(function(p)
                {
                    $("#recibo").html(p.recibo).promise().done(function()
                    {
                        JsBarcode("#code_svg",p.codigo,
                        {
                            width:2,
                            height:35,
                            fontSize:13,
                            margin:1
                        });
                        $('#recibo').printThis();
                    });
                });
            });
            $("#formReporte").submit(function(ev)
            {
                //ev.preventDefault();
                coleccionTabla = [];
                $('.rowReporte').each(function(x)
                {
                    coleccionCeldas = [];
                        $(this).children('td').each(function(y)
                        {
                            if(y != 0)
                                coleccionCeldas[y] = $(this).text().trim();
                        });
                    esteRow = new rowColeccionable(coleccionCeldas[1], coleccionCeldas[2], coleccionCeldas[3], coleccionCeldas[4], coleccionCeldas[5], coleccionCeldas[6], coleccionCeldas[7], coleccionCeldas[8], coleccionCeldas[9]);
                    coleccionTabla.push(esteRow);
                });
                coleccionTablaJSON = JSON.stringify(coleccionTabla);
                $("#r").val(coleccionTablaJSON);
                $("#fi").val($("#fechaInicio").val());
                $("#ff").val($("#fechaFin").val());
            });
            $("#formReporteXLS").submit(function(ev)
            {
                //ev.preventDefault();
                coleccionTabla = [];
                $('.rowReporte').each(function(x)
                {
                    coleccionCeldas = [];
                        $(this).children('td').each(function(y)
                        {
                            if(y != 0)
                                coleccionCeldas[y] = $(this).text().trim();
                        });
                    esteRow = new rowColeccionable(coleccionCeldas[1], coleccionCeldas[2], coleccionCeldas[3], coleccionCeldas[4], coleccionCeldas[5], coleccionCeldas[6], coleccionCeldas[7], coleccionCeldas[8], coleccionCeldas[9]);
                    coleccionTabla.push(esteRow);
                });
                coleccionTablaJSON = JSON.stringify(coleccionTabla);
                $("#rx").val(coleccionTablaJSON);
                $("#fix").val($("#fechaInicio").val());
                $("#ffx").val($("#fechaFin").val());
            });

        });

    </script>
</body>
<div id="dialog-reenviar-cfdi" class="dialog-oculto" title="Reenviar CFDI">
    <p>
        <div class="col-lg-12" id="divRespuestaModal">
        </div>
        <div class="col-lg-12" id="form">
            <div class="form-group form-inline" style="margin-bottom:0px">
                <label>Factura No.</label>
                <p class="form-control-static" id="pIdFactura"></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:4px">
                <label>Cliente</label>
                <p class="form-control-static" id="pRazonReceptor"></p>
            </div>
            <div class="form-group">
                <label>Direcci&oacute;n e-mail</label>
                <input class="form-control" id="inputEmail">
                <p class="help-block">E-mail del cliente a donde se reenviar&aacute; el CFDI</p>
            </div>
            <div class="form-group">
                <label>Opciones</label>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="1" checked=""> Adjuntar PDF y XML <i class="fa fa-file-pdf-o" aria-hidden="true"></i> <i class="fa fa-file-code-o" aria-hidden="true"></i>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="2"> Adjuntar solo PDF <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="optionsRadios" id="optionsRadios3" value="3"> Adjuntar solo XML <i class="fa fa-file-code-o" aria-hidden="true"></i>
                    </label>
                </div>
            </div>
        </div>
    </p>
</div>
<div id="dialog-agregar-comp" class="dialog-oculto" title="Generar Complemento de pago">
    <p>
        <div class="col-lg-12" id="divRespuestaModal-comp">
        </div>
        <div class="col-lg-12" id="form">
            <div class="form-group form-inline" style="margin-bottom:0px">
                <label>Factura No.</label>
                <p class="form-control-static" id="pIdFactura-comp"></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:4px">
                <label>Cliente</label>
                <p class="form-control-static" id="pRazonReceptor-comp"></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px">
                <label>Monto Factura</label>
                <p class="form-control-static" id="pMontoFactura-comp"></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px">
                <label>Monto Pagado</label>
                <p class="form-control-static" id="pMontoPagado-comp"></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px">
                <label>Pagos emitidos</label>
                <p class="form-control-static" id="pPagosEmitidos-comp"></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px">
                <label>Saldo</label>
                <p class="form-control-static" id="pSaldo-comp"></p>
            </div>



            <div class="form-group">
                <label>Monto</label>
                <input type="number" min="0" class="form-control" id="inputMonto-comp" style="text-align:right">
            </div>
            <div class="form-group">
                <label>Forma de pago:</label>
                <select id="selectFormaPago-comp" class="form-control">
        <?php
                $sql = "SELECT * FROM metodosdepago";
                $res_m = $mysqli->query($sql);
                while ($row_m   = $res_m->fetch_assoc())
                {
                    $bancarizado= $row_m['bancarizado'];
                    $id_m_sat   = $row_m['c_FormaPago'];
                    $nombre_m   = $row_m['nombre'];
                    echo "<option value='$bancarizado' name='$id_m_sat'>$id_m_sat - $nombre_m</option>";
                }
        ?>
                </select>
            </div>
            <div class="form-group">
                <label>Direcci&oacute;n e-mail</label>
                <input class="form-control" id="inputEmail-comp">
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-left:0px;padding-left:0px;padding-right:0px">
                <iframe id="ifram" style="width:100%;height:100px;border-style:none;"></iframe>
            </div>
        </div>
    </p>
</div>
</html>
<?php
}
?>
