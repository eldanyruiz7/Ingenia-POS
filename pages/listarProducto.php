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
    <title>Listar productos</title>
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
        a.aEliminar, a.aModificar, a.aDesactivar, a.aActivar, .sorting, .sorting_desc, .sorting_asc
        {
            cursor:pointer;
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
                    <h1 class="page-header"><span id="spanCargando"><!-- --><i class="fa fa-spinner fa-pulse fa-fw"></i> Espera...<!-- Listar Productos--></span></h1>
                </div>
                <div id="divRespuesta" class="col-lg-6 col-md-6 col-xs-6" style="padding-top: 20px;">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
            <?php
                /*$sql ="SELECT
                            tipoprecio
                        FROM
                            clientes
                        LIMIT 1";
                $resultadoCliente   = $mysqli->query($sql);
                $tipoCliente        = $resultadoCliente->fetch_assoc();
                $tipoprecio         = $tipoCliente['tipoprecio'];*/
                 ?>
                <div class="col-lg-12" id="cargarData" style="display:none">
                    <table id="dataTable" class="table table-striped table-bordered table-hover table-condensed" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Nombre corto</th>
                                <th>Presentaci&oacute;n</th>
                                <th>Departamento</th>
                                <th>Unidad venta</th>
                                <th>Factor</th>
                                <th>Codigo barras</th>
                                <th>Codigo corto</th>
                                <th>Existencia</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php
                $sql = "SELECT
                            productos.id AS id,
                            productos.nombrecorto AS nombrecorto,
                            productos.nombrelargo AS nombrelargo,
                            departamentos.nombre AS departamento,
                            unidadesventa.nombre AS unidadventa,
                            productos.factorconversion AS factor,
                            productos.codigo AS codigobarras,
                            productos.codigo2 AS codigo2,
                            productos.existencia AS existencia,
                            productos.pausado AS pausado
                        FROM productos
                        INNER JOIN departamentos
                        ON productos.departamento = departamentos.id
                        INNER JOIN unidadesventa
                        ON productos.unidadventa = unidadesventa.id
                        WHERE
                            productos.activo = 1
                        ORDER BY
                            productos.id ASC";
                $result = $mysqli->query($sql);
                while($arrayProductos = $result->fetch_assoc())
                {
                ?>
                        <tr id="trProducto<?php echo $arrayProductos['id'];?>">
                            <td>
                                <div class="btn-group pull-left">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu slidedown">
                                        <li>
                                            <a class="aModificar" name="<?php echo $arrayProductos['id'];?>">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar
                                            </a>
                                        </li>
                            <?php
                                if($arrayProductos['pausado'] == 0)
                                {
                            ?>
                                        <li>
                                            <a class="aDesactivar" name="<?php echo $arrayProductos['id'];?>">
                                                <i class="fa fa-hand-o-down" aria-hidden="true"></i> Desactivar
                                            </a>
                                        </li>
                            <?php
                                }
                                else
                                {
                            ?>
                                        <li>
                                            <a class="aActivar" name="<?php echo $arrayProductos['id'];?>">
                                                <i class="fa fa-hand-o-up" aria-hidden="true"></i> Activar
                                            </a>
                                        </li>
                            <?php
                                }
                             ?>
                                        <li>
                                            <a class="aEliminar" name="<?php echo $arrayProductos['id'];?>">
                                                <i class="fa fa-times-circle" aria-hidden="true"></i> Eliminar
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <?php echo $arrayProductos['nombrecorto'];?>
                            </td>
                            <td>
                                <?php echo $arrayProductos['nombrelargo'];?>
                            </td>
                            <td>
                                <?php echo $arrayProductos['departamento'];?>
                            </td>
                            <td>
                                <?php echo $arrayProductos['unidadventa'];?>
                            </td>
                            <td>
                                <?php echo $arrayProductos['factor'];?>
                            </td>
                            <td>
                                <?php echo (strlen($arrayProductos['codigobarras']) === 0) ? '--' : $arrayProductos['codigobarras'];?>
                            </td>
                            <td>
                                <?php echo (strlen($arrayProductos['codigo2']) === 0) ? '--' : $arrayProductos['codigo2'];?>
                            </td>
                            <td class="text-right">
                                <?php echo number_format($arrayProductos['existencia'],3,".",",");?>
                            </td>
                            <td>
                                <?php echo ($arrayProductos['pausado'] == 0) ? 'Activo' : 'Inactivo'; ?>
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
    <script src="../startbootstrap/vendor/jquery-upload-files/jquery.uploadfile.min.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    var idProducto; //identificar idProdcuto que sigue, con el evento keyCode (AvPag/RePag)
    $(document).ready(function()
    {
        var wHeight = $(window).height();
        $( "#dialog-confirm-eliminarImg" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
            modal: true,
            autoOpen: false,
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
                        $("#divstatus").empty();
                        $("#divstatus").html('</br><i class="fa fa-file-image-o fa-5x"></i></br>&nbsp;');
                        $("#inputReset").click();
                        $("#hiddenImgBinario").val('');
                        $("#imgToggle").val('0');
                        //$("#imgCtrl").val('1');
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
            show:
            {
                effect: "drop",
                duration: 150
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
        $(document).on('click',"#btnEliminarImg",function()
        {
            $( "#dialog-confirm-eliminarImg" ).dialog("open");
        });
        $(document).on('click',"#btnAnadirImg",function()
        {
            $("#inputFileImagen").click();
        });
        $(document).on('click',".vistaPrevia",function()
        {
            $('#dialog-Img').dialog("open");
        });
        $(document).on('change',"#inputFileImagen",function()
        {

            $("#submitImagen").click();
        });

        $('#dataTable').DataTable(
         {
             "lengthMenu": [[10, 50, 100, 500],[10, 50, 100, 500]],
            "language":
            {
                 "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
            },
            "processing": true,
            responsive: true,
            "initComplete": function(settings, json)
            {
                $("#spanCargando").html('<i class="fa fa-list" aria-hidden="true"></i> Listar productos');
                $("#cargarData").show("fold",1000);
            }
        });
        $(document).on("click",".aEliminar",function()
        {
            item = $(this).attr("name");
            //$( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            $( "#dialog-confirm-eliminarProducto" ).data('item',item).dialog("open");
        });
        $(document).on("click",".aActivar",function()
        {
            item = $(this).attr("name");
            //$( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            $( "#dialog-confirm-activarProducto" ).data('item',item).dialog("open");
        });
        $(document).on("click",".aDesactivar",function()
        {
            item = $(this).attr("name");
            //$( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            $( "#dialog-confirm-desactivarProducto" ).data('item',item).dialog("open");
        });
        $(document).on("click",".aModificar",function()
        {
            idProducto = $(this).attr("name");
            $.ajax(
            {
                method: "POST",
                url:"../control/modificarProducto.php",
                data: {idProducto:idProducto}
            })
            .done(function(p)
            {
                wHeight = $(window).height();
                $("#divDialogModificarCliente").empty();
                $("#divDialogModificarCliente").html(p);
                $( "#dialog-confirm-modificarProducto" ).data('idProducto',idProducto).dialog("open");

            })
            .fail(function()
            {
                alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
            });

        });

        $( "#dialog-confirm-eliminarProducto" ).dialog(
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
                    text: "Eliminar producto",
                    click: function()
                    {
                        idProducto_ = $( "#dialog-confirm-eliminarProducto" ).data('item');
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/eliminarProducto.php",
                            data: {idProducto:idProducto_}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                $("#dataTable").DataTable().row($("#trProducto"+p.queProducto)).remove().draw();
                            }
                                $("#divRespuesta").html(p.respuesta);
                                $("html, body").animate({ scrollTop: 0 }, 600);
                        })
                        .fail(function()
                        {
                            alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
                        })
                        .always(function(p)
                        {
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
                        $(this).parent().find(".ui-dialog-titlebar-close").remove();
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
        $( "#dialog-confirm-activarProducto" ).dialog(
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
                    text: "Activar",
                    click: function()
                    {
                        idProducto_ = $( "#dialog-confirm-activarProducto" ).data('item');
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/activarProducto.php",
                            data: {idProducto:idProducto_}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                var oTable = $("#dataTable").DataTable();
                                index = oTable.row($("#trProducto"+p.queProducto)).index();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,9).data(p.estatus).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,0).data(p.btn).draw();
                            }
                                $("#divRespuesta").html(p.respuesta);
                                $("html, body").animate({ scrollTop: 0 }, 600);
                        })
                        .fail(function()
                        {
                            alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
                        })
                        .always(function(p)
                        {
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
        $( "#dialog-confirm-desactivarProducto" ).dialog(
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
                    text: "Desactivar",
                    click: function()
                    {
                        idProducto_ = $( "#dialog-confirm-desactivarProducto" ).data('item');
                        $.ajax(
                        {
                            method: "POST",
                            url:"../control/desactivarProducto.php",
                            data: {idProducto:idProducto_}
                        })
                        .done(function(p)
                        {
                            if(p.status == 1)
                            {
                                var oTable = $("#dataTable").DataTable();
                                index = oTable.row($("#trProducto"+p.queProducto)).index();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,9).data(p.estatus).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,0).data(p.btn).draw();
                            }
                                $("#divRespuesta").html(p.respuesta);
                                $("html, body").animate({ scrollTop: 0 }, 600);
                        })
                        .fail(function()
                        {
                            alert("No se puede actualizar en este momento. Consulte con el adminsitrador del sistema");
                        })
                        .always(function(p)
                        {
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
        $( "#dialog-confirm-modificarProducto" ).dialog(
        {
            resizable: true,
            height: wHeight,
            width: "95%",
            modal: true,
            autoOpen: false,
            title: "Editar artículo",
            show:
            {
                effect: "highlight",
                duration: 250
            },
            open: function()
            {
                $('html, body').css(
                {
                    overflow: 'hidden',
                    height: '100%'
                });
                $(this).keydown(function(e)
                {
                    if(e.keyCode == 34)
                    {
                        e.preventDefault();
                        $("#btnGuardarArt").click();
                    }
                    if(e.keyCode == 33)
                    {
                        e.preventDefault();
                        alert("Repag");
                    }
                });
                $("#inputMenPxU").focus();
                $("#inputMenPxP").focus();
            },
            close: function()
            {
                $('html, body').css(
                {
                    overflow: 'auto',
                    height: 'auto'
                });
            },
            buttons:
            [
                {
                    text: "Guardar",
                    id: "btnGuardarArt",
                    click: function()
                    {
                        id = $( "#dialog-confirm-modificarProducto" ).data('idProducto');
                        //id              =   $(this).attr("name");
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
                        if(parseInt(imgCtrl)== 1 && parseInt(imgToggle) == 1)
                            img             =   $("#inputFileImagen").prop('files')[0];
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
                        esteDialogo = $( this );
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
                            if(p.status == 1)
                            {
                                var oTable = $("#dataTable").DataTable();
                                index = oTable.row($("#trProducto"+p.queProducto)).index();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,1).data(p.nombreCorto).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,2).data(p.nombreLargo).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,3).data(p.departamento).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,4).data(p.unidadVenta).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,5).data(p.factor).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,6).data(p.codigoBarras).draw();
                                oTable.row($("#trProducto"+p.queProducto)).cell(index,7).data(p.codigo2).draw();
                                esteDialogo.dialog( "close" );
                                $("#divRespuesta").html(p.respuesta);

                            }
                            else
                            {
                                $("#divRespuestaModal").html(p.respuesta);
                            }
                        })
                        .always(function(p)
                        {
                            $("#dialog-confirm-modificarProducto").animate({ scrollTop: 0 }, 400);
                            $("input")      .prop("disabled", false);
                            $("select")     .prop("disabled", false);
                            $("#selectBascula").attr("disabled","disabled");
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
    <div id="dialog-confirm-cambiar-bascula" class="dialog-oculto" title="¿Cambiar tipo de venta?">
        <p>
            <h3> <i class="fa fa-question-circle" aria-hidden="true"></i> ¿Estás seguro que deseas continuar?</h3>
            <span id= "spanTextoDialogBascula"></span></br>
        </p>
    </div>
    <div id="dialog-confirm-eliminarProducto" class="dialog-oculto" title="Eliminar producto">
        <p>
            <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ¿Eliminar producto?</h3>
        </p>
        *Nota: Esta acci&oacute;n no se puede deshacer.
    </div>
    <div id="dialog-confirm-activarProducto" class="dialog-oculto" title="Desactivar producto">
        <p>
            <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ¿Deseas activar este producto?</h3>
        </p>
    </div>
    <div id="dialog-confirm-desactivarProducto" class="dialog-oculto" title="Desactivar producto">
        <p>
            <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ¿Deseas desactivar este producto?</h3>
        </p>
        *Nota: Al desactivarlo no podr&aacute; venderse.
    </div>
    <div id="dialog-confirm-modificarProducto" class="dialog-oculto" title="">
        <div id="">
            <div class="col-lg-12" id="divDialogModificarCliente">
            </div>
        </div>
    </div>
    <div id="dialog-confirm-eliminarImg" class="dialog-oculto" title="Eliminar imagen">
        <p>
            <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ¿Deseas eliminar la imagen?</h3>
        </p>
    </div>
    <div id="dialog-Img" class="dialog-oculto" title="Imagen">
        <div class="col-lg-12 col-md-12 col-sm-12" style="text-align:center">
            <img id="imgSrc" width="600px" height="500px" src="" alt="Imagen" />
        </div>
    </div>
</body>

</html>
<?php
}
?>
