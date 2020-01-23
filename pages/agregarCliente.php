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

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Agregar cliente</title>
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
        #page-wrapper
        {
            display: none;
            z-index: 1;
        }
        tr.success .inputCantidad
        {
            background-color: #d0e9c6;
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
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"><i class="fa fa-asterisk" aria-hidden="true"></i> Agregar cliente</h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div id="divRespuesta" class="col-lg-12">
                </div>
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Datos del nuevo cliente
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <form role="form" method="POST" action="agregarCliente.php" id="formSubmit" onsubmit="submitForm();">
                                    <div class="col-lg-12">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divRazon">
                                                <label class="control-label">Raz&oacute;n social</label>
                                                <input id="inputRazon" name="inputRazon" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divRepresentante">
                                                <label class="control-label">Representante</label>
                                                <input id="inputRepresentante" name="inputRepresentante" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable">
                                                <label class="control-label">Tipo de precio</label>
                                                <select id="selectTipoPrecio" name="selectTipoPrecio" required="required" class="form-control">
                                <?php
                                    $sql = "SELECT * FROM tipoprecios ORDER BY id ASC";
                                    if ($resultTipoPrecios = $mysqli->query($sql))
                                    {
                                        while ($arrayTipoPrecios = $resultTipoPrecios->fetch_assoc())
                                        {
                                            $idTipoPrecio = $arrayTipoPrecios['id'];
                                            $nombreTipoPrecio = $arrayTipoPrecios['nombrelargo'];
                                            echo "<option value='$idTipoPrecio'>$nombreTipoPrecio</option>";
                                        }
                                    }
                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divCalle">
                                                <label class="control-label">Calle</label>
                                                <input id="inputCalle" name="inputCalle" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divNumeroExt">
                                                <label class="control-label">No. Exterior</label>
                                                <input id="inputNumeroExt" name="inputNumeroExt" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divNumeroInt">
                                                <label class="control-label">No. Interior</label>
                                                <input id="inputNumeroInt" name="inputNumeroInt" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divPoblacion">
                                                <label class="control-label">Poblaci&oacute;n</label>
                                                <input id="inputPoblacion" name="inputPoblacion" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divMunicipio">
                                                <label class="control-label">Municipio</label>
                                                <input id="inputMunicipio" name="inputMunicipio" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divCol">
                                                <label class="control-label">Colonia</label>
                                                <input id="inputCol" name="inputCol" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divCP">
                                                <label class="control-label">C&oacute;digo Postal</label>
                                                <input id="inputCP" name="inputCP" autocomplete ="off" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable">
                                                <label class="control-label">Estado</label>
                                                <select id="selectEstado" name="selectEstado" required="required" class="form-control">
                                <?php
                                    $idEstadoActual = 9;
                                    $sql = "SELECT * FROM estados ORDER BY id ASC";
                                    if ($resultEstados = $mysqli->query($sql))
                                    {
                                        while ($arrayEstados = $resultEstados->fetch_assoc())
                                        {
                                            $idEstado = $arrayEstados['id'];
                                            $nombreEstado = $arrayEstados['nombreLargo'];
                                            if($idEstado == $idEstadoActual)
                                                echo "<option selected value='$idEstado'>$nombreEstado</option>";
                                            else
                                                echo "<option value='$idEstado'>$nombreEstado</option>";
                                        }
                                    }
                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divTelefono1">
                                                <label class="control-label">Tel&eacute;fono 1</label>
                                                <input id="inputTelefono1" name="inputTelefono1" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divTelefono2">
                                                <label class="control-label">Tel&eacute;fono 2</label>
                                                <input id="inputTelefono2" name="inputTelefono2" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divTelefono1">
                                                <label class="control-label">Celular</label>
                                                <input id="inputCelular" name="inputCelular" autocomplete ="off" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divRfc">
                                                <label class="control-label">RFC</label>
                                                <input id="inputRfc" name="inputRfc" autocomplete ="false" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divEmail">
                                                <label class="control-label">E-mail</label>
                                                <input id="inputEmail" name="inputEmail" autocomplete ="false" required="required" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divDias">
                                                <label class="control-label">D&iacute;as de cr&eacute;dito</label>
                                                <input type="number" id="inputDias" name="inputDias" autocomplete ="false" required="required" value="15" class="form-control">
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
    <script src="../startbootstrap/vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="../startbootstrap/dist/js/sb-admin-2.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <!-- Metis Menu Plugin JavaScript -->
    <!-- Morris Charts JavaScript -->
    <script src="../startbootstrap/vendor/raphael/raphael.min.js"></script>
    <script src="../startbootstrap/vendor/morrisjs/morris.min.js"></script>
    <script src="../startbootstrap/data/morris-data.js"></script>
    <!-- DataTables Javascript -->
    <script src="../startbootstrap/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-responsive/dataTables.responsive.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    objetoCliente = [];
    function cliente(rsocial, representante, tipoPrecio, calle, numeroExt, numeroInt, poblacion, municipio, colonia, cp, estado, telefono1, telefono2, celular, rfc, email, dias)
    {
        this.rsocial        = rsocial;
        this.representante  = representante;
        this.tipoPrecio     = tipoPrecio;
        this.calle          = calle;
        this.numeroExt      = numeroExt;
        this.numeroInt      = numeroInt;
        this.poblacion      = poblacion;
        this.municipio      = municipio;
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
    function limpiarForm()
    {
        $("#btnGuardar").removeAttr('disabled');
        $("#btnGuardar").html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar');
        $("input").removeAttr('disabled');
        $('select').removeAttr('disabled');
        objetoCliente.length = 0;
    }
    function submitForm()
    {
        $(".divControlable").removeClass("has-error");
        $("#btnGuardar").attr('disabled', 'disabled');
        $("#btnGuardar").html('<i class="fa fa-circle-o-notch fa-spin fa-fw margin-bottom"></i> Espera...');
        $("input")      .attr('disabled','disabled');
        $("select")     .attr('disabled','disabled');
        dialogo         =   $(this);
        rsocial         =   $("#inputRazon").val();
        representante   =   $("#inputRepresentante").val();
        tipoPrecio      =   $("#selectTipoPrecio").val();
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
        esteCliente     =   new cliente(rsocial, representante, tipoPrecio, calle, numeroExt, numeroInt, poblacion, municipio, colonia, cp, estado, telefono1, telefono2, celular, rfc, email, dias);
        objetoCliente.push(esteCliente);
        console.log(objetoCliente);
        var clienteJSON = JSON.stringify(objetoCliente);
        $.ajax(
        {
            method: "POST",
            url:"../control/agregarCliente.php",
            data: {arrayCliente:clienteJSON}
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
                $("#inputRazon").val("");
                $("#inputRepresentante").val("");
                //$("#input").val("");
                $("#selectTipoPrecio>option[value="+1+"]").attr("selected",true);
                $("#inputCalle").val("");
                $("#inputNumeroExt").val("");
                $("#inputNumeroInt").val("");
                $("#inputPoblacion").val("");
                $("#inputMunicipio").val("");
                $("#inputTelefono1").val("");
                $("#inputTelefono2").val("");
                $("#inputCol").val("");
                $("#inputCP").val("");
                $("#celular").val("");
                $("#inputRfc").val("");
                $("#inputEmail").val("");
                $("#inputDias").val(15);
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
                $("#divRespuesta").html(p.respuesta);
            }
        })
        .always(function(p)
        {
            console.log(p);
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
        $("#page-wrapper").show("drop",{ direction: "left" }, 200, function()
        {
            $("#inputBuscar").focus();
        });
    });
    </script>
    <div id="dialog-guardar" class="dialog-oculto" title="Agregar nuevo cliete">
        <p>
            <h3>¿Deseas guardar el nuevo cliente?</h3>
        </p>
    </div>
</body>

</html>
<?php
}
?>
