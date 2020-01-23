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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Facturar cierre de mes</title>
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
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <!-- <h1 class="page-header"><i class="fa fa-calendar" aria-hidden="true"></i> Facturar cierre de mes</h1> -->
                    <h1 class="page-header"><span id="spanCargando"><!-- --><i class="fa fa-spinner fa-pulse fa-fw"></i> Espera...<!-- Listar Productos--></span></h1>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-6" id="divRespuesta">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <label><i class="fa fa-search-plus" aria-hidden="true"></i> Esto es lo que tienes por facturar de acuerdo al siguiente rango de fechas</label>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Sub Total</th>
                                            <th>IVA</th>
                                            <th>IEPS</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        <?php
            $sql            =  "SELECT
                                    IFNULL(SUM(detalleventa.subTotal),0)    AS sumTotal,
                                    MIN(ventas.timestamp)                   AS minFecha,
                                    MAX(ventas.timestamp)                   AS maxFecha
                                FROM detalleventa
                                INNER JOIN ventas
                                ON detalleventa.venta           = ventas.id
                                WHERE detalleventa.facturable   = 1
                                AND detalleventa.facturado      = 0
                                AND ventas.pagado               = 1";
            $result         = $mysqli->query($sql);
            $row            = $result->fetch_assoc();
            $suma_Total     = $row['sumTotal'];
            $fechaInicio    = date('d-m-Y H:i:s',strtotime($row['minFecha']));
            $fechaFinal     = date('d-m-Y H:i:s',strtotime($row['maxFecha']));
            $fechaInicio_p  = date('Y-m-d',strtotime($row['minFecha']));
            $fechaFinal_p   = date('Y-m-d',strtotime($row['maxFecha']));



            $sql            =  "SELECT
                                    productos.id                            AS idProducto,
                                    productos.IVA                           AS IVA,
                                    productos.IEPS                          AS IEPS,
                                    detalleventa.subTotal                   AS subTotal,
                                    detalleventa.venta                      AS idVenta
                                FROM productos
                                INNER JOIN detalleventa
                                ON productos.id                 = detalleventa.producto
                                INNER JOIN ventas
                                ON detalleventa.venta           = ventas.id
                                WHERE detalleventa.facturable   = 1
                                AND detalleventa.facturado      = 0
                                AND ventas.pagado               = 1";
            $result_imp         = $mysqli->query($sql);
            $totalIva           = 0;
            $totalIeps          = 0;
            while($row_imp      = $result_imp->fetch_assoc())
            {
                $esteSubTotal   = $row_imp['subTotal'];
                $esteIva_tasa   = ($row_imp['IVA']/100);
                $esteIeps_tasa  = ($row_imp['IEPS']/100);
                $totalIva       += $esteSubTotal * $esteIva_tasa;
                $totalIeps      += $esteSubTotal * $esteIeps_tasa;
            }
            $suma_SubTotal      = $suma_Total - $totalIva - $totalIeps;

         ?>
                                        <tr>
                                            <td><input type="date" name="fechahoraInicio" class="form-control" step="1" min="<?php echo $fechaInicio_p;?>" max="<?php echo $fechaFinal_p;?>" value="<?php echo $fechaInicio_p;?>"><?php //echo $fechaInicio;?></td>
                                            <td><input type="date" name="fechahoraFinal" class="form-control" step="1" min="<?php echo $fechaInicio_p;?>" max="<?php echo $fechaFinal_p;?>" value="<?php echo $fechaFinal_p;?>"><?php //echo $fechaFinal;?></td>
                                            <td><?php echo "$".number_format($suma_SubTotal,2,".",",");?></td>
                                            <td><?php echo "$".number_format($totalIva,2,".",",");?></td>
                                            <td><?php echo "$".number_format($totalIeps,2,".",",");?></td>
                                            <td><label><?php echo "$".number_format($suma_Total,2,".",",");?></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel-footer">
                        </div>
                    </div>

                </div>
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <div class="form-group input-group input-group-lg">
                        <input autofocus type="text" class="form-control" id="inputBuscar">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><i class="fa fa-search"></i>
                        </button>
                        </span>
                    </div>
                    <input type="hidden" id="hiddenBuscar">
                </div>
                <div class="col-lg-12" id="cargarData" style="display:none">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Lista de productos
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table id="dataTable" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>C&oacute;digo</th>
                                        <th>Nombre art&iacute;culo</th>
                                        <th>Clave SAT</th>
                                        <th>% IVA</th>
                                        <th>% IEPS</th>
                                        <th>Cantidad</th>
                                        <th>Precio promedio</th>
                                        <th>Sub total</th>
                                    </tr>
                                </thead>
                                <tbody>
                        <?php
                        $sql = "SELECT
                                    productos.id                            AS idProducto,
                                    productos.IVA                           AS IVA,
                                    productos.IEPS                          AS IEPS,
                                    productos.codigo                        AS codigo,
                                    productos.codigo2                       AS codigo2,
                                    productos.claveSHCP                     AS claveSat,
                                    productos.nombrecorto                   AS descripcion,
                                    detalleventa.subTotal                   AS subTotal,
                                    detalleventa.cantidad                   AS cantidad,
                                    detalleventa.precio                     AS precio,
                                    detalleventa.venta                      AS idVenta
                                FROM productos
                                INNER JOIN detalleventa
                                ON productos.id                 = detalleventa.producto
                                INNER JOIN ventas
                                ON detalleventa.venta           = ventas.id
                                WHERE detalleventa.facturable   = 1
                                AND detalleventa.facturado      = 0
                                AND ventas.pagado               = 1";
                        $result                 = $mysqli->query($sql);
                        $totalSubTotal          = 0;
                        $arrayProductos_grupo   = [];
                        while($arrayProductos   = $result->fetch_assoc())
                        {
                            $idProducto         = $arrayProductos['idProducto'];
                            $codigo             = (strlen($arrayProductos['codigo']) > 0) ? $arrayProductos['codigo'] : $arrayProductos['codigo2'];
                            $descripcion        = $arrayProductos['descripcion'];
                            $claveSat           = $arrayProductos['claveSat'];
                            $IVA                = $arrayProductos['IVA'];
                            $IEPS               = $arrayProductos['IEPS'];
                            $cantidad           = $arrayProductos['cantidad'];
                            $precio             = $arrayProductos['precio'];
                            $subTotal           = $arrayProductos['subTotal'];
                            if (sizeof($arrayProductos_grupo) == 0)
                            {
                                $arrayProductos_grupo[0]['idProducto']  = $idProducto;
                                $arrayProductos_grupo[0]['codigo']      = $codigo;
                                $arrayProductos_grupo[0]['descripcion'] = $descripcion;
                                $arrayProductos_grupo[0]['claveSat']    = $claveSat;
                                $arrayProductos_grupo[0]['IVA']         = $IVA;
                                $arrayProductos_grupo[0]['IEPS']        = $IEPS;
                                $arrayProductos_grupo[0]['cantidad']    = $cantidad;
                                $arrayProductos_grupo[0]['precio']      = $precio;
                                $arrayProductos_grupo[0]['subTotal']    = $subTotal;
                                continue;
                            }
                            else
                            {
                                $existe = 0;
                                for ($x=0; $x < sizeof($arrayProductos_grupo)  ; $x++)
                                {
                                    if ($arrayProductos_grupo[$x]['idProducto'] == $idProducto)
                                    {
                                        $arrayProductos_grupo[$x]['cantidad']   += $cantidad;
                                        $arrayProductos_grupo[$x]['subTotal']   += $subTotal;

                                        $precio_    = $arrayProductos_grupo[$x]['subTotal'] / $arrayProductos_grupo[$x]['cantidad'];
                                        $arrayProductos_grupo[$x]['precio']     = $precio_;
                                        $existe = 1;
                                        //continue 2;
                                    }
                                }
                                if ($existe == 0)
                                {
                                    $arrayProductos_grupo[$x]['idProducto']  = $idProducto;
                                    $arrayProductos_grupo[$x]['codigo']      = $codigo;
                                    $arrayProductos_grupo[$x]['descripcion'] = $descripcion;
                                    $arrayProductos_grupo[$x]['claveSat']    = $claveSat;
                                    $arrayProductos_grupo[$x]['IVA']         = $IVA;
                                    $arrayProductos_grupo[$x]['IEPS']        = $IEPS;
                                    $arrayProductos_grupo[$x]['cantidad']    = $cantidad;
                                    $arrayProductos_grupo[$x]['precio']      = $precio;
                                    $arrayProductos_grupo[$x]['subTotal']    = $subTotal;
                                }
                            }
                        }
                        for ($y=0; $y < sizeof($arrayProductos_grupo)  ; $y++)
                        {
                            //var_dump($arrayProductos_grupo);
                            $idProducto         = $$arrayProductos_grupo[$y]['idProducto'];
                            $descripcion        = $$arrayProductos_grupo[$y]['descripcion'];
                            $codigo             = $$arrayProductos_grupo[$y]['codigo'];
                            $claveSat           = $$arrayProductos_grupo[$y]['claveSat'];
                            $IVA                = $$arrayProductos_grupo[$y]['IVA'];
                            $IEPS               = $$arrayProductos_grupo[$y]['IEPS'];
                            $cantidad           = $$arrayProductos_grupo[$y]['cantidad'];
                            $precio             = $$arrayProductos_grupo[$y]['precio'];
                            $subTotal           = $$arrayProductos_grupo[$y]['subTotal'];
                        ?>
                                <tr id="trCliente<?php echo $descripcion;?>">
                                    <td>
                                        <div class="btn-group pull-left">
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-bars" aria-hidden="true"></i>
                                            </button>
                                            <ul class="dropdown-menu slidedown">
                                                <li>
                                                    <a class="aModificar" name="<?php echo $descripcion;?>">
                                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="aEliminar" name="<?php echo $descripcion;?>">
                                                        <i class="fa fa-times-circle" aria-hidden="true"></i> Eliminar
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo $codigo;?>
                                    </td>
                                    <td>
                                        <?php echo $descripcion;?>
                                    </td>
                                    <td>
                                        <?php echo $claveSat;?>
                                    </td>
                                    <td>
                                        <?php echo $IVA;?>
                                    </td>
                                    <td>
                                        <?php echo $IEPS;?>
                                    </td>
                                    <td>
                                        <?php echo $cantidad;?>
                                    </td>
                                    <td>
                                        <?php echo $precio;?>
                                    </td>
                                    <td>
                                        <?php echo $subTotal;?>
                                    </td>
                                </tr>
                        <?php
                            $totalSubTotal += $subTotal;
                        }
                         ?>
                                </tbody>
                            </table>

                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
            </div>
            <div class="row" id="divData">
                 <?php echo $totalSubTotal;?>
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
        $(document).ready(function()
        {
            $('#dataTable').DataTable(
             {
                 "lengthMenu": [[10, 50, 100, -1],[10, 50, 100, "Todos"]],
                "language":
                {
                     "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
                },
                "order": [[ 1, "asc" ]],
                "processing": true,
                responsive: true,
                "initComplete": function(settings, json)
                {
                    $("#spanCargando").html('<i class="fa fa-calendar" aria-hidden="true"></i> Factura al p&uacute;blico en general');
                    $("#cargarData").show("fold",750);
                }
            });
        });
    </script>
</body>

</html>
<?php
}
 ?>
