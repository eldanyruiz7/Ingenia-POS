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

    <title>Reporte de Notas de Crédito</title>
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
        .ui-widget-overlay
        {
            opacity: .50 !important; /* Make sure to change both of these, as IE only sees the second one */
            filter: Alpha(Opacity=50) !important;
            background-color: rgb(50, 50, 50) !important; /* This will make it darker */
        }
        tr.success .inputCantidad
        {
            background-color: #d0e9c6;
        }
        .dataTables_filter
        {
            text-align: right;
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
                    <h1 class="page-header"> <i class="fa fa-file-text-o" aria-hidden="true"></i> Reporte de notas</h1>
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
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-calculator" aria-hidden="true"></i> Calcular</button>
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
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Cajero</th>
                            <th>Cliente</th>
                            <th>$ venta</th>
                            <th>$ Devoluci&oacute;n</th>
                            <th>$ Nuevo total</th>
                            <th>Obs.</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    if ($consultar == 1)
    {
        $fechaInicioSQL = $fechaInicio." 00:00:00";
        $fechaFinSQL = $fechaFin." 23:59:59";
        $sql = "SELECT
                    notacredito.id              AS idNota,
                    notacredito.venta           AS idVenta,
                    notacredito.usuario         AS idUsuario,
                    notacredito.tipo            AS idTipo,
                    notacredito.observaciones   AS observaciones,
                    notacredito.totalventa      AS totalVenta,
                    notacredito.credito         AS credito,
                    notacredito.timestamp       AS fechaHora,
                    notacredito.nuevototalventa AS nuevoTotal,
                    notacredito.cliente         AS idCliente,
                    ventas.remision             AS remision,
                    usuarios.nombre             AS nombreCajero,
                    usuarios.apellidop          AS apellidopCajero,
                    usuarios.apellidom          AS apellidomCajero,
                    tiponotacredito.nombreCorto AS nombreTipo,
                    clientes.rsocial            AS nombreCliente
                FROM notacredito
                INNER JOIN usuarios
                ON notacredito.usuario = usuarios.id
                INNER JOIN tiponotacredito
                ON notacredito.tipo = tiponotacredito.id
                INNER JOIN clientes
                ON notacredito.cliente = clientes.id
                INNER JOIN ventas
                ON notacredito.venta = ventas.id
                WHERE   (notacredito.timestamp BETWEEN '$fechaInicioSQL' AND '$fechaFinSQL')
                ORDER BY notacredito.id ASC";
        if ($resultadoVentas = $mysqli->query($sql))
        {
            $totalFilas = $resultadoVentas->num_rows;
            while ($rowVenta = $resultadoVentas->fetch_assoc())
            {
                $fechaNota             = date('d-m-Y',strtotime($rowVenta['fechaHora']));
                $horaNota              = date('H:i:s',strtotime($rowVenta['fechaHora']));
                $nombreCompCliente      = $rowVenta['nombreCliente'];
                $nombreCompCajero       = $rowVenta['nombreCajero']." ".$rowVenta['apellidopCajero']." ".$rowVenta['apellidomCajero'];

    ?>

                <tr class="rowReporte" >
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
                                    <a href="../control/genRemisionVentaPDF.php?idVenta=<?php echo $rowVenta['idVenta'];?>" target="_blank" class="aReimprimirRem" name="<?php echo $rowVenta['idVenta'];?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Reimprimir Rem
                                    </a>
                                </li>

    <?php
        }
        else
        {
    ?>
                                <li>
                                    <a class="aReimprimirTkt" name="<?php echo $rowVenta['idVenta'];?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Reimprimir ticket
                                    </a>
                                </li>
    <?php
        }
    ?>

                                <li>
                                    <a class="aCancelar" name="<?php echo $rowVenta['idVenta'];?>">
                                        <i class="fa fa-ban" aria-hidden="true"></i> Cancelar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <?php echo $rowVenta['idVenta'];?>
                    </td>
                    <td>
        <?php
            switch ($rowVenta['idTipo'])
            {
                case 1:
                    $tipoLabel = "label-success";
                    break;
                case 2:
                    $tipoLabel = "label-primary";
                    break;
            }
         ?>
                        <span class='label <?php echo $tipoLabel;?>'>
                            <?php echo $rowVenta['nombreTipo'];?>
                        </span>
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
                    <td class="text-right">
                        <?php echo "$".number_format($rowVenta['credito'],2); ?>
                    </td>
                    <td class="text-right">
                        <?php echo "$".number_format($rowVenta['nuevoTotal'],2); ?>
                    </td>
                    <td class="text-center">
                        <?php echo $rowVenta['observaciones']; ?>
                    </td>
                </tr>
    <?php
            }
        }
    }
    ?>
                    </tbody>
                </table>
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
    coleccionTabla = [];
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

</html>
<?php
}
?>
