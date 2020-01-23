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

    <title>Dar de alta producto</title>
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
    <!--<link href="../startbootstrap/vendor/jquery-upload-files/uploadfile.css" rel="stylesheet" type="text/css">-->
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
        /* .ui-widget-overlay
        {
            opacity: .50 !important; /* Make sure to change both of these, as IE only sees the second one */
            filter: Alpha(Opacity=50) !important;
            background-color: rgb(50, 50, 50) !important; /* This will make it darker */
        /*} */
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
            border-width: 0px;
            text-align: right;
            -moz-appearance:textfield;
        }

        .inputTable::-webkit-inner-spin-button
        {
            -webkit-appearance: none;
            margin: 0;
        }
        .input-group-addon
        {
            background-color: #FFFFFF;
            border: 0px;
        }
        .has-error>input
        {
            border-width: 1px;
            color: #a94442;
        }
        .has-error>span
        {
            border: 1px;
            border-width: 1px;
        }
    </style>
</head>
<body>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
                    <h1 class="page-header"><i class="fa fa-asterisk" aria-hidden="true"></i> Agregar producto</h1>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-6" ><!--style="padding:10px;text-align:center;cursor:pointer;color:#708090;margin-top:25px;margin-bottom:15px">-->
                    <div class="col-lg-5 col-md-7 col-lg-offset-7 col-md-offset-5" style="padding-right: 0px;margin-bottom:20px; margin-top: 10px;box-shadow: 19px -3px 25px -8px #999;">
                        <div class="col-lg-12" id="status" style="height:150px;text-align:center;color:#337ab7; padding-right:0px" ><!--<style="margin-bottom5px;height:180px;width:250px">-->
                            </br>
                            <i class="fa fa-file-image-o fa-5x"></i>
                            </br>&nbsp;
                        </div>
                        <div class="col-lg-12" style="margin-bottom:-1px;">
                            <div class="progress progress-striped active" id="progressUpload" style="display:none">
                                <div id="divfileupload" class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                    <span class="sr-only">0% Completado</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-right:0px">
                            <button type="button" id="btnAnadirImg" class="btn btn-sm btn-outline btn-info btn-block" style="border-radius: 0px 0px 5px 5px;"><i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i> </br>Imagen</button>
                        </div>
                    </div>

                </div>
                <div class="col-lg-3 col-md-3 col-xs-6 col-lg-offset-9 col-md-offset-9">
                    <form id="formfileupload" method="POST" action="../control/subirImg.php" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
                        <input type="file" id="inputFileImagen" name="inputFileImagen" accept="image/*" style="display:none">
                        <input type="submit" value="Subir" id="submitImagen" style="display:none">
                        <input type="reset" id="inputReset" value="reset" style="display:none">
                        <input type="hidden" id="hiddenImgBinario">
                        <input type="hidden" id="hiddenImgTipo" value="">
                    </form>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div id="divRespuesta" class="col-lg-6 col-lg-offset-6 col-md-6 col-md-offset-6">
                </div>
                <div class="col-lg-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Datos del nuevo producto
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <form role="form" method="POST" action="agregarProducto.php" id="formSubmit" onsubmit="submitForm();">
                                    <div class="col-lg-12">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divNombreCorto">
                                                <label class="control-label">Nombre corto</label>
                                                <input id="inputNombreCorto" name="inputNombreCorto" autocomplete ="off" required="required" class="form-control">
                                                <p class="help-block">As&iacute; aparecer&aacute; en el ticket de venta.</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divNombreLargo">
                                                <label class="control-label">Presentaci&oacute;n</label>
                                                <input id="inputNombreLargo" name="inputNombreLargo" autocomplete ="off" required="required" class="form-control">
                                                <p class="help-block">Aqu&iacute; va un nombre m&aacute;s descriptivo.</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable">
                                                <label class="control-label">Departamento</label>
                                                <select id="selectCategoria" name="selectCategoria" required="required" class="form-control">
                                <?php
                                    $sql = "SELECT * FROM departamentos WHERE activo = 1 ORDER BY id ASC";
                                    if ($resultCategorias = $mysqli->query($sql))
                                    {
                                        while ($arrayCategorias = $resultCategorias->fetch_assoc())
                                        {
                                            $idCategoria = $arrayCategorias['id'];
                                            $nombreCategoria = $arrayCategorias['nombre'];
                                            echo "<option value='$idCategoria'>$nombreCategoria</option>";
                                        }
                                    }
                                ?>
                                                </select>
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divCodigoBarras">
                                                <label class="control-label">Codigo de barras</label>
                                                <input id="inputCodigoBarras" name="inputCodigoBarras" autocomplete ="off" class="form-control">
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divCodigo2">
                                                <label class="control-label">Clave corta</label>
                                                <input id="inputCodigo2" name="inputCodigo2" autocomplete ="off" class="form-control">
                                                <p class="help-block">Clave de no m&aacute;s de 5 caracteres</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divSHCP">
                                                <label class="control-label">Clave SHCP</label>
                                                <input id="inputSHCP" name="inputSHCP" autocomplete ="off" class="form-control">
                                                <p class="help-block">Clave para la Secretar&iacute;a de Hacienda</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divPrecioLista">
                                                <label class="control-label">Precio de lista</label>
                                                <input type="number" id="inputPrecioLista" name="inputPrecioLista" value="1" min="0" autocomplete ="off" class="form-control">
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable divShowHide" id="divFactor">
                                                <label class="control-label">Factor de conversi&oacute;n</label>
                                                <input id="inputFactor" type="number" value="1" min="1" name="inputFactor" autocomplete ="off" class="form-control">
                                                <p class="help-block">Unidades por caja o paquete</p>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable divShowHide" id="divUnidades">
                                                <label class="control-label">Unidad de venta</label>
                                                <select id="selectUnidad" name="selectUnidad" required="required" class="form-control">
                                <?php
                                    $sql = "SELECT * FROM unidadesventa ORDER BY id ASC";
                                    if ($resultUnidades = $mysqli->query($sql))
                                    {
                                        while ($arrayUnidades = $resultUnidades->fetch_assoc())
                                        {
                                            $idUnidad = $arrayUnidades['id'];
                                            $nombreUnidad = $arrayUnidades['nombre'];
                                            echo "<option value='$idUnidad'>$nombreUnidad</option>";
                                        }
                                    }
                                ?>
                                                </select>
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div> -->
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divIVA">
                                                <label class="control-label">I.V.A.</label>
                                                <input type="number" id="inputIVA" name="inputIVA" value="0" min="0" autocomplete ="off" class="form-control">
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divIEPS">
                                                <label class="control-label">I.E.P.S.</label>
                                                <input type="number" id="inputIEPS" name="inputIEPS" value="0" min="0" autocomplete ="off" class="form-control">
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div>
                                        <!-- <div class="col-lg-4 col-md-6">
                                            <div class="form-group divControlable" id="divBalanza">
                                                <label>¿Su venta requiere b&aacute;scula?:</label>
                                                <select id="selectBascula" name="selectBascula" required="required" class="form-control">
                                                    <option value="0">No</option>
                                                    <option value="1">Si</option>
                                                </select>
                                                <p class="help-block">&nbsp;</p>
                                            </div>
                                        </div> -->


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
                <div class="col-lg-12" id="dvCol" style="transition-duration:0.31s">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Lista de Precios
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive" id="divListaPrecios">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-6 col-lg-offset-3" style="text-align:center">
                        <button type="button" id="btnGuardar" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>
                    </div>
                </div>
            </div>
            <br/>
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
    <script src="../startbootstrap/vendor/myJs/listaProductos.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    var tabla = '';
    function limpiarForm()
    {
        $("#btnGuardar").removeAttr('disabled');
        $("#btnGuardar").html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar');
        $("input").removeAttr('disabled');
        $('select').removeAttr('disabled');
        //objetoProducto.length = 0;
    }
    function verificarCodBarras(cod)
    {
        codigoB = cod;
        $.ajax(
        {
            method: "POST",
            url:"../control/verificarCodBarras.php",
            data: {codigoB:codigoB}
        })
        .done(function(p)
        {
            $("#inputCodigoBarras").parent().removeClass("has-error");
            $("#inputCodigoBarras").parent().removeClass("has-success");
            $("#inputCodigoBarras").next().html(p.respuesta);
            $("#inputCodigoBarras").parent().addClass(p.class);
        });
    }
    function verificarCod2(cod)
    {
        codigo2 = cod;
        $.ajax(
        {
            method: "POST",
            url:"../control/verificarCod2.php",
            data: {codigo2:codigo2}
        })
        .done(function(p)
        {
            $("#inputCodigo2").parent().removeClass("has-error");
            $("#inputCodigo2").parent().removeClass("has-success");
            $("#inputCodigo2").next().html(p.respuesta);
            $("#inputCodigo2").parent().addClass(p.class);
        });
    }
    function submitForm()
    {
        $(".divControlable").removeClass("has-error");
        $(".divControlableTipoPrecios").removeClass("has-error");
        $("#btnGuardar").attr('disabled', 'disabled');
        $("#btnGuardar").html('<i class="fa fa-circle-o-notch fa-spin fa-fw margin-bottom"></i> Espera...');
        $("input")      .attr('disabled','disabled');
        $("select")     .attr('disabled','disabled');
        nombreCorto         =   $("#inputNombreCorto").val();
        nombreLargo         =   $("#inputNombreLargo").val();
        departamento        =   $("#selectCategoria").val();
        codigoBarras        =   $("#inputCodigoBarras").val();
        codigo2             =   $("#inputCodigo2").val();
        claveSHCP           =   $("#inputSHCP").val();
        balanza             =   $("#selectBascula").val();
        //alert(balanza);
        unidadVenta         =   $("#selectUnidad").val();
        precioLista         =   $("#inputPrecioLista").val();
        factor              =   $("#inputFactor").val();
        iva                 =   $("#inputIVA").val();
        ieps                =   $("#inputIEPS").val();
        //img                 =   $("#inputFileImagen").prop('files')[0];
        imagenBinario	    = $("#hiddenImgBinario").val();
        imagenTipo			= $("#hiddenImgTipo").val();
        //img                 =   imgFile[0].files;
        inputMenPxP         =   $("#inputMenPxP").val();
        inputMenDxP         =   $("#inputMenDxP").val();
        inputMenUxP         =   $("#inputMenUxP").val();
        inputMenPxU         =   $("#inputMenPxU").val();
        inputMenDxU         =   $("#inputMenDxU").val();
        inputMenUxU         =   $("#inputMenUxU").val();
        inputMedPxP         =   $("#inputMedPxP").val();
        inputMedDxP         =   $("#inputMedDxP").val();
        inputMedUxP         =   $("#inputMedUxP").val();
        inputMedPxU         =   $("#inputMedPxU").val();
        inputMedDxU         =   $("#inputMedDxU").val();
        inputMedUxU         =   $("#inputMedUxU").val();
        inputMayPxP         =   $("#inputMayPxP").val();
        inputMayDxP         =   $("#inputMayDxP").val();
        inputMayUxP         =   $("#inputMayUxP").val();
        inputMayPxU         =   $("#inputMayPxU").val();
        inputMayDxU         =   $("#inputMayDxU").val();
        inputMayUxU         =   $("#inputMayUxU").val();
        inputEspPxP         =   $("#inputEspPxP").val();
        inputEspDxP         =   $("#inputEspDxP").val();
        inputEspUxP         =   $("#inputEspUxP").val();
        inputEspPxU         =   $("#inputEspPxU").val();
        inputEspDxU         =   $("#inputEspDxU").val();
        inputEspUxU         =   $("#inputEspUxU").val();
        var data = new FormData();
        data.append('nombreCorto',nombreCorto);
        data.append('nombreLargo',nombreLargo);
        data.append('departamento',departamento);
        data.append('codigoBarras',codigoBarras);
        data.append('codigo2',codigo2);
        data.append('claveSHCP',claveSHCP);
        data.append('balanza',balanza);
        data.append('unidadVenta',unidadVenta);
        data.append('precioLista',precioLista);
        data.append('factor',factor);
        data.append('iva',iva);
        data.append('ieps',ieps);
        data.append('imagenBinario',imagenBinario);
        data.append('imagenTipo',imagenTipo);
        data.append('inputMenPxP',inputMenPxP);
        data.append('inputMenDxP',inputMenDxP);
        data.append('inputMenUxP',inputMenUxP);
        data.append('inputMenPxU',inputMenPxU);
        data.append('inputMenDxU',inputMenDxU);
        data.append('inputMenUxU',inputMenUxU);

        data.append('inputMedPxP',inputMedPxP);
        data.append('inputMedDxP',inputMedDxP);
        data.append('inputMedUxP',inputMedUxP);
        data.append('inputMedPxU',inputMedPxU);
        data.append('inputMedDxU',inputMedDxU);
        data.append('inputMedUxU',inputMedUxU);

        data.append('inputMayPxP',inputMayPxP);
        data.append('inputMayDxP',inputMayDxP);
        data.append('inputMayUxP',inputMayUxP);
        data.append('inputMayPxU',inputMayPxU);
        data.append('inputMayDxU',inputMayDxU);
        data.append('inputMayUxU',inputMayUxU);

        data.append('inputEspPxP',inputEspPxP);
        data.append('inputEspDxP',inputEspDxP);
        data.append('inputEspUxP',inputEspUxP);
        data.append('inputEspPxU',inputEspPxU);
        data.append('inputEspDxU',inputEspDxU);
        data.append('inputEspUxU',inputEspUxU);


        //var clienteJSON = JSON.stringify(objetoProducto);
        $.ajax(
        {
            type: "POST",
            url:"../control/agregarProducto.php",
            data: data,
            //dataType: "HTML",
            contentType:false,
            processData:false,
            cache:false
        })
        .done(function(p)
        {
            //response = JSON.parse(p);
            //if(p.status == 1)
            $("#divRespuesta").html(p.respuesta);
            if(p.status == 1)
            {

                $("html, body").animate({ scrollTop: 0 }, 600);
                $("#inputNombreCorto").val("");
                $("#inputNombreLargo").val("");
                $("#selectCategoria>option[value="+1+"]").attr("selected",true);
                $("#inputCodigoBarras").val("");
                $("#inputCodigoBarras").next().html('<p class="help-block"><i class="fa fa-check-circle-o" aria-hidden="true"></i> C&oacute;digo disponible</p>');
                $("#inputCodigo2").val("");
                $("#inputCodigo2").next().html('<p class="help-block"><i class="fa fa-check-circle-o" aria-hidden="true"></i> C&oacute;digo disponible</p>');
                $("#inputSHCP").val("");
                $("#inputPrecioLista").val("1");
                $("#inputFactor").val("1");
                $("#selectUnidad>option[value="+1+"]").attr("selected",true);
                $("#inputIVA").val("0");
                $("#inputIEPS").val("0");
                $("#selectBascula>option[value="+0+"]").attr("selected",true);

                $("#divListaPrecios").empty();
                $("table").css("min-width","850px");
                $("#dvCol").removeClass("col-lg-6 col-lg-offset-3");
                $("#dvCol").addClass("col-lg-12");
                $("#divListaPrecios").html(tabla);

                //$(".inputDinamico").val("");
                $("#inputReset").click();
                $('#ResetImg').click();
                $("div").removeClass("has-success");
                $("div").removeClass("has-error");

                $("#radioBalanza1").attr("checked","checked");
            }
            else
            {
                if(p.nombreCorto == 0)
                {
                    $("#divNombreCorto").addClass("has-error");
                }
                if(p.nombreLargo == 0)
                {
                    $("#divNombreLargo").addClass("has-error");
                }
                if(p.departamento == 0)
                {
                    $("#selectCategoria").addClass("has-error");
                }
                if(p.codigoBarras == 0)
                {
                    $("#divCodigoBarras").removeClass("has-success");
                    $("#divCodigoBarras").addClass("has-error");
                }
                if(p.codigo2 == 0)
                {
                    $("#divCodigo2").removeClass("has-success");
                    $("#divCodigo2").addClass("has-error");
                }
                if(p.precioLista == 0)
                {
                    $("#divPrecioLista").addClass("has-error");
                }
                if(p.factor == 0)
                {
                    $("#divFactor").addClass("has-error");
                }
                if(p.unidadVenta == 0)
                {
                    $("#divUnidades").addClass("has-error");
                }
                if(p.iva == 0)
                {
                    $("#divIVA").addClass("has-error");
                }
                if(p.ieps == 0)
                {
                    $("#divIEPS").addClass("has-error");
                }
                if(p.balanza == 0)
                {
                    $("#divBalanza").addClass("has-error");
                }
            }
        })
        .always(function(p)
        {
            limpiarForm();
            console.log(p);
        })
        .fail(function()
        {
            alert("Servidor no disponible, favor de consultar con el administrador del sistema");
        });

    }
    $(document).ready(function()
    {
        (function() {

            var bar             = $('#divfileupload');
            var percent         = $('.sr-only');
            var status          = $('#status');
            var progressUpload  = $("#progressUpload");
            var btnImg          = $("#btnAnadirImg");
            var hidden          = $("#hiddenImgBinario");
            var hiddenTipo		= $("#hiddenImgTipo");
            $('#formfileupload').ajaxForm({
                beforeSend: function() {
                    //status.empty();
                    var percentVal = '0%';
                    $('#divfileupload').width(percentVal)
                    $('.sr-only').html(percentVal);
                    $("#progressUpload").show();

                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    $('#divfileupload').width(percentVal)
                    $('.sr-only').html(percentVal);
                },
                success: function(xhr) {
                    var percentVal = '100%';
                    $('#divfileupload').width(percentVal)
                    $('.sr-only').html(percentVal);
                    if(xhr.exito == 1)
                    {
                        $("#btnAnadirImg").removeClass('btn-outline');
                        $("#btnAnadirImg").addClass('btn-success');
                        $("#btnAnadirImg").removeClass('btn-info');
                        $("#btnAnadirImg").html('<i class="fa fa-times fa-2x" aria-hidden="true"></i></br>Eliminar');
                        $("#btnAnadirImg").prop('id','btnEliminarImg');
                        $("#imgSrc").html(xhr.respuesta);
                        $('#status').html(xhr.respuesta);
                        $("#hiddenImgBinario").val(xhr.binario);
                        $("#hiddenImgTipo").val(xhr.tipo);
                    }
                    else
                    {
                        $("#divRespuesta").html(xhr.respuesta);
                        $("#inputReset").click();
                    }
                },
            	complete: function(xhr) {
            		//status.html(xhr.responseText);
                    console.log(xhr);
                    $("#progressUpload").hide();
                    $("#inputNombreCorto").focus();
            	}
            });

        })();
        $(document).on('click',"#btnAnadirImg",function()
        {
            $("#inputFileImagen").click();
        });
        $(document).on('click',"#btnEliminarImg",function()
        {
            $( "#dialog-confirm-eliminarImg" ).dialog("open");
        });
        $(document).on('click',".vistaPrevia",function()
        {
            $('#dialog-Img').dialog("open");
        });
        /*$("#btnAdjuntarImg").click(function()
        {
            $("#inputAjaxUpload").click();
        });*/
        $("#inputFileImagen").change(function()
        {
            $("#submitImagen").click();
        });
        // Asignar tipo de tabla para artículos con peso y sin peso
        tablaKg = '<table class="table table-striped table-bordered table-hover" id="tablaPrecios" style="min-width:850px">';
        tablaKg +='    <thead>';
        tablaKg +='        <tr>';
        tablaKg +='            <th><b>PRECIOS</b></th>';
        tablaKg +='            <th>Precio x Kg</th>';
        tablaKg +='            <th>Desc x Kg</th>';
        tablaKg +='            <th>Utilidad x Kg</th>';
        tablaKg +='        </tr>';
        tablaKg +='    </thead>';
        tablaKg +='    <tbody>';
        tablaKg +='        <tr>';
        tablaKg +='            <td>';
        tablaKg +='                <b>Menudeo</b>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <span class="input-group-addon">$</span>';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputPrimeroFila" id="inputMenPxU">';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" value="0.00" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenDxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputUltimoFila" id="inputMenUxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='        </tr>';
        tablaKg +='        <tr>';
        tablaKg +='            <td><b>Medio Mayoreo</b></td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <span class="input-group-addon">$</span>';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMedPxU">';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" value="0.00" min="0" class="form-control inputTable" id="inputMedDxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMedUxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='        </tr>';
        tablaKg +='        <tr>';
        tablaKg +='            <td><b>Mayoreo</b></td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <span class="input-group-addon">$</span>';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMayPxU">';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" value="0.00" min="0" class="form-control inputTable" id="inputMayDxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMayUxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='        </tr>';
        tablaKg +='        <tr>';
        tablaKg +='            <td><b>Especial</b></td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <span class="input-group-addon">$</span>';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroFila inputUltimoColumna" id="inputEspPxU">';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" value="0.00" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspDxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='            <td>';
        tablaKg +='                <div class="input-group">';
        tablaKg +='                    <input type="number" min="0" class="form-control inputTable inputUltimoColumna inputUltimoFila" id="inputEspUxU">';
        tablaKg +='                    <span class="input-group-addon">%</span>';
        tablaKg +='                </div>';
        tablaKg +='            </td>';
        tablaKg +='        </tr>';
        tablaKg +='    </tbody>';
        tablaKg +='</table>';
        tabla   = '<table class="table table-striped table-bordered table-hover" id="tablaPrecios" style="min-width:850px">';
        tabla   +='    <thead>';
        tabla   +='        <tr>';
        tabla   +='            <th><b>PRECIOS</b></th>';
        tabla   +='            <th>Precio x Paquete</th>';
        tabla   +='            <th>Desc x Paquete</th>';
        tabla   +='            <th>Utilidad x Paquete</th>';
        tabla   +='            <th>Precio x Unidad</th>';
        tabla   +='            <th>Desc x Unidad</th>';
        tabla   +='            <th>Utilidad x Unidad</th>';
        tabla   +='        </tr>';
        tabla   +='    </thead>';
        tabla   +='    <tbody>';
        tabla   +='        <tr>';
        tabla   +='            <td>';
        tabla   +='                <b>Menudeo</b>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputPrimeroFila" id="inputMenPxP">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenDxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenUxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenPxU">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenDxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputUltimoFila" id="inputMenUxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='        </tr>';
        tabla   +='        <tr>';
        tabla   +='            <td><b>Medio Mayoreo</b></td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMedPxP">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable" id="inputMedDxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable" id="inputMedUxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable" id="inputMedPxU">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable" id="inputMedDxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMedUxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='        </tr>';
        tabla   +='        <tr>';
        tabla   +='            <td><b>Mayoreo</b></td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMayPxP">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable" id="inputMayDxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable" id="inputMayUxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable" id="inputMayPxU">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable" id="inputMayDxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMayUxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='        </tr>';
        tabla   +='        <tr>';
        tabla   +='            <td><b>Especial</b></td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputPrimeroFila inputUltimoColumna" id="inputEspPxP">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspDxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td>';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspUxP">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <span class="input-group-addon">$</span>';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspPxU">';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" value="0.00" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspDxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='            <td class="sinBascula">';
        tabla   +='                <div class="input-group">';
        tabla   +='                    <input type="number" min="0" class="form-control inputTable inputUltimoColumna inputUltimoFila" id="inputEspUxU">';
        tabla   +='                    <span class="input-group-addon">%</span>';
        tabla   +='                </div>';
        tabla   +='            </td>';
        tabla   +='        </tr>';
        tabla   +='    </tbody>';
        tabla   +='</table>';
        $("#divListaPrecios").html(tabla);
        $("#selectBascula").change(function()
        {
            if($(this).val()==1)
            {
                $("#divListaPrecios").empty();
                $("#divListaPrecios").html(tablaKg);
                $("table").css("min-width","450px");
                $("#dvCol").removeClass("col-lg-12");
                $("#dvCol").addClass("col-lg-6 col-lg-offset-3");
            }
            else
            {
                $("#divListaPrecios").empty();
                $("table").css("min-width","850px");
                $("#dvCol").removeClass("col-lg-6 col-lg-offset-3");
                $("#dvCol").addClass("col-lg-12");
                $("#divListaPrecios").html(tabla);
            }

        });

        $("#inputCodigoBarras").keydown(function(e)
        {
            if(e.keyCode == 13)
                verificarCodBarras($(this).val());
        });
        $("#inputCodigoBarras").focusout(function()
        {
            verificarCodBarras($(this).val());
        });
        $("#btnGuardar").click(function()
        {
            $("#dialog-guardar").dialog("open");
        });
        $("#inputCodigo2").keydown(function(e)
        {
            if(e.keyCode == 13)
                verificarCod2($(this).val());
        });
        $("#inputCodigo2").focusout(function()
        {
            verificarCod2($(this).val());
        });
        $( "#dialog-guardar" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
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
        $( "#dialog-confirm-eliminarImg" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
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
                    text: "Eliminar",
                    id:"ResetImg",
                    click: function()
                    {
                        btnImg = $("#btnEliminarImg");
                        btnImg.addClass('btn-outline');
                        btnImg.removeClass('btn-success');
                        btnImg.addClass('btn-info');
                        btnImg.html('<i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i></br>Imagen');
                        btnImg.prop('id','btnAnadirImg');
                        $("#status").html('</br><i class="fa fa-file-image-o fa-5x"></i></br>&nbsp;');
                        $("#inputReset").click();
                        $("#hiddenImgBinario").val('');
                        $("#hiddenImgTipo").val("");
                        $( this ).dialog("close");
                        $("#inputNombreCorto").focus();
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
        $( "#dialog-Img" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
            maxHeight:"75%",
            maxWidth:"90%",
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
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $("#inputNombreCorto").focus();
    });
    </script>
    <div id="dialog-guardar" title="Agregar nuevo producto">
        <p>
            <h3>¿Deseas agregar el producto?</h3>
        </p>
    </div>
    <div id="dialog-confirm-eliminarImg" title="Eliminar imagen">
        <p>
            <h3>¿Deseas eliminar la imagen?</h3>
        </p>
    </div>
    <div id="dialog-Img" title="Imagen">
        <div id="imgSrc" class="col-lg-12 col-md-12 col-sm-12" style="text-align:center">

        </div>
    </div>
</body>

</html>
<?php
}
?>
