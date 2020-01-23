<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
    date_default_timezone_set('America/Mexico_City');
    $ipserver = $_SERVER['SERVER_ADDR'];
    require_once ('../conecta/bd.php');
    require_once ("../conecta/sesion.class.php");
    $sesion = new sesion();
    require_once ("../conecta/cerrarOtrasSesiones.php");
    require_once ("../conecta/usuarioLogeado.php");
    if($sesion->get('tipousuario') != 1)
    {
        header("Location: /pventa_std/pages/index.php");
    }
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

    <title>Reporte de ventas</title>
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
                    <h1 class="page-header"> <i class="fa fa-file-text-o" aria-hidden="true"></i> Reporte de ventas</h1>
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
            $consultarXCliente = "AND ventas.cliente = $idCliente";
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
            $fechaInicio = date('Y-m-d');
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
        $sql = "SELECT id, rsocial FROM clientes WHERE activo = 1 ORDER BY rsocial ASC";
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
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4 visible-lg" style="padding-right:0px;">
                            <div class="panel panel-info" style="padding-bottom:5px">
                                <div class="panel-heading">
                                    Nomenclatura
                                </div>
                                <div class="panel-body">
                                    <ul class="fa-ul">
                                        <li><i class="fa-li fa fa-square-o"></i> S&iacute; se puede facturar</li>
                                        <li><i class="fa-li fa fa-check-square-o"></i> Producto facturado</li>
                                        <li><i class="fa-li fa fa-ban"></i> No se puede facturar</li>
                                    </ul>
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
            <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:0px">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-list-alt" aria-hidden="true"></i> Lista de ventas realizadas

                    </div>
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="row">
                            <table id="example" class="display compact table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Tipo</th>
                                        <th>No.</th>
                                        <th>Rel Fact.</th>
                                        <th>Estatus Pago</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Cajero</th>
                                        <th>M&eacute;todo</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Abonado</th>
                                        <th class="text-center">Saldo</th>
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
                                ventas.id                   AS idVenta_,
                                ventas.timestamp            AS fechaHora,
                                ventas.cliente              AS idCliente,
                                ventas.descuento            AS descuento,
                                ventas.usuario              AS idCajero,
                                ventas.metododepago         AS idMetodoPago,
                                ventas.remision             AS remision,
                                ventas.totalventa           AS totalVenta_,
                                ventas.pagado               AS pagado,
                                ventas.esCredito            AS esCredito,
                                usuarios.nombre             AS nombreCajero,
                                usuarios.apellidop          AS apellidopCajero,
                                usuarios.apellidom          AS apellidomCajero,
                                clientes.rsocial            AS nombreCliente,
                                tipoprecios.nombrecorto     AS nombreTipoPrecio,
                                metodosdepago.nombre        AS nombreMetodoPago,
                                (SELECT IFNULL(SUM(pagosrecibidos.monto),0) FROM pagosrecibidos WHERE pagosrecibidos.idventa = idVenta_) AS montoPagado,
                                (SELECT COUNT(*) FROM detalleventa WHERE facturable = 1 AND facturado = 0 AND venta = idVenta_) AS totProdFact,
                                (SELECT IFNULL(COUNT(*),0) FROM relventafactura WHERE relventafactura.idVenta = idVenta_) AS totRelVentaFactura
                            FROM        ventas
                            INNER JOIN  usuarios
                            ON          ventas.usuario      = usuarios.id
                            INNER JOIN  clientes
                            ON          ventas.cliente      = clientes.id
                            INNER JOIN  tipoprecios
                            ON          ventas.tipoprecio   = tipoprecios.id
                            INNER JOIN  metodosdepago
                            ON          ventas.metododepago = metodosdepago.id
                            WHERE       (ventas.timestamp BETWEEN '$fechaInicioSQL' AND '$fechaFinSQL') $consultarXCliente
                            GROUP BY    ventas.id
                            ORDER BY    ventas.id ASC";
                    if ($resultadoVentas                    = $mysqli->query($sql))
                    {
                        $totalFilas                         = $resultadoVentas->num_rows;
                        $totPagado                          = 0;
                        $totCredito                         = 0;
                        $totVenta                           = 0;
                        while ($rowVenta                    = $resultadoVentas->fetch_assoc())
                        {
                            $estePagado                     = ($rowVenta['esCredito'] == 0) ? $rowVenta['totalVenta_'] : $rowVenta['montoPagado'];
                            $esteCredito                    = ($rowVenta['esCredito'] == 0) ? 0 : $rowVenta['totalVenta_'] - $rowVenta['montoPagado'];
                            $estaVenta                      = $rowVenta['totalVenta_'];
                            $totPagado                      += $estePagado;
                            $totCredito                     += $esteCredito;
                            $totVenta                       += $rowVenta['totalVenta_'];
                            $fechaVenta                     = date('d-m-Y',strtotime($rowVenta['fechaHora']));
                            $horaVenta                      = date('H:i:s',strtotime($rowVenta['fechaHora']));
                            $nombreCompCliente              = $rowVenta['nombreCliente'];
                            $nombreCompCajero               = $rowVenta['nombreCajero']." ".$rowVenta['apellidopCajero'];
                            $idVenta                        = $rowVenta['idVenta_'];

            ?>

                                <tr class="rowReporte" name="<?php echo $idVenta;?>">
                                    <td>
                                        <div class="btn-group pull-left">
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </button>
                                            <ul class="dropdown-menu slidedown">
            <?php
                    if ($rowVenta['remision'] == 1)
                    {
            ?>
                                            <li>
                                                <a href="../control/genRemisionVentaPDF.php?idVenta=<?php echo $idVenta;?>" target="_blank" class="aReimprimirRem" name="<?php echo $idVenta;?>">
                                                    <i class="fa fa-print" aria-hidden="true"></i> Reimprimir Rem
                                                </a>
                                            </li>
            <?php
                    }
                    else
                    {
            ?>
                                            <li>
                                                <a class="aReimprimirTkt" name="<?php echo $idVenta;?>">
                                                    <i class="fa fa-print" aria-hidden="true"></i> Reimprimir
                                                </a>
                                            </li>
            <?php
                    }
                    if($rowVenta['totProdFact'] > 0)
                    {
            ?>
                                            <li>
                                                <a class="aFacturar" name="<?php echo $idVenta;?>">
                                                    &nbsp;<i class="fa fa-bolt" aria-hidden="true"></i> Facturar
                                                </a>
                                            </li>
            <?php
                    }
            ?>
                                            <li>
                                                <a class="aEditar" href="editarVenta.php?idVenta=<?php echo $idVenta;?>" name="<?php echo $rowCompra['idVenta'];?>">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
                                                </a>
                                            </li>
                                            <li>
                                                <a class="aCancelar" name="<?php echo $idVenta;?>">
                                                    <i class="fa fa-times-circle" aria-hidden="true"></i> Cancelar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
            <?php
                    $lbl_venta = ($rowVenta['remision'] == 1) ? '<span class="label label-success"><i class="fa fa-print" aria-hidden="true"></i> Remisi&oacute;n</span>' : '<span class="label label-primary"><i class="fa fa-ticket" aria-hidden="true"></i> Ticket</span>';
                    echo $lbl_venta;
            ?>
                                </td>
                                <td>
                                    <?php echo $idVenta;?>
                                </td>
            <?php
                    $str_rel        = "";
                    if ($rowVenta['totRelVentaFactura'] == 0)
                    {
                        $str_rel    .= "--";
                    }
                    else
                    {
                        $cont_rel       = 0;
                        $sql_rel        = "SELECT idFactura FROM relventafactura WHERE idVenta = $idVenta";
                        $res_rel        = $mysqli->query($sql_rel);
                        while ($row_rel = $res_rel->fetch_assoc())
                        {
                            $str_rel    .= ($cont_rel == 0) ? $row_rel['idFactura'] : ", ".$row_rel['idFactura'];
                            $cont_rel++;
                        }
                    }
            ?>
                                <td class="text-right">
                                    <?php echo $str_rel; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo ($rowVenta['pagado'] == 1) ? '<span class="label label-default">Pagada <i class="fa fa-check" aria-hidden="true"></i></span>' : '<span class="label label-danger">No pagada <i class="fa fa-times" aria-hidden="true"></i></span>'; ?>
                                </td>
                                <td>
                                    <?php echo $fechaVenta." ".$horaVenta; ?>
                                </td>
                                <td>
                                    <?php echo $nombreCompCliente; ?>
                                </td>
                                <td>
                                    <?php echo $nombreCompCajero; ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $rowVenta['nombreMetodoPago']; ?>
                                </td>
                                <td class="text-right">
                                    $<?php echo number_format($estaVenta,2,".",","); ?>
                                </td>
                                <td class="text-right">
                                    $<?php echo number_format($estePagado,2,".",","); ?>
                                </td>
                                <td class="text-right">
                                    $<?php echo number_format($esteCredito,2,".",","); ?>
                                </td>
                            </tr>
                <?php
                        }
                    }
                }
                ?>
                                </tbody>
                            </table>
                            <div class="col-lg-3 col-lg-offset-9  col-md-3 col-md-offset-9 col-sm-4 col-sm-offset-8" style="margin-bottom:15px;padding-right:0px">
                                <table class="pull-right table table-striped table-bordered table-hover table-condensed">
                                    <tr>
                                        <td class="text-right">
                                            Abonado:
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($totPagado,2,".",","); ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">
                                            Saldo:
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($totCredito,2,".",","); ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">
                                            Total:
                                        </td>
                                        <td class="text-right">
                                            <label> <?php echo '$ '.number_format($totVenta,2,".",","); ?></label>
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
        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="padding-right:0px" >
            <div class="panel panel-default" id="panelDetalle">
                <div class="panel-heading">
                    <i class="fa fa-info-circle" aria-hidden="true"></i> Detalle
                </div>
                <div class="panel-body" id="divDetalle">
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
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script src="../control/custom-js/dialogosFactura.js"></script>
    <script>
    coleccionTabla = [];
    coleccionFactura = [];
    coleccionRowWarning = [];
    coleccionSubVenta   = [];
    refrescarPag        = 0; //Despues de generar factura por grupo se debe actualizare la página
    widthFlotante = $("#panelDetalle").width();
    function rowColeccionable(numero, fecha, hora, cliente, descuento, cajero, metodo, tipo, total)
    {
        this.numero     = numero;
        this.fecha      = fecha;
        this.hora       = hora;
        this.cliente    = cliente;
        this.descuento  = descuento;
        this.cajero     = cajero;
        this.metodo     = metodo;
        this.tipo       = tipo;
        this.total      = total;
    }
    function rowFactura(idProducto, idSubVenta, idVenta, claveSAT, iva, ieps, cantidad, precioU, subTotal)
    {
        this.idProducto = idProducto;
        this.idSubVenta = idSubVenta;
        this.idVenta    = idVenta;
        this.claveSAT   = claveSAT;
        this.iva        = iva;
        this.ieps       = ieps;
        this.cantidad   = cantidad;
        this.precioU    = precioU;
        this.subTotal   = subTotal;
        //this.descuento  = descuento;
    }
    function rowSubVenta(idSubVenta)
    {
        this.idSubVenta = idSubVenta;
        //this.descuento  = descuento;
    }
    function rowWarning(idVenta)
    {
        this.idVenta    = idVenta;
    }
    function refrescarPagina()
    {
        refrescarPag = 1;
    }
    function armarFactura()
    {
        coleccionFactura.length = 0;
        $(".trRowFactura").each(function()
        {

            idProducto  = $(this).attr("name");
            idSubVenta  = $(this).attr("idSubVenta");
            idVenta     = $(this).attr("idVenta");
            claveSAT    = $(this).find('.inputSAT').val();
            iva         = $(this).find('.inputIva').val();
            ieps        = $(this).find('.inputIeps').val();
            cantidad    = $(this).find('.spanCantidad').text();
            precioU     = $(this).find('.spanPrecioU').text();
            subTotal    = $(this).find('.spanSubTotal').text();
            //descuento   = $(this).find('.inputDescuento').val();

            esteRow     = new rowFactura(idProducto, idSubVenta, idVenta, claveSAT, iva, ieps, cantidad, precioU, subTotal);
            coleccionFactura.push(esteRow);
        });
        coleccionSubVenta.length = 0;
        $(".subVentaHidden").each(function()
        {

            idSubVenta  = $(this).val();
            //descuento   = $(this).find('.inputDescuento').val();

            esteRow     = new rowSubVenta(idSubVenta);
            coleccionSubVenta.push(esteRow);
        });
        //return coleccionFactura;
    }

        $(document).ready(function()
        {
            var table = $('#example').DataTable(
             {
                "lengthMenu": [[-1, 10, 20, 50, 100, 200, 500, 1000],["Todos", 10, 20, 50, 100, 200, 500, 1000]],
                "language":
                {
                     "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
                 },
                 responsive: true,
                 "order": [[ 2, "desc" ]],
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
                $.ajax(
                {
                    method: "POST",
                    url:"../control/listarDetalleVenta.php",
                    data: {idVenta:idVenta}
                })
                .done(function(p)
                {
                    $("#divDetalle").html(p);
                });
            });
            $(".aFacturar").click(function()
            {
                idVenta = $(this).attr("name");
                coleccionRowWarning.length = 0;
                esteRow     = new rowWarning(parseInt(idVenta));
                coleccionRowWarning.push(esteRow);
                var coleccionWarningJSON = JSON.stringify(coleccionRowWarning);
                console.log(coleccionWarningJSON);
                $.ajax(
                {
                    method: "POST",
                    url:"../ws/rev_factura_monto_grupo.php",
                    data: {coleccionVentas:coleccionWarningJSON}
                })
                .done(function(p)
                {
                    $( "#dialog-revisar-factura-ind" ).dialog("open");
                    $("#divRevFactura").html(p);
                })
                .fail(function()
                {
                    alert("No se puede acceder en este momento. Consulte con el adminsitrador del sistema");

                })
                .always(function(p)
                {
                    console.log(p);
                })
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
            $( "#dialog-revisar-factura-ind" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 960,
                modal: true,
                autoOpen: false,
                closeOnEscape: false,
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
                    $(this).parent().find(".ui-dialog-titlebar-close").remove();
                    $("#btnFacturarInd").css("display","inline");
                    $("#btnFacturarInd").attr("disabled",false);
                    $("#btnFacturarInd").html('<i class="fa fa-rocket" aria-hidden="true"></i> factura!');
                    $("#btnCancelarFacturarInd").attr("disabled",false);
                    $("#tdMsg").css("color","dimgrey");
                },
                buttons:
                [
                    {
                        text: "Factura!",
                        id: "btnFacturarInd",
                        click: function()
                        {
                            error = revisarClaveSAT();
                            if(error == 1)
                            {
                                return false;
                            }
                            else
                            {
                                var r = confirm("Revisa toda la información de la factura. ¿Seguro que deseas continuar?");
                                if (r == false)
                                    return false;
                                armarFactura();
                                idCliente           = $("#hiddenIdCliente").val();
                                idVenta             = $("#hiddenIdVenta").val();
                                enviarA             = $("#inputEmail_grupo").val();
                                metodoP             = $("#selectMetodo_grupo").val();
                                usoCFDI             = $("#selectUso").val();
                                enviar              = ($('#chkEmail_grupo').is(':checked')) ? 1 : 0;
                                formaPago           = $("#selectFormaPago-grupo").find('option:selected').attr("name");
                                cadenaVentas        = $("#hiddenCadenaVentas").val();
                                coleccionSubVentaJSON = JSON.stringify(coleccionSubVenta);
                                coleccionFacturaJSON = JSON.stringify(coleccionFactura);
                                console.log(coleccionFacturaJSON);
                                $("#btnFacturarInd").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> factura!');
                                //$(".inputSAT").attr("disabled",true);
                                $(".inputSAT").attr("readonly",true);
                                $(".inputIva").attr("readonly",true);
                                $(".inputIeps").attr("readonly",true);
                                //$(".inputDescuento").attr("readonly",true);
                                $("#btnFacturarInd").attr("disabled",true);
                                $("#btnCancelarFacturarInd").attr("disabled",true);
                                $("#tdMsgInd_g").css("color","dimgrey");
                                $("#tdMsgInd_g").html('<i class="fa fa-2x fa-spinner fa-pulse pull-left" aria-hidden="true"></i> No cierres esta ventana mientras est&aacute;</br> en proceso la facturaci&oacute;n!!')
                                $("#ifram_monto_grupo").css("visibility","visible");
                                $("#ifram_monto_grupo").attr('src','../ws/gen_factura_monto_grupo.php?enviarA='+enviarA+'&idCliente='+idCliente+'&enviar='+enviar+'&usoCFDI='+usoCFDI+'&metodoPago='+metodoP+'&coleccionFacturaJSON='+coleccionFacturaJSON+'&coleccionIdSubVentaJSON='+coleccionSubVentaJSON+'&formaPago='+formaPago+'&cadenaVentas='+cadenaVentas);
                                //$('iframe#ifram_monto_grupo').attr('src', $('iframe#ifram').attr('src'));
                            }
                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarFacturarInd",
                        click: function()
                        {
                            if (refrescarPag == 1)
                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/reporteVentas.php");
                            else
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
<div id="dialog-revisar-factura-ind" class="dialog-oculto" title="Revisar la factura">
    <p>
        <div class="col-lg-12" id="divRevFactura">
        </div>
    </p>
</div>
</html>
<?php
}
?>
