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
?>
<!DOCTYPE html>
<html lang="en">
<?php $ipserver = $_SERVER['SERVER_ADDR']; ?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Listar proveedores</title>
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
        a.aEliminar, a.aModificar
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
                    <h1 class="page-header"><i class="fa fa-list" aria-hidden="true"></i> Listar proveedores</h1>
                </div>
                <div id="divRespuesta" class="col-lg-6 col-md-6 col-xs-6" style="padding-top: 20px;">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <table id="dataTable" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                        <thead>
                            <tr>

                                <th></th>
                                <th>Nombre o Raz&oacute;n Social</th>
                                <th>Nombre del representante</th>
                                <th>Direcci&oacute;n</th>
                                <th>Tel&eacute;fono</th>
                                <th>RFC</th>
                                <th>E mail</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                $sql = "SELECT
                            proveedores.id AS id,
                            proveedores.rsocial AS rsocial,
                            proveedores.representante AS representante,
                            proveedores.direccion AS direccion,
                            proveedores.telefono AS telefono,
                            proveedores.rfc AS rfc,
                            proveedores.email AS email
                        FROM proveedores
                        WHERE
                            proveedores.activo = 1
                        ORDER BY
                            proveedores.id ASC";
                $result = $mysqli->query($sql);
                while($arrayProveedores = $result->fetch_assoc())
                {
                ?>
                        <tr id="trProveedor<?php echo $arrayProveedores['id'];?>">
                            <td>
                                <div class="btn-group pull-left">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu slidedown">
                                        <li>
                                            <a class="aModificar" name="<?php echo $arrayProveedores['id'];?>">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="aEliminar" name="<?php echo $arrayProveedores['id'];?>">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <?php echo $arrayProveedores['rsocial'];?>
                            </td>
                            <td>
                                <?php echo $arrayProveedores['representante'];?>
                            </td>
                            <td>
                                <?php echo $arrayProveedores['direccion'];?>
                            </td>
                            <td>
                                <?php echo $arrayProveedores['telefono'];?>
                            </td>
                            <td>
                                <?php echo $arrayProveedores['rfc'];?>
                            </td>
                            <td>
                                <?php echo $arrayProveedores['email'];?>
                            </td>
                        </tr>
                <?php
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
    objetoProveedor = [];
    function proveedor(idProveedor, rsocial, representante, direccion, telefono, rfc, email)
    {
        this.idProveedor    =   idProveedor;
        this.rsocial        =   rsocial;
        this.representante  =   representante;
        this.direccion      =   direccion;
        this.telefono       =   telefono;
        this.rfc            =   rfc;
        this.email          =   email;
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
             responsive: true
        });
        $(".aEliminar").click(function()
        {
            item = $(this).attr("name");
            //$( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            $( "#dialog-confirm-eliminarProveedor" ).data('item',item).dialog("open");
        });
        $(".aModificar").click(function()
        {
            idProveedor = $(this).attr("name");
            $.ajax(
            {
                method: "POST",
                url:"../control/modificarProveedor.php",
                data: {idProveedor:idProveedor}
            })
            .done(function(p)
            {
                $("#divDialogModificarProveedor").html(p);
                $( "#dialog-confirm-modificarProveedor" ).data('idProveedor',idProveedor).dialog("open");

            })
            .fail(function()
            {
                alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
            });

        });
        $(document).on("click","#btnActualizar",function()
        {

        });
        $( "#dialog-confirm-eliminarProveedor" ).dialog(
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
                    text: "Eliminar este proveedor",
                    click: function()
                    {
                        idProveedor = $( "#dialog-confirm-eliminarProveedor" ).data('item');
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/eliminarProveedor.php",
                            data: {idProveedor:idProveedor}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                $("#dataTable").DataTable().row($("#trProveedor"+p.queProveedor)).remove().draw();
                            }
                                $("#divRespuesta").html(p.respuesta);
                                $("html, body").animate({ scrollTop: 0 }, 600);
                        })
                        .fail(function()
                        {
                            alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
                        });
                        $( this ).dialog( "close" );
                    }
                },
                {
                    text: "Cancelar",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $( "#dialog-confirm-modificarProveedor" ).dialog(
        {
            resizable: true,
            height: "auto",
            width: "60%",
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
                    text: "Guardar cambios",
                    click: function()
                    {
                        idProveedor = $( "#dialog-confirm-modificarProveedor" ).data('idProveedor');
                        //id              =   $(this).attr("name");
                        rsocial         =   $("#inputRSocial").val();
                        representante   =   $("#inputRepresentante").val();
                        direccion       =   $("#inputDireccion").val();
                        telefono        =   $("#inputTelefono").val();
                        rfc             =   $("#inputRfc").val();
                        email           =   $("#inputEmail").val();
                        esteProveedor   =   new proveedor(idProveedor, rsocial, representante, direccion, telefono, rfc, email);
                        objetoProveedor.length = 0;
                        objetoProveedor.push(esteProveedor);
                        var clienteJSON = JSON.stringify(objetoProveedor);
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/modificarProveedorBd.php",
                            data: {arrayProveedor:clienteJSON}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                var oTable = $("#dataTable").DataTable();
                                index = oTable.row($("#trProveedor"+p.queProveedor)).index();
                                oTable.row($("#trProveedor"+p.queProveedor)).cell(index,1).data(p.rsocial).draw();
                                oTable.row($("#trProveedor"+p.queProveedor)).cell(index,2).data(p.representante).draw();
                                oTable.row($("#trProveedor"+p.queProveedor)).cell(index,3).data(p.direccion).draw();
                                oTable.row($("#trProveedor"+p.queProveedor)).cell(index,4).data(p.telefono).draw();
                                oTable.row($("#trProveedor"+p.queProveedor)).cell(index,5).data(p.rfc).draw();
                                oTable.row($("#trProveedor"+p.queProveedor)).cell(index,6).data(p.email).draw();
                            }
                            $("#divRespuesta").html(p.respuesta);
                        })
                        .always(function(p)
                        {
                            $("html, body").animate({ scrollTop: 0 }, 600);
                            console.log(p);
                        });
                        $( this ).dialog( "close" );
                    }
                },
                {
                    text: "Cancelar",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        /*$('#dataTable').on( 'click', 'tbody tr', function () {
    $("#dataTable").DataTable().row( this ).edit();
} );*/
    });
    </script>
    <div id="dialog-confirm-eliminarProveedor" title="Eliminar proveedor">
        <p>
            <h3> <i class="fa fa-exclamation-circle" aria-hidden="true"></i> ¿Eliminar este proveedor?</h3>
        </p>
        *Nota: Esta acci&oacute;n no se puede deshacer.
    </div>
    <div id="dialog-confirm-modificarProveedor" title="Modificar proveedor">
        <div class="col-lg-12" id="divDialogModificarProveedor">
        </div>
    </div>
</body>

</html>
<?php
}
?>
