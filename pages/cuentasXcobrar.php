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
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cuentas por cobrar</title>
    <!-- Bootstrap Core CSS -->
    <!-- MetisMenu CSS -->
    <link href="../startbootstrap/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../startbootstrap/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
        #example_filter
        {
            text-align: right;
            padding-right: 18px;
        }
        .trDocumento,#aAbonar,#aAbonarMonto
        {
            cursor: pointer;
        }
        .dialog-oculto
        {
            display: none;
        }
        .floatDetalle
        {
            position: fixed;
            top: 10px;
        }
        .chkTrSelect,.chkTrSelect_todo
        {
            width: 23px;
            height: 23px;
        }
        .inactive
        {
            background-color: #ececec;
            color: #b9b9b9;
        }
        .tdChk
        {
            padding: 0px;
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
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <h1 class="page-header"><i class="fa fa-credit-card" aria-hidden="true"></i> Cuentas por cobrar</h1>
                </div>
                <!-- <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="divRespuesta" style="margin-top:20px">
                </div> -->
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:0px">
                    <div class="panel panel-default">
                        <div class="panel-heading" style="padding-bottom:18px">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Lista de documentos pendientes por cobrar
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped table-bordered table-condensed" id="example">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="chkTrSelect_todo"></th>
                                                <th>#</th>
                                                <th>Tipo</th>
                                                <th>Id venta(s) Rel</th>
                                                <th>Fecha</th>
                                                <th>Expira</th>
                                                <th>D&iacute;as Cred.</th>
                                                <th>Cliente</th>
                                                <th>Cajero</th>
                                                <th>Total Venta</th>
                                                <th>Abono</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
                                    $sql = "SELECT
                                                ventas.id               AS idVenta_,
                                                ventas.usuario          AS idCajero,
                                                ventas.timestamp        AS fechaCompra,
                                                ventas.metododepago     AS metododepago,
                                                ventas.cliente          AS idCliente,
                                                ventas.totalventa       AS totalVenta,
                                                ventas.remision         AS esRemision,

                                                usuarios.nombre         AS nombreCajero,
                                                usuarios.apellidop      AS apellidopCajero,
                                                clientes.rsocial        AS nombreCliente,
                                                clientes.diasCredito    AS diasCredito,
                                                metodosdepago.nombre    AS nombreMetodoPago,
                                                (SELECT IFNULL(SUM(relventafactura.monto),0)
                                                 FROM relventafactura
                                                 WHERE relventafactura.idVenta = idVenta_) AS montoRecibidoRelFact,
                                                (SELECT IFNULL(SUM(monto),0)
                                                 FROM pagosrecibidos
                                                 WHERE pagosrecibidos.idventa = ventas.id) AS montoRecibido

                                                FROM ventas
                                                INNER JOIN usuarios
                                                ON ventas.usuario = usuarios.id
                                                INNER JOIN clientes
                                                ON ventas.cliente = clientes.id
                                                INNER JOIN metodosdepago
                                                ON ventas.metododepago = metodosdepago.id
                                                WHERE ventas.pagado = 0";
                                                //(SELECT COUNT(*) FROM detalleventa WHERE facturable = 1 AND facturado = 0 AND venta = idVenta_) AS totProdFact
                                    $result = $mysqli->query($sql);
                                    $adeudo = 0;
                                    while ($rowDoc = $result->fetch_assoc())
                                    {
                                        $diasCredito        = $rowDoc['diasCredito'];
                                        $fechaCompra        = date('d-m-Y',strtotime($rowDoc['fechaCompra']));
                                        $fechaExpira        = strtotime($rowDoc['fechaCompra']."+ ".$diasCredito." days");

                                        $fechaExpira        = date('d-m-Y', $fechaExpira);
                                        $adeudo             += $rowDoc['totalVenta'] - $rowDoc['montoRecibido'];
                                        $tipoDoc            = ($rowDoc['esRemision'] == 1) ? '<span class="label label-success"><i class="fa fa-print" aria-hidden="true"></i> Remisi&oacute;n</span>' : '<span class="label label-primary"><i class="fa fa-ticket" aria-hidden="true"></i> Ticket</span>';
                                        $tipoVenta          = ($rowDoc['esRemision'] == 1) ? 'R' : 'T';
?>
                                            <tr class="trDocumento" name="<?php echo $rowDoc['idVenta_'];?>" tipo="<?php echo $tipoVenta;?>">
                                                <td class="text-center tdChk"><input type="checkbox" class="chkTrSelect" name="<?php echo $rowDoc['idVenta_'];?>"></td>
                                                <td class="text-right"><?php echo $rowDoc['idVenta_'];?></td>
                                                <td><?php echo $tipoDoc;?></td>
                                                <td class="text-center">--</td>
                                                <td class="text-left"><?php echo $fechaCompra;?></td>
                                                <td class="text-left"><?php echo $fechaExpira;?></td>
                                                <td class="text-right"><?php echo $rowDoc['diasCredito'];?></td>
                                                <!--<td class="text-center"><?php //echo $rowDoc['nombreTipoPrecio'];?></td>-->
                                                <td class="text-left"><?php echo $rowDoc['nombreCliente'];?></td>
                                                <td class="text-left"><?php echo $rowDoc['nombreCajero']." ".$rowDoc['apellidopCajero'];?></td>
                                                <td class="text-right"><?php echo "$".number_format($rowDoc['totalVenta'],2,".",",");?></td>
                                                <td class="text-right"><?php echo "$".number_format($rowDoc['montoRecibido'],2,".",",");?></td>
                                                <td class="text-right"><span class="dialog-oculto spanSaldo"><?php echo $rowDoc['totalVenta'] - $rowDoc['montoRecibido'];?></span><b><?php echo "$".number_format($rowDoc['totalVenta'] - $rowDoc['montoRecibido'],2,".",",");?></b></td>

                                            </tr>
<?php
/////////////////////////////////////////////// SELECT FACTURAS PPD NO SALDADAS //////////////////////////////////////////////////////////////////////
                                    }
                                    $sql = "SELECT
                                                facturas.id                 AS idVenta_,
                                                facturas.idVentasRelacion   AS idVentasRelacion,
                                                facturas.usuario            AS idCajero,
                                                facturas.timestamp          AS fechaCompra,
                                                facturas.idReceptor         AS idCliente,
                                                facturas.total              AS totalVenta,
                                                facturas.emailReceptor      AS emailReceptor,
                                                facturas.razonReceptor      AS razonReceptor,
                                                usuarios.nombre             AS nombreCajero,
                                                usuarios.apellidop          AS apellidopCajero,
                                                clientes.rsocial            AS nombreCliente,
                                                clientes.diasCredito        AS diasCredito,
                                                (SELECT IFNULL(SUM(montoPago),0)
                                                FROM facturas
                                                WHERE facturas.idRelacion = idVenta_)
                                                                            AS montoRecibido
                                                FROM facturas
                                                INNER JOIN usuarios
                                                ON facturas.usuario = usuarios.id
                                                INNER JOIN clientes
                                                ON facturas.idReceptor = clientes.id
                                                WHERE facturas.pagado = 0";
                                    $result = $mysqli->query($sql);
                                    //$adeudo = 0;
                                    while ($rowDoc = $result->fetch_assoc())
                                    {
                                        $diasCredito        = $rowDoc['diasCredito'];
                                        $fechaCompra        = date('d-m-Y',strtotime($rowDoc['fechaCompra']));
                                        $fechaExpira        = strtotime($rowDoc['fechaCompra']."+ ".$diasCredito." days");

                                        $fechaExpira        = date('d-m-Y', $fechaExpira);
                                        $adeudo             += $rowDoc['totalVenta'] - $rowDoc['montoRecibido'];
?>
                                            <tr class="trDocumento" name="<?php echo $rowDoc['idVenta_'];?>" tipo="F" email-receptor="<?php echo $rowDoc['emailReceptor'];?>" razon-receptor="<?php echo $rowDoc['razonReceptor'];?>">
                                                <td class="text-center tdChk"></td>
                                                <td class="text-right"><?php echo $rowDoc['idVenta_'];?></td>
                                                <td><span class="label label-info"><i class="fa fa-rocket" aria-hidden="true"></i> Factura</span></td>
                                                <td class="text-right"><?php echo $rowDoc['idVentasRelacion'];?></td>
                                                <td class="text-left"><?php echo $fechaCompra;?></td>
                                                <td class="text-left"><?php //echo $fechaExpira;?></td>
                                                <td class="text-right"><?php echo $rowDoc['diasCredito'];?></td>
                                                <!--<td class="text-center"><?php //echo $rowDoc['nombreTipoPrecio'];?></td>-->
                                                <td class="text-left"><?php echo $rowDoc['nombreCliente'];?></td>
                                                <td class="text-left"><?php echo $rowDoc['nombreCajero']." ".$rowDoc['apellidopCajero'];?></td>
                                                <td class="text-right"><?php echo "$".number_format($rowDoc['totalVenta'],2,".",",");?></td>
                                                <td class="text-right"><?php echo "$".number_format($rowDoc['montoRecibido'],2,".",",");?></td>
                                                <td class="text-right"><span class="dialog-oculto spanSaldo"><?php echo $rowDoc['totalVenta'] - $rowDoc['montoRecibido'];?></span><b><?php echo "$".number_format($rowDoc['totalVenta'] - $rowDoc['montoRecibido'],2,".",",");?></b></td>

                                            </tr>
<?php
                                    }

?>
                                        </tbody>
                                    </table>
                                    <div class=" col-lg-offset-9 col-lg-3 col-md-3 col-sm-4 col-xs-4" style="padding-right:0px">
                                        <table class="table table-hover table-striped">
                                            <th>
                                                <td class="text-right">
                                                    Total Adeudo: <label id="labelAdeudo"><?php echo " $".number_format($adeudo,2,".",","); ?></label>
                                                </td>
                                            </th>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <div class="col-lg-12" id="panelDetalle" style="padding-right:0px;padding-left:0px">
                        <div class="row">
                            <div class="col-lg-12" id="divRespuesta">
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading" style="padding-bottom:18px">
                                <i class="fa fa-info-circle" aria-hidden="true" style=""></i>
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <a id="aReimprimir" class="btn btn-default btn-sm dialog-oculto" data-toggle="tooltip" data-placement="auto" title="Reimprimir venta"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                        <a id="aReimprimirFact" target="_blank" class="btn btn-default btn-sm dialog-oculto" data-toggle="tooltip" data-placement="auto" title="Reimprimir factura"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                        <button id="aAbonar" type="button" class="btn btn-default btn-sm dialog-oculto" data-toggle="tooltip" data-placement="auto" title="Pago en efectivo"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                        <button id="aAbonarMonto" type="button" class="btn btn-default btn-sm dialog-oculto" data-toggle="tooltip" data-placement="auto" title="Generar factura"><i class="fa fa-files-o" aria-hidden="true"></i></button>
                                        <button id="aComplemento" type="button" class="btn btn-default btn-sm dialog-oculto" data-toggle="tooltip" data-placement="auto" title="Agregar complemento de pago"><i class="fa fa-rocket" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body" id="divDetalle" style="display:none">
                            </div>
                            <iframe width="100%" height="500px" id="vp_facturaPDF" style="display:none"></iframe>
                        </div>
                        <!-- /.col-lg-8 (nested) -->
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
    <!-- Bootstrap Core JavaScript -->
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap/vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../startbootstrap/dist/js/sb-admin-2.js"></script>
    <!-- Morris Charts JavaScript -->
    <script src="../startbootstrap/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../startbootstrap/vendor/raphael/raphael.min.js"></script>
    <script src="../startbootstrap/vendor/morrisjs/morris.min.js"></script>
    <script src="../startbootstrap/data/morris-data.js"></script>
    <!-- DataTables Javascript -->
    <script src="../startbootstrap/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-responsive/dataTables.responsive.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../control/custom-js/dialogosFactura.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script src="../startbootstrap/vendor/printThis/printThis.js"></script>
    <script src="../startbootstrap/vendor/jsBarcode/JsBarcode.all.min.js"></script>
    <script>
        // $('.tooltip-demo').tooltip({
        //     selector: "[data-toggle=tooltip]",
        //     container: "body"
        // })
    </script>
    <script>
        dialogoAbierto      = 0;
        coleccionFactura    = [];
        coleccionSubVenta   = [];
        coleccionRowWarning = [];
        widthFlotante       = $("#panelDetalle").width();
        refrescarPag        = 0; //Despues de generar factura por grupo se debe actualizare la página
        function refrescarPagina()
        {
            refrescarPag = 1;
        }
        function rowWarning(idVenta)
        {
            this.idVenta    = idVenta;
        }
        function colRowWarning()
        {
            coleccionRowWarning.length = 0;
            $($("tr.info").get().reverse()).each(function()
            {
                if ($(this).attr('tipo') == 'F')
                    return true;
                idVenta     = $(this).attr("name");
                esteRow     = new rowWarning(parseInt(idVenta));
                coleccionRowWarning.push(esteRow);
            });
            coleccionRowWarning.sort((a, b) => parseInt(a.idVenta) - parseInt(b.idVenta));
            console.log(coleccionRowWarning);
            return coleccionRowWarning;
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
        // function coleccionVentas(idVenta, liquidada, montoFact) //Para asignar pagos después de facturar por grupo
        // {
        //     this.idVenta    = idVenta;
        //     this.liquidada  = liquidada;
        //     this.montoFact  = montoFact;
        // }
        function mostrarBotones()
        {
            contFact        = 0;
            contVentas      = 0;
            $(".trDocumento").each(function()
            {
                if ($(this).hasClass("info"))
                {
                    if ($(this).attr("tipo") == 'F')
                    {
                        contFact++;
                        idFact= $(this).attr("name");
                    }
                    else
                    {
                        contVentas++;
                    }
                }
            });
            if (contFact == 1)
            {
                //alert(idFact);
                $("#aComplemento").removeClass('dialog-oculto');
                $("#aReimprimirFact").removeClass('dialog-oculto').attr("href",'../ws/gen_vpreviaFacturaPDF.php?id='+idFact);

                $("#aReimprimir").addClass('dialog-oculto');
            }
            else
            {
                $("#aComplemento").addClass('dialog-oculto');
            }
            if (contVentas == 0)
            {
                $("#aReimprimir").addClass('dialog-oculto');
                $("#aAbonarMonto").addClass('dialog-oculto');
                $("#aAbonar").addClass('dialog-oculto');

            }
            if (contVentas == 1)
            {
                $("#aComplemento").addClass('dialog-oculto');
                $("#aReimprimir").removeClass('dialog-oculto');
                $("#aReimprimirFact").addClass('dialog-oculto');
                $("#aAbonarMonto").removeClass('dialog-oculto');
                $("#aAbonar").removeClass('dialog-oculto');
            }
            if (contVentas > 1)
            {
                $("#aComplemento").addClass('dialog-oculto');
                $("#aReimprimir").addClass('dialog-oculto');
                $("#aReimprimirFact").addClass('dialog-oculto');
                $("#aAbonarMonto").removeClass('dialog-oculto');
                $("#aAbonar").removeClass('dialog-oculto');
            }
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
                esteRow     = new rowSubVenta(idSubVenta);
                coleccionSubVenta.push(esteRow);
            });
        }
        $(document).ready(function()
        {
            $('[data-toggle="tooltip"]').tooltip();
            $(".chkTrSelect_todo").click(function()
            {
                $(".trDocumento").removeClass("info");
                if ($(this).prop("checked"))
                {
                    $(".chkTrSelect").prop("checked", true);
                    $(".trDocumento").each(function()
                    {
                        if ($(this).attr("tipo") != 'F')
                        {
                            $(this).addClass("info");
                        }
                    });
                }
                else
                {
                    $(".chkTrSelect").prop("checked", false);
                    $(".trDocumento").removeClass("info");
                    $("#example > tbody > tr:nth-child(1)").click();
                }
                $(".trDocumento").each(function(e)
                {
                    if ($(this).attr('tipo') != 'F' && $(this).hasClass('info'))
                        contTr++;
                });
                if (contTr > 1)
                {
                    $("#aAbonarMonto").removeClass("dialog-oculto");
                    $("#aComplemento").addClass('dialog-oculto');
                }
                else
                {
                    $("#aAbonarMonto").addClass("dialog-oculto");
                    $("#aComplemento").removeClass('dialog-oculto');
                }
            });
            $(".trDocumento").click(function(e)
            {
                if (e.target.type=='checkbox')
                {
                    if ($(this).hasClass('info'))
                    {
                        cont = 0;
                        $(".trDocumento").each(function()
                        {
                            if ($(this).attr('tipo') == 'F')
                            {
                                $(this).removeClass('info');
                            }
                            if ($(this).hasClass('info'))
                            {
                                cont++;
                            }
                        });
                        if (cont == 1)
                            return false
                        else
                        {
                            $(this).find(".chkTrSelect").prop("checked",false);
                            $(this).removeClass("info");
                        }
                    }
                    else
                    {
                        $(this).find(".chkTrSelect").prop("checked",true);
                        $(this).addClass("info");
                    }
                    $(".trDocumento").each(function()
                    {
                        if ($(this).attr('tipo') == 'F')
                        {
                            $(this).removeClass('info');
                        }
                    });
                }
                else
                {
                    $(".trDocumento").removeClass('info');
                    $(".chkTrSelect").prop("checked",false);
                    $(".chkTrSelect_todo").prop("checked",false);
                    $(this).addClass('info');
                    $(this).find(".chkTrSelect").prop("checked",true);
                }
                idVenta = $(this).attr("name");
                tipo    = $(this).attr("tipo");
                if (tipo == 'F')
                {
                    $("#divDetalle").hide();
                    $("#vp_facturaPDF").show();
                    $("#vp_facturaPDF").attr('src','../ws/gen_vpreviaFacturaPDF.php?id='+idVenta+'&vista=1');
                    $("#aComplemento").removeClass('dialog-oculto');
                    $("#aAbonar").addClass('dialog-oculto');
                    $("#aAbonarMonto").addClass('dialog-oculto');
                }
                else
                {
                    $("#divDetalle").show();
                    $("#vp_facturaPDF").hide();
                }
                //     contTr = 0;
                //     $(".trDocumento").each(function(e)
                //     {
                //         if ($(this).attr('tipo') != 'F' && $(this).hasClass('info'))
                //             contTr++;
                //     });
                //     if (contTr > 1)
                //     {
                //         $("#aAbonarMonto").removeClass("dialog-oculto");
                //         $("#aComplemento").addClass('dialog-oculto');
                //     }
                //     else
                //     {
                //         $("#aAbonarMonto").addClass("dialog-oculto");
                //         $("#aComplemento").removeClass('dialog-oculto');
                //     }
                //     $("#aAbonar").removeClass('dialog-oculto');
                    $.ajax(
                    {
                        method: "POST",
                        url:"../control/listarPagosCuentasxCobrar.php",
                        data: {idVenta:idVenta}
                    })
                    .done(function(p)
                    {
                        $("#divDetalle").html(p);
                    });
                // }
                mostrarBotones();
            });
            $("#aAbonar").click(function()
            {
                coleccionWarning = colRowWarning();
                var coleccionWarningJSON = JSON.stringify(coleccionWarning);
                console.log(coleccionWarningJSON);
                $.ajax(
                {
                    method: "POST",
                    url:"../ajax/revMismoClienteCuentasxCobrar.php",
                    data: {listaDoctos:coleccionWarningJSON}
                })
                .done(function(p)
                {
                    if (p.status == 0)
                    {
                        $("#divRespuesta").html(p.respuesta);
                    }
                    else
                    {
                        monto = p.saldoAcumulado;
                        rSocial = p.rSocial;
                        $( "#dialog-agregar-abono" ).data('rSocial',rSocial).data('monto',monto).dialog("open");
                    }
                }).always(function(p)
                {
                    console.log(p);
                });
                // $(".trDocumento").each(function()
                // {
                //     if ($(this).hasClass("info"))
                //     {
                //         idCompra = $(this).attr("name");
                //         monto = $(this).find('b').text();
                //         $( "#dialog-agregar-abono" ).data('idVenta',idVenta).data('monto',monto).dialog("open");
                //         return false;
                //     }
                // });
            });
            $("#aAbonarMonto").click(function()
            {
                coleccionWarning = colRowWarning();
                var coleccionWarningJSON = JSON.stringify(coleccionWarning);
                console.log(coleccionWarningJSON);
                $.ajax(
                {
                    method: "POST",
                    url:"../ajax/revMismoClienteCuentasxCobrar.php",
                    data: {listaDoctos:coleccionWarningJSON}
                })
                .done(function(p)
                {
                    if (p.status == 0)
                    {
                        $("#divRespuesta").html(p.respuesta);
                    }
                    else
                    {
                        monto = p.saldoAcumulado;
                        rSocial = p.rSocial;
                        $( "#dialog-agregar-abono-grupo" ).data('rSocial',rSocial).data('monto',monto).dialog("open");
                    }
                }).always(function(p)
                {
                    console.log(p);
                });
            });
            $("#aComplemento").click(function()
            {
                $(".trDocumento").each(function()
                {
                    if ($(this).hasClass("info"))
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
                        });
                    }
                });
            });
            $("#aReimprimir").click(function()
            {
                //alert("recibo");
                $(".trDocumento").each(function()
                {
                    if ($(this).hasClass("info"))
                    {
                        nombre = $(this).attr('name');
                        if ($(this).attr("tipo") == 'T')
                        {
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
                            return false;
                        }
                        else if ($(this).attr("tipo") == 'R')
                        {
                            url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionVentaPDF.php?idVenta="+nombre;
                            $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                            return false;
                        }
                    }
                });
            });
            $(document).on( 'scroll', function()
            {
                //
                var sv = $(this).scrollTop();
                console.log('Event Fired: '+sv);
                panel = $("#panelDetalle");
                if (sv >= 158)
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
            $("#divRevFactura,#divRevFactura-grupo").on("keyup change",".inputSAT,.inputIva,.inputIeps,.inputDescuento",reCalcularFactura);
            $("#divRevFactura,#divRevFactura-grupo").on("focus",".inputSAT,.inputIva,.inputIeps,.inputDescuento",function()
            {
                $(this).select();
            });
            $("#divRevFactura,#divRevFactura-grupo").on("focusout",".inputIva,.inputIeps,.inputDescuento",function()
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
            $(".table-responsive").on("keyup","input",function()
            {
                totalSaldo = 0;
                $(".spanSaldo").each(function()
                {
                    totalSaldo += parseFloat($(this).text());
                });
                var amount = totalSaldo;
                var locale = 'mx';
                var options = {style: 'currency', currency: 'MXN', minimumFractionDigits: 2, maximumFractionDigits: 2};
                var formatter = new Intl.NumberFormat(locale, options);
                console.log(formatter.format(amount));
                $("#labelAdeudo").text(formatter.format(amount))
            });
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $( "#dialog-agregar-abono" ).dialog(
            {
                resizable: true,
                height: "auto",
                width: 422,
                modal: true,
                autoOpen: false,
                position: { my: 'top', at: 'top+1' },
                show:
                {
                    effect: "slide",
                    duration: 300,
                    direction: 'up'
                },
                open: function()
                {
                    dialogoAbierto = 1;
                    $("#divRespuestaAbono").empty();
                    $("#divRespuesta").empty();
                    $("#inputAbono").val('');
                    rSocial = $( "#dialog-agregar-abono" ).data('rSocial');
                    monto   = $( "#dialog-agregar-abono" ).data('monto');
                    $("#inputAbono").parent().removeClass("has-error");
                    $("#labelNoDocto").html('</br></br><b>PAGO EN EFECTIVO</b> al Cliente: '+rSocial+"</br></br>Monto max: <b>"+monto+"</b>");
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Abonar",
                        "id": "btnAgregarAbono",
                        click: function()
                        {
                            bancarizado = $("#selectFormaPago").val();
                            //formaPago  = $("#selectFormaPago").find('option:selected').attr("name");
                            $("#inputAbono").parent().removeClass("has-error");
                            montoPago   = $("#inputAbono").val();
                            idCompra    = $( "#dialog-agregar-abono" ).data('idCompra');
                            dialogo     = $(this);
                            coleccionWarning = colRowWarning();
                            var coleccionWarningJSON = JSON.stringify(coleccionWarning);
                            console.log(coleccionWarningJSON);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../ajax/maxPagoPosibleCuentaPorCobrar-grupo.php",
                                data: {coleccionWarningJSON:coleccionWarningJSON,montoPago:montoPago,formaPago:1}
                            })
                            .done(function(p)
                            {
                                if (p.status == 1)
                                {
                                    if(p.borrar == 1)
                                    {
                                        for (var rowEliminar in p.rowBorrar)
                                        {
                                            if (p.rowBorrar.hasOwnProperty(rowEliminar))
                                            {
                                                table_example
                                                .row($("#example > tbody > tr[name="+p.rowBorrar[rowEliminar]+"]"))
                                                .remove()
                                                .draw();
                                                console.log("#example > tbody > tr[name="+p.rowBorrar[rowEliminar]+"]");
                                            }
                                        }
                                        $("#example > tbody > tr:nth-child(1)").click();

                                    }
                                    if (p.actualizar == 1)
                                    {

                                        index = table_example.row($("#example > tbody > tr[name="+p.rowActualizar+"]")).index();
                                        table_example.row($("#example > tbody > tr[name="+p.rowActualizar+"]")).cell(index,10).data(p.nuevoPagado).draw();
                                        table_example.row($("#example > tbody > tr[name="+p.rowActualizar+"]")).cell(index,11).data("<b>$"+p.nuevoSaldo+"</b>").draw();
                                    }
                                    $("#labelAdeudo").text(p.adeudo);
                                    $("#example > tbody > tr[name="+p.idVenta+"]").click();
                                    dialogo.dialog( "close" );
                                    $("#divRespuesta").html(p.respuesta);
                                    if ($(".trDocumento").length == 0)
                                    {
                                        $("#divDetalle").empty();
                                    }
                                    $(".chkTrSelect").prop('checked', false);
                                    $(".trDocumento").removeClass("warning");
                                }
                                else
                                {
                                    $("#inputAbono-grupo").parent().addClass("has-error");
                                    $("#divRespuestaAbono").html(p.respuesta);
                                    $("#inputAbono-grupo").focus();
                                }

                            }).always(function(p)
                            {
                                console.log(p);
                            })
                            .fail(function()
                            {
                                alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                            });

                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarAbono",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            ///////////////////////////////////////////////////////////////////////////////////////////////////////
            $( "#dialog-agregar-abono-grupo" ).dialog(
            {
                resizable: true,
                height: "auto",
                width: 422,
                modal: true,
                autoOpen: false,
                position: { my: 'top', at: 'top+1' },
                show:
                {
                    effect: "slide",
                    duration: 300,
                    direction: 'up'
                },
                open: function()
                {
                    dialogoAbierto = 1;
                    $("#divRespuestaAbono-grupo").empty();
                    $("#divRespuesta").empty();
                    $("#inputAbono-grupo").val('');
                    rSocial = $( "#dialog-agregar-abono-grupo" ).data('rSocial');
                    monto   = $( "#dialog-agregar-abono-grupo" ).data('monto');
                    $("#inputAbono-grupo").parent().removeClass("has-error");
                    $("#labelNoDocto-grupo").html("</br></br>Cliente: <b>"+rSocial+"</b></br></br>Monto a facturar: <b>$"+monto+"</b>");
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Continuar",
                        "id": "btnAgregarAbono-grupo",
                        click: function()
                        {
                            bancarizado = $("#selectFormaPago-grupo").val();
                            formaPago  = $("#selectFormaPago-grupo").find('option:selected').attr("name");
                            $("#inputAbono-grupo").parent().removeClass("has-error");
                            montoPago   = $("#inputAbono-grupo").val();
                            idCompra    = $( "#dialog-agregar-abono-grupo" ).data('idCompra');
                            dialogo     = $(this);
                            // if(bancarizado == 0)
                            // {
                            //     coleccionWarning = colRowWarning();
                            //     var coleccionWarningJSON = JSON.stringify(coleccionWarning);
                            //     console.log(coleccionWarningJSON);
                            //     $.ajax(
                            //     {
                            //         method: "POST",
                            //         url:"../ajax/maxPagoPosibleCuentaPorCobrar-grupo.php",
                            //         data: {coleccionWarningJSON:coleccionWarningJSON,montoPago:montoPago,formaPago:formaPago}
                            //     })
                            //     .done(function(p)
                            //     {
                            //         if (p.status == 1)
                            //         {
                            //             if(p.borrar == 1)
                            //             {
                            //                 for (var rowEliminar in p.rowBorrar)
                            //                 {
                            //                     if (p.rowBorrar.hasOwnProperty(rowEliminar))
                            //                     {
                            //                         table_example
                            //                         .row($("#example > tbody > tr[name="+p.rowBorrar[rowEliminar]+"]"))
                            //                         .remove()
                            //                         .draw();
                            //                         console.log("#example > tbody > tr[name="+p.rowBorrar[rowEliminar]+"]");
                            //                     }
                            //                 }
                            //                 $("#example > tbody > tr:nth-child(1)").click();
                            //
                            //             }
                            //             if (p.actualizar == 1)
                            //             {
                            //
                            //                 index = table_example.row($("#example > tbody > tr[name="+p.rowActualizar+"]")).index();
                            //                 table_example.row($("#example > tbody > tr[name="+p.rowActualizar+"]")).cell(index,10).data(p.nuevoPagado).draw();
                            //                 table_example.row($("#example > tbody > tr[name="+p.rowActualizar+"]")).cell(index,11).data("<b>$"+p.nuevoSaldo+"</b>").draw();
                            //             }
                            //             $("#labelAdeudo").text(p.adeudo);
                            //             $("#example > tbody > tr[name="+p.idVenta+"]").click();
                            //             dialogo.dialog( "close" );
                            //             $("#divRespuesta").html(p.respuesta);
                            //             if ($(".trDocumento").length == 0)
                            //             {
                            //                 $("#divDetalle").empty();
                            //             }
                            //             $(".chkTrSelect").prop('checked', false);
                            //             $(".trDocumento").removeClass("warning");
                            //         }
                            //         else
                            //         {
                            //             if (p.montoPago == 0)
                            //             {
                            //                 $("#inputAbono-grupo").parent().addClass("has-error");
                            //                 $("#divRespuestaAbono-grupo").html(p.respuesta);
                            //                 $("#inputAbono-grupo").focus();
                            //             }
                            //         }
                            //
                            //     }).always(function(p)
                            //     {
                            //         console.log(p);
                            //     })
                            //     .fail(function()
                            //     {
                            //         alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                            //     });
                            // }
                            // else
                            // {
                                coleccionWarning = colRowWarning();
                                var coleccionWarningJSON = JSON.stringify(coleccionWarning);
                                console.log(coleccionWarningJSON);
                                //idVenta = $(this).attr("name");
                                $.ajax(
                                {
                                    method: "POST",
                                    url:"../ws/rev_factura_monto_grupo.php",
                                    data: {coleccionVentas:coleccionWarningJSON}
                                })
                                .done(function(p)
                                {
                                    if (p.status == 0)
                                    {
                                        /*if (p.montoPago == 0 || p.saldoMenor == 0 || p.saldoMayor == 0)
                                        {*/
                                            $("#inputAbono-grupo").parent().addClass("has-error");
                                            $("#divRespuestaAbono-grupo").html(p.respuesta);
                                            $("#inputAbono-grupo").focus();
                                        // }

                                    }
                                    else
                                    {
                                        $("#dialog-revisar-factura-monto-grupo" ).dialog("open");
                                        $("#divRevFactura-grupo").html(p);
                                    }


                                })
                                .fail(function()
                                {
                                    alert("No se puede acceder en este momento. Consulte con el adminsitrador del sistema");

                                })
                                .always(function(p)
                                {
                                    console.log(p);
                                })
                            // }
                            //$( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarAbono",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $( "#dialog-agregar-comp" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
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
                    $("#inputMonto-comp").val(saldo);
                    $("#tdMsg").empty();
                    $("#ifram").css("visibility","hidden");
                    $(this).parent().find(".ui-dialog-titlebar-close").remove();
                },
                close: function()
                {
                    $("#divRespuestaModal-comp").empty();
                },
                buttons:
                [
                    {
                        text: "Facturar...",
                        id: "btnReenviarCfdiCompl",
                        click: function()
                        {
                            enviarA         = $('#inputEmail-comp').val();
                            idFactura       = $("#dialog-agregar-comp" ).data('idFactura');
                            montoEmitir     = $("#inputMonto-comp").val();
                            formaPago       = $("#selectFormaPago-comp").find('option:selected').attr("name");
                            //opciones    = $("input:radio[name=optionsRadios]:checked").val();

                            //$('iframe').attr('src', $('iframe').attr('src'));
                            $("#btnReenviarCfdiCompl").attr("disabled", true);
                            $("#btnCancelarReenviarCfdi-comp").attr("disabled", true);
                            $("#btnReenviarCfdiCompl").html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i> Facturando...');
                            $('#inputEmail').attr("disabled", true);
                            // $("#btnReenviarCfdiCompl").attr("disabled", false);
                            // $("#btnCancelarReenviarCfdi-comp").attr("disabled", false);
                            // $("#btnReenviarCfdiCompl").html('Facturar');
                            $('#inputEmail-comp').attr("disabled", false);
                            //$("#divRespuestaModal").html(e.respuesta);
                            $("#ifram").css("visibility","visible");
                            $("#ifram").attr('src','../ws/gen_complemento_pago.php?idFactura='+idFactura+'&enviarA='+enviarA+'&enviar=1&formaPago='+formaPago+'&montoEmitir='+montoEmitir);
                            $("#tdMsg").css("color","dimgrey");
                            $("#tdMsg").html('<i class="fa fa-2x fa-spinner fa-pulse pull-left" aria-hidden="true"></i> No cierres esta ventana mientras est&aacute;</br> en proceso la facturaci&oacute;n!!')

                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarReenviarCfdi-comp",
                        click: function()
                        {
                            if (refrescarPag == 1)
                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/cuentasXcobrar.php");
                            else
                                $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            var table_example = $('#example').DataTable(
            {
                "lengthMenu": [[-1, 10, 20, 50, 100, 200, 500, 1000],["Todos", 10, 20, 50, 100, 200, 500, 1000]],
                "order": [[ 1, "desc" ]],
                "columnDefs": [{ "orderable": false, "targets": 0 }],
                "language":
                {
                     "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
                 },
                 "initComplete": function(settings, json)
                 {
                     $("#example > tbody > tr:nth-child(1)").click();
                 }
            });
            $( "#dialog-revisar-factura" ).dialog(
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
                    $("#tdMsgInd").css("color","dimgrey");
                    $("#ifram_monto").css("visibility","hidden");
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

                                armarFactura();
                                idVenta = $("#hiddenIdVenta").val();
                                enviarA = $("#inputEmail").val();
                                enviar  = ($('#chkEmail').is(':checked')) ? 1 : 0;
                                metodoP = $("#selectMetodo").val();
                                usoCFDI = $("#selectUso").val();
                                var coleccionFacturaJSON = JSON.stringify(coleccionFactura);
                                console.log(coleccionFacturaJSON);
                                $("#btnFacturarInd").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> factura!');
                                //$(".inputSAT").attr("disabled",true);
                                $(".inputSAT").attr("readonly",true);
                                $(".inputIva").attr("readonly",true);
                                $(".inputIeps").attr("readonly",true);
                                //$(".inputDescuento").attr("readonly",true);
                                $("#btnFacturarInd").attr("disabled",true);
                                $("#btnCancelarFacturarInd").attr("disabled",true);
                                $("#tdMsgInd").css("color","dimgrey");
                                $("#tdMsgInd").html('<i class="fa fa-2x fa-spinner fa-pulse pull-left" aria-hidden="true"></i> No cierres esta ventana mientras est&aacute;</br> en proceso la facturaci&oacute;n!!')
                                $("#ifram_monto").css("visibility","visible");
                                $("#ifram_monto").attr('src','../ws/gen_factura_monto.php?idVenta='+idVenta+'&enviarA='+enviarA+'&enviar='+enviar+'&usoCFDI='+usoCFDI+'&metodoPago='+metodoP+'&coleccionFacturaJSON='+coleccionFacturaJSON+'&formaPago='+formaPago);
                                //$('iframe#ifram').attr('src', $('iframe#ifram').attr('src'));
                            }
                            //$( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarFacturarInd",
                        click: function()
                        {
                            if (refrescarPag == 1)
                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/cuentasXcobrar.php");
                            else
                                $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-revisar-factura-monto-grupo" ).dialog(
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
                    $("#btnFacturarInd-g").css("display","inline");
                    $("#btnFacturarInd-g").attr("disabled",false);
                    $("#btnFacturarInd-g").html('<i class="fa fa-rocket" aria-hidden="true"></i> factura!');
                    $("#btnCancelarFacturarInd").attr("disabled",false);
                    $("#tdMsgInd_g").css("color","dimgrey");
                    $("#ifram_monto_grupo").css("visibility","hidden");
                },
                buttons:
                [
                    {
                        text: "Factura!",
                        id: "btnFacturarInd-g",
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
                                idCliente                   = $("#hiddenIdCliente").val();
                                enviarA                     = $("#inputEmail_grupo").val();
                                metodoP                     = $("#selectMetodo_grupo").val();
                                usoCFDI                     = $("#selectUso").val();
                                enviar                      = ($('#chkEmail_grupo').is(':checked')) ? 1 : 0;
                                formaPago                   = $("#selectFormaPago-grupo").find('option:selected').attr("name");
                                cadenaVentas                = $("#hiddenCadenaVentas").val();
                                var coleccionSubVentaJSON   = JSON.stringify(coleccionSubVenta);
                                var coleccionFacturaJSON    = JSON.stringify(coleccionFactura);
                                console.log(coleccionFacturaJSON);
                                $("#btnFacturarInd-g").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> factura!');
                                //$(".inputSAT").attr("disabled",true);
                                $(".inputSAT").attr("readonly",true);
                                $(".inputIva").attr("readonly",true);
                                $(".inputIeps").attr("readonly",true);
                                //$(".inputDescuento").attr("readonly",true);
                                $("#btnFacturarInd-g").attr("disabled",true);
                                $("#btnCancelarFacturarInd-g").attr("disabled",true);
                                $("#tdMsgInd_g").css("color","dimgrey");
                                $("#tdMsgInd_g").html('<i class="fa fa-2x fa-spinner fa-pulse pull-left" aria-hidden="true"></i> No cierres esta ventana mientras est&aacute;</br> en proceso la facturaci&oacute;n!!')
                                $("#ifram_monto_grupo").css("visibility","visible");
                                $("#ifram_monto_grupo").attr('src','../ws/gen_factura_monto_grupo.php?enviarA='+enviarA+'&idCliente='+idCliente+'&enviar='+enviar+'&usoCFDI='+usoCFDI+'&metodoPago='+metodoP+'&coleccionFacturaJSON='+coleccionFacturaJSON+'&coleccionIdSubVentaJSON='+coleccionSubVentaJSON+'&formaPago='+formaPago+'&cadenaVentas='+cadenaVentas);
                                // f = $("#ifram");
                                // f.reload();
                                //$('iframe#ifram').attr('src', $('iframe#ifram').attr('src'));
                            }
                            //$( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarFacturarInd-g",
                        click: function()
                        {
                            if (refrescarPag == 1)
                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/cuentasXcobrar.php");
                            else
                                $( this ).dialog( "close" );
                        }
                    }
                ]
            });


        });
    </script>
    <div id="dialog-agregar-abono" class="dialog-oculto" title="Agregar abono">
        <p>
            <div class="col-lg-12" id="divRespuestaAbono" style="padding-right:0px;padding-left:0px">
            </div>
            <h4><i class="fa fa-asterisk" aria-hidden="true"></i> Agregar abono <span id="labelNoDocto"></span></h4>
            </br>
            <div class="form-group input-group">
                <span class="input-group-addon">$</span>
                <input style="text-align:right;" id="inputAbono" type="number" class="form-control" min="1">
            </div>
        </p>
    </div>
    <div id="dialog-agregar-abono-grupo" class="dialog-oculto" title="Generar factura">
        <p>
            <div class="col-lg-12" id="divRespuestaAbono-grupo" style="padding-right:0px;padding-left:0px">
            </div>
            <h4><i class="fa fa-asterisk" aria-hidden="true"></i> Generar factura para <span id="labelNoDocto-grupo"></span></h4>
        </p>
    </div>
    <div id="dialog-revisar-factura" class="dialog-oculto" title="Revisar la factura">
        <p>
            <div class="col-lg-12" id="divRevFactura">
            </div>
        </p>
    </div>
    <div id="dialog-revisar-factura-monto-grupo" class="dialog-oculto" title="Revisar la factura">
        <p>
            <div class="col-lg-12" id="divRevFactura-grupo">
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
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align:right">
                    <h4 style="" id="tdMsg"></h4>
                </div>
            </div>
        </p>
    </div>
</body>
</html>
<?php
}
?>
