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

    <title>Reporte de inventario</title>
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
    </style>
</head>
<body>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"> <i class="fa fa-file-text-o" aria-hidden="true"></i> Reporte de inventario</h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            Opciones
                        </div>
                        <form role="form" method="POST" action="../control/reporteInventario.php" target="_blank">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Departamentos</label></br>
                                            <button type="button" id="btnSelectTodosDeptos" class="btn btn-link btn-sm">Seleccionar todo</button>
                                            <button type="button" id="btnSelectNingunDeptos" class="btn btn-link btn-sm">Deseleccionar todo</button>
                                            <select multiple="multiple" id="selectDepartamentos" name="selectDepartamentos[]" size="14" class="form-control">
                            <?php
                                                $sql            = "SELECT * FROM departamentos ORDER BY nombre ASC";
                                                $resultDep      = $mysqli->query($sql);
                                                while ($rowDep  = $resultDep->fetch_assoc())
                                                {
                                                    $idDep      = $rowDep['id'];
                                                    $nombreDep  = $rowDep['nombre'];
                                                    echo "<option value='$idDep' selected='selected'>$nombreDep</option>";
                                                }
                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- /.col-lg-6 (nested) -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Agrupar</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input name="chkAgruparDepto" type="checkbox" checked value="1">Agrupar por departamentos
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Art&iacute;culos</label>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="radioArts" value="1" checked="checked">Todos
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="radioArts" value="2">S&oacute;lo art&iacute;culos con existencia
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="radioArts" value="3">S&oacute;lo art&iacute;culos sin existencia
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Campos a mostrar</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" checked="checked" name="mPromedio" value="1">Mostrar costo promedio
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" checked="checked" name="mUltimo" value="2">Mostrar &uacute;ltimo preico de compra
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" checked="checked" name="mPublico" value="3">Mostrar precio al p&uacute;blico
                                                </label>
                                            </div>
                                        </div>
                                        <label>Ordenar por</label>
                                        <div class="form-group row">
                                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                            <select class="form-control" name="selectOrden">
                                                <option value="1">Id</option>
                                                <option value="2">Unidad de venta</option>
                                                <option value="3">Existencias</option>
                                                <option selected="selected" value="4">Nombre</option>
                                            </select>
                                        </div>

                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding-left: 0px;">
                                                <select class="form-control" name="selectAsc">
                                                    <option selected="" value="1">Ascendente</option>
                                                    <option value="2">Descendente</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12" style="padding-bottom:20px">
                                        &nbsp;
                                    </div>
                                    <!-- /.col-lg-6 (nested) -->
                                    <div class="col-lg-4 col-lg-offset-4">
                                        <button type="submit" class="btn btn-info btn-lg btn-block"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generar reporte</button>
                                    </div>
                                </div>
                                <!-- /.row (nested) -->
                            </div>
                        </form>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
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
        $(document).ready(function()
        {
            $("#btnSelectTodosDeptos").click(function()
            {
                    $('#selectDepartamentos option').prop('selected', true);
            });
            $("#btnSelectNingunDeptos").click(function()
            {
                    $('#selectDepartamentos option').prop('selected', false);
            });
        });
    </script>
</body>

</html>
<?php
}
?>
