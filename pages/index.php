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
        $sql = "SELECT saldoinicial FROM sesionescontrol WHERE id = $idSesion LIMIT 1";
        $res_saldo = $mysqli->query($sql);
        $row_saldo = $res_saldo->fetch_assoc();
        $saldoinicial = $row_saldo['saldoinicial'];
        if($saldoinicial == null)
            header("Location: /pventa_std/pages/sld_ini.php");
   $pagina = 1;
?>
<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Punto de venta</title>
    <!-- Bootstrap Core CSS-->
    <link href="../startbootstrap/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS-->
    <link href="../startbootstrap/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <link href="/htdocs/departamentos/startbootstrap/vendor/bootstrap-social/bootstrap-social.css" rel="stylesheet">
    <!-- Custom CSS-->
    <link href="../startbootstrap/dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Morris Charts CSS-->
    <!-- <link href="../startbootstrap/vendor/morrisjs/morris.css" rel="stylesheet"> -->
    <!-- Custom Fonts-->
    <link href="../startbootstrap/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"> -->
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
            background-color: transparent; /* #d0e9c6;
            opacity: 0;/**/
        }
        #dataTable_filter
        {
            text-align: right;
        }
        .dialog-oculto
        {
            display: none;
        }
        .btn-outline
        {
            padding-left: 0px;
        }
        .inputCantidad .inputPrecioU
        {
            text-align: right;
            -moz-appearance:textfield;
        }

        .inputCantidad::-webkit-inner-spin-button .inputPrecioU::-webkit-inner-spin-button
        {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

</head>

<body>
    <div id="recibo" style="display:none">
    </div>
    <?php //echo $sesion->get("nombreComercio"); ?>
    <div id="wrapper">

<?php include "nav.php" ?>
        <div id="page-venta1" style="display:none">
        </div>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-1 col-md-2 visible-lg visible-md" style="height:155px;padding-left:0px">
                    <img class="rounded mx-auto d-block" src="../images/logo2.jpg" style="width:140%">
                </div>
                <div class="col-lg-9 col-md-8 col-sm-12 col-xs-12" style="padding-right:0px">
                    <div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
                        <h1 class="page-header" style="margin-top:10px"><span id="spanCargando"><i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Punto de venta</span></h1>
                    </div>
                    <div class="col-lg-8 col-md-8 col-xs-8 col-sm-8" style="padding-left:0px">
                        <div id="divGroup" class="input-group custom-search-form input-group-lg">
                            <input type="text" class="form-control" placeholder="Clave o código de barras" id="inputBuscar" autocomplete="off">
                            <span class="input-group-btn">
                                <button class="btn btn-info" type="button" id="btnBuscar">
                                    <i class="fa fa-check" aria-hidden="true"></i> Agregar
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <div class="form-group input-group input-group-lg">
                            <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i></span>
                            <select id="selectCliente" class="form-control">
                                <option value="1">==Venta al p&uacute;blico en gral==</option>
                            <?php
                                $sql = "SELECT
                                            id AS id,
                                            rsocial AS rsocial,
                                            tipoprecio AS tipoprecio
                                        FROM clientes
                                        WHERE id > 1
                                        ORDER BY rsocial ASC";
                                if ($resultClientes = $mysqli->query($sql))
                                {
                                    while($filaCliente = $resultClientes->fetch_assoc())
                                    {
                                        $idCliente = $filaCliente['id'];
                                        $nombreCliente = $filaCliente['rsocial'];
                                        echo "<option value='$idCliente'>$nombreCliente</option>";
                                    }
                                }
                             ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 visible-lg visible-md" style="height:155px;padding-left:0px">
                    <img id="vistaPrevia" class="img-thumbnail" style="width:100%;height:100%">
                </div>
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
                                    <div class="panel panel-primary">
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
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Eliminar</th>
                                                    <th>C&oacute;digo</th>
                                                    <th>Nombre</th>
                                                    <th>Cantidad</th>
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
    <script src="../startbootstrap/vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../startbootstrap/vendor/Cross-Origin-Ajax/lib/chromeSuperAjax.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="../startbootstrap/dist/js/sb-admin-2.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <!-- Metis Menu Plugin JavaScript -->
    <!-- Morris Charts JavaScript -->
    <!-- <script src="../startbootstrap/vendor/raphael/raphael.min.js"></script> -->
    <script src="../startbootstrap/vendor/morrisjs/morris.min.js"></script>
    <script src="../startbootstrap/data/morris-data.js"></script>
    <!-- DataTables Javascript -->
    <script src="../startbootstrap/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../startbootstrap/vendor/datatables-responsive/dataTables.responsive.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../startbootstrap/vendor/printThis/printThis.js"></script>
    <script src="../startbootstrap/vendor/typeahead/typeahead.min.js"></script>
    <script src="../control/custom-js/redondearDec.js"></script>
    <script src="../startbootstrap/vendor/jsBarcode/JsBarcode.all.min.js"></script>
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    estadoPag           = 1; // 1 = Venta; 0 = Mostrar ticket anterior (mostrarVentaAnterior());
    transicion          = 0; // Efecto de transicion en proceso
    basculaDisponible   = 1;
    itemsEnLista        = 0; //Cantidad de productos en la lista sin contar cantidades individuales.
    idCliente           = 1;
    arrayListaProductos = [];
    objetoCliente       = []; // Para agregar un nuevo cliente al vuelo
    dialogoAbierto      = 0;
    multiplicador       = 1; //Número que va antes del * para utilizar como multiplicador
    function producto(id, codigo, nombre, nombreCache, precioU, cantidad, subTot, precioXunidad, precioXpaquete, factorconversion) //Prototipo producto
    {
        this.id                 = id;
        this.codigo             = codigo;
        this.nombre             = nombre;
        this.nombreCache        = nombreCache;
        this.precioU            = precioU;
        this.cantidad           = cantidad;
        this.subTot             = subTot;
        this.precioXunidad      = precioXunidad;
        this.precioXpaquete     = precioXpaquete;
        this.factorconversion   = factorconversion;
    }
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
        $(".divControlable").removeClass('has-error');
        objetoCliente.length = 0;
    }
    function buscarUtilidadesMenos()
    {
        $.ajax(
        {
            url:"../procesos/chkProdUtil.php"
        }).done(function(e)
        {
            if(e.status == 1)
            {
                $("#btnMensajes").removeClass("btn-link");
                $("#btnMensajes").addClass("btn-danger");
                $("#btnMensajes").html('<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>')
                $("#msgDesplegable").html(e.msg);
                console.log(e.idProducto);
                console.log(e.status);
                console.log(e.x);
            }
        });
    }
    function preguntarAntesDeSalir ()
    {
        var respuesta;
        var lengthLista = arrayListaProductos.length;
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
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
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
            url:"../control/mostrarUltimaVenta.php",
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
            //console.log(p);
        });
        console.log(arrayListaProductos);

    }
