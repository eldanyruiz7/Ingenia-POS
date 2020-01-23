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

    <title>Generar nota de crédito</title>
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
                    <h1 class="page-header"> <i class="fa fa-sticky-note-o" aria-hidden="true"></i> Generar nota de cr&eacute;dito</h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="form-group input-group input-group-lg">
                        <input id="inputBuscar" autofocus type="text" class="form-control" placeholder="No. de ticket o remisión">
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
    <script src="../control/custom-js/redondearDec.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
        var listaProductos          = [];
        var listaProductosOriginal  = [];
        var totalCredito            = 0;
        var granTotal               = 0;
        var antiguoTotal            = 0;
        var idCliente               = 0;
        var idVenta                 = 0;
        function itemNotaVenta(id, idProducto, precioU, cantidad, subTotal)
        {
            this.id = id;
            this.idProducto = idProducto;
            this.precioU = precioU;
            this.cantidad = cantidad;
            this.subTotal = subTotal;
        }
        function itemNotaVenta_O(id, idProducto, precioU, cantidad, subTotal)
        {
            this.id = id;
            this.idProducto = idProducto;
            this.precioU = precioU;
            this.cantidad = cantidad;
            this.subTotal = subTotal;
        }
        function armarNota()
        {
            listaProductos = [];
            listaProductosOriginal = [];
            $(".trItemLista").each(function(x)
            {
                id          = $(this).find(".tdId").attr("name");
                idProducto  = $(this).find(".tdProducto").attr("name");
                precioU     = $(this).find(".tdPrecio").attr("name");
                cantidad    = $(this).find(".tdCantidad").attr("name");
                subTotal    = $(this).find(".tdSubTotal").attr("name");
                prod        = new itemNotaVenta(id, idProducto, precioU, cantidad, subTotal);
                prod_O      = new itemNotaVenta_O(id, idProducto, precioU, cantidad, subTotal);
                listaProductos.push(prod);
                listaProductosOriginal.push(prod_O);
            });
            listarArray();
        }
        function generarNota()
        {
            console.log(listaProductosOriginal);
            listaProductosCredito = [];
            for (var x = 0; x < listaProductosOriginal.length; x++)
            {
                encontrado  = 0;
                idArt       = listaProductosOriginal[x]["id"];
                idProd_i    = listaProductosOriginal[x]["idProducto"];
                precioU     = listaProductosOriginal[x]["precioU"];
                cantArt     = parseFloat(listaProductosOriginal[x]["cantidad"]);
                subTot      = parseFloat(listaProductosOriginal[x]["subTotal"]);
                for (var y = 0; y < listaProductos.length; y++)
                {
                    if(idArt == listaProductos[y]["id"])
                    {
                        if (parseFloat(cantArt) > parseFloat(listaProductos[y]["cantidad"]) )//&& subTot > parseFloat(listaProductos[y]["subTotal"]))
                        {
                            idProducto      = listaProductos[y]["idProducto"];
                            precioU         = listaProductos[y]["precioU"];
                            cantGuardar     = parseFloat(cantArt) - parseFloat(listaProductos[y]["cantidad"]);
                            subTotGuardar   = parseFloat(subTot)  - parseFloat(listaProductos[y]["subTotal"]);
                            prod            = new itemNotaVenta(idArt, idProducto, precioU, cantGuardar.toFixed(3), subTotGuardar.toFixed(2));
                            listaProductosCredito.push(prod);
                        }
                        encontrado = 1;
                    }
                }
                if(encontrado == 0) // No se encontró en el nuevo array
                {
                    prod = new itemNotaVenta(idArt, idProd_i, precioU, cantArt.toFixed(3), subTot.toFixed(2));
                    listaProductosCredito.push(prod);
                }
            }
            if(listaProductosCredito.length > 0)
                return listaProductosCredito
            else
                return false;
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
            credito = parseFloat(nuevoTotal.toFixed(2)) - parseFloat(antiguoTotal.toFixed(2));
            credito = Math.abs(credito.toFixed(2));
            totalCredito = credito.toFixed(2);
            $("#spanCredito").text(credito.toFixed(2));
            $(".trItemLista").each(function(x)
            {
                $(this).attr("name", x);
                $(this).find(".btnEliminarItem").attr("name", x);
                s = parseFloat(listaProductos[x]["subTotal"]);
                $(this).find(".tdSubTotal").text("$"+s.toFixed(2));
            });
        }
        function listarArray()
        {
            console.log(listaProductos);
            console.log(listaProductosOriginal);
        }
        $(document).ready(function()
        {
            $("#btnBuscar").click(function()
            {
                id = $("#inputBuscar").val();
                $.ajax(
                {
                    method: "POST",
                    data: {id:id},
                    url: "../control/consultarNotaCredito.php"
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
                        armarNota();
                    }
                }).fail(function()
                {
                    alert("Servidor no disponible, consúltalo con el administrador del sistema");
                }).always(function(p)
                {
                    console.log(p);
                });
            });
            $("#inputBuscar").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnBuscar").click();
            });
            $("body").on("click","#btnGuardar",function()
            {
                $( "#dialog-confirm-guardar" ).dialog("open");
            });
            $("body").on("keydown",".inputCantidad",function(e)
            {
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    $(this).parent().parent().prev().find(".inputCantidad").focus();
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
            $("body").on("change",".inputCantidad",function()
            {
                cant = $(this).val();
                if(parseFloat(cant) == 0 || cant.length == 0 || isNaN(cant))
                    $(this).parent().parent().find(".btnEliminarItem").click();
                else
                {
                    index = $(this).parent().parent().attr("name");
                    tope = parseFloat($(this).parent().attr("name"));
                    cant = parseFloat(cant);
                    if(cant > tope)
                    {
                        //actualizarNota();
                        input = $(this);
                        $( "#dialog-confirm-max-cant" ).data('input',input).dialog("open");
                        return false;
                    }
                    $(this).val(cant.toFixed(3));
                    listaProductos[index]["cantidad"] = cant.toFixed(3);
                    $("#btnGuardar").prop("disabled",false);
                    actualizarNota();
                    console.log(listaProductos[index]["cantidad"]);
                    console.log(listaProductosOriginal[index]["cantidad"]);
                }
            });
            $("body").on("click", ".btnEliminarItem", function()
            {
                item = $(this).attr("name");
                valCant = $(this).parent().parent().find(".inputCantidad").val();
                input = $(this).parent().parent().find(".inputCantidad");
                $( "#dialog-confirm-eliminarItem" ).data('item',item).data('input',input).dialog("open");
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
                close: function()
                {

                },
                buttons:
                [
                    {
                        text: "Generar nota",
                        id: "btnGenNota",
                        click: function()
                        {
                            dialogo = $(this);
                            $("#btnGenNota").prop("disabled", true);
                            $("#btnGenNota").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Espera...');
                            $("#btnCancelarNota").prop("disabled", true);
                            resultNota = generarNota();
                            if(resultNota != false)
                            {
                                var listaProductosCreditoJSON = JSON.stringify(resultNota);
                                var listaProductosJSON = JSON.stringify(listaProductos);
                                tipoCredito = $("#selectTipoCredito").val();
                                observaciones = $("#inputObs").val();
                                //nuevoTotalVenta
                                $.ajax(
                                {
                                    method: "POST",
                                    url:"../control/agregarNotaCredito.php",
                                    data: {listaProductosCredito:listaProductosCreditoJSON,listaProductos:listaProductosJSON,totalCredito:totalCredito,tipoCredito:tipoCredito,idCliente:idCliente,idVenta:idVenta,granTotal:granTotal,observaciones:observaciones,antiguoTotal:antiguoTotal}
                                }).done(function(p)
                                {
                                    if(p.status == 1)
                                    {
                                        if (p.remision == 0)
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
                                                setTimeout(function()
                                                {
                                                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/notaCredito.php");
                                                }, 2250);
                                            });
                                        }
                                        else
                                        {
                                            url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionVentaPDF.php?idVenta="+p.idVenta;
                                            $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                            setTimeout(function()
                                            {
                                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/notaCredito.php");
                                            }, 620);
                                        }
                                    }
                                    else
                                    {
                                        $("#divRespuesta").html(e.respuesta);
                                        $("#btnGenNota").prop("disabled", false);
                                        $("#btnGenNota").html('Generar nota');
                                        $("#btnCancelarNota").prop("disabled", false);
                                        dialogo.dialog( "close" );
                                    }
                                }).fail(function()
                                {
                                    alert("Servidor no disponible, consúltalo con el administrador del sistema");
                                    dialogo.dialog( "close" );
                                    $("#btnGenNota").prop("disabled", false);
                                    $("#btnGenNota").html('Generar nota');
                                    $("#btnCancelarNota").prop("disabled", false);
                                }).always(function(p)
                                {
                                    console.log(p);
                                });
                            }
                            //$(this).dialog("close");
                        }
                    },
                    {
                        text: "Cancelar",
                        id: "btnCancelarNota",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-confirm-max-cant" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                autoOpen: false,
                close: function()
                {
                    input   = $( "#dialog-confirm-max-cant" ).data('input');
                    index = input.parent().parent().attr("name");
                    cacheVal = parseFloat(input.parent().attr("name"));
                    listaProductos[index]["cantidad"] = cacheVal;
                    input.val(cacheVal.toFixed(3));
                    actualizarNota();
                },
                buttons:
                [
                    {
                        text: "Aceptar",
                        click: function()
                        {
                            /*item = $( "#dialog-confirm-eliminarItem" ).data('item');
                            y = listaProductos.splice(item,1);
                            $(".trItemLista[name="+item+"]").remove();
                            listarArray();
                            actualizarNota();
                            //reordenarItems();*/
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
        });
    </script>
</body>
<div id="dialog-confirm-eliminarItem" title="Eliminar producto">
    <p>
        <h3>¿Eliminar producto?</h3>
    </p>
</div>
<div id="dialog-confirm-guardar" title="Generar nota">
    <p>
        <div class="form-group">
            <label>Tipo de cr&eacute;dito:</label>
            <select class="form-control" id="selectTipoCredito">
<?php
            $sql = "SELECT * FROM tiponotacredito ORDER BY id DESC";
            $result_tipo_n = $mysqli->query($sql);
            while($row_tipo_n = $result_tipo_n->fetch_assoc())
                echo "<option value='".$row_tipo_n['id']."'>".$row_tipo_n['nombre']."</option>";
?>
            </select>
        </div>
    </p>
    <p>
        <label>Observaciones:</label>
        <input class="form-control" autofocus id="inputObs">
    </p>
</div>
<div id="dialog-confirm-max-cant" title="Alerta" class="dialog-oculto">
    <p>
        <h3><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se puede ingresar una cantidad mayor a la de compra</h3>
    </p>
</div>
</html>
<?php
}
?>
