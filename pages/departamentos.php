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
        /*if($sesion->get("tipousuario") == 2)
            header("location: /rest/orden/index.php");*/
   // Aquí va el contenido de la pagina qu se mostrara en caso de que se haya iniciado sesion
?>
<!DOCTYPE html>
<html lang="en">
<?php //$ipserver = $_SERVER['SERVER_ADDR']; ?>
<?php //require_once 'conecta/bd.php';?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Editor departamentos</title>
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
        .dataTables_filter
        {
            text-align: right;
        }
        #tablaG_filter
        {
            display: none;
        }
        </style>
    </style>
</head>
<body>
    <div id="recibo" style="display:none"></div>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6">
                    <h1 class="page-header"> <i class="fa fa-users" aria-hidden="true"></i> Editor de departamentos</h1>
                </div>
                <div id="respuesta" class="col-lg-6 col-md-6 col-xs-6">

                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-10 col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            DEPARTAMENTOS
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu slidedown">
                                    <li>
                                        <a href="#" id="menu-nuevoDepartamento"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Agregar departamento </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="col-lg-12 col-md-12" id="tableTarget">
                                <table id="tablaG" width="100%" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center"></th>
                                            <th class="text-left">ID</th>
                                            <th class="text-left">Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php
                                $sql = "SELECT * FROM departamentos WHERE activo = 1 ORDER BY id ASC";
                                $resultG = $mysqli->query($sql);
                                while ($rowG = $resultG->fetch_assoc())
                                {
                                ?>
                                        <tr class="trOrden" name="<?php echo $rowG['id'];?>">
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                        <i class="fa fa-chevron-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu slidedown">
                                                        <li class="menuEliminarP" name="<?php echo $rowG['id'];?>">
                                                            <a>
                                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td class="text-left"><?php echo $rowG['id'];?></td>
                                            <td class="text-left tdNombreU"><?php echo $rowG['nombre'];?></td>
                                        </tr>
                                <?php
                                }
                                ?>
                                    </tbody>
                                </table>

                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
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
    <!-- BootstraCore JavaScript -->
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
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
    cache       = "";
    cacheNuevo  = "";
    function actualizarNombre(input)
    {
        id = input.parent().parent().attr("name");
        cacheNuevo = input.val();
        input.parent().addClass("tdNombreU");
        input.parent().html(cacheNuevo);
        input.focus();
        $.ajax(
            {
                method: "POST",
                url: "../control/actualizarNombreDepartamento.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/departamentos.php");
            });
    }
    function revertirNombre(input)
    {
        //cache = input.val();
        input.parent().addClass("tdNombreU");
        input.parent().html(cache);
        input.focus();
    }
    // function actualizarApellidoP(input)
    // {
    //     id = input.parent().parent().attr("name");
    //     cacheNuevo = input.val();
    //     input.parent().addClass("tdApellidoP");
    //     input.parent().html(cacheNuevo);
    //     input.focus();
    //     $.ajax(
    //         {
    //             method: "POST",
    //             url: "../control/actualizarApellidoPUsuario.php",
    //             data: {id:id,cacheNuevo:cacheNuevo}
    //         }).done(function()
    //         {
    //             window.location.assign("http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
    //         });
    // }
    // function revertirApellidoP(input)
    // {
    //     //cache = input.val();
    //     input.parent().addClass("tdApellidoP");
    //     input.parent().html(cache);
    //     input.focus();
    // }
    // function actualizarApellidoM(input)
    // {
    //     id = input.parent().parent().attr("name");
    //     cacheNuevo = input.val();
    //     input.parent().addClass("tdApellidoM");
    //     input.parent().html(cacheNuevo);
    //     input.focus();
    //     $.ajax(
    //         {
    //             method: "POST",
    //             url: "../control/actualizarApellidoMUsuario.php",
    //             data: {id:id,cacheNuevo:cacheNuevo}
    //         }).done(function()
    //         {
    //             window.location.assign("http://<?php// echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
    //         });
    // }
    // function revertirApellidoM(input)
    // {
    //     //cache = input.val();
    //     input.parent().addClass("tdApellidoM");
    //     input.parent().html(cache);
    //     input.focus();
    // }
    // function actualizarNick(input)
    // {
    //     id = input.parent().parent().attr("name");
    //     cacheNuevo = input.val();
    //     input.parent().addClass("tdNickU");
    //     input.parent().html(cacheNuevo);
    //     input.focus();
    //     $.ajax(
    //         {
    //             method: "POST",
    //             url: "../control/actualizarNickUsuario.php",
    //             data: {id:id,cacheNuevo:cacheNuevo}
    //         }).done(function()
    //         {
    //             window.location.assign("http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
    //         });
    // }
    // function revertirNick(input)
    // {
    //     //cache = input.val();
    //     input.parent().addClass("tdNickU");
    //     input.parent().html(cache);
    //     input.focus();
    // }
    // function actualizarEmail(input)
    // {
    //     id = input.parent().parent().attr("name");
    //     cacheNuevo = input.val();
    //     input.parent().addClass("tdEmail");
    //     input.parent().html(cacheNuevo);
    //     input.focus();
    //     $.ajax(
    //         {
    //             method: "POST",
    //             url: "../control/actualizarEmailUsuario.php",
    //             data: {id:id,cacheNuevo:cacheNuevo}
    //         }).done(function()
    //         {
    //             window.location.assign("http://<?php //echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
    //         });
    // }
    // function revertirEmail(input)
    // {
    //     //cache = input.val();
    //     input.parent().addClass("tdEmail");
    //     input.parent().html(cache);
    //     input.focus();
    // }
    // function actualizarCelular(input)
    // {
    //     id = input.parent().parent().attr("name");
    //     cacheNuevo = input.val();
    //     input.parent().addClass("td");
    //     input.parent().html(cacheNuevo);
    //     input.focus();
    //     $.ajax(
    //         {
    //             method: "POST",
    //             url: "../control/actualizarCelularUsuario.php",
    //             data: {id:id,cacheNuevo:cacheNuevo}
    //         }).done(function()
    //         {
    //             window.location.assign("http://<?php// echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
    //         });
    // }
    // function revertirCelular(input)
    // {
    //     //cache = input.val();
    //     input.parent().addClass("tdCelularU");
    //     input.parent().html(cache);
    //     input.focus();
    // }
    $(document).ready(function()
    {
        $("#menu-nuevoDepartamento").click(function(e)
        {
            e.preventDefault();
            $( "#dialog-nuevo-departamento" ).dialog("open");
        });
        $(".menuEliminarP").click(function()
        {
            id = $(this).attr("name");
            $( "#dialog-eliminar-departamento" ).data('id',id).dialog("open");
        });
        $( "#dialog-nuevo-departamento" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
            modal: true,
            autoOpen: false,
            show:
            {
                effect: "scale",
                duration: 250
            },
            open: function()
            {
                dialogo = $(this);
            },
            close: function()
            {

            },
            buttons:
            [
                {
                    text: 'CREAR DEPARTAMENTO',
                    "id": 'btnCrearGrupo',
                    click: function()
                    {
                        //idOrden = $( "#dialog-pagar-orden" ).data('idOrden');
                        if($("#inputNombreDepartamento").val().length < 1)
                            return false;
                        $("#btnCrearGrupo").prop("disabled","disabled");
                        $("#btnCancelarGrupo").prop("disabled","disabled");
                        nombre      = $("#inputNombreDepartamento").val();

                        $.ajax(
                        {
                           method: "POST",
                           url: "../control/crearDepartamento.php",
                           data: {nombre:nombre}
                        }).done(function(p)
                        {

                           if(p.status == 1)
                           {
                               window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/departamentos.php");

                           }
                           else
                           {
                               $("#btnCrearGrupo").removeAttr("disabled");
                               $("#btnCancelarGrupo").removeAttr("disabled");
                               $("#respuesta").html(p.respuesta);
                               dialogo.dialog( "close" );
                               console.log(p);
                           }
                       })
                       .always(function(p)
                       {
                           console.log(p);
                       });

                    }
                },
                {
                    text: "Cancelar",
                    id: "btnCancelarGrupo",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]

        });
        $( "#dialog-eliminar-departamento" ).dialog(
        {
            resizable: false,
            height: "auto",
            width: "auto",
            modal: true,
            autoOpen: false,
            show:
            {
                effect: "scale",
                duration: 250
            },
            open: function()
            {
                dialogo = $(this);
            },
            close: function()
            {

            },
            buttons:
            [
                {
                    text: 'ELIMINAR',
                    "id": 'btnEliminarGrupo',
                    click: function()
                    {
                        $("#btnEliminarGrupo").prop("disabled","disabled");
                        $("#btnCancelarEliminarGrupo").prop("disabled","disabled");

                        id = $( "#dialog-eliminar-departamento" ).data('id');
                        $.ajax(
                            {
                               method: "POST",
                               url: "../control/eliminarDepartamento.php",
                               data: {id:id}
                           }).done(function(p)
                           {
                               if(p.status == 1)
                               {
                                   window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/departamentos.php");

                               }
                               else
                               {
                                   $("#btnEliminarGrupo").removeAttr("disabled");
                                   $("#btnCancelarEliminarGrupo").removeAttr("disabled");
                                   $("#respuesta").html(p.respuesta);
                                   dialogo.dialog( "close" );
                                   console.log(p);
                               }
                           })
                           .always(function(p)
                           {
                               console.log(p);
                           });

                    }
                },
                {
                    text: "Cancelar",
                    id: "btnCancelarEliminarGrupo",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]

        });
////////////////// MODIFICAR NOMBRE DEPARTAMENTO  /////////////////////////
        $("#tablaG").on("dblclick",".tdNombreU",function()
        {
            td = $(this);
            td.removeClass("tdNombreU");
            cache = td.html();
            td.empty();
            td.html('<input type="text" class="inputCacheNombre" value="'+cache+'">');
        });
        $("#tablaG").on("focusout",".inputCacheNombre",function()
        {
            if($(this).val().length == 0)
                revertirNombre($(this));
            else
                actualizarNombre($(this));
        });
        $("#tablaG").on("keydown",".inputCacheNombre",function(e)
        {
            if(e.keyCode == 13)
                if($(this).val().length == 0)
                    revertirNombre($(this));
                else
                    actualizarNombre($(this));
        });

        $('#tablaG').DataTable(
        {
            "lengthMenu": [[-1],["Todos"]],
            "order": [[ 1, "asc" ]],
            "language":
            {
                "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
            },
            responsive: true
        });
    });
    </script>
    <div id="dialog-eliminar-departamento" title="ELIMINAR DEPARTAMENTO DEL SISTEMA">
        <p>
            <div class="form-group">
                <label><i class="fa fa-question-circle" aria-hidden="true"></i> Est&aacute;s seguro que quieres eliminar el departamento seleccionado?</label>
            </div>
        </p>
    </div>
    <div id="dialog-nuevo-departamento" title="DATOS DEL NUEVO DEPARTAMENTO">
        <p>
            <div class="form-group">
                <label>Nombre</label>
                <input class="form-control" type="text" id="inputNombreDepartamento">
                <p class="help-block">Nombre del nuevo departamento</p>
            </div>
        </p>

    </div>
</body>

</html>
<?php } ?>