<?php } ?>
    function agregarProducto(e,peso)
    {
        existeProducto = 0; //Saber si ya existe el producto en la lista tabla
        cantidad = ""; //Celda cantidad a modificar en caso de que exista el producto
        subtotal = ""; //Celda subtotal a modificar en caso de que exista el producto
        cantidadEnPeso = parseFloat(peso);
        elementoAnadirSuccess = 0;
        abort   = false;
        cont_e_codigo = 0;
        //Averiguar si existe el producto en la tabla
        if (arrayListaProductos.length > 0)
            for (var x = 0; x < arrayListaProductos.length && !abort; x++)
            {
                if(e.codigo == arrayListaProductos[x]['codigo'])
                {
                    if ((arrayListaProductos[x]["cantidad"] % e.factorconversion) != 0 || e.factorconversion == 1)
                    {
                        arrayListaProductos[x]["cantidad"]  = cantidadEnPeso = parseFloat(arrayListaProductos[x]["cantidad"]) + cantidadEnPeso;
                        arrayListaProductos[x]["subTot"]    = parseFloat(arrayListaProductos[x]["cantidad"]) * parseFloat(arrayListaProductos[x]["precioU"]);
                        arrayListaProductos[x]["subTot"]    = redondearDec(arrayListaProductos[x]["subTot"]);
                        existeProducto                      = 1;
                        elementoAnadirSuccess               = x;
                        abort = true;
                    }
                    else
                    {
                        for (var z = 0; z < arrayListaProductos.length && !abort; z++)
                        {
                            if(e.codigo == arrayListaProductos[z]['codigo'] && (arrayListaProductos[z]["cantidad"] % e.factorconversion) == 0)
                            {
                                console.log("%:"+0);
                                // Comprobar concurrencias para e.codigo dentro del array
                                for (var i = 0; i < arrayListaProductos.length; i++)
                                    if (e.codigo == arrayListaProductos[i]['codigo'])
                                        cont_e_codigo++;
                                if (cont_e_codigo == 2)
                                {
                                    cant_antes = parseFloat(arrayListaProductos[z]["cantidad"]);
                                    w = arrayListaProductos.splice(z,1);
                                    $(".trItemLista[name="+z+"]").remove();
                                    arrayListaProductos[x]['cantidad'] = parseFloat(arrayListaProductos[x]["cantidad"]) + cant_antes;
                                    arrayListaProductos[x]['subTot'] = parseFloat(arrayListaProductos[x]["cantidad"]) * parseFloat(arrayListaProductos[x]["precioU"]);
                                    elementoAnadirSuccess            = z;
                                    existeProducto = 1;
                                }
                                if (cont_e_codigo == 1)
                                {
                                    existeProducto = 0;
                                }
                                abort = true;
                            }
                            else if (e.codigo == arrayListaProductos[z]['codigo'] && (arrayListaProductos[z]["cantidad"] % e.factorconversion) != 0)
                            {
                                //subTotal = parseFloat(e.precio) * parseFloat(peso);
                                arrayListaProductos[z]['cantidad'] = parseFloat(arrayListaProductos[z]["cantidad"]) + cantidadEnPeso;
                                // arrayListaProductos[z]["precioU"] = parseFloat(e.precioXpaquete) / parseFloat(e.factorconversion);
                                arrayListaProductos[z]['subTot'] = parseFloat(arrayListaProductos[z]["cantidad"]) * parseFloat(arrayListaProductos[z]["precioU"]);
                                console.log("%:dif");
                                existeProducto = 0;
                                elementoAnadirSuccess                   = z;
                                abort = true;
                            }
                        }
                    }
                }
            }
        ///////////////////////////////////Corregir duplicidad de artículos después de añadir///////////////////////////////////////////////////////////
        //indexBorrar = -1;
        //indexAumentar = -1;
        cont_e_codigo = 0;
        for (var i = 0; i < arrayListaProductos.length; i++)
            if (e.codigo == arrayListaProductos[i]['codigo'])
            {
                cont_e_codigo++;
                console.log("entra for[i], cont_e_codigo="+cont_e_codigo);
                if (cont_e_codigo == 1)
                    indexBorrar = i;
                if (cont_e_codigo == 2)
                    indexAumentar = i;
            }
        if (cont_e_codigo == 1)
        {
            precioXpaq_promo    = parseFloat(e.precioXpaquete) / parseFloat(e.factorconversion);
            arrayListaProductos[indexBorrar]["precioU"] = precioXpaq_promo.toFixed(2);
        }
        if (cont_e_codigo == 2)
        {
            if ((arrayListaProductos[indexBorrar]['cantidad'] % e.factorconversion) == 0)
            {
                cant_antes = parseFloat(arrayListaProductos[indexBorrar]["cantidad"]);
                precioXpaq_promo    = parseFloat(e.precioXpaquete) / parseFloat(e.factorconversion);
                arrayListaProductos[indexAumentar]["precioU"] = precioXpaq_promo.toFixed(2);
                console.log("Remover item:"+ ".trItemLista[name="+z+"]"+"Precio promo:"+arrayListaProductos[indexAumentar]["precioU"]);
                arrayListaProductos[indexAumentar]['cantidad'] = parseFloat(arrayListaProductos[indexAumentar]["cantidad"]) + cant_antes;
                //arrayListaProductos[indexAumentar]['subTot'] = parseFloat(arrayListaProductos[indexAumentar]["cantidad"]) * parseFloat(arrayListaProductos[indexAumentar]["precioU"]);
                w = arrayListaProductos.splice(indexBorrar,1);
                $(".trItemLista[name="+indexBorrar+"]").remove();
                elementoAnadirSuccess                   = indexAumentar - 1;
            }
            //abort = true;
        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if(existeProducto != 1)
        {
            subTotal = parseFloat(e.precio) * parseFloat(peso);
            subTotal = subTotal.toFixed(2);
            precioU = parseFloat(e.precio);
            precioU = precioU.toFixed(2);
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
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0;" class="inputCantidad" value="'+cantidadEnPeso.toFixed(3)+'">';
            row +='     </td>';
            row +='     <td class="text-right tdPrecioU">';
            row +=          '<input type="number" min=0 style="text-align:right; border-width:0;" class="inputPrecioU" value="'+precioU+'">';
            row +='     </td>';
            stot = parseFloat(cantidadEnPeso.toFixed(3)) * parseFloat(precioU);
            stot_ = redondearDec(stot);
            row +='     <td class="tdSubTotal text-right">';
            row +=          stot_;
            row +='     </td>';
            row +='  </tr>';
            $("#listaProductos").prepend(row);
            //sumaSubtotal += parseFloat(c);
            prod = new producto(e.id, e.codigo, e.nombre, e.nombreCache, precioU, cantidadEnPeso.toFixed(3), stot_, e.precioXunidad, e.precioXpaquete, e.factorconversion);
            arrayListaProductos.unshift(prod); //al final
        }
        $("#divGroup").removeClass("has-error");
        $("#inputBuscar").val('');
        $("#vistaPrevia").attr("src",e.imagen);
        $("#imgSrc").attr("src",e.imagen);
        multiplicador = 1;
        reordenarItems(elementoAnadirSuccess);
        limpiarPeso();
    }
    function cargarListaProductos()
    {
        $("#spanCargando").html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Cargando...');
        $.ajax(
        {
            method: "POST",
            url:"cargaListaProductos.php",
            data: {idCliente:idCliente,tipo:1}
        })
        .done(function(p)
        {
            $.when($("#divListaProducto").html(p)).done(function(x)
            {
                $( "#dialog-buscar-producto" ).dialog("open");
            });
        });
    }
    function trError(index)
    {
        $("#listaProductos tr[name="+index+"]").addClass("danger",50, function()
        {
            setTimeout(function()
            {
                $( "#listaProductos tr" ).removeClass( "danger",200 );
            }, 900 );
        });
    }
    function reordenarItems(index)
    {
        ///////////////////////////////////////////// Reordenar artículos por factor conversión //////////////////////////////////////////////////////
        for (var j          = 0; j < arrayListaProductos.length;j++)
        {
            //alert("Entra al primer for j="+j);
            if (arrayListaProductos[j]['factorconversion'] == 1)
            {
                console.log("continue");
                continue;
            }
            cont_e_codigo   = 0;
            codigo          = arrayListaProductos[j]['codigo'];
            suma_cantidades = 0;
            for (var i      = 0; i < arrayListaProductos.length; i++)
            {
                if (codigo  == arrayListaProductos[i]['codigo'])
                {
                    cont_e_codigo++;
                    suma_cantidades += parseFloat(arrayListaProductos[i]['cantidad']);
                    if (cont_e_codigo == 1)
                        indexBorrar = i;
                    if (cont_e_codigo == 2)
                        indexAumentar = i;
                }
            }
            console.log("indexBorrar="+indexBorrar);
            if (cont_e_codigo == 2)
                console.log("indexAumentar="+indexAumentar);
            factor      = parseFloat(arrayListaProductos[j]['factorconversion']);
            entero      = suma_cantidades/factor;
            entero      = entero.toString();
            arrayEntero = entero.split(".");
            parteEntero = arrayEntero[0];
            //alert(arrayEntero[0]);
            console.log(cont_e_codigo);
            if (cont_e_codigo == 1)
            {
                if (suma_cantidades > factor)
                {
                    precioUnidad    = parseFloat(arrayListaProductos[indexBorrar]['precioXpaquete']) / parseFloat(factor);
                    arrayListaProductos[indexBorrar]['precioU'] = precioUnidad.toFixed(2);
                    cantFloat       = parseFloat(factor) * parseFloat(parteEntero);
                    arrayListaProductos[indexBorrar]['cantidad'] = cantFloat.toFixed(3);
                    subTot          = cantFloat * precioUnidad;
                    arrayListaProductos[indexBorrar]['subTot'] = redondearDec(subTot);
                    arrayListaProductos[indexBorrar]['nombre'] = arrayListaProductos[indexBorrar]['nombreCache'];
                    //arrayListaProductos[indexBorrar]['nombre'] += " (PROMO)";
                    id_             = arrayListaProductos[indexBorrar]['id'];
                    codigo_         = arrayListaProductos[indexBorrar]['codigo'];
                    nombre_         = arrayListaProductos[indexBorrar]['nombre'];
                    nombreCache_    = arrayListaProductos[indexBorrar]['nombre'];
                    precioXunidad_  = arrayListaProductos[indexBorrar]['precioXunidad'];
                    precioXpaquete_ = arrayListaProductos[indexBorrar]['precioXpaquete'];
                    factorconversion_= arrayListaProductos[indexBorrar]['factorconversion'];
                    precioU_        = parseFloat(arrayListaProductos[indexBorrar]['precioXpaquete']) * parseFloat(arrayListaProductos[indexBorrar]['factorconversion']);
                    cant_b          = parseFloat(suma_cantidades) - parseFloat(cantFloat);
            //////////////////////////////////////////// Agregar row Lista de artículos /////////////////////////////////////////////////////////////
                    row  ='  <tr class="trItemLista">';
                    row +='     <td class="text-center">';
                    row +=          '<button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem"><i class="fa fa-times"></i></button>';
                    row +='     </td>';
                    row +='     <td class="tdCodigo">';
                    row +=          codigo_;
                    row +='     </td>';
                    row +='     <td class="tdNombre">';
                    row +=          nombre_;
                    row +='     </td>';
                    row +='     <td class="text-right">';
                    row +=          '<input type="number" min=0 style="text-align:right; border-width:0;" class="inputCantidad" value="'+cant_b.toFixed(3)+'">';
                    row +='     </td>';
                    row +='     <td class="text-right tdPrecioU">';
                    row +=          '<input type="number" min=0 style="text-align:right; border-width:0;" class="inputPrecioU" value="'+precioU_.toFixed(2)+'">';
                    row +='     </td>';
                    stot = parseFloat(cant_b.toFixed(3)) * parseFloat(precioU_);
                    stot_ = redondearDec(stot);
                    row +='     <td class="tdSubTotal text-right">';
                    row +=          stot_;
                    row +='     </td>';
                    row +='  </tr>';
                    $("#listaProductos").prepend(row);
                    prod = new producto(id_, codigo_, nombre_, nombreCache_, precioU_, cant_b.toFixed(3), stot_, precioXunidad_, precioXpaquete_, factorconversion_);
                    arrayListaProductos.unshift(prod); //al final
                    $(".btnEliminarItem").each(function(atributoItemName)
                    {
                        $(this).attr("name",atributoItemName);
                        $(this).parent().parent().attr("name",atributoItemName);
                        atributoItemName++;
                    });
                }
                else if (suma_cantidades < factor)
                {
                    cantFloat       = parseFloat(suma_cantidades);
                    precioUFloat    = parseFloat(arrayListaProductos[indexBorrar]['precioXunidad']);
                    arrayListaProductos[indexBorrar]['cantidad'] = cantFloat.toFixed(3);
                    arrayListaProductos[indexBorrar]['precioU'] = precioUFloat.toFixed(2);
                    subTot          = cantFloat * precioUFloat;
                    arrayListaProductos[indexBorrar]['subTot'] = redondearDec(subTot);
                    arrayListaProductos[indexBorrar]['nombre'] = arrayListaProductos[indexBorrar]['nombreCache'];
                    // w = arrayListaProductos.splice(indexBorrar,1);
                    // $(".trItemLista[name="+indexBorrar+"]").remove();
                }
                else {
                    precioUnidad    = parseFloat(arrayListaProductos[indexBorrar]['precioXpaquete']) / parseFloat(factor);
                    arrayListaProductos[indexBorrar]['precioU'] = precioUnidad.toFixed(2);
                    cantFloat       = parseFloat(factor) * parseFloat(parteEntero);
                    arrayListaProductos[indexBorrar]['cantidad'] = cantFloat.toFixed(3);
                    subTot          = cantFloat * precioUnidad;
                    arrayListaProductos[indexBorrar]['subTot'] = redondearDec(subTot);
                    arrayListaProductos[indexBorrar]['nombre'] = arrayListaProductos[indexBorrar]['nombreCache'];
                    arrayListaProductos[indexBorrar]['nombre'] += " (PROMO)";
                }
            }
            if (cont_e_codigo == 2)
            {
                //alert("cont_e_codigo="+cont_e_codigo);
                console.log("suma_cantidades="+suma_cantidades);
                console.log("factor="+factor);
                if (suma_cantidades > factor)
                {
                    cantFloat       = parseFloat(factor) * parseFloat(parteEntero);
                    precioUFloat    = parseFloat(arrayListaProductos[indexAumentar]['precioXpaquete']) / parseFloat(factor);
                    arrayListaProductos[indexAumentar]['cantidad'] = cantFloat.toFixed(3);
                    arrayListaProductos[indexAumentar]['precioU'] = precioUFloat.toFixed(2);
                    subTot          = cantFloat * precioUFloat;
                    arrayListaProductos[indexAumentar]['subTot'] = redondearDec(subTot);
                    arrayListaProductos[indexAumentar]['nombre'] = arrayListaProductos[indexAumentar]['nombreCache']
                    arrayListaProductos[indexAumentar]['nombre'] += " (PROMO)";
                    //arrayListaProductos[indexBorrar]['cantidad'] = cantFloat.toFixed(3);
                    cant_b          = suma_cantidades - cantFloat;
                    if (cant_b != 0)
                    {
                        precioUnidad    = parseFloat(arrayListaProductos[indexBorrar]['precioXunidad']);
                        arrayListaProductos[indexBorrar]['precioU'] = precioUnidad.toFixed(2);
                        arrayListaProductos[indexBorrar]['cantidad'] = cant_b.toFixed(3);
                        subTot          = cant_b * precioUnidad;
                        arrayListaProductos[indexBorrar]['subTot'] = redondearDec(subTot);
                    }
                    else
                    {
                        w = arrayListaProductos.splice(indexBorrar,1);
                        $(".trItemLista[name="+indexBorrar+"]").remove();
                        console.log("indexBorrar:"+indexBorrar);
                    }
                }
                else if (suma_cantidades < factor)
                {
                    cantFloat       = parseFloat(suma_cantidades);
                    precioUFloat    = parseFloat(arrayListaProductos[indexAumentar]['precioXunidad']);
                    arrayListaProductos[indexAumentar]['cantidad'] = cantFloat.toFixed(3);
                    arrayListaProductos[indexAumentar]['precioU'] = precioUFloat.toFixed(2);
                    subTot          = cantFloat * precioUFloat;
                    arrayListaProductos[indexAumentar]['subTot'] = redondearDec(subTot);
                    arrayListaProductos[indexAumentar]['nombre'] = arrayListaProductos[indexAumentar]['nombreCache'];
                    w = arrayListaProductos.splice(indexBorrar,1);
                    $(".trItemLista[name="+indexBorrar+"]").remove();
                }
                else
                {
                    cantFloat       = parseFloat(suma_cantidades);
                    precioUFloat    = parseFloat(arrayListaProductos[indexAumentar]['precioXpaquete']) / parseFloat(factor);
                    arrayListaProductos[indexAumentar]['cantidad'] = cantFloat.toFixed(3);
                    arrayListaProductos[indexAumentar]['precioU'] = precioUFloat.toFixed(2);
                    arrayListaProductos[indexAumentar]['nombre'] = arrayListaProductos[indexAumentar]['nombreCache'];
                    arrayListaProductos[indexAumentar]['nombre'] += " (PROMO)";
                    subTot          = cantFloat * precioUFloat;
                    arrayListaProductos[indexAumentar]['subTot'] = redondearDec(subTot);
                    w = arrayListaProductos.splice(indexBorrar,1);
                    $(".trItemLista[name="+indexBorrar+"]").remove();
                }
            }
        }
        // if (cont_e_codigo == 1)
        // {
        //     precioXpaq_promo    = parseFloat(e.precioXpaquete) / parseFloat(e.factorconversion);
        //     arrayListaProductos[indexBorrar]["precioU"] = precioXpaq_promo.toFixed(2);
        // }
        totalItems          = 0;
        sumaSubtotal        = 0;
        $(".btnEliminarItem").each(function(atributoItemName)
        {
            $(this).attr("name",atributoItemName);
            $(this).parent().parent().attr("name",atributoItemName);
            atributoItemName++;
        });
        $(".tdCodigo").each(function(x)
        {
            $(this).text(arrayListaProductos[x]["codigo"]);
        });
        $(".tdNombre").each(function(x)
        {
            $(this).text(arrayListaProductos[x]["nombre"]);
        });
        $(".inputPrecioU").each(function(x)
        {
            $(this).val(arrayListaProductos[x]["precioU"]);
            // if ((arrayListaProductos[x]["cantidad"] % arrayListaProductos[x]["factorconversion"]) == 0)
            //     precioU = parseFloat(arrayListaProductos[x]["precioXpaquete"]) / parseFloat(arrayListaProductos[x]["factorconversion"]);
            // else
            //     precioU = parseFloat(arrayListaProductos[x]["precioXunidad"]);
            // arrayListaProductos[x]["precioU"] = precioU.toFixed(2);
            //
            // arrayListaProductos[x]["subTot"] = precioU.toFixed(2) * parseFloat(arrayListaProductos[x]["cantidad"]);
            //$(this).html('<input type="number" min=0 style="text-align:right; border-width:0;" class="inputPrecioU" value="'+arrayListaProductos[x]["precioU"]+'">');
        });
        $(".inputPrecioU").each(function(x)
        {
            c = parseFloat(arrayListaProductos[x]["precioU"]);
            $(this).val(c.toFixed(3));
        });
        $(".inputCantidad").each(function(x)
        {
            c = parseFloat(arrayListaProductos[x]["cantidad"]);
            $(this).val(c.toFixed(3));
        });
        $(".tdSubTotal").each(function(x)
        {
            c = parseFloat(arrayListaProductos[x]["subTot"]);
            c = redondearDec(c);
            $(this).text(c);
            sumaSubtotal += parseFloat(c);
        });

        $("#spanTotal").text(formatoMoneda(sumaSubtotal));
        $("#spanTotalArts").text(arrayListaProductos.length);
        //$("inputAbono").val(sumaSubtotal);
        if(arrayListaProductos.length > 0)
            $("#selectCliente").attr("disabled",true);
        else
            $("#selectCliente").attr("disabled",false);

        $("#listaProductos tr[name="+index+"]").addClass("success",50, function()
        {
            setTimeout(function()
            {
                $( "#listaProductos tr" ).removeClass( "success",200 );
            }, 900 );
        });
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
        ChromeSuperAjax(
        {
            method: "POST",
            url:"http://127.0.0.1/pventa_std/balanza/obtenpeso.php",
            data: {producto:e.codigo},
            success: function(p)
            {
                if(isNaN(p))
                {
                    $("#inputPeso").attr("disabled",false);
                    $("#inputPeso").prop("type","number");
                    $("#inputPeso").attr("min",0);
                    $("#divErrorBascula").show();
                    $("#inputPeso").focus();
                    clearInterval(timerAjax);
                    basculaDisponible = 0;
                    console.log(p);
                }
                else
                {
                    $("#divErrorBascula").hide();
                    $("#inputPeso").attr("disabled",true);
                    $("#inputPeso").prop("type","text");
                    basculaDisponible = 1;
                    p = parseFloat(p);
                    $("#inputPeso").val(p.toFixed(3));
                    //$("#inputPeso").val(p);
                    console.log(p);
                }
            },
            error: function(p)
            {
                $("#inputPeso").attr("disabled",false);
                $("#inputPeso").prop("type","number");
                $("#inputPeso").attr("min",0);
                $("#divErrorBascula").show();
                $("#inputPeso").focus();
                clearInterval(timerAjax);
                basculaDisponible = 0;
                console.log(p);
            }
        })
        /*.done(function(p)
        {
            if(p=='')
            {
                $("#inputPeso").attr("disabled",false);
                $("#inputPeso").prop("type","number");
                $("#inputPeso").attr("min",0);
                $("#divErrorBascula").show();
                $("#inputPeso").focus();
                clearInterval(timerAjax);
                basculaDisponible = 0;
            }
            else
            {
                $("#divErrorBascula").hide();
                $("#inputPeso").attr("disabled",true);
                $("#inputPeso").prop("type","text");
                basculaDisponible = 1;
                $("#inputPeso").val(p);
            }
            console.log(p);
            //alert(p);
        });*/

    }
    function limpiarPeso()
    {
        if(typeof(timerAjax)!="undefined")
            clearInterval(timerAjax);
        $("#inputPeso").val('');
    }
    function ocultarDismissAlert()
    {
        setTimeout(function()
        {
            $( "#dismissAlert" ).hide("fade",800);
        }, 4000 );
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
                if(e.keyCode == 112)
                {
                    e.preventDefault();
                    $("#inputBuscar").focus();
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
                if(e.keyCode == 115)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    $("#btnf4").click();
                }
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                if(e.keyCode == 116)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                    return;
                    $("#btnf5").click();
                }
<?php } ?>
                if(e.keyCode == 117)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    $("#btnf6").click();
                }
                if(e.keyCode == 118)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    $("#btnf7").click();
                }
                if(e.keyCode == 119)
                {

                    e.preventDefault();
                    if(dialogoAbierto == 1)
                        return;
                    $("#btnf8").click();
                }
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                if(e.keyCode == 121)
                {
                    e.preventDefault();
                    $("#btnf10").click();
                }
