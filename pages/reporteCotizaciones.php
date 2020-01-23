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

    <title>Cotizaciones</title>
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
        .dialog-oculto
        {
            display: none;
        }
        .aReimprimirTkt, .aReimprimirVenta, .aCancelar, .aConvertiraVentaTkt, .aConvertiraVentaRem, .aReenviarCotizacion, .aEditar
        {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="recibo" style="display:none"></div>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-8 col-md-9 col-sm-12 col-xs-12">
                    <h1 class="page-header"> <i class="fa fa-file-text-o" aria-hidden="true"></i> Cotizaciones</h1>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
<?php
        if (isset($_POST['fechaInicio']) && isset($_POST['fechaFin']))
        {
            if(validarFecha($_POST['fechaInicio'], 'Y-m-d') && validarFecha($_POST['fechaFin'], 'Y-m-d') && $_POST['fechaInicio'] <= $_POST['fechaFin'])
            {
                $fechaInicio    = $_POST['fechaInicio'];
                $fechaFin       = $_POST['fechaFin'];
?>
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="divRespuesta" style="margin-left:15px;padding-right:30px">

            </div>
<?php
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
?>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12" id="divRespuesta" style="margin-left:15px;padding-right:30px">

                </div>
<?php
        }

 ?>
            </div>
            <div class="row">
                <form role="form" method="POST">
                    <div class="col-lg-12">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
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
                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    &nbsp;
                                    <!--<div class="btn-group pull-right">
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
                                    </div>-->
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Fecha Fin</label>
                                        <input type="date" required value="<?php echo $fechaFin;?>" class="form-control" placeholder="Enter text" id="fechaFin" name="fechaFin">
                                    </div>
                                </div>
                                <div class="panel-footer" style="text-align:right">
                                    <button type="submit" class="btn btn-primary btn-sm" id="btnSubmit"><i class="fa fa-calculator" aria-hidden="true"></i> Calcular</button>
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
            <div class="row">
                <table id="example" class="display compact table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Folio</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cajero</th>
                            <th>Cliente</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    if ($consultar == 1)
    {
        $fechaInicioSQL = $fechaInicio." 00:00:00";
        $fechaFinSQL = $fechaFin." 23:59:59";
        $sql = "SELECT
                    cotizaciones.id             AS idCotizacion,
                    cotizaciones.idVenta        AS idVenta,
                    cotizaciones.timestamp      AS fechaHora,
                    cotizaciones.cliente        AS idCliente,
                    cotizaciones.descuento      AS descuento,
                    cotizaciones.usuario        AS idUsuario,
                    cotizaciones.metododepago   AS idMetodo,
                    cotizaciones.totalventa     AS totalVenta,
                    usuarios.nombre             AS nombreCajero,
                    usuarios.apellidop          AS apellidopCajero,
                    clientes.rsocial            AS rSocial,
                    clientes.representante      AS representante,
                    clientes.email              AS email,
                    tipoprecios.nombreCorto     AS tipoPrecio
                FROM cotizaciones
                INNER JOIN usuarios
                ON cotizaciones.usuario = usuarios.id
                INNER JOIN clientes
                ON cotizaciones.cliente = clientes.id
                INNER JOIN tipoprecios
                ON cotizaciones.tipoprecio = tipoprecios.id
                WHERE   (cotizaciones.timestamp BETWEEN '$fechaInicioSQL' AND '$fechaFinSQL')
                ORDER BY cotizaciones.id ASC";
        if ($resultadoVentas = $mysqli->query($sql))
        {
            $totalFilas = $resultadoVentas->num_rows;
            while ($rowVenta = $resultadoVentas->fetch_assoc())
            {
                $fechaNota             = date('d-m-Y',strtotime($rowVenta['fechaHora']));
                $horaNota              = date('H:i:s',strtotime($rowVenta['fechaHora']));
                $nombreCompCliente      = $rowVenta['rSocial'];
                $nombreCompCajero       = $rowVenta['nombreCajero']." ".$rowVenta['apellidopCajero'];

    ?>

                <tr class="rowReporte">
                    <td>
                        <div class="btn-group pull-left">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-bars" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu slidedown">
                                <li>
                                    <a href="../control/genRemisionCotizacionPDF.php?idCotizacion=<?php echo $rowVenta['idCotizacion'];?>" target="_blank" name="<?php echo $rowVenta['idVenta'];?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Reimprimir cotizaci&oacute;n
                                    </a>
                                </li>
    <?php
                if($rowVenta['idVenta'] == NULL)
                {
    ?>
                                <li>
                                    <a class="aConvertiraVentaTkt" name="<?php echo $rowVenta['idCotizacion'];?>" totalVenta="<?php echo $rowVenta['totalVenta'];?>" idCliente="<?php echo $rowVenta['idCliente'];?>">
                                        <i class="fa fa-ticket" aria-hidden="true"></i> Vender como ticket
                                    </a>
                                </li>
                                <li>
                                    <a class="aConvertiraVentaRem" name="<?php echo $rowVenta['idCotizacion'];?>" totalVenta="<?php echo $rowVenta['totalVenta'];?>" idCliente="<?php echo $rowVenta['idCliente'];?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Vender como remisi&oacute;n
                                    </a>
                                </li>
                                <li>
                                    <a class="aEditar" href="editarCotizacion.php?idCotizacion=<?php echo $rowVenta['idCotizacion'];?>" name="<?php echo $rowVenta['idCotizacion'];?>">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
                                    </a>
                                </li>
    <?php
                }
                else
                {
    ?>
    <?php
                    $idVenta = $rowVenta['idVenta'];
                    $sqlRemision        = "SELECT remision FROM ventas WHERE id = $idVenta LIMIT 1";
                    $resRemision        = $mysqli->query($sqlRemision);
                    $rowRemision        = $resRemision->fetch_assoc();
                    if ($rowRemision['remision'] == 1)
                    {
    ?>
                                <li>
                                    <a href="../control/genRemisionVentaPDF.php?idVenta=<?php echo $rowVenta['idVenta'];?>" target="_blank" class="aReimprimirRem" name="<?php echo $rowVenta['idVenta'];?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Reimprimir Remisi&oacute;n
                                    </a>
                                </li>
    <?php
                    }
                    else
                    {
    ?>
                                <li>
                                    <a class="aReimprimirVenta" name="<?php echo $rowVenta['idVenta'];?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Reimprimir ticket
                                    </a>
                                </li>
    <?php
                    }
                }
    ?>
                                <li>
                                    <a class="aReenviarCotizacion" name="<?php echo $rowVenta['idCotizacion'];?>" email="<?php echo $rowVenta['email'];?>" rSocial="<?php echo $rowVenta['rSocial'];?>">
                                        <i class="fa fa-share-square-o" aria-hidden="true"></i> Enviar por e-mail
                                    </a>
                                </li>
                                <li>
                                    <a class="aCancelar" name="<?php echo $rowVenta['idCotizacion'];?>">
                                        <i class="fa fa-ban" aria-hidden="true"></i> Cancelar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <?php echo $rowVenta['idCotizacion'];?>
                    </td>
                    <td>
        <?php
            if($rowVenta['idVenta'] == NULL)
            {
        ?>
                <span class='label label-default'>
                    Sin vender
                </span>
        <?php
            }
            else
            {
        ?>
                <span class='label label-primary'>
                    Vendida
                </span>
        <?php
            }
         ?>

                    </td>
                    <td>
                        <?php echo $fechaNota; ?>
                    </td>
                    <td>
                        <?php echo $horaNota; ?>
                    </td>
                    <td>
                        <?php echo $nombreCompCajero; ?>
                    </td>
                    <td>
                        <?php echo $nombreCompCliente; ?>
                    </td>
                    <td class="text-right">
                        <?php echo "$".number_format($rowVenta['totalVenta'],2); ?>
                    </td>
                </tr>
    <?php
            }
        }
    }
    ?>
                    </tbody>
                </table>
                <input type="hidden" id="hiddenRemision" name="hiddenRemision" value="0">
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
    <script src="../control/custom-js/redondearDec.js"></script>
    <script src="../startbootstrap/vendor/printThis/printThis.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    coleccionTabla  = [];
    dialogoAbierto  = 0;
    function rowColeccionable(numero, fecha, hora, cliente, descuento, cajero, metodo, tipo, total)
    {
        this.numero = numero;
        this.fecha = fecha;
        this.hora = hora;
        this.cliente = cliente;
        this.descuento = descuento;
        this.cajero = cajero;
        this.metodo = metodo;
        this.tipo = tipo;
        this.total = total;
    }
    function formatoMoneda(amount)
    {
        decimals = 2;
        amount += ''; // por si pasan un numero en vez de un string
        amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto
        decimals = decimals || 0; // por si la variable no fue fue pasada
        // si no es un numero o es igual a cero retorno el mismo cero
        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);
        // si es mayor o menor que cero retorno el valor formateado como numero
        amount = '' + amount.toFixed(decimals);
        var amount_parts = amount.split('.'),
            regexp = /(\d+)(\d{3})/;
        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');
        return amount_parts.join('.');
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
                 "order": [[ 1, "desc" ]],
                 responsive: true
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
                    url:"../control/reimprimirCotizacionRecibo.php",
                    data: {id:nombre}
                })
                .done(function(p)
                {
                    $("#recibo").html(p.recibo).promise().done(function()
                    {
                        JsBarcode("#code_svg",p.codigo,
                        {
                            width:1,
                            height:35,
                            fontSize:10,
                            margin:1
                        });
                        $('#recibo').printThis();
                    });
                });
            });
            $(".aReenviarCotizacion").click(function()
            {
                nombre  = $(this).attr('name');
                email   = $(this).attr('email');
                rSocial = $(this).attr('rSocial');
                $( "#dialog-reenviar-cotizacion" ).data('idCotizacion',nombre).data('email',email).data('rSocial',rSocial).dialog("open");
            });
            $(".aReimprimirVenta").click(function()
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
            $(".aConvertiraVentaRem,.aConvertiraVentaTkt").click(function()
            {
                tipoVenta       = $(this).hasClass('aConvertiraVentaRem') ? 1 : 0
                $("#hiddenRemision").val(tipoVenta);
                idCotizacion    = $(this).attr('name');
                totalVenta      = $(this).attr('totalVenta');
                idCliente       = $(this).attr('idCliente');
                $( "#dialog-confirm-venta" ).data('idCotizacion',idCotizacion).data('totalVenta',totalVenta).data('idCliente',idCliente).dialog("open");
            });
            // $(".aConvertiraVentaTkt").click(function()
            // {
            //     $("#hiddenRemision").val(0);
            //     idCotizacion = $(this).attr('name');
            //     $( "#dialog-confirm-venta" ).data('idCotizacion',idCotizacion).dialog("open");
            // });
            $( "#dialog-confirm-convertirCot" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
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
                        text: "Guardar",
                        id: "btnDialogGuardarVenta",
                        click: function()
                        {
                            $("#btnDialogGuardarVenta").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            $("#btnDialogGuardarVenta").prop("disabled", true);
                            $("#btnDialogCancelarVenta").prop("disabled", true);
                            //y = listaProductos.splice(idCotizacion,1);
                            //$(".tridCotizacionLista[name="+idCotizacion+"]").remove();
                            //$( this ).dialog( "close" );
                            //reordenaridCotizacions();
                            dialogo         = $(this);
                            idCotizacion    = $( "#dialog-confirm-convertirCot" ).data('item');
                            tipoVenta       = $("#selectTipoVenta").val();
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/cotizacionaVentaBd.php",
                                data: {idCotizacion:idCotizacion,tipoVenta:tipoVenta}
                            })
                            .done(function(e)
                            {
                                if(e.status == 1)
                                {
                                    if (e.remision == 0)
                                    {
                                        $("#recibo").html(e.recibo).promise().done(function()
                                        {
                                            $('#recibo').printThis();
                                            setTimeout(function()
                                            {
                                                $("#btnSubmit").click();
                                                //dialogo.dialog( "close" );
                                                //window.location.assign("http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/reporteCotizaciones.php");
                                            }, 2250);

                                        });
                                    }
                                    else
                                    {
                                        url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionCotizacionPDF.php?idCotizacion="+e.idCotizacion;
                                        $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                        window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/reporteCotizaciones.php");
                                        dialogo.dialog("close");
                                    }
                                }
                                else
                                {
                                    $("#btnDialogGuardarVenta").html('Guardar');
                                    $("#btnDialogGuardarVenta").prop("disabled", false);
                                    $("#btnDialogCancelarVenta").prop("disabled", false);
                                    $("#divRespuesta").html(e.respuesta);
                                    dialogo.dialog( "close" );
                                }
                            }).fail(function(e)
                            {
                                console.log(e);
                            });
                        }
                    },
                    {
                        text: "Cancelar",
                        id: "btnDialogCancelarVenta",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-reenviar-cotizacion" ).dialog(
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
                    idCotizacion    = $( "#dialog-reenviar-cotizacion" ).data('idCotizacion');
                    email           = $( "#dialog-reenviar-cotizacion" ).data('email');
                    rSocial         = $( "#dialog-reenviar-cotizacion" ).data('rSocial');
                    $('#inputEmail').val(email);
                    $("#pIdFactura").text(idCotizacion);
                    $("#pRazonReceptor").text(rSocial);
                },
                close: function()
                {
                    $("#divRespuestaModal").empty();
                },
                buttons:
                [
                    {
                        text: "Reenviar",
                        id: "btnReenviarCotizacion",
                        click: function()
                        {
                            eMail       = $('#inputEmail').val();
                            idCotizacion= $("#dialog-reenviar-cotizacion" ).data('idCotizacion');
                            $("#btnReenviarCotizacion").attr("disabled", true);
                            $("#btnCancelarReenviarCotizacion").attr("disabled", true);
                            $("#btnReenviarCotizacion").html('<i class="fa fa-spinner fa-pulse fa-fw" aria-hidden="true"></i> Enviando...');
                            $('#inputEmail').attr("disabled", true);

                            $.ajax(
                            {
                                method: "POST",
                                url: "../control/reenviarCotizacion.php",
                                data: {idCotizacion:idCotizacion,eMail:eMail}
                            })
                            .done(function(e)
                            {
                                $("#btnReenviarCotizacion").attr("disabled", false);
                                $("#btnCancelarReenviarCotizacion").attr("disabled", false);
                                $("#btnReenviarCotizacion").html('Reenviar');
                                $('#inputEmail').attr("disabled", false);
                                $("#divRespuestaModal").html(e.respuesta);
                            })
                            .always(function(e)
                            {
                                console.log(e);
                            });
                        }
                    },
                    {
                        text: "Cerrar",
                        id: "btnCancelarReenviarCotizacion",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-confirm-venta" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
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

                    totalVenta      = $( "#dialog-confirm-venta" ).data('totalVenta');
                    idCliente       = $( "#dialog-confirm-venta" ).data('idCliente');
                    $("#selectClientePos").val(idCliente);
                    $("#chkAbonos").prop('checked',false);
                    $("#divAbono")      .hide();
                    if (idCliente   == '1')
                    {
                        $("#divChkAbonos")  .hide();
                        $("#inputAbono")    .prop("disabled", true);
                    }
                    else
                    {
                        $("#divChkAbonos")  .show();
                        $("#inputAbono")    .prop("disabled", false);
                    }
                    //$("#divChkAbonos")  .show("fade",200);
                    totalVenta      = parseFloat(totalVenta);
                    tipoVenta       = parseInt($("#hiddenRemision").val());
                    txtVenta        = (tipoVenta == 1) ? '<i class="fa fa-print" aria-hidden="true"></i> REMISIÓN' : '<i class="fa fa-ticket" aria-hidden="true"></i> TICKET';
                    $("#h3Remision").html(txtVenta);
                    dialogoAbierto = 1;
                    //reordenarItems();
                    //tot = 0;
                    // for(x=0; x < arrayListaProductos.length; x++)
                    // {
                    //     tot = parseFloat(tot) + parseFloat(redondearDec(arrayListaProductos[x]["precioU"] * arrayListaProductos[x]["cantidad"]));
                    // }
                    $("#inputMontoTotal").val(formatoMoneda(totalVenta));
                    $("#hiddenMontoTotal").val(totalVenta);
                    $("#inputAbono").val(totalVenta.toFixed(2));
                    $("#inputPagaCon").val("");
                    if(isNaN($("#inputPagaCon").val()))
                        $("#inputCambio").val(0);
                    $("#inputPagaCon").parent().addClass("has-error")
                    $("#inputPagaCon").focus();
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Aceptar",
                        "id": "btnTerminarVenta",
                        click: function()
                        {
                            $("#divRespuestaVta").empty();
                            msg = ' <div class="alert alert-danger alert-dismissable">';
                            msg +='     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                            msg +='     <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Completa los campos marcados';
                            msg +=' </div>';
                            if ($("#chkAbonos").prop("checked"))
                            {
                                if ($("#inputPagaCon").parent().hasClass("has-error") ||
                                    $("#inputAbono").parent().hasClass("has-error"))
                                    {
                                        $("#divRespuestaVta").html(msg);
                                        return false;
                                    }
                            }
                            else
                            {
                                if ($("#inputPagaCon").parent().hasClass("has-error"))
                                {
                                    $("#divRespuestaVta").html(msg);
                                    return false;
                                }
                            }

                            //return false;
                            idCotizacion    = $( "#dialog-confirm-venta" ).data('idCotizacion');
                            idCliente       = $("#selectClientePos").val();
                            montoTotal      = $( "#dialog-confirm-venta" ).data('totalVenta');
                            //total           = $("#hiddenMontoTotal").val();
                            btnTerminarVenta= $("#btnTerminarVenta");
                            btnCancelarVenta= $("#btnCancelarVenta");
                            btnTerminarVenta.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            btnTerminarVenta.prop("disabled", true);
                            btnCancelarVenta.prop("disabled", true);
                            dialogo         = $(this);
                            //form_           = $("#form-confirm-venta").serialize();
                            chkImprimir     = ($("#chkImprimir").prop("checked")) ? 1 : 0;
                            chkOcultarPU    = ($("#chkOcultarPU").prop("checked")) ? 1 : 0;
                            pagaCon         = $("#inputPagaCon").val();
                            //idCliente       = $("#selectClientePos").val();
                            //montoTotal      = $("#hiddenMontoTotal").val();
                            remision        = $("#hiddenRemision").val();
                            chkAbonos       = ($("#chkAbonos").prop("checked")) ? 1 : 0;
                            inputAbono      = $("#inputAbono").val();
                            inputCambio     = $("#inputCambio").val();

                            //var listaProductosJSON = JSON.stringify(arrayListaProductos);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/cotizacionaVentaBd.php",
                                data: {idCotizacion:idCotizacion,remision:remision,chkImprimir:chkImprimir,chkOcultarPU:chkOcultarPU,pagaCon:pagaCon,idCliente:idCliente,montoTotal:montoTotal,chkAbonos:chkAbonos,inputAbono:inputAbono,inputCambio:inputCambio}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    //arrayListaProductos.length = 0;
                                    if(p.imprimir == 1)
                                    {
                                        if (p.remision == 1)
                                        {
                                            url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionVentaPDF.php?idVenta="+p.idVenta;
                                            $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                            setTimeout(function()
                                            {
                                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/reporteCotizaciones.php");
                                            }, 620);
                                        }
                                        else
                                        {
                                            $('#recibo').html(p.recibo).promise().done(function()
                                            {
                                                JsBarcode("#code_svg",p.codigo,
                                                {
                                                    width:2,
                                                    height:35,
                                                    fontSize:13,
                                                    margin:1
                                                });
                                                $('#recibo').printThis();
                                                setTimeout(function()
                                                {
                                                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/reporteCotizaciones.php");
                                                }, 2250);
                                            });
                                        }
                                    }
                                    else
                                    {
                                        setTimeout(function()
                                        {
                                            window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/reporteCotizaciones.php");
                                        }, 620);
                                    }
                                }
                                else
                                {
                                    alert("No se puede guardar");
                                    dialogo.dialog( "close" );
                                }
                            })
                            .fail(function()
                            {
                                msg = ' <div class="alert alert-danger alert-dismissable">';
                                msg +='     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                                msg +='     <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se puede guardar en este momento. Consulte con el adminsitrador del sistema';
                                msg +=' </div>';
                                $("#divRespuestaVta").html(msg);
                                btnTerminarVenta.html('Aceptar');
                                btnTerminarVenta.prop("disabled", false);
                                btnCancelarVenta.prop("disabled", false);
                            })
                            .always(function(p)
                            {
                                console.log(p);
                            });
                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarVenta",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $("body").on("change","#selectClientePos",function()
            {
                //reordenarItems();
                val = $(this).val();
                $("#selectCliente").val(val);
                if(val==1)
                {
                    $("#inputPagaCon")  .focus();
                    if($("#chkAbonos")  .prop('checked') == true)
                    {
                        $("#chkAbonos") .click();
                        $("#chkAbonos") .prop('checked',false);
                    }
                    $("#divChkAbonos")  .hide("fade",200);
                    //$("#divAbono")      .show( 'fade', 400, $("#inputAbono").focus());
                    $("#inputAbono")    .prop("disabled", true);
                    hid_                = parseFloat($("#hiddenMontoTotal").val());
                    $("#inputAbono")    .val(hid_.toFixed(2));
                }
                else
                {
                    $("#chkAbonos")     .focus();
                    $("#divChkAbonos")  .show("fade",200);
                    $("#inputAbono")    .prop("disabled", false);
                    $("#inputAbono")    .focus();
                }
            });
            $("#chkAbonos").click(function()
            {
                if ($(this).prop('checked'))
                {
                    $("#divAbono").show( 'fade', 400, function()
                    {
                        // $("#inputAbono").focus();
                        // $("#inputAbono").select();
                        $("#inputPagaCon").val('0');
                        $("#inputAbono").val('0').focus().select();
                        $("#inputAdeudo").val($("#inputMontoTotal").val());
                        if (parseFloat($("#inputPagaCon").val()) < parseFloat($("#inputAbono").val()) ||
                            $("#inputPagaCon").val() == "")
                        {
                            $("#inputPagaCon").parent().addClass("has-error");
                        }
                        else
                        {
                            $("#inputPagaCon").parent().removeClass("has-error");
                        }
                        if (parseFloat($("#inputAbono").val()) >= parseFloat($("#hiddenMontoTotal").val()))
                        {
                            $("#inputAbono").parent().addClass("has-error");
                        }
                        else
                        {
                            $("#inputAbono").parent().removeClass("has-error");
                        }
                    });
                }
                else
                {
                    $("#divAbono").hide( 'fade', 300, function()
                    {
                        tot    = parseFloat($("#hiddenMontoTotal").val());
                        paga   = parseFloat($("#inputPagaCon").val());
                        cambio = paga - tot;

                        if (paga < tot || cambio < 0 || isNaN($("#inputPagaCon").val()) || $("#inputPagaCon").val() == "")
                        {
                            $("#inputPagaCon").parent().addClass("has-error");
                            $("#inputCambio").val(0);
                            //return false;
                        }
                        else
                        {
                            $("#inputPagaCon").parent().removeClass("has-error");
                            $("#inputAbono").val(tot.toFixed(2));
                            cambio_ = (isNaN(cambio)) ? 0 : cambio;
                            $("#inputCambio").val(cambio_);
                            $("#inputAdeudo").val(0);
                        }
                        $("#inputPagaCon").focus()
                    });
                }

            });
            $("body").on("focus",".inputCantidad,.inputPrecioU,#inputPagaCon,#inputAbono",function()
            {
                this.select();
            });
            $("#inputPagaCon,#inputAbono").keyup(function(e)
            {
                total   = parseFloat($("#hiddenMontoTotal").val());
                abono   = parseFloat($("#inputAbono").val());
                if ($(this).attr('id') == 'inputAbono')
                {
                    $("#inputPagaCon").val(abono);
                }
                paga    = parseFloat($("#inputPagaCon").val());
                if(e.keyCode == 13)
                {
                    $("#btnTerminarVenta").click();
                }
                if($("#chkAbonos").prop("checked"))
                {
                    cambio  = redondearDec(paga  - abono);
                    adeudo  = redondearDec(total - abono);
                    if(paga == 0 && abono == 0)
                    {
                        $("#inputPagaCon").parent().removeClass("has-error");
                        $("#inputAbono").parent().removeClass("has-error");
                    }
                    else
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                        $("#inputAbono").parent().addClass("has-error");
                    }
                    if (isNaN($("#inputPagaCon").val())     ||
                        $("#inputPagaCon")      .val() == "")
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                        //return false;
                    }
                    else
                    {
                        $("#inputPagaCon").parent().removeClass("has-error");
                    }

                    if (isNaN($("#inputAbono")  .val())     ||
                        $("#inputAbono")        .val() == ""||
                        abono == 0 || abono == total)
                    {
                        $("#inputAbono").parent().addClass("has-error");
                    }
                    else
                    {
                        $("#inputAbono").parent().removeClass("has-error");
                    }
                    if( paga < abono || $("#inputPagaCon").val() == "")
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                    }
                    else
                    {
                        $("#inputPagaCon").parent().removeClass("has-error");
                    }
                    if( abono >= total || $("#inputAbono").val() == "")
                    {
                        $("#inputAbono").parent().addClass("has-error");
                    }
                    else
                    {
                        $("#inputAbono").parent().removeClass("has-error");
                    }
                    cambio_ = (isNaN(cambio)) ? 0 : cambio;
                    adeudo_ = (isNaN(adeudo)) ? 0 : adeudo;
                    $("#inputCambio").val(cambio_);
                    $("#inputAdeudo").val(adeudo_);
                }
                else
                {
                    if (isNaN($("#inputPagaCon").val()) || $("#inputPagaCon").val() == "" || paga == 0 || paga < total)
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                        $("#inputCambio").val(0);
                    }
                    else
                    {
                        cambio  = redondearDec(paga - total);
                        cambio_ = (isNaN(cambio)) ? 0 : cambio;
                        $("#inputCambio").val(cambio_);
                        $("#inputPagaCon").parent().removeClass("has-error");
                    }
                }


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
    <div id="dialog-confirm-convertirCot" class="dialog-oculto" title="Realizar la venta">
        <p>
            <h3><i class="fa fa-question-circle-o" aria-hidden="true"></i> ¿Deseas guardar la cotizaci&oacute;n como venta?</h3>
        </p>
        <div class="form-group">
            <label>Guardar como</label>
            <select class="form-control" id="selectTipoVenta">
                <option value="0">Ticket</option>
                <option value="1">Remisi&oacute;n</option>
            </select>
        </div>
    </div>
    <div id="dialog-confirm-venta" class="dialog-oculto" title="¿Convertir a venta?">
        <form role="form" id="form-confirm-venta">
            <div class="col-lg-12" id="divRespuestaVta" style="padding:0px">
            </div>
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 text-left">
                    <h2 id="h3Remision"></h2>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-left">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="chkImprimir" name="chkImprimir" checked style="width:18px;height:18px">Imprimir
                        </label>
                    </div>
                    <fieldset id="fieldOcultarPU" style="margin-top:-10px">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="chkOcultarPU" name="chkOcultarPU" style="width:18px;height:18px">Ocultar P. Unit
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>
            <p>
                <h4> Paga con: <h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" id="inputPagaCon" autofocus tyle="text-align:right;">
                </div>
            </p>
            <p>
                <h4> Cliente: </h4>
                <!--<h1><input type="number" class="form-control" disabled="disabled" id="inputCambio"></h1>-->
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i></span>
                    <select id="selectClientePos" name="selectClientePos" class="form-control">
                        <option value="1">==Venta al p&uacute;blico en gral==</option>
                    <?php
                        $sql = "SELECT
                                    id AS id,
                                    rsocial AS rsocial,
                                    tipoprecio AS tipoprecio
                                FROM clientes
                                WHERE id > 1
                                ORDER BY rsocial ASC";
                        if ($resultClientes = $mysqli->query($sql))
                        {
                            while($filaCliente = $resultClientes->fetch_assoc())
                            {
                                $idCliente = $filaCliente['id'];
                                $nombreCliente = $filaCliente['rsocial'];
                                echo "<option value='$idCliente'>$nombreCliente</option>";
                            }
                        }
                     ?>
                    </select>
                    <span class="input-group-addon" id="spanAgregarCliente" style="cursor:pointer;color:seagreen"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                </div>
            </p>
            <p>
                <h4> Total a pagar:<h4>
                <!--<h1><input type="number" class="form-control"  i></h1>-->
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control" id="inputMontoTotal" disabled="disabled" style="text-align:right;">
                </div>
                <input type="hidden" id="hiddenMontoTotal" name="hiddenMontoTotal">
                <input type="hidden" id="hiddenRemision" name="hiddenRemision" value="0">
            </p>
            <p>
                <h4>
                    <div class="checkbox text-center" id="divChkAbonos" style="display:none">
                        <input type="checkbox" id="chkAbonos" name="chkAbonos" style="width:22px;height:22px;"><span style="position:relative;margin-left:21px;">Pago en abonos</span>
                    </div>
                </h4>
            </p>
            <div style="display:none" id="divAbono">
                <h4> Abono: <h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" id="inputAbono" name="inputAbono" style="text-align:right;" disabled>
                </div>
                <h4> Adeudo:<h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control" id="inputAdeudo" name="inputAdeudo" value="0" disabled="disabled" style="text-align:right;">
                </div>
            </div>
            <p>
                <h4> Su cambio:<h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control" value="0" disabled="disabled" id="inputCambio" name="inputCambio" style="text-align:right;">
                </div>
            </p>
        </form>
    </div>
    <div id="dialog-reenviar-cotizacion" class="dialog-oculto" title="Enviar Cotizaci&oacute;n por E-mail">
        <p>
            <div class="col-lg-12" id="divRespuestaModal">
            </div>
            <div class="col-lg-12" id="form">
                <div class="form-group form-inline" style="margin-bottom:0px">
                    <label>Cotizaci&oacute;n No.</label>
                    <p class="form-control-static" id="pIdFactura"></p>
                </div>
                <div class="form-group form-inline" style="margin-bottom:4px">
                    <label>Cliente</label>
                    <p class="form-control-static" id="pRazonReceptor"></p>
                </div>
                <div class="form-group">
                    <label>Direcci&oacute;n e-mail</label>
                    <input class="form-control" id="inputEmail">
                    <p class="help-block">E-mail del cliente a donde se enviar&aacute; la cotizaci&oacute;n</p>
                </div>
            </div>
        </p>
    </div>
</body>
</html>
<?php
}
?>
