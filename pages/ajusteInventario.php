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

    <title>Ajuste de inventario</title>
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
        #dataTable_filter
        {
            text-align: right;
        }
        .dialog-oculto
        {
            display: none;
        }
    </style>
</head>
<body>
    <div id="recibo" style="display:none">
    </div>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"><i class="fa fa-list-ol" aria-hidden="true"></i> Ajuste de inventario</h1>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-6" id="divRespuesta">
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-5 col-md-4 col-sm-4 col-xs-4">
                    <label>Buscar por nombre</label>
                    <div class="form-group input-group input-group-lg">
                        <input type="text" autofocus class="form-control" id="inputBuscar">
                        <span class="input-group-btn">
                        <button tabindex="-1" class="btn btn-default" type="button"><i class="fa fa-search"></i>
                        </button>
                        </span>
                    </div>
                    <input type="hidden" id="hiddenBuscar">
                </div>
                <div class="col-lg-5 col-md-4 col-sm-4 col-xs-4">
                    <label>Buscar por C&oacute;digo</label>
                    <div class="form-group input-group input-group-lg">
                        <input type="text" class="form-control" id="inputBuscarCod">
                        <span class="input-group-btn">
                        <button tabindex="-1" class="btn btn-default" type="button"><i class="fa fa-barcode" aria-hidden="true"></i>
                        </button>
                        </span>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4">
                    </br>
                    <button type="button" style="padding-bottom:10px;margin-top:5px" class="btn btn-primary btn-lg btn-block" id="btnGuardar"><i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar</button>
                </div>
            </div>
            <div class="row" id="divData">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Eliminar</th>
                                    <th>Id</th>
                                    <th>C&oacute;digo</th>
                                    <th>Nombre</th>
                                    <th>Exist. sistema</th>
                                    <th>Exist. f&iacute;sicas</th>
                                    <th>Diferencia</th>
                                </tr>
                            </thead>
                            <tbody id="listaProductos">

                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>

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
    <script src="../startbootstrap/vendor/printThis/printThis.js"></script>
    <script src="../startbootstrap/vendor/jsBarcode/JsBarcode.all.min.js"></script>
    <script src="../startbootstrap/vendor/jquery-upload-files/jquery.uploadfile.min.js"></script>
    <script src="../startbootstrap/vendor/typeahead/typeahead.min.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
        listaProductos = [];
        function producto (id, codigobarras, cantidadantes, cantidaddespues)
        {
            this.id                 = id;
            this.codigobarras       = codigobarras;
            this.cantidadantes      = cantidadantes;
            this.cantidaddespues    = cantidaddespues;
        }
        function agregarTr(p)
        {
            existencia = parseFloat(p.existencia);
            existenciaFisico = parseFloat($("#inputCantidadFisico").val());
            dif = existenciaFisico - existencia;
            row  = '<tr class="trItemLista">'
            row +='     <td class="text-center">';
            row +=          '<button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem"><i class="fa fa-times"></i></button>';
            row +='     </td>';
            row += '    <td>'+p.idProducto+'</td>';
            row += '    <td>'+p.codigo+'</td>';
            row += '    <td>'+p.nombreProducto+'</td>';
            row += '    <td class="text-right">'+existencia.toFixed(3)+'</td>';
            row += '    <td class="text-right">'+existenciaFisico.toFixed(3)+'</td>';
            row += '    <td class="text-right">'+dif.toFixed(3)+'</td>';
            row += '</tr>'
            $("#listaProductos").prepend(row);
            prod = new producto(p.idProducto, p.codigo, existencia, existenciaFisico);
            listaProductos.unshift(prod); //al final
            console.log(listaProductos);
            reordenarItems();
        }
        function reordenarItems()
        {
            $(".btnEliminarItem").each(function(index)
            {
                $(this).attr("name",index);
                $(this).parent().parent().attr("name",index);
                //atributoItemName++;
            });
        }
        $(document).ready(function()
        {
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
                            //alert("response="+response);
                            //$("#inputBuscar").focus();
                            if($("#hiddenBuscar").val().length > 0)
                            {
                                //idProducto = response;
                                idProducto = $("#hiddenBuscar").val();
                                existe = 0;
                                if(listaProductos.length > 0)
                                {
                                    for (var i = 0; i < listaProductos.length; i++)
                                    {
                                        if(idProducto == listaProductos[i]['id'])
                                        {
                                            $("#listaProductos tr[name="+i+"]").addClass("success",80, function()
                                            {
                                                setTimeout(function()
                                                {
                                                    $( "#listaProductos tr" ).removeClass( "success",240 );
                                                }, 2000 );
                                            });
                                            existe = 1;
                                            break;
                                        }
                                    }
                                }
                                //datos = $("#inputBuscar").val();
                                if(existe == 0)
                                $.ajax(
                                {
                                    method: "POST",
                                    url:"../control/modalExistAjusteInv.php",
                                    data: {idProducto:idProducto}
                                })
                                .done(function(d)
                                {
                                    if(d.status == 1)
                                    {
                                        $("#rowModalExist").html(d.html);
                                        $( "#dialog-actualizar-cantidad" ).dialog("open");
                                    }
                                    else
                                    {
                                        $("#divRespuesta").html(d.respuesta);
                                    }
                                }).always(function(d)
                                {
                                    console.log(d);
                                });
                            }
                        }
                        else {

                            //alert("response length = 0");
                        }
                    });
                }

            });
            $("#inputBuscarCod").keydown(function(e)
            {
                if(e.keyCode == 13)
                {
                    codBarras = $(this).val();
                    if(codBarras.length == 0)
                        return false;
                    //idProducto = $("#hiddenBuscar").val();
                    //datos = $("#inputBuscar").val();
                    existe = 0;
                    if(listaProductos.length > 0)
                    {
                        for (var i = 0; i < listaProductos.length; i++)
                        {
                            if(codBarras == listaProductos[i]['codigobarras'])
                            {
                                $("#listaProductos tr[name="+i+"]").addClass("success",80, function()
                                {
                                    setTimeout(function()
                                    {
                                        $( "#listaProductos tr" ).removeClass( "success",240 );
                                    }, 2000 );
                                });
                                existe = 1;
                                break;
                            }
                        }
                    }
                    //datos = $("#inputBuscar").val();
                    if(existe == 0)
                    $.ajax(
                    {
                        method: "POST",
                        url:"../control/modalExistAjusteInv.php",
                        data: {codBarras:codBarras}
                    })
                    .done(function(d)
                    {
                        if(d.status == 1)
                        {
                            $("#hiddenBuscar").val(d.id);
                            $("#rowModalExist").html(d.html);
                            $( "#dialog-actualizar-cantidad" ).dialog("open");
                        }
                        else
                        {
                            $("#divRespuesta").html(d.respuesta);
                        }
                    });
                }
            });
            $( "#dialog-actualizar-cantidad" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                autoOpen: false,
                show:
                {
                    effect: "scale",
                    duration: 250
                },
                open: function()
                {
                    dialogoAbierto = 1;
                    $("#divRespuesta").empty();
                },
                close: function()
                {
                    dialogoAbierto = 0;
                    $("#inputBuscar").val('');
                    $("#inputBuscarCod").val('');
                    $("#inputBuscar").focus();
                },
                buttons:
                [
                    {
                        text: "Agregar",
                        "id": "btnAgregarItem",
                        click: function()
                        {
                            dialogo = $(this);
                            idProducto = $("#hiddenBuscar").val();
                            cant_ = $("#inputCantidadFisico").val();
                            if(cant_.length == 0 || isNaN(cant_) || cant_ < 0)
                            {
                                $("#inputCantidadFisico").parent().addClass('has-error');
                                return false;
                            }
                            else
                            {
                                $("#inputCantidadFisico").parent().removeClass('has-error');
                            }
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/agregarItemAjusteInv.php",
                                data: {idProducto:idProducto}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    agregarTr(p);
                                }
                                else
                                {
                                    $("#divRespuesta").html(p.respuesta);
                                }
                                dialogo.dialog( "close" );
                            })
                            .fail(function()
                            {
                                alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                                dialogo.dialog( "close" );
                                btnTerminarCotizacion.html('Aceptar');
                                btnTerminarCotizacion.prop("disabled", false);
                                btnCancelarCotizacion.prop("disabled", false);
                            })
                            .always(function(p)
                            {
                                console.log(p);
                            })

                            //$( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarItem",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-confirm-eliminarItem" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                autoOpen: false,
                show:
                {
                    effect: "scale",
                    duration: 250
                },
                close: function()
                {
                    dialogoAbierto = 0;
                    $("#inputBuscar").focus();
                },
                buttons:
                [
                    {
                        text: "Eliminar producto",
                        click: function()
                        {
                            item = $( "#dialog-confirm-eliminarItem" ).data('item');
                            y = listaProductos.splice(item,1);
                            $(".trItemLista[name="+item+"]").remove();
                            $( this ).dialog( "close" );
                            reordenarItems();
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
            $( "#dialog-confirm-guardar" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 460,
                modal: true,
                autoOpen: false,
                show:
                {
                    effect: "scale",
                    duration: 250
                },
                close: function()
                {
                    dialogoAbierto = 0;
                    $("#inputBuscar").focus();
                },
                open: function()
                {

                },
                buttons:
                [
                    {
                        text: "Actualizar inventario",
                        id:"btnActualizarInv",
                        click: function()
                        {
                            btnActualizarInv = $("#btnActualizarInv");
                            btnCancelarInv = $("#btnCancelarInv");
                            btnActualizarInv.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            btnActualizarInv.prop("disabled", true);
                            btnCancelarInv.prop("disabled", true);
                            if ($('#chkCompra').prop('checked'))
                                imprimirCompra  = 1;
                            else
                                imprimirCompra  = 0;
                            if ($('#chkNota').prop('checked'))
                                imprimirNotaS    = 1;
                            else
                                imprimirNotaS    = 0;
                            dialogo = $(this);
                            var arrayJSON = JSON.stringify(listaProductos);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/guardarAjusteInventarioBd.php",
                                data: {arrayJSON:arrayJSON,imprimirCompra:imprimirCompra,imprimirNotaS:imprimirNotaS}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    if (p.ajuste == 1)
                                    {
                                        //url = "http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/index.php";
                                        //url = "http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genAjusteInventarioPDF.php?idAjuste="+p.idAjuste;
                                        //$("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                    }
                                    if (p.hayNotaS == 1)
                                    {
                                        url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genNotaSalidaPDF.php?idNota="+p.idNota;
                                        $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                    }
                                    if(p.hayCompra == 1)
                                    {
                                        $('#recibo').html(p.recibo).promise().done(function()
                                        {
                                            //your callback logic / code here
                                            JsBarcode("#code_svg",p.codigo,
                                            {
                                                width:2,
                                                height:35,
                                                fontSize:13,
                                                margin:1
                                            });
                                            $('#recibo').printThis();

                                        });
                                    }
                                    setTimeout(function()
                                    {
                                        //dialogo.dialog( "close" );
                                        window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/ajusteInventario.php");
                                    }, 2250);
                                    dialogo.dialog( "close" );
                                }
                                else
                                {
                                    $("#divRespuesta").html(p.respuesta);
                                    dialogo.dialog( "close" );
                                    btnActualizarInv.html('Actualizar inventario');
                                    btnActualizarInv.prop("disabled", false);
                                    btnCancelarInv.prop("disabled", false);
                                    dialogo.dialog( "close" );
                                    //console.log(p);
                                }
                            }).fail(function(p)
                            {
                                alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                                dialogo.dialog( "close" );
                                btnActualizarInv.html('Actualizar inventario');
                                btnActualizarInv.prop("disabled", false);
                                btnCancelarInv.prop("disabled", false);
                                console.log(p);
                            }).always(function(p)
                            {
                                console.log(p);
                            });

                        }
                    },
                    {
                        text: "Cancelar",
                        id:"btnCancelarInv",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $("#rowModalExist").on("keyup", "#inputCantidadFisico", function(e)
            {
                cantFisico = $("#inputCantidadFisico");

                if(e.keyCode == 13)
                {
                    if (isNaN(cantFisico.val()) || cantFisico.val().length == 0)
                        return false;
                    else
                        $("#btnAgregarItem").click();
                }
                else
                {
                    cantSis = parseFloat($("#inputCantidadSistema").val());
                    dif = parseFloat(cantFisico.val()) - cantSis;
                    $("#inputCantidadDiferencia").val(dif);
                }
            });
            $("#listaProductos").on("click", ".btnEliminarItem", function()
            {
                item = $(this).attr("name");
                //alert("item: "+item);
                $( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            });
            $("body").on("click", "#btnGuardar", function()
            {
                $( "#dialog-confirm-guardar" ).dialog("open");
            });
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
        });
    </script>
</body>
<div id="dialog-actualizar-cantidad" class="dialog-oculto" title="Ajustar cantidad">
    <div class="row" id="rowModalExist">

    </div>
</div>
<div id="dialog-confirm-eliminarItem" class="dialog-oculto" title="Eliminar producto">
    <p>
        <h3>¿Eliminar producto?</h3>
    </p>
</div>

<div id="dialog-confirm-guardar" class="dialog-oculto" title="Actualizar inventario">
    <p>
        <h3><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas actualizar el inventario?</h3>
    </p>
    <div class="form-group">
        <div class="checkbox">
            <label>
                <input id="chkCompra" type="checkbox" checked>Imprimir Ticket de compra
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input id="chkNota" type="checkbox" checked>Imprimir Nota de salida
            </label>
        </div>
    </div>
</div>

</html>
<?php
}
 ?>