<?php } ?>
                if(e.keyCode == 122)
                {
                    e.preventDefault();
                    $("#btnf11").click();
                }
                if(e.keyCode == 123)
                {
                    e.preventDefault();
                    $("#btnf12").click();
                }
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
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
<?php } ?>
            });
            $("#inputBuscar").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnBuscar").click();
                if(e.keyCode == 40)
                    //$("#listaProductos > tr:nth-child(1)").find(".inputCantidad").focus();
                    $("#listaProductos > tr:nth-child(1)").find(".inputCantidad").focus();
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
                            separador = $("#inputBuscar").val();
                            $("#inputBuscar").val(separador + response);
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
                reordenarItems();
            });
            //Evento click capturar peso en caso de no encontrar báscula
            $("#inputPeso").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnCapturarPeso").click();
            });
            $("#btnBuscar").click(function()
            {
                str                     = $("#inputBuscar").val();
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
                        $("#inputBuscar").focus();
                        return false;
                    }
                    if(d.balanza == '1')
                    {
                        $("#dialog-confirm-peso").attr("title","Capturar peso "+d.nombre);
                        $("#ui-id-1").text("Capturar peso "+d.nombre);
                        $( "#dialog-confirm-peso" ).data('d',d).dialog("open");
                        timerAjax = setInterval(ajaxPeso,490,d);
                        $("#divGroup").removeClass("has-error");
                        // Modal Dialog UI obtener peso balanza
                    }
                    else
                    {
                        agregarProducto(d,multiplicador);
                        console.log("multiplicador:"+multiplicador);
                    }
                });
            });
            $("#btnf2").click(function()
            {
                cargarListaProductos();
            });
            $("#btnf3").click(function()
            {
                $("#btnModalBuscarNombre").click();
            });
            $("#btnf4").click(function()
            {
                if (arrayListaProductos.length > 0)
                    $( "#dialog-confirm-salir" ).data("url","notaCredito.php").dialog("open");
                else
                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/notaCredito.php");
            });
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
            $("#btnf5").click(function()
            {
                if(arrayListaProductos.length == 0 || dialogoAbierto == 1)
                    return;
                else
                    $( "#dialog-confirm-nota-salida" ).dialog("open");
            });
