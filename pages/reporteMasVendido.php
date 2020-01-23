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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Reporte de productos +/- vendidos</title>
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
        #dataTable_filter
        {
            text-align: right;
        }
        .inputTable
        {
            text-align: right;
            -moz-appearance:textfield;
        }

        .inputTable::-webkit-inner-spin-button
        {
            -webkit-appearance: none;
            margin: 0;
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
                    <h1 class="page-header"> <span id="spanCargando"><!-- --><i class="fa fa-spinner fa-pulse fa-fw"></i> Espera...<!-- Listar Productos--></span></h1>
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
        if (isset($_POST['selectDepartamentos']))
        {
            $noDept         = ($_POST['selectDepartamentos'] != 0) ? $_POST['selectDepartamentos'] : 0;
            if ($noDept     != 0)
            {
                $compl_sql  = "WHERE productos.departamento = $noDept";
            }
            else
            {
                $compl_sql  = "";
            }

        }
        else
        {
            $compl_sql      = "";
            $noDept         = 0;
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
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-4">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    &nbsp;
                                    <div class="btn-group pull-right">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                            Exportar <i class="fa fa-chevron-down"></i>
                                        </button>
                                        <ul class="dropdown-menu slidedown">
                                            <li>
                                                <a href="#" id="menu-exportPDF"> <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Departamento</label>
                                            <select id="selectDepartamentos" name="selectDepartamentos" class="form-control">
                                                <option value="0">Todos</option>
                                    <?php
                                        $sql = "SELECT * from departamentos ORDER BY nombre ASC";
                                        $resDep = $mysqli->query($sql);
                                        while ($rowDep = $resDep->fetch_assoc())
                                        {
                                            $idDep  = $rowDep['id'];
                                            $nomDep = $rowDep['nombre'];
                                            echo ($noDept == $idDep) ? "<option selected value='$idDep'>$nomDep</option>" : "<option value='$idDep'>$nomDep</option>";
                                        }
                                     ?>
                                            </select>
                                    </div>
                                </div>
                                <div class="panel-footer" style="text-align:right">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-calculator" aria-hidden="true"></i> Calcular</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <form id="formReporte" target="_blank" method="post" action="reportes/reporteMasMenosVendidoPDF.php">
                    <button type="submit" id="btn-exportPDF" class="btn btn-danger btn-sm" style="display:none"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exportar</button>
                    <input type="hidden" id="r" name="r">
                    <input type="hidden" id="fi" name="fi">
                    <input type="hidden" id="ff" name="ff">
                </form>
            </div>
            <div class="col-lg-12" id="cargarData" style="display:none">
                <table id="dataTable" class="display compact table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>C&oacute;digo</th>
                            <th>Nombre</th>
                            <th>Departamento</th>
                            <th>Unidad Venta</th>
                            <th>Vendidos</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
    if ($consultar == 1)
    {
        $fechaInicioSQL     = $fechaInicio." 00:00:00";
        $fechaFinSQL        = $fechaFin." 23:59:59";
        $sql = "SELECT
                    productos.id            AS id,
                    productos.nombrelargo   AS descripcion,
                    productos.codigo        AS codigo,
                    productos.codigo2       AS codigo2,
                    productos.departamento  AS idDepartamento,
                    unidadesventa.nombre    AS unidadVenta,
                    departamentos.nombre    AS nombreDepartamento
                FROM productos
                INNER JOIN departamentos
                ON productos.departamento = departamentos.id
                INNER JOIN unidadesventa
                ON productos.unidadventa = unidadesventa.id
                $compl_sql

                ORDER BY productos.id ASC";
        if ($resultadoP = $mysqli->query($sql))
        {
            //$totalV = 0;
            $totalFilas = $resultadoP->num_rows;
            while ($rowP = $resultadoP->fetch_assoc())
            {
                //$totalV += $rowVenta['totalVenta'];
                //$fechaVenta             = date('d-m-Y / H:i:s',strtotime($rowVenta['fechaInicio']));
                //$nombreCompMesero       = $rowVenta['nombreMesero']." ".$rowVenta['apellidosMesero'];
                //$idCajero = $rowVenta['idCajero'];
                $idProducto = $rowP['id'];
                $codigo     = (strlen($rowP['codigo'])>0) ? $rowP['codigo'] : $rowP['codigo2'];
                $sql = "SELECT IFNULL(SUM(detalleventa.cantidad),0) AS sumaCant
                        FROM detalleventa
                        INNER JOIN ventas
                        ON detalleventa.venta = ventas.id
                        WHERE (ventas.timestamp BETWEEN '$fechaInicioSQL' AND '$fechaFinSQL') AND detalleventa.producto = $idProducto";
                // echo $sql;
                $resultadoC         = $mysqli->query($sql);
                $rowCantCurrent     = $resultadoC->fetch_assoc();
                $cantCurrent        = number_format($rowCantCurrent['sumaCant'],3,".","");

                // $cantCurrent = $resultadoC->num_rows;
                $rowC = $resultadoC->fetch_assoc();
    ?>
                <tr class="rowReporte" >
                    <td class="text-right">
                        <?php echo $rowP['id']; ?>
                    </td>
                    <td>
                        <?php echo $codigo; ?>
                    </td>
                    <td>
                        <?php echo $rowP['descripcion'];?>
                    </td>
                    <td>
                        <?php echo $rowP['nombreDepartamento'];?>
                    </td>
                    <td>
                        <?php echo $rowP['unidadVenta'];?>
                    </td>
                    <td class="text-right">
                        <?php echo $cantCurrent;?>
                    </td>

                </tr>
    <?php
            }
        }
    }
    ?>
                    </tbody>
                </table>
            </br>
            &nbsp;
            </br>
            &nbsp;
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
    <script src="../startbootstrap/vendor/jquery-upload-files/jquery.uploadfile.min.js"></script>
    <script src="../startbootstrap/vendor/typeahead/typeahead.min.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
        function rowColeccionable(id, codigo, nombre, departamento, unidadVenta, cantidad)
        {
            this.id             = id;
            this.codigo         = codigo;
            this.nombre         = nombre;
            this.departamento   = departamento;
            this.unidadVenta    = unidadVenta;
            this.cantidad       = cantidad;
        }
        $(document).ready(function()
        {
            $('#dataTable').DataTable(
             {
                 "lengthMenu": [[10, 50, 100, -1],[10, 50, 100, "Todos"]],
                "language":
                {
                     "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
                },
                "order": [[ 5, "desc" ]],
                "processing": true,
                responsive: true,
                "initComplete": function(settings, json)
                {
                    $("#spanCargando").html('<i class="fa fa-file-text-o" aria-hidden="true"></i> Reporte productos +/- vendidos');
                    $("#cargarData").show("fold",750);
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
                            //if(y != 0)
                                coleccionCeldas[y] = $(this).text().trim();
                        });
                    esteRow = new rowColeccionable(coleccionCeldas[0], coleccionCeldas[1], coleccionCeldas[2], coleccionCeldas[3], coleccionCeldas[4], coleccionCeldas[5]);
                    coleccionTabla.push(esteRow);
                });
                coleccionTablaJSON = JSON.stringify(coleccionTabla);
                $("#r").val(coleccionTablaJSON);
                $("#fi").val($("#fechaInicio").val());
                $("#ff").val($("#fechaFin").val());
            });
            $("#menu-exportPDF").click(function()
            {
                $('#btn-exportPDF').click();
            });
        });
    </script>
</body>

</html>
<?php
}
?>
