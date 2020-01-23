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
    <title>Listar recibos emitidos</title>
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
        a.aEliminar, a.aImprimir
        {
            cursor:pointer;
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
                    <h1 class="page-header"><i class="fa fa-th-list" aria-hidden="true"></i> Listar recibos emitidos</h1>
                </div>
                <div id="divRespuesta" class="col-lg-6 col-md-6 col-xs-6" style="padding-top: 20px;">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
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
             <div class="col-lg-12">
                 <table id="dataTable" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Id</th>
                                <th>Id Relaci&oacute;n</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Recibo emitido por</th>
                                <th>Monto</th>
                                <th>Forma pago</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php
            $idPagoRelacionAnt  = 0;
            if ($consultar      == 1)
            {
                $fechaInicioSQL = $fechaInicio." 00:00:00";
                $fechaFinSQL = $fechaFin." 23:59:59";
                $sql = "SELECT
                            pagosrecibidos.id               AS id,
                            pagosrecibidos.idventa          AS idVenta_,
                            pagosrecibidos.idFactura        AS idFactura_,
                            pagosrecibidos.fechahora        AS fechaHr,
                            pagosrecibidos.monto            AS monto,
                            pagosrecibidos.idPagoRelacion   AS idPagoRelacion,
                            pagosrecibidos.usuario          AS idUsuario,
                            pagosrecibidos.cliente          AS idCliente,
                            (SELECT IFNULL(SUM(pagosrecibidos.monto),0)
                            FROM pagosrecibidos
                            WHERE pagosrecibidos.idFactura = idFactura_) AS montoTotalFac,
                            (SELECT IFNULL(SUM(pagosrecibidos.monto),0)
                            FROM pagosrecibidos
                            WHERE pagosrecibidos.idVenta = idVenta_) AS montoTotalRem,
                            usuarios.nombre                 AS nombreUsuario,
                            usuarios.apellidop              AS apellidopUsuario,
                            clientes.rsocial                AS rSocial,
                            metodosdepago.nombre            AS formaPago
                        FROM pagosrecibidos
                        INNER JOIN usuarios
                        ON pagosrecibidos.usuario = usuarios.id
                        INNER JOIN clientes
                        ON pagosrecibidos.cliente = clientes.id
                        INNER JOIN metodosdepago
                        ON pagosrecibidos.metodoPago = metodosdepago.id
                        WHERE (pagosrecibidos.fechahora BETWEEN '$fechaInicioSQL' AND '$fechaFinSQL')
                        AND pagosrecibidos.activo = 1
                        ORDER BY pagosrecibidos.fechahora ASC";
                $result                 = $mysqli->query($sql);
                $idFactura_             = "";
                //echo $sql;
                while($arrayClientes    = $result->fetch_assoc())
                {
                    $fecha_ac           = $arrayClientes['fechaHr'];
                    // $sql                = "SELECT idventa FROM pagosrecibidos WHERE fechahora = '$fecha_ac'";
                    // $result_ac          = $mysqli->query($sql);
                    $cont_ac            = 0;
                    $idVentas_str       = " ";
                    if ($arrayClientes['idFactura_'] != NULL)
                    {
                        if ($idFactura_     == $arrayClientes['idFactura_'])
                        continue;
                        $idVentas_str   = $arrayClientes['idFactura_'];
                        $tipoRelacion   = '<span class="label label-info"><i class="fa fa-rocket" aria-hidden="true"></i> Factura</span>';
                        $montoTotal     = $arrayClientes['montoTotalFac'];
                    }
                    else
                    {
                        if ($idPagoRelacionAnt == $arrayClientes['idPagoRelacion'])
                            continue;
                        $esteIdRel   = $arrayClientes['idPagoRelacion'];
                        $sql = "SELECT IFNULL(SUM(pagosrecibidos.monto),0) AS montoRem
                                FROM pagosrecibidos
                                WHERE pagosrecibidos.idPagoRelacion = $esteIdRel";
                                //echo '</br>'.$sql;//$idVentas_str   = $arrayClientes['idVenta_'];
                        $res_estaSum = $mysqli->query($sql);
                        $row_estaSum = $res_estaSum->fetch_assoc();
                        //$idPagoRelacion = $arrayClientes['idPagoRelacion'];
                        $montoTotal  = $row_estaSum['montoRem'];
                        $idPagoRelacionAnt = $arrayClientes['idPagoRelacion'];
                        $tipoRelacion   = '<span class="label label-primary"><i class="fa fa-print" aria-hidden="true"></i> Remisión</span>';
                    }
                ?>
                        <tr id="trCliente<?php echo $arrayClientes['id'];?>">
                            <td>
                                <div class="btn-group pull-left">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu slidedown">
                                        <li>
                                            <a class="aImprimir" name="<?php echo $arrayClientes['id'];?>">
                                                <i class="fa fa-print" aria-hidden="true"></i> Reimprimir
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <?php echo $arrayClientes['id'];?>
                            </td>
                            <td>
                                <?php echo $idVentas_str;?>
                            </td>
                            <td>
                                <?php echo $tipoRelacion;?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['fechaHr'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['rSocial'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['nombreUsuario']." ".$arrayClientes['apellidopUsuario'];?>
                            </td>
                            <td class="text-right">
                                <?php echo "$".number_format($montoTotal,2,".",",");?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['formaPago'];?>
                            </td>
                        </tr>
                <?php
                    $idFactura_ = $arrayClientes['idFactura_'];
                }
            }
                 ?>
                        </tbody>
                    </table>
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
    objetoCliente = [];
    function cliente(idCliente, rsocial, representante, tipoPrecio, tipoPrecioText, calle, numeroExt, numeroInt, poblacion, municipio, colonia, cp, estado, telefono1, telefono2, celular, rfc, email, dias)
    {
        this.idCliente      = idCliente;
        this.rsocial        = rsocial;
        this.representante  = representante;
        this.tipoPrecio     = tipoPrecio;
        this.tipoPrecioText = tipoPrecioText;
        this.calle          = calle;
        this.numeroExt      = numeroExt,
        this.numeroInt      = numeroInt,
        this.poblacion      = poblacion,
        this.municipio      = municipio,
        this.colonia        = colonia;
        this.cp             = cp;
        this.estado         = estado;
        this.telefono1      = telefono1;
        this.telefono2      = telefono2;
        this.celular        = celular;
        this.rfc            = rfc;
        this.email          = email;
        this.dias           = dias;
    }
    $(document).ready(function()
    {
        $('#dataTable').DataTable(
         {
             "aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0 ] }
      ],
            "language":
            {
                 "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
             },
             "order": [[ 4, "desc" ]],
             responsive: true
        });
        $(".aEliminar").click(function()
        {
            item = $(this).attr("name");
            //$( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            $( "#dialog-confirm-eliminarCliente" ).data('item',item).dialog("open");
        });
        $(".aImprimir").click(function()
        {
            idRecibo = $(this).attr("name");
            url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/pdfRecibo.php?recibo="+idRecibo;
            $("<a>").attr("href", url).attr("target", "_blank")[0].click();
        });
    });
    </script>
</body>

</html>
<?php
}
?>
