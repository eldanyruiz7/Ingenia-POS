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
<?php   $ipserver = $_SERVER['SERVER_ADDR']; ?>
<?php   require_once '../conecta/bd.php';
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Agregar proveedor</title>
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
    </style>
</head>
<body>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"><i class="fa fa-asterisk" aria-hidden="true"></i> Agregar proveedor</h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div id="divRespuesta" class="col-lg-11">
                </div>
                <div class="col-lg-11">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Datos del nuevo proveedor
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <form role="form" method="POST" action="agregarProveedor.php" id="formSubmit" onsubmit="submitForm();">
                                    <div class="col-lg-12">
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group divControlable" id="divRSocial">
                                                <label class="control-label">Nombre o Raz&oacute;n social</label>
                                                <input id="inputRSocial" name="inputRSocial" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12">
                                            <div class="form-group divControlable" id="divRepresentante">
                                                <label class="control-label">Representante</label>
                                                <input id="inputRepresentante" name="inputRepresentante" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-12">
                                            <div class="form-group divControlable" id="divDireccion">
                                                <label class="control-label">Direcci&oacute;n</label>
                                                <input id="inputDireccion" name="inputDireccion" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divTelefono">
                                                <label class="control-label">Tel&eacute;fono</label>
                                                <input id="inputTelefono" name="inputTelefono" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divRfc">
                                                <label class="control-label">RFC</label>
                                                <input id="inputRfc" name="inputRfc" autocomplete ="false" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divEmail">
                                                <label class="control-label">E-mail</label>
                                                <input id="inputEmail" name="inputEmail" autocomplete ="false" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div style="text-align:center;" class="col-lg-4 col-lg-offset-4 col-md-4 col-md-offset-4 col-xs-4 col-xs-offset-4">
                                        <button id="btnGuardar" name="btnGuardar" type="button" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                                    </div>
                                </form>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
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
    function proveedor(rSocial, representante, direccion, telefono, rfc, email)
    {
        this.rSocial        =   rSocial;
        this.representante  =   representante;
        this.direccion      =   direccion;
        this.telefono       =   telefono;
        this.rfc            =   rfc;
        this.email          =   email;
    }
    function limpiarForm()
    {
        $("#btnGuardar").removeAttr('disabled');
        $("#btnGuardar").html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar');
        $("input").removeAttr('disabled');
        $('select').removeAttr('disabled');
        objetoProveedor.length = 0;
    }
    function submitForm()
    {
        $(".divControlable").removeClass("has-error");
        $("#btnGuardar").attr('disabled', 'disabled');
        $("#btnGuardar").html('<i class="fa fa-circle-o-notch fa-spin fa-fw margin-bottom"></i> Espera...');
        $("input")      .attr('disabled','disabled');
        $("select")     .attr('disabled','disabled');
        rSocial         =   $("#inputRSocial").val();
        representante   =   $("#inputRepresentante").val();
        direccion       =   $("#inputDireccion").val();
        telefono        =   $("#inputTelefono").val();
        rfc             =   $("#inputRfc").val();
        email           =   $("#inputEmail").val();
        esteProveedor     =   new proveedor(rSocial, representante, direccion, telefono, rfc, email);
        objetoProveedor.push(esteProveedor);
        var clienteJSON = JSON.stringify(objetoProveedor);
        $.ajax(
        {
            method: "POST",
            url:"../control/agregarProveedor.php",
            data: {arrayProveedor:clienteJSON}
        })
        .done(function(p)
        {
            //response = JSON.parse(p);
            //if(p.status == 1)
            console.log(p);
            $("#divRespuesta").html(p.respuesta);
            if(p.status == 1)
            {

                $("html, body").animate({ scrollTop: 0 }, 600);
                $("#inputRSocial").val("");
                $("#inputRepresentante").val("");
                $("#inputApellidoMat").val("");
                $("#inputDireccion").val("");
                $("#inputTelefono").val("");
                $("#inputRfc").val("");
                $("#inputEmail").val("");
            }
            else
            {
                if(p.email == 0)
                {
                    $("#divEmail").addClass("has-error");
                    $("#inputEmail").focus();
                }
                if(p.rfc == 0)
                {
                    $("#divRfc").addClass("has-error");
                    $("#inputRfc").focus();
                }
                if(p.telefono == 0)
                {
                    $("#divTelefono").addClass("has-error");
                    $("#inputTelefono").focus();
                }
                if(p.direccion == 0)
                {
                    $("#divDireccion").addClass("has-error");
                    $("#inputDireccion").focus();
                }
                if(p.representante == 0)
                {
                    $("#divRepresentante").addClass("has-error");
                    $("#inputRepresentante").focus();
                }
                if(p.razon == 0)
                {
                    $("#divRSocial").addClass("has-error");
                    $("#inputRSocial").focus();
                }
            }
        })
        .always(function()
        {
            limpiarForm();
        })
        .fail(function()
        {
            alert("Servidor no disponible, favor de consultar con el administrador del sistema");
        });

    }
    $(document).ready(function()
    {
        $("#btnGuardar").click(function()
        {
            $("#dialog-guardar").dialog("open");
        });
        $( "#dialog-guardar" ).dialog(
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
                    text: "Aceptar",
                    "id": "btnAceptarGuardarModal",
                    click: function()
                    {
                        submitForm();
                        $( this ).dialog( "close" );
                    }
                },
                {
                    text: "Cancelar",
                    "id": "btnCancelarGuardarModal",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
    });
    </script>
    <div id="dialog-guardar" title="Agregar nuevo proveedor">
        <p>
            <h3>¿Deseas agregar el nuevo proveedor?</h3>
        </p>
    </div>
</body>

</html>
<?php
}
?>
