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

    <title>Cuentas por pagar</title>
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
        #example_filter
        {
            text-align: right;
            padding-right: 18px;
        }
        .trDocumento,#aAbonar
        {
            cursor: pointer;
        }
        .dialog-oculto
        {
            display: none;
        }

    </style>
</head>
<body>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <h1 class="page-header"><i class="fa fa-credit-card-alt" aria-hidden="true"></i> Cuentas por pagar</h1>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" id="divRespuesta" style="margin-top:20px">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9" style="padding-right:0px">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-list-alt" aria-hidden="true"></i> Lista de documentos pendientes por pagar

                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                <!--<div class="table-responsive">-->
                                    <table class="display compact table table-striped table-bordered table-hover" id="example" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>No. Docto</th>
                                                <th>Tipo</th>
                                                <th>Cajero</th>
                                                <th>Fecha Compra</th>
                                                <th>Fecha Expira</th>
                                                <th>Proveedor</th>
                                                <th>Total venta</th>
                                                <th>Abono</th>
                                                <th>Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
                                    $sql = "SELECT
                                                compras.id              AS idCompra,
                                                compras.nodocumento     AS noDocto,
                                                compras.esfactura       AS esFactura,
                                                compras.usuario         AS idCajero,
                                                compras.timestamp       AS fechaCompra,
                                                compras.fechaexpira     AS fechaExpira,
                                                compras.proveedor       AS idProveedor,
                                                compras.monto           AS montoCompra,
                                                usuarios.nombre         AS nombreCajero,
                                                usuarios.apellidop      AS apellidopCajero,
                                                proveedores.rsocial     AS nombreProveedor,
                                                (SELECT SUM(monto) FROM pagosemitidos
                                                WHERE pagosemitidos.idcompra = compras.id) AS montoPagado
                                                FROM compras
                                                INNER JOIN usuarios
                                                ON compras.usuario = usuarios.id
                                                INNER JOIN proveedores
                                                ON compras.proveedor = proveedores.id
                                                WHERE compras.pagado = 0";
                                    $result = $mysqli->query($sql);
                                    $adeudo = 0;
                                    while ($rowDoc = $result->fetch_assoc())
                                    {
                                        $fechaCompra        = date('d-m-Y',strtotime($rowDoc['fechaCompra']));
                                        $fechaExpira        = date('d-m-Y',strtotime($rowDoc['fechaExpira']));
                                        $adeudo             += $rowDoc['montoCompra'] - $rowDoc['montoPagado'];
?>
                                            <tr class="trDocumento" name="<?php echo $rowDoc['idCompra'];?>">
                                                <td class="text-right"><?php echo $rowDoc['idCompra'];?></td>
                                                <td class="text-right"><?php echo $rowDoc['noDocto'];?></td>
                                                <td class="text-center"><?php echo ($rowDoc['esFactura'] == 1) ? '<span class="label label-info"><i class="fa fa-file-text-o" aria-hidden="true"></i> Factura</span>' : '<span class="label label-success"><i class="fa fa-print" aria-hidden="true"></i> Remisi&oacute;n</span>';?></td>
                                                <td><?php echo $rowDoc['nombreCajero']." ".$rowDoc['apellidopCajero'];?></td>
                                                <td class="text-left"><?php echo $fechaCompra;?></td>
                                                <td class="text-left"><?php echo $fechaExpira;?></td>
                                                <td><?php echo $rowDoc['nombreProveedor'];?></td>
                                                <td class="text-right"><?php echo "$".number_format($rowDoc['montoCompra'],2,".",",");?></td>
                                                <td class="text-right"><?php echo "$".number_format($rowDoc['montoPagado'],2,".",",")?></td>

                                                <td class="text-right"><b><?php echo "$".number_format($rowDoc['montoCompra'] - $rowDoc['montoPagado'],2,".",",");?></b></td>
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
                                <!--</div>-->
                                <!-- /.table-responsive -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Lista de pagos
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        Acciones
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a id="aAbonar">Abonar...</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body" id="divDetalle">
                        </div>
                    </div>
                    <!-- /.col-lg-8 (nested) -->
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
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
        dialogoAbierto = 0;
        $(document).ready(function()
        {
            $(".trDocumento").click(function()
            {
                $(".trDocumento").removeClass("success");
                $(this).addClass("success");
                idCompra = $(this).attr("name");
                $.ajax(
                {
                    method: "POST",
                    url:"../control/listarPagosCuentasxPagar.php",
                    data: {idCompra:idCompra}
                })
                .done(function(p)
                {
                    $("#divDetalle").html(p);
                });
            });
            $("#aAbonar").click(function()
            {
                $(".trDocumento").each(function()
                {
                    if ($(this).hasClass("success"))
                    {
                        idCompra = $(this).attr("name");
                        $( "#dialog-agregar-abono" ).data('idCompra',idCompra).dialog("open");
                        return false;
                    }
                });
            });
            $( "#dialog-agregar-abono" ).dialog(
            {
                resizable: true,
                height: "auto",
                width: 410,
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
                    idCompra = $( "#dialog-agregar-abono" ).data('idCompra');
                    $("#inputAbono").parent().removeClass("has-error");
                    $("#labelNoDocto").text(idCompra);
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Agregar",
                        "id": "btnAgregarAbono",
                        click: function()
                        {
                            $("#inputAbono").parent().removeClass("has-error");
                            montoPago   = $("#inputAbono").val();
                            idCompra    = $( "#dialog-agregar-abono" ).data('idCompra');
                            dialogo     = $(this);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../ajax/maxPagoPosibleCuentaPorPagar.php",
                                data: {idCompra:idCompra,montoPago:montoPago}
                            })
                            .done(function(p)
                            {
                                if (p.status == 1)
                                {
                                    if(typeof p.borrar != "undefined")
                                    {
                                        table_example
                                        .row($("#example > tbody > tr[name="+p.borrar+"]"))
                                        .remove()
                                        .draw();
                                        $("#example > tbody > tr:nth-child(1)").click();

                                    }
                                    else
                                    {
                                        index = table_example.row($("#example > tbody > tr[name="+p.idCompra+"]")).index();
                                        table_example.row($("#example > tbody > tr[name="+p.idCompra+"]")).cell(index,8).data(p.pagado).draw();
                                        table_example.row($("#example > tbody > tr[name="+p.idCompra+"]")).cell(index,9).data(p.saldo).draw();
                                        $("#example > tbody > tr[name="+p.idCompra+"]").click();
                                    }
                                    $("#labelAdeudo").text(p.adeudo);
                                    dialogo.dialog( "close" );
                                    $("#divRespuesta").html(p.respuesta);
                                    if ($(".trDocumento").length == 0)
                                    {
                                        $("#divDetalle").empty();
                                    }
                                }
                                else
                                {
                                    if (p.montoPago == 0)
                                    {
                                        $("#inputAbono").parent().addClass("has-error");
                                    }
                                    $("#divRespuestaAbono").html(p.respuesta);
                                }

                            }).always(function(p)
                            {
                                console.log(p);
                            })
                            .fail(function()
                            {
                                alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                            });
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
            var table_example = $('#example').DataTable(
             {
                "lengthMenu": [[-1, 10, 20, 50, 100, 200, 500, 1000],["Todos", 10, 20, 50, 100, 200, 500, 1000]],
                "order": [[ 0, "desc" ]],
                "language":
                {
                     "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
                 },
                 responsive: true,
                 "initComplete": function(settings, json)
                 {
                     $("#example > tbody > tr:nth-child(1)").click();
                    //#example > tbody > tr:nth-child(1)
                 }
            });
        });
    </script>
    <div id="dialog-agregar-abono" class="dialog-oculto" title="Agregar abono">
        <p>
            <div class="col-lg-12" id="divRespuestaAbono" style="padding-right:0px;padding-left:0px">
            </div>
            <h4><i class="fa fa-asterisk" aria-hidden="true"></i> Agregar abono a documento #<label id="labelNoDocto"></label></h4>
            <div class="form-group input-group">
                <span class="input-group-addon">$</span>
                <input style="text-align:right;" id="inputAbono" type="number" class="form-control" min="1">
            </div>
        </p>
    </div>
</body>

</html>
<?php
}
?>
