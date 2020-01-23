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
<?php require_once '../conecta/bd.php';?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Listar clientes</title>
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
                    <h1 class="page-header"><i class="fa fa-list" aria-hidden="true"></i> Listar clientes</h1>
                </div>
                <div id="divRespuesta" class="col-lg-6 col-md-6 col-xs-6" style="padding-top: 20px;">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
            <?php
                $sql ="SELECT
                            tipoprecio
                        FROM
                            clientes
                        LIMIT 1";
                $resultadoCliente   = $mysqli->query($sql);
                $tipoCliente        = $resultadoCliente->fetch_assoc();
                $tipoprecio         = $tipoCliente['tipoprecio'];
                 ?>
                <div class="col-lg-12">
                    <table id="dataTable" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Raz&oacute;n social</th>
                                <th>Precio</th>
                                <th>Direcci&oacute;n</th>
                                <th>Municipio</th>
                                <th>Tel 1</th>
                                <th>Tel 2</th>
                                <th>Celular</th>
                                <th>RFC</th>
                                <th>E mail</th>
                                <th>D&iacute;as Cr&eacute;dito
                            </tr>
                        </thead>
                        <tbody>
                <?php
                $sql = "SELECT
                            clientes.id             AS id,
                            clientes.rsocial        AS rsocial,
                            clientes.representante  AS representante,
                            clientes.calle          AS calle,
                            clientes.numeroext      AS numeroExt,
                            clientes.numeroint      AS numeroInt,
                            clientes.colonia        AS colonia,
                            clientes.poblacion      AS poblacion,
                            clientes.municipio      AS municipio,
                            clientes.cp             AS cp,
                            clientes.estado         AS idEstado,
                            estados.nombreLargo     AS nombreEstado,
                            clientes.telefono1      AS telefono1,
                            clientes.telefono2      AS telefono2,
                            clientes.celular        AS celular,
                            clientes.rfc            AS rfc,
                            clientes.email          AS email,
                            clientes.diasCredito    AS diasCredito,
                            tipoprecios.id          AS idprecio,
                            tipoprecios.nombrecorto AS nombreprecio
                        FROM clientes
                        INNER JOIN tipoprecios
                        ON clientes.tipoprecio = tipoprecios.id
                        INNER JOIN estados
                        ON clientes.estado = estados.id
                        WHERE
                            clientes.activo = 1
                        AND
                            clientes.id > 1
                        ORDER BY
                            clientes.id ASC";
                $result = $mysqli->query($sql);
                while($arrayClientes = $result->fetch_assoc())
                {
                ?>
                        <tr id="trCliente<?php echo $arrayClientes['id'];?>">
                            <td>
                                <div class="btn-group pull-left">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu slidedown">
                                        <li>
                                            <a class="aModificar" name="<?php echo $arrayClientes['id'];?>">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
                                            </a>
                                        </li>
                                        <li>
                                            <a class="aEliminar" name="<?php echo $arrayClientes['id'];?>">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <?php echo $arrayClientes['rsocial'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['nombreprecio'];?>
                            </td>
                            <?php
                                $direccion = $arrayClientes['calle']." ".$arrayClientes['numeroExt'];
                                $direccion .= (strlen($arrayClientes['numeroInt']) > 0) ? " Int. ". $arrayClientes['numeroInt'] : '';
                                $direccion .= ", ".$arrayClientes['poblacion'];
                            ?>
                            <td>
                                <?php echo $direccion;?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['municipio'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['telefono1'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['telefono2'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['celular'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['rfc'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['email'];?>
                            </td>
                            <td>
                                <?php echo $arrayClientes['diasCredito'];?>
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
             responsive: true
        });
        $(".aEliminar").click(function()
        {
            item = $(this).attr("name");
            //$( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            $( "#dialog-confirm-eliminarCliente" ).data('item',item).dialog("open");
        });
        $(".aModificar").click(function()
        {
            idCliente = $(this).attr("name");
            $.ajax(
            {
                method: "POST",
                url:"../control/modificarCliente.php",
                data: {idCliente:idCliente}
            })
            .done(function(p)
            {
                $("#divDialogModificarCliente").html(p);
                $( "#dialog-confirm-modificarCliente" ).data('idCliente',idCliente).dialog("open");

            })
            .fail(function()
            {
                alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
            });

        });
        $( "#dialog-confirm-eliminarCliente" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            autoOpen: false,
            show:
            {
                effect: "drop",
                duration: 250
            },
            buttons:
            [
                {
                    text: "Eliminar cliente",
                    click: function()
                    {
                        idCliente = $( "#dialog-confirm-eliminarCliente" ).data('item');
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/eliminarCliente.php",
                            data: {idCliente:idCliente}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                $("#dataTable").DataTable().row($("#trCliente"+p.queCliente)).remove().draw();
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
        $( "#dialog-confirm-modificarCliente" ).dialog(
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
            open: function()
            {
                $("#divRespuesta2").empty();
            },
            buttons:
            [
                {
                    text: "Modificar",
                    click: function()
                    {
                        $(".divControlable").removeClass('has-error');
                        dialogo         =   $(this);
                        idCliente       =   $( "#dialog-confirm-modificarCliente" ).data('idCliente');
                        rsocial         =   $("#inputRazon").val();
                        representante   =   $("#inputRepresentante").val();
                        tipoPrecio      =   $("#selectTipoPrecio").val();
                        tipoPrecioText  =   $("#selectTipoPrecio option:selected").html();
                        calle           =   $("#inputCalle").val();
                        numeroExt       =   $("#inputNumeroExt").val();
                        numeroInt       =   $("#inputNumeroInt").val();
                        poblacion       =   $("#inputPoblacion").val();
                        municipio       =   $("#inputMunicipio").val();
                        telefono1       =   $("#inputTelefono1").val();
                        telefono2       =   $("#inputTelefono2").val();
                        celular         =   $("#inputCelular").val();
                        colonia         =   $("#inputCol").val();
                        cp              =   $("#inputCP").val();
                        estado          =   $("#selectEstado").val();
                        rfc             =   $("#inputRfc").val();
                        email           =   $("#inputEmail").val();
                        dias            =   $("#inputDias").val();
                        esteCliente     =   new cliente(idCliente, rsocial, representante, tipoPrecio, tipoPrecioText, calle, numeroExt, numeroInt, poblacion, municipio, colonia, cp, estado, telefono1, telefono2, celular, rfc, email, dias);
                        objetoCliente.length = 0;
                        objetoCliente.push(esteCliente);
                        console.log(objetoCliente);
                        var clienteJSON = JSON.stringify(objetoCliente);
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/modificarClienteBd.php",
                            data: {arrayCliente:clienteJSON}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                var oTable = $("#dataTable").DataTable();
                                index = oTable.row($("#trCliente"+p.queCliente)).index();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,1).data(p.rsocial).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,2).data(p.tipoPrecioText).draw();
                                direccion_ = p.calle+" "+p.numeroExt;
                                direccion_ += (p.numeroInt.length > 0) ? " Int "+ p.numeroInt : '';
                                direccion_ += ", "+p.poblacion;
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,3).data(direccion_).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,4).data(p.municipio).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,5).data(p.telefono1).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,6).data(p.telefono2).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,7).data(p.celular).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,8).data(p.rfc).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,9).data(p.email).draw();
                                oTable.row($("#trCliente"+p.queCliente)).cell(index,10).data(p.dias).draw();
                                dialogo.dialog( "close" );
                                $("#divRespuesta").html(p.respuesta);
                            }
                            else
                            {
                                if (p.s_rsocial == 0)
                                    $("#inputRazon").parent().addClass("has-error");
                                if (p.s_colonia == 0)
                                    $("#inputCol").parent().addClass("has-error");
                                if (p.s_cp == 0)
                                    $("#inputCP").parent().addClass("has-error");
                                if (p.s_estado == 0)
                                    $("#selectEstado").parent().addClass("has-error");
                                if (p.s_representante == 0)
                                    $("#inputRepresentante").parent().addClass("has-error");
                                if (p.s_tipoPrecio == 0 || p.s_tipoPrecioText == 0)
                                    $("#selectTipoPrecio").parent().addClass("has-error");
                                if (p.s_calle == 0)
                                    $("#inputCalle").parent().addClass("has-error");
                                if (p.s_numeroExt == 0)
                                    $("#inputNumeroExt").parent().addClass("has-error");
                                if (p.s_numeroInt == 0)
                                    $("#inputNumeroInt").parent().addClass("has-error");
                                if (p.s_poblacion == 0)
                                    $("#inputPoblacion").parent().addClass("has-error");
                                if (p.s_municipio == 0)
                                    $("#inputMunicipio").parent().addClass("has-error");
                                if (p.s_telefono1 == 0)
                                    $("#inputTelefono1").parent().addClass("has-error");
                                if (p.s_rfc == 0)
                                    $("#inputRfc").parent().addClass("has-error");
                                if (p.s_email == 0)
                                    $("#inputEmail").parent().addClass("has-error");
                                if (p.s_dias == 0)
                                    $("#inputDias").parent().addClass("has-error");
                                $("#divRespuesta2").html(p.respuesta);
                            }
                        })
                        .always(function(p)
                        {
                            $("html, body").animate({ scrollTop: 0 }, 600);
                            console.log(p);
                        });
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
    <div id="dialog-confirm-eliminarCliente" title="Eliminar cliente">
        <p>
            <h3><i class="fa fa-exclamation-circle" aria-hidden="true"></i> ¿Eliminar cliente?</h3>
        </p>
        *Nota: Esta acci&oacute;n no se puede deshacer.
    </div>
    <div id="dialog-confirm-modificarCliente" title="Modificar cliente">
        <div class="col-lg-12" id="divRespuesta2">
        </div>
        <div class="col-lg-12" id="divDialogModificarCliente">
        </div>
    </div>
</body>

</html>
<?php
}
?>
