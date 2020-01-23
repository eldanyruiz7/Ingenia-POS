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
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Buscar producto</title>
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
            /* opacity: .50 !important; /* Make sure to change both of these, as IE only sees the second one
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
                    <h1 class="page-header"><i class="fa fa-search" aria-hidden="true"></i> Buscar producto</h1>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-6" id="divRespuesta">
                </div>
                <form id="formfileupload" method="POST" action="../control/agregarImgProducto.php" enctype="multipart/form-data" style="margin: 0px; padding: 0px;">
                    <input type="file" id="inputFileImagen" name="inputFileImagen" accept="image/*" style="display:none">
                    <input type="submit" value="Subir" id="submitImagen" style="display:none">
                    <input type="hidden" value="-1" id="hiddenIdProducto-img" name="idProducto">
                    <!-- <input type="reset" id="inputReset" value="reset" style="display:none"> -->
                    <!-- <input type="hidden" id="hiddenImgBinario">
                    <input type="hidden" id="hiddenImgTipo" value=""> -->
                    <!-- <input type="hidden" id="imgCtrl" value="0">
                    <input type="hidden" id="imgToggle" value='<?php echo (strlen($arrayProducto['img']) > 0) ? "1" : "0"; ?>'> -->
                </form>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <div class="form-group input-group input-group-lg">
                        <input autofocus autocomplete="off" type="text" class="form-control" id="inputBuscar">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button"><i class="fa fa-search"></i>
                        </button>
                        </span>
                    </div>
                    <input type="hidden" id="hiddenBuscar" value="-1">
                </div>
            </div>
            <div class="row" id="divData">

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
    function actualizarProducto()
    {
        console.log("actualizarProducto()");
        response            = false;
        id                  = $("#hiddenBuscar").val();
        $(".divControlable").removeClass("has-error");
        $(".divControlableTipoPrecios").removeClass("has-error");
        $("#btnGuardar").attr('disabled', 'disabled');
        $("#btnGuardar").html('<i class="fa fa-circle-o-notch fa-spin fa-fw margin-bottom"></i> Espera...');
        $("input")      .attr('disabled','disabled');
        $("select")     .attr('disabled','disabled');
        nombreCorto         =   $("#inputNombreCorto").val();
        nombreLargo         =   $("#inputNombreLargo").val();
        departamento        =   $("#selectCategoria").val();
        departamentoText    =   $("#selectCategoria option:selected").html();
        codigoBarras        =   $("#inputCodigoBarras").val();
        codigo2             =   $("#inputCodigo2").val();
        claveSHCP           =   $("#inputSHCP").val();
        balanza             =   $("#selectBascula").val();
        unidadVenta         =   $("#selectUnidad").val();
        unidadVentaText     =   $("#selectUnidad option:selected").html();
        precioLista         =   $("#inputPrecioLista").val();
        factor              =   $("#inputFactor").val();
        iva                 =   $("#inputIVA").val();
        ieps                =   $("#inputIEPS").val();
        imgCtrl             =   $("#imgCtrl").val();
        imgToggle           =   $("#imgToggle").val();
        // if(parseInt(imgCtrl)== 1 && parseInt(imgToggle) == 1)
        //     img             =   $("#inputFileImagen").prop('files')[0];
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
        data.append('id',id);
        data.append('nombreCorto',nombreCorto);
        data.append('nombreLargo',nombreLargo);
        data.append('departamento',departamento);
        data.append('departamentoText',departamentoText);
        data.append('codigoBarras',codigoBarras);
        data.append('codigo2',codigo2);
        data.append('claveSHCP',claveSHCP);
        data.append('balanza',balanza);
        data.append('unidadVenta',unidadVenta);
        data.append('unidadVentaText',unidadVentaText);
        data.append('precioLista',precioLista);
        data.append('factor',factor);
        data.append('iva',iva);
        data.append('ieps',ieps);
        if(parseInt(imgCtrl)== 1 && parseInt(imgToggle) == 1)
            data.append('img',img);
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
        // esteDialogo = $( this );
        $.ajax(
        {
            type: "POST",
            url:"../control/modificarProductoBd.php",
            data: data,
            //dataType: "HTML",
            contentType:false,
            processData:false,
            cache:false
        })
        .done(function(p)
        {
            response = true;
            if(p.status == 1)
            {
                $("#divRespuesta").html(p.respuesta);
            }
            else
            {
                $("#divRespuesta").html(p.respuesta);
            }
        })
        .always(function(p)
        {
            //$("html, body").animate({ scrollTop: 0 }, 600);
            $("input")      .prop("disabled", false);
            $("select")     .prop("disabled", false);
            //$("#selectBascula").attr("disabled","disabled");
            $("#btnGuardar").prop("disabled", false);
            $("#btnGuardar").html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar');
            console.log(p);
        });
        return response;
    }
    //$(document).on()

    $(document).ready(function()
    {
        $( "#dialog-confirm-cambiar-bascula" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            autoOpen: false,
            closeOnEscape: false,
            position: { my: 'top', at: 'top+1' },
            show:
            {
                effect: "slide",
                duration: 300,
                direction: 'up'
            },
            open: function()
            {
                dialogoAbierto = 1;
                $(this).parent().find(".ui-dialog-titlebar-close").remove();
                texto = $( "#dialog-confirm-cambiar-bascula" ).data('texto');
                $("#spanTextoDialogBascula").html(texto);


            },
            close: function()
            {
                dialogoAbierto = 0;
            },
            buttons:
            [
                {
                    text: "Aceptar",
                    click: function()
                    {
                        val = $( "#dialog-confirm-cambiar-bascula" ).data('val');

                        if (val != 1)
                        {
                            inputMenPxU = $("#inputMenPxU").val();
                            inputMedPxU = $("#inputMedPxU").val();
                            inputMayPxU = $("#inputMayPxU").val();
                            inputEspPxU = $("#inputEspPxU").val();
                            $("#divListaPrecios").empty();
                            $("#divListaPrecios").html(tablaKg);
                            $("table").css("min-width","450px");
                            $("#dvCol").removeClass("col-lg-12");
                            $("#dvCol").addClass("col-lg-6 col-lg-offset-3");
                            $("#inputFactor").val(1);
                            $("#selectUnidad").val(1);
                            $("#inputMenPxU").val(inputMenPxU);
                            $("#inputMedPxU").val(inputMedPxU);
                            $("#inputMayPxU").val(inputMayPxU);
                            $("#inputEspPxU").val(inputEspPxU);
                            actualizarPxP($("#inputMenPxU"));
                            actualizarPxP($("#inputMedPxU"));
                            actualizarPxP($("#inputMayPxU"));
                            actualizarPxP($("#inputEspPxU"));
                        }
                        else
                        {
                            inputMenPxU = $("#inputMenPxU").val();
                            inputMedPxU = $("#inputMedPxU").val();
                            inputMayPxU = $("#inputMayPxU").val();
                            inputEspPxU = $("#inputEspPxU").val();
                            $("#divListaPrecios").empty();
                            $("table").css("min-width","850px");
                            $("#dvCol").removeClass("col-lg-6 col-lg-offset-3");
                            $("#dvCol").addClass("col-lg-12");
                            $("#divListaPrecios").html(tabla);
                            $("#inputMenPxP").val(inputMenPxU);
                            $("#inputMedPxP").val(inputMedPxU);
                            $("#inputMayPxP").val(inputMayPxU);
                            $("#inputEspPxP").val(inputEspPxU);
                            actualizarPxP($("#inputMenPxP"));
                            actualizarPxP($("#inputMedPxP"));
                            actualizarPxP($("#inputMayPxP"));
                            actualizarPxP($("#inputEspPxP"));
                        }
                        $( this ).dialog( "close" );

                    }
                },
                {
                    text: "Cancelar",
                    click: function()
                    {
                        val = $( "#dialog-confirm-cambiar-bascula" ).data('val');
                        $("#selectBascula").val(val);
                        $( this ).dialog( "close" );
                    }
                }
            ]
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

        dialogoAbierto          = 0;
        processAjax             = 0;
        guardado                = false;
        $(document).keydown(function(e)
        {
            if (e.keyCode       == 34 || e.keyCode == 33)
            {
                e.preventDefault();
                if (processAjax == 1 || dialogoAbierto == 1)
                    return false;
                processAjax     = 1;
                if (e.keyCode   == 33)
                {
                    idActual    = parseInt($("#hiddenBuscar").val());
                    direccion   = 0;
                }
                if (e.keyCode   == 34)
                {
                    idActual    = parseInt($("#hiddenBuscar").val());
                    direccion   = 1;
                }
                idFocus         = document.activeElement.id;
                //$("#btnGuardar").click();
                if (idActual != -1)
                {
                    guardado        = actualizarProducto();
                }
                //processAjax     = 1;
                //alert(document.activeElement.id);
                // if (guardado)
                // {
                    $.ajax(
                    {
                        url:"../control/convId-Nombre.php",
                        method:"POST",
                        data:{idProducto:idActual,direccion:direccion}
                    }).done(function(response)
                    {
                        if(response.status == 1)
                        {
                            $("#hiddenBuscar").val(response.id);
                            $("#hiddenIdProducto-img").val(response.id);
                            //$("#inputBuscar").focus();
                            if($("#hiddenBuscar").val().length > 0)
                            {
                                idProducto = $("#hiddenBuscar").val();
                                //datos = $("#inputBuscar").val();
                                $.ajax(
                                {
                                    method: "POST",
                                    url:"../control/modificarProducto.php",
                                    data: {idProducto:idProducto}
                                })
                                .done(function(d)
                                {
                                    d += '<div class="col-lg-12">';
                                    d += '    <div class="col-lg-6 col-lg-offset-3" style="text-align:center">';
                                    d += '        <button type="button" id="btnGuardar" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>';
                                    d += '    </div>';
                                    d += '</div>';
                                    $("#divData").html(d);
                                    $("#inputBuscar").val(response.nombreLargo);
                                    processAjax = 0;
                                    $("#"+idFocus).focus();
                                });
                            }
                        }
                    })
                    .always(function(e)
                    {
                        console.log(e);
                    });
                // }
                }

            });
            $(document).on('click',"#btnEliminarImg",function()
            {
                $( "#dialog-confirm-eliminarImg" ).dialog("open");
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
                            dialogo = $( this );

                            // $("#hiddenImgBinario").val('');
                            // $("#hiddenImgTipo").val("");
                            idProducto = $("#hiddenBuscar").val();
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/eliminarImgProducto.php",
                                data: {idProducto:idProducto}
                            })
                            .done(function(p)
                            {
                                console.log(p);
                                if (p.exito == 1)
                                {
                                    dialogo.dialog("close");
                                    btnImg = $("#btnEliminarImg");
                                    btnImg.addClass('btn-outline');
                                    btnImg.removeClass('btn-success');
                                    btnImg.addClass('btn-info');
                                    btnImg.html('<i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i></br>Imagen');
                                    btnImg.prop('id','btnAnadirImg');
                                    $("#vistaPrevia").removeAttr("src");
                                    $("#vistaPrevia").attr("src","");
                                    $("#inputReset").click();
                                    $("#divRespuesta").html(p.respuesta);
                                }
                                else
                                {
                                    dialogo.dialog("close");
                                    $("#divRespuesta").html(p.respuesta);
                                }
                            })
                            .always(function(p)
                            {
                                dialogo.dialog("close");
                                console.log(p);
                            });
                            //$( this ).dialog("close");
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
            ////////////////// MODAL BUSCAR POR NOMBRE /////////////
            $("#inputBuscar").keydown(function(e)
            {
                if(e.keyCode == 13)
                {
                    nombre = $(this).val();
                    $.ajax(
                    {
                        url:"../control/convNombre-Id.php",
                        method:"POST",
                        data:{nombre:nombre}
                    }).done(function(response)
                    {
                        if(response.length > 0)
                        {
                            $("#hiddenBuscar").val(response);
                            $("#hiddenIdProducto-img").val(response);
                            //$("#inputBuscar").focus();
                            if($("#hiddenBuscar").val().length > 0)
                            {
                                idProducto = $("#hiddenBuscar").val();
                                //datos = $("#inputBuscar").val();
                                $.ajax(
                                {
                                    method: "POST",
                                    url:"../control/modificarProducto.php",
                                    data: {idProducto:idProducto}
                                })
                                .done(function(d)
                                {
                                    d += '<div class="col-lg-12">';
                                    d += '    <div class="col-lg-6 col-lg-offset-3" style="text-align:center">';
                                    d += '        <button type="button" id="btnGuardar" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar</button>';
                                    d += '    </div>';
                                    d += '</div>';
                                    $("#divData").html(d);
                                    $("#inputNombreCorto").focus();
                                });
                            }
                        }
                    });
                }

            });
            $("body").on("click", "#btnGuardar", function()
            {
                actualizarProducto();
            });
            $(document).on('click',"#btnAnadirImg",function()
            {
                $("#inputFileImagen").click();
            });
            $("#inputFileImagen").change(function()
            {
                $("#submitImagen").click();
            });
            (function() {



                $('#formfileupload').ajaxForm({
                    beforeSend: function() {
                        //status.empty();
                        var bar             = $('#divfileupload');
                        var percent         = $('.sr-only');
                        var status          = $('#status');
                        var divRespuesta    = $("#divRespuesta");
                        var vistaPrevia     = $("#vistaPrevia");
                        var progressUpload  = $("#progressUpload");
                        var btnImg          = $("#btnAnadirImg");
                        var hidden          = $("#hiddenImgBinario");
                        var hiddenTipo		= $("#hiddenImgTipo");

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
                            //alert("Se subió");
                            $("#btnAnadirImg").removeClass('btn-outline');
                            $("#btnAnadirImg").addClass('btn-success');
                            $("#btnAnadirImg").removeClass('btn-info');
                            $("#btnAnadirImg").html('<i class="fa fa-times fa-2x" aria-hidden="true"></i></br>Eliminar');
                            $("#btnAnadirImg").prop('id','btnEliminarImg');
                            $("#imgSrc").html(xhr.respuesta);
                            $('#status').html(xhr.respuesta);
                            $("#divRespuesta").html(xhr.respuesta);
                            $("#vistaPrevia").attr("src",xhr.src);
                            // hidden.val(xhr.binario);
                            // hiddenTipo.val(xhr.tipo);
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
            $("#inputBuscar").typeahead
            ({
                source: function(query, result)
                {
                    $.ajax(
                    {
                        url:"../control/fetch.php",
                        method:"POST",
                        data:{query,query},
                        dataType:"json"
                    }).done(function(data)
                    {
                        result($.map(data, function(item)
                        {
                            return item;
                        }));
                    });
                }
            });
            $(document).on('change',"#selectBascula",function()
            {
                //alert('cambio');
                if($(this).val()==1)
                {
                    val = 0;
                    texto = 'Al cambiar el campo a "Sí se requiere báscula para su venta" el Factor de Conversión cambiará a 1 y la Unidad de Venta cambiará a Kilogramo.';

                }
                else
                {
                    val = 1;
                    texto = 'Al cambiar el campo a "No se requiere báscula para su venta" se volverá a recalcular la lista de precios.';


                }
                $("#dialog-confirm-cambiar-bascula").data('texto',texto).data('val',val).dialog('open');
            });
        });
    </script>
    <div id="dialog-confirm-cambiar-bascula" class="dialog-oculto" title="¿Cambiar tipo de venta?">
        <p>
            <h3> <i class="fa fa-question-circle" aria-hidden="true"></i> ¿Estás seguro que deseas continuar?</h3>
            <span id= "spanTextoDialogBascula"></span></br>
        </p>
    </div>
    <div id="dialog-confirm-eliminarImg" title="Eliminar imagen">
        <p>
            <h3>¿Deseas eliminar la imagen?</h3>
        </p>
    </div>
</body>

</html>
<?php
}
 ?>
