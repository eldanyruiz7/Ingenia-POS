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
   // Aquí va el contenido de la pagina qu se mostrara en caso de que se haya iniciado sesion
   $pagina = 4;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Editar Cotización</title>
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
                    <h1 class="page-header"> <span id="spanCargando"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar cotización</span></h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group input-group input-group-lg">
                        <input id="inputBuscar" autofocus type="text" value="<?php echo (isset($_GET['idCotizacion'])) ? $_GET['idCotizacion'] : ''; ?>" class="form-control" placeholder="No. de cotización">
                        <span class="input-group-btn">
                            <button id="btnBuscar" class="btn btn-success" type="button" style="background-color:mediumpurple;border-color:mediumpurple"><i class="fa fa-search"></i> Buscar
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6" id="divRespuesta">
                </div>
            </div>
            <div class="row" id="data">

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
    <script src="../startbootstrap/vendor/typeahead/typeahead.min.js"></script>
    <script src="../control/custom-js/redondearDec.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
        var listaProductos          = [];
        //var listaProductosOriginal  = [];
        var totalCredito            = 0;
        var granTotal               = 0;
        var antiguoTotal            = 0;
        var idCliente               = 0;
        var idVenta                 = 0;
        var dialogoAbierto          = 0;
        multiplicador               = 1; //Número que va antes del * para utilizar como multiplicador
        function itemCotizacion(idSubCotizacion, codigo, idProducto, precioU, cantidad, subTotal, descripcion)
        {
            this.idSubCotizacion    = idSubCotizacion;
            this.codigo             = codigo;
            this.idProducto         = idProducto;
            this.precioU            = precioU;
            this.cantidad           = cantidad;
            this.subTotal           = subTotal;
            this.descripcion        = descripcion;
        }
        // function itemCotizacion_O(id, idProducto, precioU, cantidad, subTotal)
        // {
        //     this.id = id;
        //     this.idProducto = idProducto;
        //     this.precioU = precioU;
        //     this.cantidad = cantidad;
        //     this.subTotal = subTotal;
        // }
        function preguntarAntesDeSalir ()
        {
            var respuesta;
            cambio = $("#btnGuardar").attr("disabled");
            if ( !cambio || ($("#data").find("#idCliente").length < 1) == true)
            {
                respuesta = confirm ( '¿Seguro que quieres salir?' );
                if ( respuesta ) {
                    window.onunload = function ()
                    {
                        return true;
                    }
                } else
                {
                    return false;
                }
            }

        }
        function armarCotizacion()
        {
            listaProductos = [];
            //listaProductosOriginal = [];
            $(".trItemLista").each(function(x)
            {
                idSubCotizacion     = $(this).attr("idsubcotizacion");
                codigo              = $(this).find(".tdId").attr("name");
                idProducto          = $(this).find(".tdProducto").attr("name");
                precioU             = $(this).find(".tdPrecio").attr("name");
                cantidad            = $(this).find(".tdCantidad").attr("name");
                subTotal            = $(this).find(".tdSubTotal").attr("name");
                descripcion         = $(this).find(".tdProducto").text();
                prod                = new itemCotizacion(idSubCotizacion, codigo, idProducto, precioU, cantidad, subTotal, descripcion);
                //prod_O      = new itemCotizacion_O(id, idProducto, precioU, cantidad, subTotal);
                listaProductos.push(prod);
                //listaProductosOriginal.push(prod_O);
            });
            listarArray();
        }
        function cargarListaProductos()
        {
            if(dialogoAbierto == 1 || $("#data").find("#idCliente").length < 1)
                return;
            dialogoAbierto = 1;
            $("#spanCargando").html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Cargando...');
            $.ajax(
            {
                method: "POST",
                url:"cargaListaProductos.php",
                data: {idCliente:idCliente,tipo:3}
            })
            .done(function(p)
            {
                $.when($("#divListaProducto").html(p)).done(function(x)
                {
                    $( "#dialog-buscar-producto" ).dialog("open");
                });
            })
            .always(function(p)
            {
                console.log(p);
            });
        }
        function agregarProducto(e,peso)
        {
            peso                    = 1
            existeProducto          = 0;
            row                     = 0;
            for (var i = 0; i < listaProductos.length; i++)
            {
                if (listaProductos[i]["idProducto"] == e.id)
                {
                    existeProducto  = 1;
                    row             = i;
                }
            }
            if (existeProducto      == 0)
            {
                cantidadEnPeso      = parseFloat(peso);
                subTotal            = parseFloat(e.precio) * parseFloat(cantidadEnPeso);
                subTotal            = subTotal.toFixed(2);
                precioU             = parseFloat(e.precio);
                precioU             = precioU.toFixed(2);
                row  ='  <tr class="trItemLista">';
                row +='     <td class="text-center">';
                row +=          '<button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem"><i class="fa fa-times"></i></button>';
                row +='     </td>';
                row +='     <td class="tdId">';
                row +=          e.codigo;
                row +='     </td>';
                row +='     <td class="tdProducto">';
                row +=          e.descripcion;
                row +='     </td>';
                row +='     <td class="text-right tdCantidad">';
                row +=          '<input type="number" min=0 style="text-align:right; border-width:0;width:100px;" class="inputCantidad" value="'+cantidadEnPeso.toFixed(3)+'">';
                row +='     </td>';
                row +='     <td class="text-right tdPrecio">';
                row +=          '$<input type="number" min=0 style="text-align:right; border-width:0;width:100px;" class="inputPrecio" value="'+precioU+'">';
                row +='     </td>';
                row +='     <td class="tdSubTotal text-right">';
                row +=          subTotal
                row +='     </td>';
                row +='  </tr>';
                $("#listaProductos").prepend(row);
                prod        = new itemCotizacion(-1,e.codigo, e.id, precioU, cantidadEnPeso, subTotal, e.descripcion);
                //prod = new producto(e.id, e.codigo, e.nombre, e.nombreCache, precioU, cantidadEnPeso.toFixed(3), stot_, e.precioXunidad, e.precioXpaquete, e.factorconversion);
                listaProductos.unshift(prod); //al final
            }
            else
            {
                $("#listaProductos tr[name="+row+"]").addClass("warning",50, function()
                {
                    $(this).find(".inputCantidad").focus();
                    setTimeout(function()
                    {
                        $( "#listaProductos tr" ).removeClass( "warning",200 );
                    }, 900 );
                });
            }
            $("#inputBuscarProducto").val('');
            $("#inputBuscarProducto").parent().removeClass('has-error');
            $("#btnGuardar").prop("disabled",false);
            actualizarNota();
        }
        function actualizarNota()
        {
            credito = 0;
            nuevoTotal = 0;
            for (var i = 0; i < listaProductos.length; i++)
            {

                precioU = listaProductos[i]["precioU"];
                cantidad = listaProductos[i]["cantidad"];
                nuevoSubTot = parseFloat(redondearDec(precioU * cantidad));
                listaProductos[i]["subTotal"] = nuevoSubTot;
                nuevoTotal = parseFloat(nuevoTotal) + parseFloat(nuevoSubTot);
                //console.log(listaProductos[i]["subTotal"]);
            }
            granTotal = nuevoTotal.toFixed(2);
            $("#spanNuevoTotal").text(nuevoTotal.toFixed(2));
            totalCredito = credito.toFixed(2);
            $(".trItemLista").each(function(x)
            {
                idSubCotizacion     = listaProductos[x]['idSubCotizacion'];
                codigo              = listaProductos[x]['codigo'];
                idProducto          = listaProductos[x]['idProducto'];
                precioU             = listaProductos[x]['precioU'];
                cantidad            = listaProductos[x]['cantidad'];
                subTotal            = listaProductos[x]['subTotal'];
                $(this).attr("name", idSubCotizacion);
                $(this).find(".tdId").attr("name",codigo);
                $(this).find(".tdProducto").attr("name",idProducto);
                $(this).find(".tdPrecio").attr("name",precioU);
                $(this).find(".tdCantidad").attr("name",cantidad);
                $(this).find(".tdSubTotal").attr("name",subTotal);
                $(this).attr("name", x);
                $(this).find(".btnEliminarItem").attr("name", x);
                subTotal            = parseFloat(listaProductos[x]["subTotal"]);
                $(this).find(".tdSubTotal").text("$"+subTotal.toFixed(2));

            });
        }
        function listarArray()
        {
            console.log(listaProductos);
            //console.log(listaProductosOriginal);
        }
        $(document).ready(function()
        {
            window.onbeforeunload = preguntarAntesDeSalir;
            ////////////////// MODAL BUSCAR POR NOMBRE /////////////
            $("#inputBuscarNombre").keydown(function(e)
            {
                if(e.keyCode == 13)
                {
                    nombre = $(this).val();
                    $.ajax(
                    {
                        url:"../control/convNombre-Cod.php",
                        method:"POST",
                        data:{nombre:nombre}
                    }).done(function(response)
                    {
                        if(response.length > 0)
                        {
                            separador = $("#inputBuscar").val();
                            $("#data").find("#inputBuscarProducto").val(response);
                            $("#modal-auto-complete").modal('hide');

                        }
                    });
                }

            });
            $("#inputBuscarNombre").typeahead
            ({
                source: function(query, result)
                {
                    $.ajax(
                    {
                        url:"../control/fetch.php",
                        method:"POST",
                        data:{query:query},
                        dataType:"json"
                    }).done(function(data)
                    {
                        result($.map(data, function(item)
                        {
                            return item;
                        }));
                    });
                },
                limit: 26
            });
            $("#modal-auto-complete").on('shown.bs.modal', function ()
            {
                //$("#inputBuscarNombre").val('');
                dialogoAbierto = 1;
                $("#inputBuscarNombre").focus();
            });
            $("#modal-auto-complete").on('hidden.bs.modal', function ()
            {
                dialogoAbierto = 0;
                $("#inputBuscarProducto").focus();
                if($("#inputBuscarProducto").val().length > 0)
                    $("#data").find("#btnBuscarProducto").click();
                $("#inputBuscarNombre").val('');
                //$("#inputBuscar").trigger("enterPress");
            });
            $("#btnBuscar").click(function()
            {
                id = $("#inputBuscar").val();
                $.ajax(
                {
                    method: "POST",
                    data: {id:id},
                    url: "../control/consultarCotizacion.php"
                }).done(function(d)
                {
                    if(d.status == 0)
                    {
                        $("#divRespuesta").html(d.respuesta);
                        $("#data").empty();
                    }
                    else
                    {
                        $("#data").html(d);
                        $("#divRespuesta").empty();
                        spanTotal = $("#spanTotal").text();
                        spanTotal = spanTotal.replace(",","");
                        antiguoTotal = parseFloat(spanTotal);
                        idCliente = $("#idCliente").val();
                        idVenta = $("#idVenta").val();
                        $("#inputBuscarProducto").focus();
                        armarCotizacion();
                    }
                }).fail(function()
                {
                    alert("Servidor no disponible, consúltalo con el administrador del sistema");
                });
            });
            $(document).keydown(function(e)
            {
                if(e.keyCode == 113)
                {
                    e.preventDefault();
                    $("#btnf2").click();
                }
                if(e.keyCode == 114)
                {
                    e.preventDefault();
                    if(dialogoAbierto == 1 || $("#data").find("#idCliente").length < 1)
                        return;
                    dialogoAbierto = 1;
                    $("#btnf3").click();
                }
            });
            $("#inputBuscar").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnBuscar").click();
            });
            $(document).on("keydown","#inputBuscarProducto",function(e)
            {
                if(e.keyCode == 13)
                    $("#btnBuscarProducto").click();
            });
            $(document).on("click","#btnBuscarProducto",function(e)
            {
                str                     = $("#inputBuscarProducto").val();
                array_str               = str.split('*',2);
                if(array_str.length     == 1)
                    datos               = str;
                else
                {
                    datos               = array_str[1];
                    if (isNaN(array_str[0]) || array_str[0].length == 0 || array_str[0] <= 0)
                        multiplicador   = 1;
                    else
                        multiplicador   = Math.round(array_str[0]);
                }
                $.ajax(
                {
                    method: "POST",
                    url:"../balanza/buscarProducto.php",
                    data: {datos:datos,idCliente:idCliente}
                })
                .done(function(d)
                {
                    if(!d.id)
                    {
                        $("#divGroup").addClass("has-error");
                        $("#inputBuscarProducto").focus();
                        return false;
                    }
                    else
                    {
                        agregarProducto(d,multiplicador);
                        console.log("multiplicador:"+multiplicador);
                    }
                })
                .always(function(e)
                {
                    console.log(e);
                });
            });
            $("body").on("click","#btnGuardar",function()
            {
                $( "#dialog-confirm-guardar" ).dialog("open");
            });
            $(document).on("keydown",".inputCantidad",function(e)
            {
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputCantidad").focus();
                }
                if(e.keyCode == 39)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputPrecio").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputCantidad").focus();
                }
            });
            $("body").on("focusin",".inputCantidad",function(e)
            {
                this.select();
            });
            $(document).on("change","#selectCliente", function()
            {
                $("#btnGuardar").attr("disabled",false);
            });
            $("body").on("change",".inputCantidad",function()
            {
                cant = $(this).val();
                if(parseFloat(cant) == 0 || cant.length == 0 || isNaN(cant))
                    $(this).parent().parent().find(".btnEliminarItem").click();
                else
                {
                    index = $(this).parent().parent().attr("name");
                    // tope = parseFloat($(this).parent().attr("name"));
                    cant = parseFloat(cant);
                    $(this).val(cant.toFixed(3));
                    listaProductos[index]["cantidad"] = cant.toFixed(3);
                    $("#btnGuardar").prop("disabled",false);
                    actualizarNota();
                    console.log(listaProductos[index]["cantidad"]);
                    //console.log(listaProductosOriginal[index]["cantidad"]);
                }
            });

            $(document).on("keydown",".inputPrecio",function(e)
            {
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputPrecio").focus();
                }
                if(e.keyCode == 37)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputCantidad").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputPrecio").focus();
                }
            });
            $("body").on("focusin",".inputPrecio",function(e)
            {
                this.select();
            });
            $("body").on("change",".inputPrecio",function()
            {
                precio = $(this).val();
                if(parseFloat(precio) == 0 || precio.length == 0 || isNaN(precio))
                    $(this).parent().parent().find(".btnEliminarItem").click();
                else
                {
                    index = $(this).parent().parent().attr("name");
                    // tope = parseFloat($(this).parent().attr("name"));
                    precio = parseFloat(precio);
                    $(this).val(precio.toFixed(2));
                    listaProductos[index]["precioU"] = precio.toFixed(2);
                    $("#btnGuardar").prop("disabled",false);
                    actualizarNota();
                    console.log(listaProductos[index]["cantidad"]);
                    //console.log(listaProductosOriginal[index]["cantidad"]);
                }
            });


            $("body").on("click", ".btnEliminarItem", function()
            {
                item = $(this).attr("name");
                valCant = $(this).parent().parent().find(".inputCantidad").val();
                input = $(this).parent().parent().find(".inputCantidad");
                $( "#dialog-confirm-eliminarItem" ).data('item',item).data('input',input).dialog("open");
            });
            $( "#dialog-buscar-producto" ).dialog(
            {
                resizable: true,
                height: "auto",
                width: 1300,
                modal: true,
                autoOpen: false,
                position: ["bottom",200],
                show:
                {
                    effect: "drop",
                    duration: 250
                },
                open: function()
                {
                    dialogoAbierto = 1;
                    setTimeout(function()
                    {
                        $("#dataTable_filter input.input-sm").focus();
                    },100);

                },
                close: function()
                {
                    dialogoAbierto = 0;
                    $("#divListaProducto").hide();
                    $("#inputBuscarProducto").focus();
                },
                buttons:
                [
                    {
                        text: "Añadir",
                        'id': "btnAnadirProductoXBusqueda",
                        'disabled':'disabled',
                        click: function()
                        {
                            cad = $(".trProductoElegible.success td .tdCodigoElegible").text();
                            cad = cad.trim()+
                                ""; //Sanitizar
                            //cad += "\n";
                            $("#inputBuscarProducto").val(cad);
                            $( this ).dialog( "close" );
                            $("#btnBuscarProducto").click();
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

            $( "#dialog-confirm-eliminarItem" ).dialog(
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
                close: function()
                {
                    input   = $( "#dialog-confirm-eliminarItem" ).data('input');
                    cacheVal = parseFloat(input.parent().attr("name"));
                    input.val(cacheVal.toFixed(3));
                    actualizarNota();
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
                            listarArray();
                            $("#btnGuardar").prop("disabled",false);
                            //actualizarNota();
                            //reordenarItems();
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
                close: function()
                {
                    $("#inputBuscar").focus();
                },
                buttons:
                [
                    {
                        text: "Actualizar cotización",
                        id: "btnActualCot",
                        click: function()
                        {
                            dialogo = $(this);
                            $("#btnActualCot").prop("disabled", true);
                            $("#btnActualCot").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Espera...');
                            $("#btnCancelarCot").prop("disabled", true);
                            var listaProductosJSON  = JSON.stringify(listaProductos);
                            var idCotizacion        = $("#idCotizacion").val();
                            var idCliente           = $("#selectCliente").val();
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/actualizarCotizacion.php",
                                data: {listaProductos:listaProductosJSON,idCotizacion:idCotizacion,idCliente:idCliente}
                            }).done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    $( "#btnGuardar" ).remove();
                                    $("#divRespuesta").html(p.respuesta);
                                    $("#data").empty();
                                    listaProductos.length = 0;
                                    $("#btnActualCot").prop("disabled", false);
                                    $("#btnActualCot").html('Actualizar cotización');
                                    $("#btnCancelarCot").prop("disabled", false);
                                    dialogo.dialog( "close" );
                                }
                                else
                                {
                                    $("#divRespuesta").html(p.respuesta);
                                    $("#btnActualCot").prop("disabled", false);
                                    $("#btnActualCot").html('Generar nota');
                                    $("#btnCancelarCot").prop("disabled", false);
                                    dialogo.dialog( "close" );
                                }
                            }).fail(function()
                            {
                                alert("Servidor no disponible, consúltalo con el administrador del sistema");
                                dialogo.dialog( "close" );
                                $("#btnActualCot").prop("disabled", false);
                                $("#btnActualCot").html('Generar nota');
                                $("#btnCancelarCot").prop("disabled", false);
                            })
                            .always(function(p)
                            {
                                console.log(p);
                            });
                        // }
                        //$(this).dialog("close");
                        }
                    },
                    {
                        text: "Cancelar",
                        id: "btnCancelarCot",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $(document).on("click", ".trProductoElegible",function() //marcar producto en lista modal
            {
                if($(this).hasClass("success"))
                {
                    $("#btnAnadirProductoXBusqueda").click();
                    return;
                }
                $(".trProductoElegible").each(function()
                {
                    $(this).removeClass("success");
                });
                $(this).addClass("success");
                $("#btnAnadirProductoXBusqueda").attr("disabled",false);
                $("#dataTable_filter input.input-sm").focus();
            });
            $(document).on("keydown", "#dataTable_filter input.input-sm", function(e) //desmarcar productos en lista moda
            {
                setTimeout(function()
                {

                    if(e.keyCode == 8)
                    {
                        $(".trProductoElegible").each(function()
                        {
                            $(this).removeClass("success");
                        });
                        $("#btnAnadirProductoXBusqueda").attr("disabled",true);
                    }
                    if(e.keyCode == 13)
                    {

                        if($(".trProductoElegible").length != 1)
                            return;
                        $("#btnAnadirProductoXBusqueda").click();
                    }
                    if($(".trProductoElegible").length == 1)
                    {
                        $(".trProductoElegible").addClass("success");
                        $("#btnAnadirProductoXBusqueda").attr("disabled",false);
                    }
                },100);
            });
            $("#btnf2").click(function()
            {
                cargarListaProductos();
            });
            $("#btnf3").click(function()
            {
                $("#btnModalBuscarNombre").click();
            });
            <?php if (isset($_GET['idCotizacion']))
            {
                echo '$("#btnBuscar").click();';
            }
            ?>

        });
    </script>
</body>
<div id="dialog-confirm-eliminarItem" title="Eliminar producto">
    <p>
        <h3>¿Eliminar producto?</h3>
    </p>
</div>
<div id="dialog-confirm-guardar" title="Actualizar cotización">
    <h4>
        <i class="fa fa-question-circle" aria-hidden="true"></i> Deseas guardar los cambios?
    </h4>
</div>
<div id="modal-auto-complete" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Buscar por nombre</h4>
            </div>
            <div class="modal-body">
                <input id="inputBuscarNombre" class="form-control" autofocus>
            </div>
        </div>
    </div>
</div>
<div id="dialog-buscar-producto" class="dialog-oculto" title="Buscar producto">
    <div id="divListaProducto" style="display:none">
    </div>
</div>
<button type="button" style="display:none" id="btnModalBuscarNombre" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modal-auto-complete">Open Modal</button>

</html>
<?php
}
?>
