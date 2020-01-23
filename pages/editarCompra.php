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
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: /pventa_std/pages/salir.php");
    }
    else
    {
   // Aquí va el contenido de la pagina qu se mostrara en caso de que se haya iniciado sesion
   $pagina = 7;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Editar Compra</title>
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
                    <h1 class="page-header"> <span id="spanCargando"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar compra</span></h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group input-group input-group-lg">
                        <input id="inputBuscar" autofocus type="text" value="<?php echo (isset($_GET['idCompra'])) ? $_GET['idCompra'] : ''; ?>" class="form-control" placeholder="No. de compra">
                        <span class="input-group-btn">
                            <button id="btnBuscar" class="btn btn-success" type="button"><i class="fa fa-search"></i> Buscar
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
        idProveedor                 = 1;
        //var listaProductosOriginal  = [];
        var totalCredito            = 0;
        var granTotal               = 0;
        var antiguoTotal            = 0;
        var idCliente               = 0;
        var idVenta                 = 0;
        var dialogoAbierto          = 0;
        multiplicador               = 1; //Número que va antes del * para utilizar como multiplicador
        function itemCompra(idSubCompra, codigo, idProducto, subTotal, precioU, cantidad, descripcion, iva, ieps, unidadSat, claveSat)
        {
            this.idSubCompra        = idSubCompra;
            this.codigo             = codigo;
            this.idProducto         = idProducto;
            this.subTotal           = subTotal;
            this.precioU            = precioU;
            this.cantidad           = cantidad;
            this.descripcion        = descripcion;
            this.iva                = iva;
            this.ieps               = ieps;
            this.unidadSat          = unidadSat;
            this.claveSat           = claveSat;
        }
        // function itemCompra_O(id, idProducto, precioU, cantidad, subTotal)
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
            if ( ($(document).find("#btnGuardar").length < 1) == false || ($("#data").find("#idProveedor").length < 1) == false)
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
        function reordenarItems(index)
        {
            totalItems          = 0;
            atributoItemName    = 0;
            sumaSubtotal        = 0;
            $(".btnEliminarItem").each(function()
            {
                $(this).attr("name",atributoItemName);
                $(this).parent().parent().attr("name",atributoItemName);
                atributoItemName++;
            });
            $(".tdCodigo").each(function(x)
            {
                $(this).text(listaProductos[x]["codigo"]);
            });
            $(".tdNombre").each(function(x)
            {
                $(this).text(listaProductos[x]["nombre"]);
            });
            $(".inputPrecioU").each(function(x)
            {
                c = parseFloat(listaProductos[x]["precioU"]);
                $(this).val(c.toFixed(2));
            });
            $(".inputClaveSat").each(function(x)
            {
                c = listaProductos[x]["claveSat"];
                $(this).val(c);
            });
            $(".selectUSat").each(function(x)
            {
                c = listaProductos[x]["unidadSat"];
                $(this).val(c);
            });
            $(".inputIVA").each(function(x)
            {
                c = parseFloat(listaProductos[x]["iva"]);
                $(this).val(c.toFixed(2));
            });
            $(".inputIEPS").each(function(x)
            {
                c = parseFloat(listaProductos[x]["ieps"]);
                $(this).val(c.toFixed(2));
            });
            $(".inputCantidad").each(function(x)
            {
                c = parseFloat(listaProductos[x]["cantidad"]);
                $(this).val(c.toFixed(3));
            });
            $(".tdSubTotal").each(function(x)
            {
                c = parseFloat(listaProductos[x]["cantidad"]) * parseFloat(listaProductos[x]["precioU"]);
                //c = redondearDec(c);
                c = c.toFixed(2);
                listaProductos[x]["subTotal"] = c;
                $(this).text(c);
                sumaSubtotal += parseFloat(c);
            });
            nuevoTotal = 0;
            for (var i = 0; i < listaProductos.length; i++)
            {
                precioU             = listaProductos[i]["precioU"];
                cantidad            = listaProductos[i]["cantidad"];
                nuevoSubTot         = parseFloat(precioU * cantidad);
                //listaProductos[i]["subTotal"] = nuevoSubTot;
                nuevoTotal          = parseFloat(nuevoTotal) + parseFloat(nuevoSubTot);
                //console.log(listaProductos[i]["subTotal"]);
            }
            granTotal = nuevoTotal.toFixed(2);
            $("#spanNuevoTotal").text(nuevoTotal.toFixed(2));
            //$("#spanTotal").text(formatoMoneda(sumaSubtotal));
            $("#spanTotalArts").text(listaProductos.length);
            /*if(listaProductos.length > 0)
                $("#selectCliente").attr("disabled",true);
            else
                $("#selectCliente").attr("disabled",false);*/
            for(x = 0; x < listaProductos.length; x++)
            {
                console.log("Index: " + x + " " + listaProductos[x]["codigo"] + " " + listaProductos[x]["nombre"] + " " + listaProductos[x]["precioU"] + " " + listaProductos[x]["cantidad"]+" "+ listaProductos[x]["subTot"]+" Proveedor: ");
            }

            $("#listaProductos tr[name="+index+"]").addClass("success",50, function()
            {
                setTimeout(function()
                {
                    $( "#listaProductos tr" ).removeClass( "success",200 );
                }, 900 );
            });
            //$("#inputBuscarProducto").focus();
            $("#btnGuardar").prop("disabled",false);
        }
        function armarCotizacion()
        {
            listaProductos = [];
            //listaProductosOriginal = [];
            //$($(".trItemLista").get().reverse()).each(function() { /* ... */ });
            $(".trItemLista").each(function(x)
            {
                idSubCompra         = $(this).attr("idsubcompra");
                codigo              = $(this).find(".tdId").attr("name");
                idProducto          = $(this).find(".tdProducto").attr("name");
                subTotal            = $(this).find(".tdSubTotal").attr("name");
                precioU             = $(this).find(".inputPrecioU").val();
                cantidad            = $(this).find(".inputCantidad").val();
                descripcion         = $(this).find(".tdProducto").text();
                iva                 = $(this).find(".inputIVA").val();
                ieps                = $(this).find(".inputIEPS").val();
                unidadSat           = $(this).find(".selectUSat").val();
                claveSat            = $(this).find(".inputClaveSat").val();

                prod                = new itemCompra(idSubCompra, codigo, idProducto, subTotal, precioU, cantidad, descripcion, iva, ieps, unidadSat, claveSat);
                //prod_O      = new itemCompra_O(id, idProducto, precioU, cantidad, subTotal);
                listaProductos.push(prod);
                //listaProductosOriginal.push(prod_O);
            });
            actualizarNota();
            listarArray();
        }
        function cargarListaProductos()
        {
            if(dialogoAbierto == 1 || $("#data").find("#idProveedor").length < 1)
                return;
            dialogoAbierto = 1;
            $("#spanCargando").html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Cargando...');
            $.ajax(
            {
                method: "POST",
                url:"cargaListaProductos.php",
                data: {idCliente:1,tipo:3}
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
                precioU             = parseFloat(e.preciolista);
                precioU             = precioU.toFixed(2);
                row  ='  <tr class="trItemLista">';
                row +='     <td class="text-center">';
                row +=          '<button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem"><i class="fa fa-times"></i></button>';
                row +='     </td>';
                row +='     <td class="tdCodigo">';
                row +=          e.codigo;
                row +='     </td>';
                row +='     <td class="tdNombre">';
                row +=          e.nombre;
                row +='     </td>';

                row +='     <td class="text-right">';
                row +=          '<select style="text-align:right; border-width:0; max-width: 80px; background-color: white;" class="selectUSat">';
                row +=              e.htmlSelectU;
                row +=          '</select>';
                row +='     </td>';
                row +='     <td class="text-right">';
                row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 80px;" class="inputClaveSat" value="'+e.claveSAT+'">';
                row +='     </td>';
                row +='     <td class="text-right">';
                row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 75px;" class="inputCantidad" value="1">';
                row +='     </td>';
                row +='     <td class="text-right">';
                row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 80px;" class="inputIVA" value="'+e.IVA+'">';
                row +='     </td>';
                row +='     <td class="text-right">';
                row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 80px;" class="inputIEPS" value="'+e.IEPS+'">';
                row +='     </td>';
                row +='     <td class="text-right">';
                row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 75px;" class="inputPrecioU" value="'+precioU+'">';
                row +='     </td>';
                row +='     <td class="tdSubTotal text-right">';
                //row +=          redondearDec(subTotal);
                row +='     </td>';
                row +='  </tr>';
                $("#listaProductos").prepend(row);
                prod        = new itemCompra(-1,e.codigo, e.id, subTotal, precioU, cantidadEnPeso, e.descripcion, e.IVA, e.IEPS, e.unidadVenta, e.claveSAT);
                //itemCompra(idSubCompra, codigo, idProducto, subTotal, precioU, cantidad, descripcion, iva, ieps, unidadSat, claveSat);
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
            reordenarItems();
        }
        function actualizarNota()
        {
            credito                 = 0;
            nuevoTotal              = 0;
            for (var i = 0; i < listaProductos.length; i++)
            {
                precioU             = listaProductos[i]["precioU"];
                cantidad            = listaProductos[i]["cantidad"];
                nuevoSubTot         = parseFloat(precioU * cantidad);
                listaProductos[i]["subTotal"] = nuevoSubTot;
                nuevoTotal          = parseFloat(nuevoTotal) + parseFloat(nuevoSubTot);
                //console.log(listaProductos[i]["subTotal"]);
            }
            granTotal = nuevoTotal.toFixed(2);
            $("#spanNuevoTotal").text(nuevoTotal.toFixed(2));
            totalCredito = credito.toFixed(2);
            $(".trItemLista").each(function(x)
            {
                idSubCompra         = listaProductos[x]['idSubCompra'];
                codigo              = listaProductos[x]['codigo'];
                idProducto          = listaProductos[x]['idProducto'];
                precioU             = listaProductos[x]['precioU'];
                cantidad            = listaProductos[x]['cantidad'];
                subTotal            = listaProductos[x]['subTotal'];
                unidadSat           = listaProductos[x]['unidadSat'];
                claveSat            = listaProductos[x]['claveSat'];
                iva                 = listaProductos[x]['iva'];
                ieps                = listaProductos[x]['ieps'];
                //$(this).attr("name", idSubCompra);
                $(this).attr("name", x);
                $(this).attr("idSubCompra", idSubCompra);
                $(this).attr("codigo", codigo);
                $(this).attr("idProducto", idProducto);
                $(this).attr("precioU", precioU);
                $(this).attr("cantidad", cantidad);
                $(this).attr("subTotal", subTotal);
                $(this).attr("unidadSat", unidadSat);
                $(this).attr("claveSat", claveSat);
                $(this).attr("iva", iva);
                $(this).attr("ieps", ieps);
                $(this).find(".tdId").attr("name",codigo);
                $(this).find(".tdProducto").attr("name",idProducto);
                $(this).find(".tdPrecio").attr("name",precioU);
                $(this).find(".tdCantidad").attr("name",cantidad);
                $(this).find(".tdSubTotal").attr("name",subTotal);
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
                    url: "../control/consultarCompra.php"
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
                    if(dialogoAbierto == 1 || $("#data").find("#idProveedor").length < 1)
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
                idProveedor = $("#selectProveedor").val();
                $.ajax(
                {
                    method: "POST",
                    url:"../balanza/buscarProductoCompra.php",
                    data: {datos:datos,idCliente:idProveedor}
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
            /////////////////////////////////////////////////GRILLA//////////////////////////////////////////////////////////////
            $(document).on("keydown",".inputClaveSat",function(e)
            {
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputClaveSat").focus();
                }
                if(e.keyCode == 39)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputCantidad").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputClaveSat").focus();
                }
            });
            $(document).on("keydown",".inputCantidad",function(e)
            {
                if(e.keyCode == 37)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputClaveSat").focus();
                }
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputCantidad").focus();
                }
                if(e.keyCode == 39)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputIVA").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputCantidad").focus();
                }
            });
            $(document).on("keydown",".inputIVA",function(e)
            {
                if(e.keyCode == 37)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputCantidad").focus();
                }
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputIVA").focus();
                }
                if(e.keyCode == 39)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputIEPS").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputIVA").focus();
                }
            });
            $(document).on("keydown",".inputIEPS",function(e)
            {
                if(e.keyCode == 37)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputIVA").focus();
                }
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputIEPS").focus();
                }
                if(e.keyCode == 39)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputPrecioU").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputIEPS").focus();
                }
            });
            $(document).on("keydown",".inputPrecioU",function(e)
            {
                if(e.keyCode == 37)
                {
                    e.preventDefault();
                    $(this).parent().prev().find(".inputIEPS").focus();
                }
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputPrecioU").focus();
                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputPrecioU").focus();
                }

            });
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $("body").on("focusin",".inputClaveSat,.inputCantidad,.inputIVA,.inputIEPS,.inputPrecioU",function(e)
            {
                this.select();
            });
            $(document).on("change","#selectCliente", function()
            {
                $("#btnGuardar").attr("disabled",false);
            });
            // $("body").on("change",".inputCantidad",function()
            // {
            //     cant = $(this).val();
            //     if(parseFloat(cant) == 0 || cant.length == 0 || isNaN(cant))
            //         $(this).parent().parent().find(".btnEliminarItem").click();
            //     else
            //     {
            //         index = $(this).parent().parent().attr("name");
            //         cant = parseFloat(cant);
            //         $(this).val(cant.toFixed(3));
            //         listaProductos[index]["cantidad"] = cant.toFixed(3);
            //         $("#btnGuardar").prop("disabled",false);
            //         actualizarNota();
            //         console.log(listaProductos[index]["cantidad"]);
            //     }
            // });
            $("body").on("change",".selectUSat",function()
            {
                c = $(this).val();
                index = $(this).parent().parent().attr("name");
                listaProductos[index]["unidadSat"] = c;
                reordenarItems(index);
            });
            $("body").on("change",".inputClaveSat",function()
            {
                c = $(this).val();
                index = $(this).parent().parent().attr("name");
                listaProductos[index]["claveSat"] = c;
                reordenarItems(index);
            });
            $("body").on("change",".inputCantidad",function()
            {

                cant = parseFloat($(this).val());
                if(cant <= 0 || cant.length == 0 || isNaN(cant))
                    $(this).parent().parent().find(".btnEliminarItem").click();
                else
                {
                    index = $(this).parent().parent().attr("name");
                    listaProductos[index]["cantidad"] = cant;
                    reordenarItems(index);
                }
            });
            $("body").on("change",".inputIVA",function()
            {
                c = parseFloat($(this).val());
                index = $(this).parent().parent().attr("name");
                listaProductos[index]["iva"] = c;
                reordenarItems(index);
            });
            $("body").on("change",".inputIEPS",function()
            {
                c = parseFloat($(this).val());
                index = $(this).parent().parent().attr("name");
                listaProductos[index]["ieps"] = c;
                reordenarItems(index);
            });
            $("body").on("change",".inputPrecioU",function()
            {
                precio = parseFloat($(this).val());
                if(precio <= 0 || precio.length == 0 || isNaN(precio))
                    $(this).parent().parent().find(".btnEliminarItem").click();
                else
                {
                    index = $(this).parent().parent().attr("name");
                    listaProductos[index]["precioU"] = precio.toFixed(2);
                    reordenarItems(index);
                }
            });
            $("body").on("change","#selectTipoDoc,#selectCondiciones,#selectProveedor",function()
            {
                $("#btnGuardar").prop("disabled",false);
            });
            // $("body").on("change",".inputPrecioU",function()
            // {
            //     precio = $(this).val();
            //     if(parseFloat(precio) == 0 || precio.length == 0 || isNaN(precio))
            //         $(this).parent().parent().find(".btnEliminarItem").click();
            //     else
            //     {
            //         index = $(this).parent().parent().attr("name");
            //         precio = parseFloat(precio);
            //         $(this).val(precio.toFixed(2));
            //         listaProductos[index]["precioU"] = precio.toFixed(2);
            //         $("#btnGuardar").prop("disabled",false);
            //         actualizarNota();
            //         console.log(listaProductos[index]["precioU"]);
            //     }
            // });

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
                    $("#inputBuscarProducto").focus();
                },
                buttons:
                [
                    {
                        text: "Actualizar compra",
                        id: "btnActualCot",
                        click: function()
                        {
                            dialogo = $(this);
                            $("#btnActualCot").prop("disabled", true);
                            $("#btnActualCot").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Espera...');
                            $("#btnCancelarCot").prop("disabled", true);
                            listaProductos.reverse();
                            var listaProductosJSON  = JSON.stringify(listaProductos);
                            var idCompra            = $("#idCompra").val();
                            var idProveedor         = $("#selectProveedor").val();
                            var esFactura           = $("#selectTipoDoc").val();
                            var esCredito           = $("#selectCondiciones").val();
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/actualizarCompra.php",
                                data: {listaProductos:listaProductosJSON,idCompra:idCompra,idProveedor:idProveedor,esFactura:esFactura,esCredito:esCredito}
                            }).done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    $( "#btnGuardar" ).remove();
                                    $("#divRespuesta").html(p.respuesta);
                                    $("#data").empty();
                                    listaProductos.length = 0;
                                    $("#btnActualCot").prop("disabled", false);
                                    $("#btnActualCot").html('Actualizar compra');
                                    $("#btnCancelarCot").prop("disabled", false);
                                    dialogo.dialog( "close" );
                                }
                                else
                                {
                                    $("#divRespuesta").html(p.respuesta);
                                    $("#btnActualCot").prop("disabled", false);
                                    $("#btnActualCot").html('Actualizar compra');
                                    $("#btnCancelarCot").prop("disabled", false);
                                    dialogo.dialog( "close" );
                                }
                            }).fail(function()
                            {
                                alert("Servidor no disponible, consúltalo con el administrador del sistema");
                                dialogo.dialog( "close" );
                                $("#btnActualCot").prop("disabled", false);
                                $("#btnActualCot").html('Actualizar compra');
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
            <?php if (isset($_GET['idCompra']))
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
<div id="dialog-confirm-guardar" title="Actualizar compra">
    <h4>
        <i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas guardar los cambios?
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
