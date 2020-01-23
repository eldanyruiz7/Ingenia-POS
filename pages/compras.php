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
        $pagina = 6;
?>
<!DOCTYPE html>
<html lang="en">
<?php $ipserver = $_SERVER['SERVER_ADDR']; ?>
<?php require '../conecta/bd.php';?>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Compras</title>
    <!--<div class="col-lg-12">-->
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
        #page-wrapper,#page-venta1,#page-venta2
        {
            display: none;
            z-index: 1;
        }
        tr.success .inputCantidad .inputPrecioU
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
        .divider
        {
            height: 1px;
            width:100%;
            display:block; /* for use on default inline elements like span */
            margin: 9px 0;
            overflow: hidden;
            background-color: #e5e5e5;
        }

    </style>

</head>

<body>
    <div id="recibo" style="display:none">
    </div>

    <div id="wrapper">

<?php include "nav.php" ?>
        <div id="page-venta1" style="display:none">
        </div>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"><span id="spanCargando"><i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Compras</span></h1>
                </div>
                <div class="col-lg-2 col-md-2 col-xs-2">
                </div>
                <!-- /.col-lg-4 .col-md-4 .col-xs-6 -->
                <div class="col-lg-4 col-md-6 col-xs-6" style="padding-top:15px;padding-left:30px">
                    <?php
                    if (isset($_GET['success']))
                        if($_GET['success']==1)
                        {
                            echo '<div class="alert alert-success alert-dismissable" id="dismissAlert">';
                            echo '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                            echo '    <i class="fa fa-cart-plus fa-2x fa-pull-left" aria-hidden="true"></i> <b>Gracias por su preferencia</b>.';
                            echo '</div>';
                        }
                     ?>
                </div>
                <!-- /.col-lg-8 .col-md-8 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
                    <div id="divGroup" class="input-group custom-search-form input-group-lg">
                        <input type="text" class="form-control" placeholder="Clave o código de barras" id="inputBuscar">
                        <span class="input-group-btn">
                            <button class="btn btn-success" type="button" id="btnBuscar">
                                <i class="fa fa-check" aria-hidden="true"></i> Agregar
                            </button>
                        </span>
                    </div>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                    <div class="form-group input-group input-group-lg">
                        <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i></span>
                        <select id="selectCliente" class="form-control">
                            <option value="1">==Proveedor no especificado==</option>
                        <?php
                            $sql = "SELECT
                                        id AS id,
                                        nombres AS nombres,
                                        apellidop AS apellidop,
                                        apellidom AS apellidom
                                    FROM proveedores
                                    WHERE id > 1
                                    ORDER BY nombres ASC";
                            if ($resultProveedores = $mysqli->query($sql))
                            {
                                while($filaProveedor = $resultProveedores->fetch_assoc())
                                {
                                    $idProveedor = $filaProveedor['id'];
                                    $nombreProveedor = $filaProveedor['nombres']." ".$filaProveedor['apellidop']." ".$filaProveedor['apellidom'];
                                    echo "<option value='$idProveedor'>$nombreProveedor</option>";
                                }
                            }
                         ?>
                        </select>
                    </div>
                </div>
                <!--div class="col-lg-3 col-md-6">

                </div>-->
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-list-ol" aria-hidden="true"></i> Lista de productos:
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                <!-- /.col-lg-4 (nested) -->
                                <div class="col-md-3 col-md-push-9">
                                    <div class="panel panel-green">
                                        <div class="panel-heading">
                                            <h4>TOTAL <i class="fa fa-usd" aria-hidden="true"></i></h4>
                                        </div>
                                        <div class="panel-body" class="text-right">
                                            <h1>$<span id="spanTotal">0.00</span></h1>
                                        </div>
                                        <div class="panel-footer">
                                            <h4>TOTAL ART&Iacute;CULOS: <span id="spanTotalArts">0</span></h4>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col-lg-4 (nested) -->
                                <div class="col-md-9 col-md-pull-3">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>Eliminar</th>
                                                    <th>C&oacute;digo</th>
                                                    <th>Nombre</th>
                                                    <th>Unidad</th>
                                                    <th>Clave Prod.</th>
                                                    <th>Cantidad</th>
                                                    <th>IVA</th>
                                                    <th>IEPS</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Sub total</th>
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
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-8 -->
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
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    estadoPag           = 1; // 1 = Venta; 0 = Mostrar ticket anterior (mostrarVentaAnterior());
    basculaDisponible   = 1; //?
    itemsEnLista        = 0; //Cantidad de productos en la lista sin contar cantidades individuales.
    idProveedor         = 1;
    listaProductos      = [];
    dialogoAbierto      = 0;
    function producto(id, codigo, nombre, unidadVenta, htmlSelectU, claveSAT, precioLista, cantidad, iva, ieps, subTot) //Prototipo producto
    {
        this.id             = id;
        this.codigo         = codigo;
        this.nombre         = nombre;
        this.unidadVenta    = unidadVenta;
        this.htmlSelectU    = htmlSelectU;
        this.claveSAT       = claveSAT;
        this.precioLista    = precioLista;
        this.cantidad       = cantidad;
        this.iva            = iva;
        this.ieps           = ieps;
        this.subTot         = subTot;
    }
    function redondearDec(dec)
    {
        dec         = dec.toString();
        partes      = dec.split(".");
        parteEntera = partes[0];
        parteDecimal= partes[1];
        if(parteDecimal == undefined)
            parteDecimal = "00";
        if(parteDecimal.length == 1)
            parteDecimal = parteDecimal + "0";
        parteDecimal= parteDecimal.slice(0, 2);
        dec         = parteEntera + "." + parteDecimal;
        str         = dec.toString();
        str         = str.split(".");
        p  = str[1];
        segundoDec  = parseInt(parteDecimal.charAt(1));
        if(segundoDec  == 0)
            return parseFloat(dec).toFixed(2);
        else if (segundoDec > 0)
        {
            dec     = parseFloat(dec).toFixed(1);
            if(segundoDec < 5)
                dec = parseFloat(dec) + parseFloat("0.1");
            return parseFloat(dec).toFixed(2);
        }
    }
    function mostrarVentaAnterior(orden)
    {
        if (estadoPag == 1 && orden == 1)
            return false;
        if ( $("#hiddenIdMostrarVenta").length == 0)
            idVentaActual = -1;
        else
            idVentaActual = $("#hiddenIdMostrarVenta").val();
        $.ajax(
        {
            method: "POST",
            url:"../control/mostrarUltimaCompra.php",
            data: {idVentaActual:idVentaActual,orden:orden}
        })
        .done(function(p)
        {
            if (p.pageWrapper == 1)
            {
                $("#hiddenIdMostrarVenta").val(-1);
                $("#page-venta1").hide();
                $("#page-wrapper").show("drop",{ direction: "right" }, 200, function()
                {
                    estadoPag       = 1;
                    $("#inputBuscar").focus();
                });
            }
            else
            {
                estadoPag           = 0;
                $("#page-venta1").html(p);
                $("#page-wrapper").hide("drop",{ direction: "right" }, 200);
                $("#page-venta1").show("drop",{ direction: "left" }, 200);
            }
        })
        .always(function(p)
        {
            console.log(p);
        });
        //console.log(arrayListaProductos);

    }
    function agregarProducto(e,peso)
    {
        existeProducto  = 0; //Saber si ya existe el producto en la lista tabla
        cantidad        = ""; //Celda cantidad a modificar en caso de que exista el producto
        subtotal        = ""; //Celda subtotal a modificar en caso de que exista el producto
        //arrayPeso = peso.split(" ");
        //cantidadEnPeso = parseFloat($("#inputPeso").val()) * parseFloat($("#inputPrecioLista").val()) ;//parseFloat(arrayPeso[1]);
        cantidadEnPeso  = parseFloat($("#inputPeso").val());
        precioLista     = parseFloat($("#inputPrecioLista").val());
        //unidadVenta     = $("#")
        iva             = parseFloat($("#inputIVA").val());
        ieps            = parseFloat($("#inputIEPS").val());
        claveSAT        = $("#inputClaveProdSAT").val();
        elementoAnadirSuccess = 0;
        //Averiguar si existe el producto en la tabla
        for (var x = 0; x < listaProductos.length; x++)
        {
            if(e.codigo == listaProductos[x]['codigo'])
            {
                listaProductos[x]["cantidad"] = cantidadEnPeso = parseFloat(listaProductos[x]["cantidad"]) + cantidadEnPeso;
                listaProductos[x]["precioLista"] = precioLista;
                listaProductos[x]["IVA"] = iva;
                listaProductos[x]["IEPS"] = ieps;
                listaProductos[x]["claveSAT"] = claveSAT;
                existeProducto = 1;
                elementoAnadirSuccess = x;
                break;
            }
        }
        if(existeProducto != 1)
        {
            subTotal    = parseFloat($("#inputPeso").val()) * parseFloat($("#inputPrecioLista"));
            //subTotal = parseFloat(e.precio) * parseFloat(peso);
            subTotal    = subTotal.toFixed(2);
            precioLista = parseFloat($("#inputPrecioLista").val());
            precioLista = precioLista.toFixed(2);
            iva         = $("#inputIVA").val();
            iva         = (isNaN(iva)) ? 0 : iva;
            iva         = parseFloat(iva);
            iva         = iva.toFixed(2);
            ieps        = $("#inputIEPS").val();
            ieps        = (isNaN(ieps)) ? 0 : ieps;
            ieps        = parseFloat(ieps);
            ieps        = ieps.toFixed(2);
            unidadVenta = $("#selectUnidadSAT").val();
            itemsEnLista++;
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
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 80px;" class="inputClaveSat" value="'+claveSAT+'">';
            row +='     </td>';
            row +='     <td class="text-right">';
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 75px;" class="inputCantidad" value="'+cantidadEnPeso.toFixed(3)+'">';
            row +='     </td>';
            row +='     <td class="text-right">';
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 80px;" class="inputIVA" value="'+iva+'">';
            row +='     </td>';
            row +='     <td class="text-right">';
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 80px;" class="inputIEPS" value="'+ieps+'">';
            row +='     </td>';
            row +='     <td class="text-right">';
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0; max-width: 75px;" class="inputPrecioU" value="'+precioLista+'">';
            row +='     </td>';
            row +='     <td class="tdSubTotal text-right">';
            //row +=          redondearDec(subTotal);
            row +='     </td>';
            row +='  </tr>';
            $("#listaProductos").prepend(row);
            prod = new producto(e.id, e.codigo, e.nombre, unidadVenta, e.htmlSelectU, claveSAT, precioLista, cantidadEnPeso.toFixed(3), iva, ieps, subTotal);
            listaProductos.unshift(prod); //al final
        }
        $("#divGroup").removeClass("has-error");
        $("#inputBuscar").val('');
        reordenarItems(elementoAnadirSuccess);
        limpiarPeso();
        $("#inputBuscar").focus();

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
            c = parseFloat(listaProductos[x]["precioLista"]);
            $(this).val(c.toFixed(2));
        });
        $(".inputClaveSat").each(function(x)
        {
            c = listaProductos[x]["claveSAT"];
            $(this).val(c);
        });
        $(".selectUSat").each(function(x)
        {
            c = listaProductos[x]["unidadVenta"];
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
            c = parseFloat(listaProductos[x]["cantidad"]) * parseFloat(listaProductos[x]["precioLista"]);
            //c = redondearDec(c);
            c = c.toFixed(2);
            listaProductos[x]["subTot"] = c;
            $(this).text(c);
            sumaSubtotal += parseFloat(c);
        });

        $("#spanTotal").text(formatoMoneda(sumaSubtotal));
        $("#spanTotalArts").text(listaProductos.length);
        /*if(listaProductos.length > 0)
            $("#selectCliente").attr("disabled",true);
        else
            $("#selectCliente").attr("disabled",false);*/
        for(x = 0; x < listaProductos.length; x++)
        {
            console.log("Index: " + x + " " + listaProductos[x]["codigo"] + " " + listaProductos[x]["nombre"] + " " + listaProductos[x]["precioLista"] + " " + listaProductos[x]["cantidad"]+" "+ listaProductos[x]["subTot"]+" Proveedor: ");
        }

        $("#listaProductos tr[name="+index+"]").addClass("success",50, function()
        {
            setTimeout(function()
            {
                $( "#listaProductos tr" ).removeClass( "success",200 );
            }, 900 );
        });
        $("#inputBuscar").focus();
    }
    function formatoMoneda(amount)
    {
        decimals = 2;
        amount += ''; // por si pasan un numero en vez de un string
        amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto
        decimals = decimals || 0; // por si la variable no fue fue pasada
        // si no es un numero o es igual a cero retorno el mismo cero
        if (isNaN(amount) || amount === 0)
            return parseFloat(0).toFixed(decimals);
        // si es mayor o menor que cero retorno el valor formateado como numero
        amount = '' + amount.toFixed(decimals);
        var amount_parts = amount.split('.'),
            regexp = /(\d+)(\d{3})/;
        while (regexp.test(amount_parts[0]))
            amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2');
        return amount_parts.join('.');
    }
    function ajaxPeso(e)
    {
        $.ajax(
        {
            method: "POST",
            url:"../balanza/obtenpeso.php",
            data: {producto:e.codigo}
        })
        .done(function(p)
        {
            if(p=='')
            {
                //$("#inputPeso").attr("disabled",false);
                $("#inputPeso").prop("type","number");
                $("#inputPeso").attr("min",0);
                //$("#divErrorBascula").show();
                $("#inputPeso").focus();
                clearInterval(timerAjax);
                basculaDisponible = 0;
            }
            else
            {
                //$("#divErrorBascula").hide();
                //$("#inputPeso").attr("disabled",true);
                $("#inputPeso").prop("type","text");
                basculaDisponible = 1;
                $("#inputPeso").val(p);
            }
        });
    }
    function limpiarPeso()
    {
        if(typeof(timerAjax)!="undefined")
            clearInterval(timerAjax);
        $("#inputPeso").val('');
        $("#inputPrecioLista").val('');
    }
    function ocultarDismissAlert()
    {
        setTimeout(function()
        {
            $( "#dismissAlert" ).hide("fade",800);
        }, 4000 );
    }
    function preguntarAntesDeSalir ()
    {
        var respuesta;
        var lengthLista = listaProductos.length;
        if ( lengthLista > 0 ) {
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
    function cargarListaProductos()
    {
        $("#spanCargando").html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Cargando...');
        $.ajax(
        {
            method: "POST",
            url:"cargaListaProductos.php",
            data: {idCliente:1,tipo:2}
        })
        .done(function(p)
        {
            $("#divListaProducto").html(p);
                $( "#dialog-buscar-producto" ).dialog("open");

        })
        .always(function(p)
        {
            console.log(p);
        });
    }
        $(document).ready(function()
        {
            window.onbeforeunload = preguntarAntesDeSalir;
            <?php
            if (isset($_GET['success']))
                if($_GET['success']==1)
                {
                echo 'ocultarDismissAlert();'; //Ocultar al tiempo alerta venta exitosa
                }
             ?>
            $(document).keydown(function(e)
            {
                if(e.keyCode == 33)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    mostrarVentaAnterior(0); //1 RePag
                }
                if(e.keyCode == 34)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    mostrarVentaAnterior(1); //0 AvPag
                }
                if(e.keyCode == 112)
                {
                    e.preventDefault();
                    $("#btnf1").click();
                }
                if(e.keyCode == 113)
                {
                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    dialogoAbierto = 1;
                    $("#btnf2").click();
                }
                if(e.keyCode == 114)
                {
                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    dialogoAbierto = 1;
                    $("#btnf3").click();
                }
                if(e.keyCode == 123)
                {
                    e.preventDefault();
                    $("#btnf12").click();
                }
            });
            $("#inputBuscar").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnBuscar").click();
            });
            $("#btnf2").click(function()
            {
                cargarListaProductos();
            });
            $("#btnf3").click(function()
            {
                $("#btnModalBuscarNombre").click();
            });
            $("#btnf12").click(function()
            {
                if(listaProductos.length == 0 || dialogoAbierto == 1)
                    return;
                else
                    $( "#dialog-confirm-compra" ).dialog("open");
            });
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
                            $("#inputBuscar").val(response);
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
                        data:{query,query},
                        dataType:"json"
                    }).done(function(data)
                    {
                        result($.map(data, function(item)
                        {
                            return item;
                        }));
                    });
                },
                limit: 26,
                minLength: 1
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
                $("#inputBuscar").focus();
                if($("#inputBuscar").val().length > 0)
                    $("#btnBuscar").click();
                $("#inputBuscarNombre").val('');
                //$("#inputBuscar").trigger("enterPress");
            });
            $("body").on("click", ".btnEliminarItem", function()
            {
                item = $(this).attr("name");
                $( "#dialog-confirm-eliminarItem" ).data('item',item).dialog("open");
            });
            //Evento click capturar peso en caso de no encontrar báscula
            $("#inputPeso,#inputPrecioLista,#inputIVA,#inputIEPS,#inputClaveProdSAT").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnCapturarPeso").click();
            });
            $("#btnBuscar").click(function()
            {
                datos = $("#inputBuscar").val();
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
                            $("#inputBuscar").focus();
                            return false;
                        }
                        else
                        {
                            $("#dialog-confirm-peso").attr("title",d.nombre);
                            $("#ui-id-1").text(d.id+" - "+d.nombre);
                            $("#spanUnidadVentaNombre").text(d.unidadventanombre+"s");
                            $("#inputPrecioLista").val(d.preciolista);
                            $("#inputClaveProdSAT").val(d.claveSAT);
                            $("#inputIVA").val(d.IVA);
                            $("#inputIEPS").val(d.IEPS);
                            $("#selectUnidadSAT").val(d.unidadVenta);
                            $( "#dialog-confirm-peso" ).data('d',d).dialog("open");
                            //timerAjax = setInterval(ajaxPeso,600,d);
                            $("#divGroup").removeClass("has-error");
                            //console.log(d.conversionarray[0]);
                            //console.log(d.conversionarray[1]);
                            console.log(d.factorconversion);

                        }
                    })
                    .fail(function(d)
                    {
                        console.log(d);
                    });
                });
                $("body").on("change",".selectUSat",function()
                {
                    c = $(this).val();
                    index = $(this).parent().parent().attr("name");
                    listaProductos[index]["unidadVenta"] = c;
                    reordenarItems(index);
                });
                $("body").on("change",".inputClaveSat",function()
                {
                    c = $(this).val();
                    index = $(this).parent().parent().attr("name");
                    listaProductos[index]["claveSAT"] = c;
                    reordenarItems(index);
                });
                $("body").on("change",".inputCantidad",function()
                {
                    c = parseFloat($(this).val());
                    index = $(this).parent().parent().attr("name");
                    listaProductos[index]["cantidad"] = c;
                    reordenarItems(index);
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
                    c = parseFloat($(this).val());
                    index = $(this).parent().parent().attr("name");
                    listaProductos[index]["precioLista"] = c.toFixed(2);
                    reordenarItems(index);
                });


                $("body").on("change","#selectCliente",function()
                {
                    reordenarItems();
                });
                $("body").on("focus",".inputCantidad,.inputPrecioU,.inputIVA,.inputIEPS,.inputClaveSat",function()
                {
                    this.select();
                });
                $("body").on("click", ".trProductoElegible",function() //marcar producto en lista modal
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
                /*$(document).on("keyup","#inputPagaCon", function(e)
                {
                    if(e.keyCode == 13)
                    {
                        $("#btnTerminarCompra").click();
                    }
                    if($(this).val()=='')
                        {
                            $("#inputCambio").val("");
                            return;
                        }
                    //inputCambio inputMontoTotal inputPagaCon
                    total = parseFloat($("#hiddenMontoTotal").val());
                    paga = parseFloat($("#inputPagaCon").val());
                    //cambio = redondearDec(paga - total);
                    cambio = paga - total;
                    $("#inputCambio").val(cambio);
                });*/
                $("#selectCliente").change(function()
                {
                    idProveedor = $(this).val();
                });
                $( "#dialog-confirm-peso" ).dialog(
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
                    open: function()
                    {
                        dialogoAbierto = 1;
                    },
                    close: function()
                    {
                        dialogoAbierto = 0;
                        limpiarPeso();
                    },
                    buttons:
                    [
                        {
                            text: "Aceptar",
                            "id": 'btnCapturarPeso',
                            click: function()
                            {
                                if($("#inputPeso").val()=='' || $("#inputPeso").val()==' 0.000 kg' || $("#inputPeso").val()=='  NEG.    ' || $("#inputPrecioLista").val()=='' || parseFloat($("#inputPrecioLista").val())== 0 || parseFloat($("#inputPeso").val())== 0)
                                {
                                    $("#inputPeso").focus();
                                    return false;
                                }
                                e = $( "#dialog-confirm-peso" ).data('d');
                                $("#inputPeso").prop("type","text");
                                cad = " ";
                                cad += $("#inputPeso").val();
                                $("#inputPeso").val(cad);
                                agregarProducto(e,$("#inputPeso").val());
                                //$("#inputPeso").attr("disabled",true);
                                $(this).dialog("close");
                            }
                        },
                        {
                            text: "Cancelar",
                            click: function() {
                                limpiarPeso();
                                $( this ).dialog( "close" );
                                //$("#inputPeso").attr("disabled",true);
                            }
                        }
                    ]
                });
                $( "#dialog-confirm-compra" ).dialog(
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
                    open: function()
                    {
                        dialogoAbierto = 1;
                        reordenarItems();
                        tot = 0;
                        for(x=0; x < listaProductos.length; x++)
                        {
                            tot = parseFloat(tot) + parseFloat(listaProductos[x]["precioLista"] * listaProductos[x]["cantidad"]);
                        }
                        $("#inputMontoTotal").val(formatoMoneda(tot));
                        $("#hiddenMontoTotal").val(tot);
                        $("#inputMontoTotalAbono").val('');
                        $("#inputCambio").val("");
                        $("#inputPagaCon").val("");
                        //$("#btnTerminarCompra").focus();
                    },
                    close: function()
                    {
                        dialogoAbierto = 0;
                        $("#divRespuestaCompra").empty();
                    },
                    buttons:
                    [
                        {
                            text: "Aceptar",
                            "id": "btnTerminarCompra",
                            click: function()
                            {
                                $("#divRespuestaCompra").empty();
                                $(".form-group").removeClass("has-error");
                                noDocto             = $("#inputNoDocto").val();
                                noDocto             = noDocto.replace(/\s/g,"");
                                btnTerminarCompra   = $("#btnTerminarCompra");
                                btnCancelarCompra   = $("#btnCancelarCompra");
                                abono               = $("#inputMontoTotalAbono").val();
                                inputMontoTotal     = $("#hiddenMontoTotal").val();
                                fechaExpira         = $("#fechaExpira").val();
                                idProv              = $("#selectCliente").val();
                                tipoPago            = $("input[name=tipoPago]:checked").val(); // Crédito o contado
                                fact                = $("input[name=fact]:checked").val(); // Remisión o factura
                                hoy                 = new Date();
                                fechaExpira_d       = new Date(fechaExpira);
                                hoy.setHours(0,0,0,0);
                                dialogo             = $(this);
                                /*console.log("-"+noDocto+"-");
                                console.log(fechaExpira);*/
                                if (noDocto.length  == 0)
                                {
                                    res             =  '<div class="alert alert-danger alert-dismissable">';
                                    res             += '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                                    res             += '    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Completa el campo No. de Documento';
                                    res             += '</div>';
                                    $("#divRespuestaCompra").html(res);
                                    $("#inputNoDocto").parent().addClass("has-error");
                                    $("#inputNoDocto").focus();
                                    return false;
                                }
                                if(tipoPago != 1)
                                {
                                    if (fechaExpira_d <= hoy)
                                    {
                                        res         =  '<div class="alert alert-danger alert-dismissable">';
                                        res         += '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                                        res         += '    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> La fecha de expiraci&oacute;n no puede igual o anterior a la fecha actual';
                                        res         += '</div>';
                                        $("#divRespuestaCompra").html(res);
                                        $("#fechaExpira").parent().addClass("has-error");
                                        $("#fechaExpira").focus();
                                        return false;
                                    }
                                    if(isNaN(abono) || parseFloat(abono) >= parseFloat(inputMontoTotal))
                                    {
                                        res         =  '<div class="alert alert-danger alert-dismissable">';
                                        res         += '    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                                        res         += '    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> El campo de abono no puede ser mayor o igual que el monto total de la compra. b) Contener letras o signos que no sean n&uacute;meros';
                                        res         += '</div>';
                                        $("#divRespuestaCompra").html(res);
                                        $("#inputMontoTotalAbono").parent().addClass("has-error");
                                        $("#inputMontoTotalAbono").focus();
                                        return false;
                                    }

                                }
                                btnTerminarCompra.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                                btnTerminarCompra.prop("disabled", true);
                                btnCancelarCompra.prop("disabled", true);
                                //$("#inputPagaCon").prop("disabled", true);
                                var listaProductosJSON = JSON.stringify(listaProductos);
                                $.ajax(
                                {
                                    method: "POST",
                                    url:"../control/guardarCompra.php",
                                    data: {listaProductos:listaProductosJSON,idProveedor:idProv,montoTotal:inputMontoTotal,fact:fact,noDocto:noDocto,tipoPago:tipoPago,fechaExpira:fechaExpira,abono:abono}
                                })
                                .done(function(p)
                                {
                                    if(p.status == 1)
                                    {
                                        listaProductos.length = 0;
                                        url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionCompraPDF.php?idCompra="+p.idCompra;
                                        $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                        setTimeout(function()
                                        {
                                            window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/compras.php");
                                        }, 620);
                                        // $('#recibo').html(p.recibo).promise().done(function()
                                        // {
                                        //     //your callback logic / code here
                                        //     JsBarcode("#code_svg",p.codigo,
                                        //     {
                                        //         width:2,
                                        //         height:35,
                                        //         fontSize:13,
                                        //         margin:1
                                        //     });
                                        //     $('#recibo').printThis();
                                        //     setTimeout(function()
                                        //     {
                                        //         //dialogo.dialog( "close" );
                                        //         window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/compras.php");
                                        //     }, 2250);
                                        // });
                                        //});
                                    }
                                    else
                                    {
                                        alert("No se puede guardar");
                                        dialogo.dialog( "close" );
                                    }
                                    //window.location.assign("http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages?success=1");
                                })
                                .fail(function(p)
                                {
                                    alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                                    dialogo.dialog( "close" );
                                    btnTerminarCompra.html('Aceptar');
                                    btnTerminarCompra.prop("disabled", false);
                                    btnCancelarCompra.prop("disabled", false);
                                    $("#inputPagaCon").prop("disabled", false);
                                    console.log(p);
                                })
                                .always(function(p)
                                {
                                    console.log(p);
                                    console.log("p.sql="+p.sql);
                                })

                                //$( this ).dialog( "close" );
                            }
                        },
                        {
                            text: "Cancelar",
                            "id": "btnCancelarCompra",
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
                $( "#dialog-buscar-producto" ).dialog(
                {
                    resizable: true,
                    height: "auto",
                    width: parseInt($(document).width()),
                    modal: true,
                    autoOpen: false,
                    position: ["bottom",200],
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
                        setTimeout(function()
                        {
                            $("#dataTable_filter input.input-sm").focus();
                        },100);
                    },
                    close: function()
                    {
                        dialogoAbierto = 0;
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
                                $("#inputBuscar").val(cad);
                                $( this ).dialog( "close" );
                                $("#btnBuscar").click();
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
                $(".tipoPago").change(function()
                {
                    if($("input[name=tipoPago]:checked").val() == 1)
                    {
                        $("#fechaExpira").attr("disabled", true);
                        $("#inputMontoTotalAbono").val(0);
                        $("#inputMontoTotalAbono").attr("disabled", true);
                        $("#inputMontoTotalAbono").parent().removeClass("has-error");
                        $("#fechaExpira").parent().removeClass("has-error");
                        $("#inputNoDocto").focus();
                    }
                    else
                    {
                        $("#fechaExpira").attr("disabled", false);
                        $("#inputMontoTotalAbono").attr("disabled", false);
                        $("#inputMontoTotalAbono").focus();
                    }
                });
                $(".fact").change(function()
                {
                    $("#inputNoDocto").focus();
                });
                $("#page-wrapper").show("drop",{ direction: "left" }, 200, function()
                {
                    $("#inputBuscar").focus();
                });
        });
    </script>
    <!--Modal balanza-->
    <div id="dialog-confirm-peso" class="dialog-oculto" title="Capturar peso">

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Cantidad</label>
            <div class="form-group input-group">
                <input type="number" class="form-control" id="inputPeso" style="text-align:right;">
                <span class="input-group-addon" id="spanUnidadVentaNombre"></span>
            </div>

        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>&Uacute;ltimo precio de compra</label>
            <div class="form-group input-group">
                <span class="input-group-addon">$</span>
                <input type="number" class="form-control" id="inputPrecioLista" style="text-align:right;">
            </div>
        </div>
        <li class="divider"></li>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>IVA</label>
            <div class="form-group input-group">
                <span class="input-group-addon">%</span>
                <input type="number" class="form-control" id="inputIVA" style="text-align:right;">
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>IEPS</label>
            <div class="form-group input-group">
                <span class="input-group-addon">%</span>
                <input type="number" class="form-control" id="inputIEPS" style="text-align:right;">
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Clave Unidad SAT</label>
            <div class="form-group input-group">
                <span class="input-group-addon"><i class="fa fa-rocket" aria-hidden="true"></i></span>
                <select class="form-control" id="selectUnidadSAT">
<?php
                    $sql = "SELECT * FROM unidadesventa";
                    $res_u_venta = $mysqli->query($sql);
                    while ($row_u_venta = $res_u_venta->fetch_assoc())
                    {
                        $idUnidad       = $row_u_venta['id'];
                        $nombreUnidad   = $row_u_venta['nombre'];
                        $claveUSat      = $row_u_venta['c_ClaveUnidad'];
                        echo "<option value='$idUnidad'>$claveUSat</option>";
                    }
?>
                </select>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Clave Producto o Serv. SAT</label>
            <div class="form-group input-group">
                <span class="input-group-addon"><i class="fa fa-rocket" aria-hidden="true"></i></span>
                <input type="text" class="form-control" id="inputClaveProdSAT" style="text-align:right;">
            </div>
        </div>


    </div>
    <div id="dialog-buscar-producto" class="dialog-oculto" title="Buscar producto">
        <div id="divListaProducto" style="display:none">
        </div>
    </div>
    <div id="dialog-confirm-compra" class="dialog-oculto" title="¿Concluir compra?">
        <div class="col-lg-12" id="divRespuestaCompra" style="padding-left:0px;padding-right:0px">
        </div>
        <label> Total de la compra</label>
        <!--<h1><input type="number" class="form-control"  i></h1>-->
        <div class="form-group input-group">
            <span class="input-group-addon">$</span>
            <input type="text" class="form-control" id="inputMontoTotal" disabled="disabled" style="text-align:right;">
        </div>
        <input type="hidden" id="hiddenMontoTotal">
        <div class="radio">
            <label>
                <input type="radio" class="fact" name="fact" id="fact2" value="0" checked> Remisi&oacute;n
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" class="fact" name="fact" id="fact1" value="1"> Factura
            </label>
        </div>
        <label>No. de Documento</label>
        <div class="form-group input-group" id="divNoDocto">
            <span class="input-group-addon"><i class="fa fa-slack" aria-hidden="true"></i></span>
            <input autofocus type="text" class="form-control" id="inputNoDocto">
        </div>
        <div class="radio">
            <label>
                <input type="radio" class="tipoPago" name="tipoPago" id="tipoPago1" value="1" checked> Pago de contado
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" class="tipoPago" name="tipoPago" id="tipoPago2" value="0"> Cr&eacute;dito
            </label>
        </div>
        <label>Fecha expiraci&oacute;n</label>
        <div class="form-group input-group" id="divFechaExpira">
            <span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>
            <input type="date" class="form-control" disabled style="z-index:1000" value='<?php echo date('Y-m-d');?>' min='<?php echo date('Y-m-d');?>' id="fechaExpira">
        </div>
        <label>Abono</label>
        <div class="form-group input-group">
            <span class="input-group-addon">$</span>
            <input type="number" class="form-control" disabled value="0" min="0" placeholder="0" id="inputMontoTotalAbono">
        </div>

    </div>
    <div id="dialog-confirm-eliminarItem" class="dialog-oculto" title="Eliminar producto">
        <p>
            <h3>¿Eliminar producto?</h3>
        </p>
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
                    <input id="inputBuscarNombre" class="typeahead form-control">
                </div>
            </div>
        </div>
    </div>
    <!-- Trigger the modal with a button -->
    <button type="button" style="display:none" id="btnModalBuscarNombre" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modal-auto-complete">Open Modal</button>

</body>

</html>
<?php
}
?>