<?php } ?>
            $("#btnf6").click(function()
            {
                if (arrayListaProductos.length > 0)
                    $( "#dialog-confirm-salir" ).data("url","compras.php").dialog("open");
                else
                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/compras.php");
            });
            $("#btnf7").click(function()
            {
                if (arrayListaProductos.length > 0)
                    $( "#dialog-confirm-salir" ).data("url","corteCaja.php").dialog("open");
                else
                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/corteCaja.php");
            });
            $("#btnf8").click(function()
            {
                if (arrayListaProductos.length > 0)
                    $( "#dialog-confirm-salir" ).data("url","retiroEfectivo.php").dialog("open");
                else
                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/retiroEfectivo.php");
            });
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
            $("#btnf10").click(function()
            {
                if(arrayListaProductos.length == 0 || dialogoAbierto == 1)
                    return;
                else
                $( "#dialog-confirm-cotizacion" ).dialog("open");
            });
<?php } ?>
            $("#btnf11").click(function()
            {
                if(arrayListaProductos.length == 0 || dialogoAbierto == 1)
                    return;
                $("#hiddenRemision").val(1);
                $( "#dialog-confirm-venta" ).dialog("open");
            });
            $("#btnf12").click(function()
            {
                if(arrayListaProductos.length == 0 || dialogoAbierto == 1)
                    return;
                $("#hiddenRemision").val(0);
                $( "#dialog-confirm-venta" ).dialog("open");
            });
            $("body").on("keydown", ".inputCantidad", function(e)
            {
                if(e.keyCode == 13)
                {
                    e.preventDefault();
                    c = parseFloat($(this).val());
                    if(isNaN(c) || c.length == 0 || c <= 0)
                    {
                        index = $(this).parent().parent().attr("name");
                        trError(index);
                        $(this).focus();
                    }
                    else
                    {
                        $("#inputBuscar").focus();
                    }

                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputCantidad").focus();
                }
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    if($(this).parent().parent().attr("name") == 0)
                        $("#inputBuscar").focus();
                    else
                        $(this).parent().parent().prev().find(".inputCantidad").focus();
                }
                if(e.keyCode == 39)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputPrecioU").focus();
                }
                if(e.keyCode == 27)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".btnEliminarItem").click();
                }

            });
            $("body").on("keydown", ".inputPrecioU", function(e)
            {
                if(e.keyCode == 13)
                {
                    e.preventDefault();
                    c = parseFloat($(this).val());
                    if(isNaN(c) || c.length == 0 || c <= 0)
                    {
                        index = $(this).parent().parent().attr("name");
                        trError(index);
                        $(this).focus();
                    }
                    else
                    {
                        $("#inputBuscar").focus();
                    }

                }
                if(e.keyCode == 40)
                {
                    e.preventDefault();
                    $(this).parent().parent().next().find(".inputPrecioU").focus();
                }
                if(e.keyCode == 38)
                {
                    e.preventDefault();
                    if($(this).parent().parent().attr("name") == 0)
                        $("#inputBuscar").focus();
                    else
                        $(this).parent().parent().prev().find(".inputPrecioU").focus();
                }
                if(e.keyCode == 37)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".inputCantidad").focus();
                }
                if(e.keyCode == 27)
                {
                    e.preventDefault();
                    $(this).parent().parent().find(".btnEliminarItem").click();
                }

            });
            $("body").on("focusout",".inputCantidad",function()
            {
                c = parseFloat($(this).val());
                if(isNaN(c) || c.length == 0 || c <= 0)
                {
                    index = $(this).parent().parent().attr("name");
                    trError(index);
                    $(this).focus();
                    //return false;
                }
            });
            $("body").on("focusout",".inputPrecioU",function()
            {
                c = parseFloat($(this).val());
                if(isNaN(c) || c.length == 0 || c <= 0)
                {
                    index = $(this).parent().parent().attr("name");
                    trError(index);
                    $(this).focus();
                    //return false;
                }
            });
            $("body").on("change",".inputCantidad",function()
            {
                c = parseFloat($(this).val());
                if(isNaN(c) || c.length == 0 || c <= 0)
                {
                    $(this).focus();
                    return false;
                }
                index = $(this).parent().parent().attr("name");
                arrayListaProductos[index]["cantidad"] = c.toFixed(3);
                arrayListaProductos[index]["subTot"] = parseFloat(arrayListaProductos[index]["precioU"]) * parseFloat(arrayListaProductos[index]["cantidad"]);
                reordenarItems(index);
            });
            $("body").on("change",".inputPrecioU",function()
            {
                c = parseFloat($(this).val());
                if(isNaN(c) || c.length == 0 || c <= 0)
                {
                    $(this).focus();
                    return false;
                }
                index = $(this).parent().parent().attr("name");
                <?php
                    if ($sesion->get("tipousuario") != 1)
                    {
                ?>
                        if (c < arrayListaProductos[index]["precioXunidad"])
                        {
                            pXu = parseFloat(arrayListaProductos[index]["precioXunidad"]);
                            $(this).val(pXu.toFixed(2));
                            reordenarItems();
                            return false;
                        }
                        else
                        {
                            reordenarItems();
                        }
                <?php
                    }
                    else
                    {
                ?>
                        if (arrayListaProductos[index]['factorconversion'] > 1)
                        {
                            $("#dialog-confirm-cambiar-factorconversion").data('index',index).dialog('open');
                        }
                        else
                        {
                            arrayListaProductos[index]["precioU"] = c.toFixed(2);
                            arrayListaProductos[index]["subTot"] = parseFloat(arrayListaProductos[index]["precioU"]) * parseFloat(arrayListaProductos[index]["cantidad"]);
                            reordenarItems(index);
                        }
                <?php
                    }
                ?>

            });
            $("body").on("change","#selectCliente",function()
            {
                reordenarItems();
                val = $(this).val();
                $("#selectClientePos").val(val);
                if(val==1)
                {
                    if($("#chkAbonos")  .prop('checked') == true)
                    {
                        $("#chkAbonos") .click();
                        $("#chkAbonos") .prop('checked',false);
                    }
                    $("#inputAbono").prop("disabled", true);
                    $("#inputAbono").val($("#hiddenMontoTotal").val());
                    $("#divChkAbonos").hide("fade",200,$("#divAbono").hide( 'fade', 400));
                }
                else
                {
                    $("#inputAbono").prop("disabled", false);
                    $("#divChkAbonos").show("fade",200);
                }
            });
            $("body").on("change","#selectClientePos",function()
            {
                reordenarItems();
                val = $(this).val();
                $("#selectCliente").val(val);
                if(val==1)
                {
                    $("#inputPagaCon")  .focus();
                    if($("#chkAbonos")  .prop('checked') == true)
                    {
                        $("#chkAbonos") .click();
                        $("#chkAbonos") .prop('checked',false);
                    }
                    $("#divChkAbonos")  .hide("fade",200);
                    //$("#divAbono")      .show( 'fade', 400, $("#inputAbono").focus());
                    $("#inputAbono")    .prop("disabled", true);
                    hid_                = parseFloat($("#hiddenMontoTotal").val());
                    $("#inputAbono")    .val(hid_.toFixed(2));
                }
                else
                {
                    $("#chkAbonos")     .focus();
                    $("#divChkAbonos")  .show("fade",200);
                    $("#inputAbono")    .prop("disabled", false);
                    $("#inputAbono")    .focus();
                }
            });
            $("body").on("focus",".inputCantidad,.inputPrecioU,#inputPagaCon,#inputAbono",function()
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
            $("#inputPagaCon,#inputAbono").keyup(function(e)
            {
                total   = parseFloat($("#hiddenMontoTotal").val());
                abono   = parseFloat($("#inputAbono").val());
                if ($(this).attr('id') == 'inputAbono')
                {
                    $("#inputPagaCon").val(abono);
                }
                paga    = parseFloat($("#inputPagaCon").val());
                if(e.keyCode == 13)
                {
                    $("#btnTerminarVenta").click();
                }
                if($("#chkAbonos").prop("checked"))
                {
                    cambio  = redondearDec(paga  - abono);
                    adeudo  = redondearDec(total - abono);
                    if(paga == 0 && abono == 0)
                    {
                        $("#inputPagaCon").parent().removeClass("has-error");
                        $("#inputAbono").parent().removeClass("has-error");
                    }
                    else
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                        $("#inputAbono").parent().addClass("has-error");
                    }
                    if (isNaN($("#inputPagaCon").val())     ||
                        $("#inputPagaCon")      .val() == "")
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                        //return false;
                    }
                    else
                    {
                        $("#inputPagaCon").parent().removeClass("has-error");
                    }

                    if (isNaN($("#inputAbono")  .val())     ||
                        $("#inputAbono")        .val() == ""||
                        abono == 0 || abono == total)
                    {
                        $("#inputAbono").parent().addClass("has-error");
                    }
                    else
                    {
                        $("#inputAbono").parent().removeClass("has-error");
                    }
                    if( paga < abono || $("#inputPagaCon").val() == "")
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                    }
                    else
                    {
                        $("#inputPagaCon").parent().removeClass("has-error");
                    }
                    if( abono >= total || $("#inputAbono").val() == "")
                    {
                        $("#inputAbono").parent().addClass("has-error");
                    }
                    else
                    {
                        $("#inputAbono").parent().removeClass("has-error");
                    }
                    cambio_ = (isNaN(cambio)) ? 0 : cambio;
                    adeudo_ = (isNaN(adeudo)) ? 0 : adeudo;
                    $("#inputCambio").val(cambio_);
                    $("#inputAdeudo").val(adeudo_);
                }
                else
                {
                    if (isNaN($("#inputPagaCon").val()) || $("#inputPagaCon").val() == "" || paga == 0 || paga < total)
                    {
                        $("#inputPagaCon").parent().addClass("has-error");
                        $("#inputCambio").val(0);
                    }
                    else
                    {
                        cambio  = redondearDec(paga - total);
                        cambio_ = (isNaN(cambio)) ? 0 : cambio;
                        $("#inputCambio").val(cambio_);
                        $("#inputPagaCon").parent().removeClass("has-error");
                    }
                }


            });
            $("#chkImprimir").change(function()
            {
                if ($(this).prop("checked"))
                {
                    $("#fieldOcultarPU").prop("disabled",false);
                }
                else
                {
                    $("#chkOcultarPU").prop("checked",false);
                    $("#fieldOcultarPU").prop("disabled",true);
                }
            });
            $("#chkAbonos").click(function()
            {
                if ($(this).prop('checked'))
                {
                    $("#divAbono").show( 'fade', 400, function()
                    {
                        // $("#inputAbono").focus();
                        // $("#inputAbono").select();
                        $("#inputPagaCon").val('0');
                        $("#inputAbono").val('0').focus().select();
                        $("#inputAdeudo").val($("#inputMontoTotal").val());
                        if (parseFloat($("#inputPagaCon").val()) < parseFloat($("#inputAbono").val()) ||
                            $("#inputPagaCon").val() == "")
                        {
                            $("#inputPagaCon").parent().addClass("has-error");
                        }
                        else
                        {
                            $("#inputPagaCon").parent().removeClass("has-error");
                        }
                        if (parseFloat($("#inputAbono").val()) >= parseFloat($("#hiddenMontoTotal").val()))
                        {
                            $("#inputAbono").parent().addClass("has-error");
                        }
                        else
                        {
                            $("#inputAbono").parent().removeClass("has-error");
                        }
                    });
                }
                else
                {
                    $("#divAbono").hide( 'fade', 300, function()
                    {
                        tot    = parseFloat($("#hiddenMontoTotal").val());
                        paga   = parseFloat($("#inputPagaCon").val());
                        cambio = paga - tot;

                        if (paga < tot || cambio < 0 || isNaN($("#inputPagaCon").val()) || $("#inputPagaCon").val() == "")
                        {
                            $("#inputPagaCon").parent().addClass("has-error");
                            $("#inputCambio").val(0);
                            //return false;
                        }
                        else
                        {
                            $("#inputPagaCon").parent().removeClass("has-error");
                            $("#inputAbono").val(tot.toFixed(2));
                            cambio_ = (isNaN(cambio)) ? 0 : cambio;
                            $("#inputCambio").val(cambio_);
                            $("#inputAdeudo").val(0);
                        }
                        $("#inputPagaCon").focus()
                    });
                }

            });
            $("#selectCliente,#selectClientePos").change(function()
            {
                idCliente = $(this).val();
            });
            $("#spanAgregarCliente").click(function()
            {
                $.ajax(
                {
                    method: "POST",
                    url:"../control/modificarCliente.php",
                    data: {idCliente:0}
                })
                .done(function(p)
                {
                    $("#divAgregarCliente").html(p);
                    $( "#dialog-agregar-cliente" ).dialog("open");
                });

            });
            $(document).on('click',"#vistaPrevia",function()
            {
                src = $(this).attr("src");
                //console.log("src length="+src.length);
                if (src == undefined || src.length == 0)
                    return false;
                $('#dialog-Img').dialog("open");
            });
            $( "#dialog-confirm-peso" ).dialog(
            {
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                autoOpen: false,
                close: function()
                {
                    limpiarPeso();
                },
                buttons:
                [
                    {
                        text: "Capturar peso",
                        "id": 'btnCapturarPeso',
                        click: function()
                        {
                            //alert($("#inputPeso").val());
                            //if($("#inputPeso").val()=='' || $("#inputPeso").val()=='  0.000 kg' || $("#inputPeso").val()==' 0.000 kg' || $("#inputPeso").val()=='  NEG.    ')
                            if(isNaN(parseFloat($("#inputPeso").val())) || parseFloat($("#inputPeso").val()) <= 0)
                                return false;
                            e = $( "#dialog-confirm-peso" ).data('d');
                            if(!(basculaDisponible))
                            {
                                $("#inputPeso").prop("type","text");
                                cad = " ";
                                cad += $("#inputPeso").val();
                                $("#inputPeso").val(cad);
                            }
                            agregarProducto(e,$("#inputPeso").val());
                            $("#inputPeso").attr("disabled",true);
                            $(this).dialog("close");
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function() {
                            limpiarPeso();
                            $( this ).dialog( "close" );
                            $("#inputPeso").attr("disabled",true);
                        }
                    }
                ]
            });
            $( "#dialog-confirm-venta" ).dialog(
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
                    tipoVenta = parseInt($("#hiddenRemision").val());
                    txtVenta = (tipoVenta == 1) ? '<i class="fa fa-print" aria-hidden="true"></i> REMISIÓN' : '<i class="fa fa-ticket" aria-hidden="true"></i> TICKET';
                    $("#h3Remision").html(txtVenta);
                    dialogoAbierto = 1;
                    reordenarItems();
                    tot = 0;
                    for(x=0; x < arrayListaProductos.length; x++)
                    {
                        tot = parseFloat(tot) + parseFloat(redondearDec(arrayListaProductos[x]["precioU"] * arrayListaProductos[x]["cantidad"]));
                    }
                    $("#inputMontoTotal").val(formatoMoneda(tot));
                    $("#hiddenMontoTotal").val(tot);
                    $("#inputAbono").val(tot.toFixed(2));
                    $("#inputPagaCon").val("");
                    if(isNaN($("#inputPagaCon").val()))
                        $("#inputCambio").val(0);

                    $("#inputPagaCon").focus();
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Aceptar",
                        "id": "btnTerminarVenta",
                        click: function()
                        {
                            //if($("#dialog-confirm-venta").find(".has-error").length > 0)
                            $("#divRespuestaVta").empty();
                            msg = ' <div class="alert alert-danger alert-dismissable">';
                            msg +='     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                            msg +='     <i class="fa fa-exclamation-circle" aria-hidden="true"></i> Completa los campos marcados';
                            msg +=' </div>';
                            if ($("#chkAbonos").prop("checked"))
                            {
                                if ($("#inputPagaCon").parent().hasClass("has-error") ||
                                    $("#inputAbono").parent().hasClass("has-error"))
                                    {
                                        $("#divRespuestaVta").html(msg);
                                        return false;
                                    }
                            }
                            else
                            {
                                if ($("#inputPagaCon").parent().hasClass("has-error"))
                                {
                                    $("#divRespuestaVta").html(msg);
                                    return false;
                                }
                            }
                            if(isNaN(parseInt($("#inputPagaCon").val())) == true)
                            {
                                $("#inputPagaCon").parent().addClass("has-error");
                                $("#divRespuestaVta").html(msg);
                                $("#inputPagaCon").focus();
                                return false;
                            }

                            //return false;
                            total = $("#hiddenMontoTotal").val();
                            btnTerminarVenta= $("#btnTerminarVenta");
                            btnCancelarVenta= $("#btnCancelarVenta");
                            btnTerminarVenta.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            btnTerminarVenta.prop("disabled", true);
                            btnCancelarVenta.prop("disabled", true);
                            dialogo         = $(this);
                            //form_           = $("#form-confirm-venta").serialize();
                            chkImprimir     = ($("#chkImprimir").prop("checked")) ? 1 : 0;
                            chkOcultarPU    = ($("#chkOcultarPU").prop("checked")) ? 1 : 0;
                            pagaCon         = $("#inputPagaCon").val();
                            idCliente       = $("#selectClientePos").val();
                            montoTotal      = $("#hiddenMontoTotal").val();
                            remision        = $("#hiddenRemision").val();
                            chkAbonos       = ($("#chkAbonos").prop("checked")) ? 1 : 0;
                            inputAbono      = $("#inputAbono").val();
                            inputCambio     = $("#inputCambio").val();

                            var listaProductosJSON = JSON.stringify(arrayListaProductos);
                            $.ajax(
                            {
                                method: "POST",
                                url:"guardarVenta.php",
                                data: {listaProductos:listaProductosJSON,chkImprimir:chkImprimir,chkOcultarPU:chkOcultarPU,pagaCon:pagaCon,idCliente:idCliente,montoTotal:montoTotal,remision:remision,chkAbonos:chkAbonos,inputAbono:inputAbono,inputCambio:inputCambio}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    arrayListaProductos.length = 0;
                                    if(p.imprimir == 1)
                                    {
                                        if (p.remision == 1)
                                        {
                                            url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionVentaPDF.php?idVenta="+p.idVenta;
                                            $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                            setTimeout(function()
                                            {
                                                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/index.php");
                                            }, 620);
                                        }
                                        else
                                        {
                                            $('#recibo').html(p.recibo).promise().done(function()
                                            {
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
                                                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/index.php");
                                                }, 2250);
                                            });
                                        }
                                    }
                                    else
                                    {
                                        setTimeout(function()
                                        {
                                            window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/index.php");
                                        }, 620);
                                    }
                                }
                                else
                                {
                                    alert("No se puede guardar");
                                    dialogo.dialog( "close" );
                                }
                            })
                            .fail(function()
                            {
                                msg = ' <div class="alert alert-danger alert-dismissable">';
                                msg +='     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                                msg +='     <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se puede guardar en este momento. Consulte con el adminsitrador del sistema';
                                msg +=' </div>';
                                $("#divRespuestaVta").html(msg);
                                btnTerminarVenta.html('Aceptar');
                                btnTerminarVenta.prop("disabled", false);
                                btnCancelarVenta.prop("disabled", false);
                            })
                            .always(function(p)
                            {
                                console.log(p);
                            });
                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarVenta",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-Img" ).dialog(
            {
                resizable: true,
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
            $( "#dialog-confirm-nota-salida" ).dialog(
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
                    for(x=0; x < arrayListaProductos.length; x++)
                    {
                        tot = parseFloat(tot) + parseFloat(redondearDec(arrayListaProductos[x]["precioU"] * arrayListaProductos[x]["cantidad"]));
                    }
                    $("#inputMontoTotal").val(formatoMoneda(tot));
                    $("#hiddenMontoTotal").val(tot);
                    //$("#inputAbono").val(tot.toFixed(2));
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Aceptar",
                        "id": "btnTerminarNotaSalida",
                        click: function()
                        {
                            total   = $("#hiddenMontoTotal").val();
                            obs     = $("#inputObsNotaSalida").val();
                            btnTerminarNotaSalida = $("#btnTerminarNotaSalida");
                            btnCancelarNotaSalida = $("#btnCancelarNotaSalida");
                            btnTerminarNotaSalida.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            btnTerminarNotaSalida.prop("disabled", true);
                            btnCancelarNotaSalida.prop("disabled", true);
                            dialogo = $(this);
                            var listaProductosJSON = JSON.stringify(arrayListaProductos);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/guardarNotaSalida.php",
                                data: {listaProductos:listaProductosJSON,t:total,obs:obs}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    arrayListaProductos.length = 0;
                                    url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genNotaSalidaPDF.php?idNota="+p.idNota;
                                    $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                    window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/index.php");
                                    dialogo.dialog( "close" );
                                }
                                else
                                {
                                    alert("No se puede guardar");
                                    dialogo.dialog( "close" );
                                }
                            })
                            .fail(function()
                            {
                                alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                                dialogo.dialog( "close" );
                                btnTerminarNotaSalida.html('Aceptar');
                                btnTerminarNotaSalida.prop("disabled", false);
                                btnCancelarNotaSalida.prop("disabled", false);
                            })
                            .always(function(p)
                            {
                                console.log(p);
                            })
                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarNotaSalida",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            });
            $( "#dialog-confirm-cotizacion" ).dialog(
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
                    for(x=0; x < arrayListaProductos.length; x++)
                    {
                        tot = parseFloat(tot) + parseFloat(redondearDec(arrayListaProductos[x]["precioU"] * arrayListaProductos[x]["cantidad"]));
                    }
                    $("#inputMontoTotal").val(formatoMoneda(tot));
                    $("#hiddenMontoTotal").val(tot);
                    $("#selectClienteCotizacion").val($("#selectCliente").val());
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Guardar",
                        "id": "btnTerminarCotizacion",
                        click: function()
                        {
                            total                   = $("#hiddenMontoTotal").val();
                            cliente                 = $("#selectClienteCotizacion").val();
                            btnTerminarCotizacion   = $("#btnTerminarCotizacion");
                            btnCancelarCotizacion   = $("#btnCancelarCotizacion");
                            btnTerminarCotizacion.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            btnTerminarCotizacion.prop("disabled", true);
                            btnCancelarCotizacion.prop("disabled", true);
                            dialogo = $(this);
                            var listaProductosJSON = JSON.stringify(arrayListaProductos);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/guardarCotizacion.php",
                                data: {listaProductos:listaProductosJSON,idCliente:cliente,t:total}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    arrayListaProductos.length = 0;
                                    url = "http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/control/genRemisionCotizacionPDF.php?idCotizacion="+p.idCotizacion;
                                    $("<a>").attr("href", url).attr("target", "_blank")[0].click();
                                    setTimeout(function()
                                    {
                                        window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/index.php");
                                    }, 620);
                                    // $('#recibo').html(p.recibo).promise().done(function()
                                    // {
                                    //     //your callback logic / code here
                                    //     $('#recibo').printThis();
                                    //     setTimeout(function()
                                    //     {
                                    //         //dialogo.dialog( "close" );
                                    //         window.location.assign("http://<?php// echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/index.php");
                                    //     }, 2250);
                                    // });
                                }
                                else
                                {
                                    alert("No se puede guardar");
                                    dialogo.dialog( "close" );
                                }
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
                        "id": "btnCancelarCotizacion",
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
                    $("#inputBuscar").focus();
                },
                buttons:
                [
                    {
                        text: "Eliminar producto",
                        click: function()
                        {
                            item = $( "#dialog-confirm-eliminarItem" ).data('item');
                            y = arrayListaProductos.splice(item,1);
                            $(".trItemLista[name="+item+"]").remove();
                            $("#vistaPrevia").attr("src","");
                            $("#imgSrc").attr("src","");
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
            $( "#dialog-confirm-salir" ).dialog(
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
                        text: "Salir de aquí",
                        click: function()
                        {
                            arrayListaProductos.length = 0;
                            url = $( "#dialog-confirm-salir" ).data('url');
                            window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/"+url);
                        }
                    },
                    {
                        text: "Cancelar",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                            $("#inputBuscar").focus();
                        }
                    }
                ]
            });
            $( "#dialog-confirm-cambiar-factorconversion" ).dialog(
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
                            index = $( "#dialog-confirm-cambiar-factorconversion" ).data('index');
                            idProductoBuscar = arrayListaProductos[index]['id'];
                            for (var i = 0; i < arrayListaProductos.length; i++)
                            {
                                if (arrayListaProductos[i]['id'] == idProductoBuscar)
                                    arrayListaProductos[i]['factorconversion'] = 1;
                            }
                            //arrayListaProductos[index]['factorconversion'] = 1;
                            arrayListaProductos[index]["precioU"] = c.toFixed(2);
                            arrayListaProductos[index]["subTot"] = parseFloat(arrayListaProductos[index]["precioU"]) * parseFloat(arrayListaProductos[index]["cantidad"]);
                            reordenarItems(index);
                            $( this ).dialog( "close" );

                        }
                    },
                    {
                        text: "Cancelar",
                        click: function()
                        {
                            $( this ).dialog( "close" );
                            reordenarItems();
                            $("#inputBuscar").focus();
                        }
                    }
                ]
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
                    $("#inputBuscar").focus();
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
            $( "#dialog-agregar-cliente" ).dialog(
            {
                resizable: true,
                height: "auto",
                width: 850,
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
                    $("#divRespuestaCte").empty();
                },
                close: function()
                {
                    dialogoAbierto = 0;
                },
                buttons:
                [
                    {
                        text: "Agregar",
                        "id": "btnAgregarCliente",
                        click: function()
                        {
                            limpiarForm();
                            total = $("#hiddenMontoTotal").val();
                            btnAgregarCliente = $("#btnAgregarCliente");
                            btnCancelarCliente = $("#btnCancelarCliente");
                            btnAgregarCliente.html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                            btnAgregarCliente.prop("disabled", true);
                            btnCancelarCliente.prop("disabled", true);
                            dialogo = $(this);
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
                            var nuevoClienteJSON = JSON.stringify(objetoCliente);
                            $.ajax(
                            {
                                method: "POST",
                                url:"../control/agregarCliente.php",
                                data: {arrayCliente:nuevoClienteJSON}
                            })
                            .done(function(p)
                            {
                                if(p.status == 1)
                                {
                                    dialogo.dialog("close");
                                    idCliente = p.queCliente;
                                    $("#divRespuestaVta").html(p.respuesta);
                                    $("#divChkAbonos").show("fade",200);
                                    $("#divRespuestaCte").empty();
                                    $("#selectClientePos").append(new Option(p.rsocial, p.queCliente, true, true));
                                    $("#selectClientePos").parent().addClass("has-success");
                                    $("#selectCliente").append(new Option(p.rsocial, p.queCliente, true, true));
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
                                    if (p.s_direccion == 0)
                                        $("#inputDireccion").parent().addClass("has-error");
                                    if (p.s_telefono1 == 0)
                                        $("#inputTelefono1").parent().addClass("has-error");
                                    if (p.s_rfc == 0)
                                        $("#inputRfc").parent().addClass("has-error");
                                    if (p.s_email == 0)
                                        $("#inputEmail").parent().addClass("has-error");
                                    $("#divRespuestaCte").html(p.respuesta);
                                }
                            })
                            .fail(function()
                            {
                                alert("No se puede guardar en este momento. Consulte con el adminsitrador del sistema");
                                dialogo.dialog( "close" );
                                btnAgregarCliente.html('Agregar');
                                btnAgregarCliente.prop("disabled", false);
                                btnCancelarCliente.prop("disabled", false);
                            })
                            .always(function(p)
                            {
                                console.log(p);
                                btnAgregarCliente.html('Agregar');
                                btnAgregarCliente.prop("disabled", false);
                                btnCancelarCliente.prop("disabled", false);
                            })

                            //$( this ).dialog( "close" );
                        }
                    },
                    {
                        text: "Cancelar",
                        "id": "btnCancelarCliente",
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
            buscarUtilidadesMenos();

            // $.key('ctrl+f', function(e)
            // {
            //     e.preventDefault();
            //     pageWrapper = $("#page-wrapper");
            //     sideBar = $( ".sidebar" );
            //     if (sideBar.is(":visible"))
            //     {
            //         pageWrapper.animate({marginLeft: "0px"}, 300);
            //         sideBar.hide("drop",{ direction: "left" }, 300);
            //     }
            //     else
            //     {
            //         pageWrapper.animate({marginLeft: "250px"}, 300);
            //         sideBar.show("drop",{ direction: "left" }, 300);
            //     }
            // });
        });

    </script>
    <!--Modal balanza-->
    <div id="dialog-confirm-peso" class="dialog-oculto" title="Capturar peso">
        <p>
            <h1>
                <div class="form-group input-group">
                    <input type="text" class="form-control" id="inputPeso" style="text-align:right;" disabled="disabled">
                    <span class="input-group-addon"><b>Kg</b></span>
                </div>
            </h1>
        </p>
        <div id="divErrorBascula" style="display:none">
            *No se detecta la b&aacute;scula. Introduce el peso de forma manual
        </div>
    </div>
    <div id="dialog-buscar-producto" class="dialog-oculto" title="Buscar producto">
        <div id="divListaProducto" style="display:none">
        </div>
    </div>
    <div id="dialog-confirm-venta" class="dialog-oculto" title="¿Concluir venta?">
        <form role="form" id="form-confirm-venta">
            <div class="col-lg-12" id="divRespuestaVta" style="padding:0px">
            </div>
            <div class="row">
                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 text-left">
                    <h2 id="h3Remision"></h2>
                </div>
                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 text-left">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="chkImprimir" name="chkImprimir" checked style="width:18px;height:18px">Imprimir
                        </label>
                    </div>
                    <fieldset id="fieldOcultarPU" style="margin-top:-10px">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="chkOcultarPU" name="chkOcultarPU" style="width:18px;height:18px">Ocultar P. Unit
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>
            <p>
                <h4> Paga con: <h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" id="inputPagaCon" autofocus tyle="text-align:right;">
                </div>
            </p>
            <p>
                <h4> Cliente: </h4>
                <!--<h1><input type="number" class="form-control" disabled="disabled" id="inputCambio"></h1>-->
                <div class="form-group input-group">
                    <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i></span>
                    <select id="selectClientePos" name="selectClientePos" class="form-control">
                        <option value="1">==Venta al p&uacute;blico en gral==</option>
                    <?php
                        $sql = "SELECT
                                    id AS id,
                                    rsocial AS rsocial,
                                    tipoprecio AS tipoprecio
                                FROM clientes
                                WHERE id > 1
                                ORDER BY rsocial ASC";
                        if ($resultClientes = $mysqli->query($sql))
                        {
                            while($filaCliente = $resultClientes->fetch_assoc())
                            {
                                $idCliente = $filaCliente['id'];
                                $nombreCliente = $filaCliente['rsocial'];
                                echo "<option value='$idCliente'>$nombreCliente</option>";
                            }
                        }
                     ?>
                    </select>
                    <span class="input-group-addon" id="spanAgregarCliente" style="cursor:pointer;color:seagreen"><i class="fa fa-plus-square" aria-hidden="true"></i></span>
                </div>
            </p>
            <p>
                <h4> Total a pagar:<h4>
                <!--<h1><input type="number" class="form-control"  i></h1>-->
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control" id="inputMontoTotal" disabled="disabled" style="text-align:right;">
                </div>
                <input type="hidden" id="hiddenMontoTotal" name="hiddenMontoTotal">
                <input type="hidden" id="hiddenRemision" name="hiddenRemision" value="0">
            </p>
            <p>
                <h4>
                    <div class="checkbox text-center" id="divChkAbonos" style="display:none">
                        <input type="checkbox" id="chkAbonos" name="chkAbonos" style="width:22px;height:22px;"><span style="position:relative;margin-left:21px;">Pago en abonos</span>
                    </div>
                </h4>
            </p>
            <div style="display:none" id="divAbono">
                <h4> Abono: <h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="number" class="form-control" id="inputAbono" name="inputAbono" style="text-align:right;" disabled>
                </div>
                <h4> Adeudo:<h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control" id="inputAdeudo" name="inputAdeudo" value="0" disabled="disabled" style="text-align:right;">
                </div>
            </div>
            <p>
                <h4> Su cambio:<h4>
                <div class="form-group input-group">
                    <span class="input-group-addon">$</span>
                    <input type="text" class="form-control" value="0" disabled="disabled" id="inputCambio" name="inputCambio" style="text-align:right;">
                </div>
            </p>
        </form>
    </div>
    <div id="dialog-confirm-nota-salida" class="dialog-oculto" title="Nota de salida">
        <p>
            <i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas guardar esta lista como nota de salida y darla de baja del inventario?
        </p>
        <p>
            <h3>Observaciones</h3>
            <div class="form-group">
                <input type="text" class="form-control" id="inputObsNotaSalida">
            </div>
        </p>
    </div>
    <div id="dialog-confirm-cotizacion" class="dialog-oculto" title="¿Guardar cotizaci&oacute;n?">
        <p>
            <h3><i class="fa fa-question-circle" aria-hidden="true"></i> ¿Deseas guardar esta lista como cotizaci&oacute;n?</h3>
            <!--<h1><input type="number" class="form-control"  i></h1>-->
            <label>Cliente:</label>
            <div class="form-group input-group">
                <span class="input-group-addon"><i class="fa fa-male" aria-hidden="true"></i></span>
                <select id="selectClienteCotizacion" class="form-control">
                    <option value="1">==Venta al p&uacute;blico en gral==</option>
                <?php
                    $sql = "SELECT
                                id AS id,
                                rsocial AS rsocial,
                                tipoprecio AS tipoprecio
                            FROM clientes
                            WHERE id > 1
                            ORDER BY rsocial ASC";
                    if ($resultClientes = $mysqli->query($sql))
                    {
                        while($filaCliente = $resultClientes->fetch_assoc())
                        {
                            $idCliente = $filaCliente['id'];
                            $nombreCliente = $filaCliente['rsocial'];
                            echo "<option value='$idCliente'>$nombreCliente</option>";
                        }
                    }
                 ?>
                </select>
            </div>
        </p>
    </div>
    <div id="dialog-confirm-eliminarItem" class="dialog-oculto" title="Eliminar producto">
        <p>
            <h3>¿Eliminar producto?</h3>
        </p>
    </div>
    <div id="dialog-confirm-salir" class="dialog-oculto" title="Salir?">
        <p>
            <h3> <i class="fa fa-question-circle" aria-hidden="true"></i> Tienes una venta sin concluir.</h3>
            ¿Est&aacute;s seguro que quieres salir?</br>
            Nota: Esta lista se borrar&aacute;
        </p>
    </div>
    <div id="dialog-agregar-cliente" class="dialog-oculto" title="Agregar nuevo cliente">
        <p>
            <div class="col-lg-12" id="divRespuestaCte">
            </div>
            <h3><i class="fa fa-asterisk" aria-hidden="true"></i> Agregar nuevo cliente</h3>
            <div class="col-lg-12" id="divAgregarCliente"  style="padding-left:0px;padding-right:0px;">
            </div>
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
                    <input id="inputBuscarNombre" class="form-control" autofocus>
                </div>
            </div>
        </div>
    </div>
    <div id="dialog-confirm-cambiar-factorconversion" class="dialog-oculto" title="¿Romper dependencia?">
        <p>
            <h3> <i class="fa fa-question-circle" aria-hidden="true"></i> Este art&iacute;culo tiene un precio de venta diferente por paquete.</h3>
            ¿Deseas romper esta dependencia para poder modificar el precio manualmente?</br>
        </p>
    </div>
    <div id="dialog-Img" title="Imagen">
        <div class="col-lg-12 col-md-12 col-sm-12" style="text-align:center">
            <img id="imgSrc" class="img-thumbnail" style="width:100%;height:100%" src="">
        </div>
    </div>
    <!-- Trigger the modal with a button -->
    <button type="button" style="display:none" id="btnModalBuscarNombre" class="btn btn-info btn-lg" data-toggle="modal" data-target="#modal-auto-complete">Open Modal</button>

</body>
</html>
<?php } ?>
