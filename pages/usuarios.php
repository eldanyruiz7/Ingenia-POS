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

    <title>Editor usuarios</title>
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
                    <h1 class="page-header"> <i class="fa fa-users" aria-hidden="true"></i> Editor de usuarios</h1>
                </div>
                <div id="respuesta" class="col-lg-6 col-md-6 col-xs-6">

                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-10 col-md-10">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            USUARIOS
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu slidedown">
                                    <li>
                                        <a href="#" id="menu-nuevoUsuario"> <i class="fa fa-plus-circle" aria-hidden="true"></i> Agregar usuario </a>
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
                                            <th class="text-left">Apellido Paterno</th>
                                            <th class="text-left">Apellido Materno</th>
                                            <th class="text-left">Nick Name</th>
                                            <th class="text-left">Celular</th>
                                            <th class="text-left">E-mail</th>
                                            <th class="text-left">Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php
                                $sql = "SELECT * FROM usuarios WHERE activo = 1 ORDER BY id ASC";
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
                                                        <li class="menuCambiarPwd" name="<?php echo $rowG['id'];?>">
                                                            <a>
                                                                <i class="fa fa-key" aria-hidden="true"></i> Cambiar password
                                                            </a>
                                                        </li>
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
                                            <td class="text-left tdApellidoP"><?php echo $rowG['apellidop'];?></td>
                                            <td class="text-left tdApellidoM"><?php echo $rowG['apellidom'];?></td>
                                            <td class="text-left tdNickU"><?php echo $rowG['nick'];?></td>
                                            <td class="text-left tdCelularU"><?php echo $rowG['celular'];?></td>
                                            <td class="text-left tdEmail"><?php echo $rowG['email'];?></td>
                                            <td class="text-left"><?php echo ($rowG['tipousuario'] == 1) ? '<span class="label label-primary">Admin</span>'  : '<span class="label label-success">Normal</span>';?></td>
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
                url: "../control/actualizarNombreUsuario.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
            });
    }
    function revertirNombre(input)
    {
        //cache = input.val();
        input.parent().addClass("tdNombreU");
        input.parent().html(cache);
        input.focus();
    }
    function actualizarApellidoP(input)
    {
        id = input.parent().parent().attr("name");
        cacheNuevo = input.val();
        input.parent().addClass("tdApellidoP");
        input.parent().html(cacheNuevo);
        input.focus();
        $.ajax(
            {
                method: "POST",
                url: "../control/actualizarApellidoPUsuario.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
            });
    }
    function revertirApellidoP(input)
    {
        //cache = input.val();
        input.parent().addClass("tdApellidoP");
        input.parent().html(cache);
        input.focus();
    }
    function actualizarApellidoM(input)
    {
        id = input.parent().parent().attr("name");
        cacheNuevo = input.val();
        input.parent().addClass("tdApellidoM");
        input.parent().html(cacheNuevo);
        input.focus();
        $.ajax(
            {
                method: "POST",
                url: "../control/actualizarApellidoMUsuario.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
            });
    }
    function revertirApellidoM(input)
    {
        //cache = input.val();
        input.parent().addClass("tdApellidoM");
        input.parent().html(cache);
        input.focus();
    }
    function actualizarNick(input)
    {
        id = input.parent().parent().attr("name");
        cacheNuevo = input.val();
        input.parent().addClass("tdNickU");
        input.parent().html(cacheNuevo);
        input.focus();
        $.ajax(
            {
                method: "POST",
                url: "../control/actualizarNickUsuario.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
            });
    }
    function revertirNick(input)
    {
        //cache = input.val();
        input.parent().addClass("tdNickU");
        input.parent().html(cache);
        input.focus();
    }
    function actualizarEmail(input)
    {
        id = input.parent().parent().attr("name");
        cacheNuevo = input.val();
        input.parent().addClass("tdEmail");
        input.parent().html(cacheNuevo);
        input.focus();
        $.ajax(
            {
                method: "POST",
                url: "../control/actualizarEmailUsuario.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
            });
    }
    function revertirEmail(input)
    {
        //cache = input.val();
        input.parent().addClass("tdEmail");
        input.parent().html(cache);
        input.focus();
    }
    function actualizarCelular(input)
    {
        id = input.parent().parent().attr("name");
        cacheNuevo = input.val();
        input.parent().addClass("td");
        input.parent().html(cacheNuevo);
        input.focus();
        $.ajax(
            {
                method: "POST",
                url: "../control/actualizarCelularUsuario.php",
                data: {id:id,cacheNuevo:cacheNuevo}
            }).done(function()
            {
                window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");
            });
    }
    function revertirCelular(input)
    {
        //cache = input.val();
        input.parent().addClass("tdCelularU");
        input.parent().html(cache);
        input.focus();
    }
    $(document).ready(function()
    {
        $(".menuCambiarPwd").click(function()
        {
            id = $(this).attr("name");
            $( "#dialog-cambiar-pwd" ).data('id',id).dialog("open");
        });
        $("#menu-nuevoUsuario").click(function(e)
        {
            e.preventDefault();
            $( "#dialog-nuevo-usuario" ).dialog("open");
        });
        $(".menuEliminarP").click(function()
        {
            id = $(this).attr("name");
            $( "#dialog-eliminar-usuario" ).data('id',id).dialog("open");
        });
        $(".menuDesactivarG").click(function()
        {
            id = $(this).attr("name");
            $( "#dialog-desactivar-grupo" ).data('id',id).dialog("open");
        });
        $( "#dialog-nuevo-usuario" ).dialog(
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
                    text: 'CREAR',
                    "id": 'btnCrearGrupo',
                    click: function()
                    {
                        //idOrden = $( "#dialog-pagar-orden" ).data('idOrden');
                        if($("#inputNombreUsuario").val().length < 1 || $("#inputApellidoPUsuario").val().length < 1 || $("#inputNickUsuario").val().length < 1 || $("#inputCelularUsuario").val().length < 1)
                            return false;
                        $("#btnCrearGrupo").prop("disabled","disabled");
                        $("#btnCancelarGrupo").prop("disabled","disabled");
                        nombre      = $("#inputNombreUsuario").val();
                        apellidop   = $("#inputApellidoPUsuario").val();
                        apellidom   = $("#inputApellidoMUsuario").val();
                        nick        = $("#inputNickUsuario").val();
                        celular     = $("#inputCelularUsuario").val();
                        email       = $("#inputEmailUsuario").val();
                        tipo        = $("#selectTipoUsuario").val();
                        pwd3        = $("#inputPassword3").val();
                        pwd4        = $("#inputPassword4").val();
                        if(pwd3 === pwd4)
                        {
                            $.ajax(
                                {
                                   method: "POST",
                                   url: "../control/crearUsuario.php",
                                   data: {nombre:nombre,apellidop:apellidop,apellidom:apellidom,nick:nick,celular:celular,email:email,tipo:tipo,pwd:pwd3}
                               }).done(function(p)
                               {

                                   if(p.status == 1)
                                   {
                                       window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");

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
                        else
                        {
                            $("#btnCrearGrupo").removeAttr("disabled");
                            $("#btnCancelarGrupo").removeAttr("disabled");
                            $("#respuesta").html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Los passwords no coinciden.</div>');
                            dialogo.dialog( "close" );
                        }
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
        $( "#dialog-cambiar-pwd" ).dialog(
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
                    text: 'CAMBIAR',
                    "id": 'btnCambiarPwd',
                    click: function()
                    {
                        $("#btnCambiarPwd").prop("disabled","disabled");
                        $("#btnCancelarPwd").prop("disabled","disabled");
                        if($("#inputPassword1").val() === $("#inputPassword2").val())
                        {
                            id = $( "#dialog-cambiar-pwd" ).data('id');
                            pwd = $("#inputPassword1").val();
                            $.ajax(
                                {
                                   method: "POST",
                                   url: "../control/cambiarPwd.php",
                                   data: {id:id,pwd:pwd}
                               }).done(function(p)
                               {
                                   if(p.status == 1)
                                   {

                                       //window.location.assign("http://<?php //echo $_SERVER['HTTP_HOST'];?>/rest/usuarios.php");
                                       $("#btnCambiarPwd").removeAttr("disabled");
                                       $("#btnCancelarPwd").removeAttr("disabled");
                                       $("#respuesta").html(p.respuesta);
                                       dialogo.dialog( "close" );
                                       $("#inputPassword1").val('');
                                       $("#inputPassword2").val('');
                                   }
                                   else
                                   {
                                       $("#btnCambiarPwd").removeAttr("disabled");
                                       $("#btnCancelarPwd").removeAttr("disabled");
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
                        else
                        {
                            $("#respuesta").html('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Los campos no coinciden.</div>');
                            $("#btnCambiarPwd").removeAttr("disabled");
                            $("#btnCancelarPwd").removeAttr("disabled");
                            dialogo.dialog( "close" );
                        }
                    }
                },
                {
                    text: "Cancelar",
                    id: "btnCancelarPwd",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]

        });
        $( "#dialog-desactivar-grupo" ).dialog(
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
                    text: 'DESACTIVAR',
                    "id": 'btnDesactivarGrupo',
                    click: function()
                    {
                        $("#btnDesactivarGrupo").prop("disabled","disabled");
                        $("#btnCancelarDesactivarGrupo").prop("disabled","disabled");

                        id = $( "#dialog-desactivar-grupo" ).data('id');
                        $.ajax(
                            {
                               method: "POST",
                               url: "control/desactivarGrupo.php",
                               data: {id:id}
                           }).done(function(p)
                           {
                               if(p.status == 1)
                               {
                                   window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/rest/grupos.php");

                               }
                               else
                               {
                                   $("#btnDesactivarGrupo").removeAttr("disabled");
                                   $("#btnCancelarDesactivarGrupo").removeAttr("disabled");
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
                    id: "btnCancelarDesactivarGrupo",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]

        });
        $( "#dialog-eliminar-usuario" ).dialog(
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

                        id = $( "#dialog-eliminar-usuario" ).data('id');
                        $.ajax(
                            {
                               method: "POST",
                               url: "../control/eliminarUsuario.php",
                               data: {id:id}
                           }).done(function(p)
                           {
                               if(p.status == 1)
                               {
                                   window.location.assign("http://<?php echo $_SERVER['HTTP_HOST'];?>/pventa_std/pages/usuarios.php");

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
////////////////// MODIFICAR NOMBRE USUARIO  /////////////////////////
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
////////////////// MODIFICAR APELLIDO MATERNO USUARIO  /////////////////////////

        $("#tablaG").on("dblclick",".tdApellidoM",function()
        {
            td = $(this);
            td.removeClass("tdApellidoM");
            cache = td.html();
            td.empty();
            td.html('<input type="text" class="inputCacheApellidoM" value="'+cache+'">');
        });
        $("#tablaG").on("focusout",".inputCacheApellidoM",function()
        {
            if($(this).val().length == 0)
                revertirApellidoM($(this));
            else
                actualizarApellidoM($(this));
        });
        $("#tablaG").on("keydown",".inputCacheApellidoM",function(e)
        {
            if(e.keyCode == 13)
                if($(this).val().length == 0)
                    revertirApellidoM($(this));
                else
                    actualizarApellidoM($(this));
        });
////////////////// MODIFICAR APELLIDO PATERNO USUARIO  /////////////////////////
        $("#tablaG").on("dblclick",".tdApellidoP",function()
        {
            td = $(this);
            td.removeClass("tdApellidoP");
            cache = td.html();
            td.empty();
            td.html('<input type="text" class="inputCacheApellidoP" value="'+cache+'">');
        });
        $("#tablaG").on("focusout",".inputCacheApellidoP",function()
        {
            if($(this).val().length == 0)
                revertirApellidoP($(this));
            else
                actualizarApellidoP($(this));
        });
        $("#tablaG").on("keydown",".inputCacheApellidoP",function(e)
        {
            if(e.keyCode == 13)
                if($(this).val().length == 0)
                    revertirApellidoP($(this));
                else
                    actualizarApellidoP($(this));
        });
////////////////// MODIFICAR NICK USUARIO  /////////////////////////
        $("#tablaG").on("dblclick",".tdNickU",function()
        {
            td = $(this);
            td.removeClass("tdNickU");
            cache = td.html();
            td.empty();
            td.html('<input type="text" class="inputCacheNick" value="'+cache+'">');
        });
        $("#tablaG").on("focusout",".inputCacheNick",function()
        {
            if($(this).val().length == 0)
                revertirNick($(this));
            else
                actualizarNick($(this));
        });
        $("#tablaG").on("keydown",".inputCacheNick",function(e)
        {
            if(e.keyCode == 13)
                if($(this).val().length == 0)
                    revertirNick($(this));
                else
                    actualizarNick($(this));
        });
////////////////// MODIFICAR CELULAR USUARIO  /////////////////////////
        $("#tablaG").on("dblclick",".tdCelularU",function()
        {
            td = $(this);
            td.removeClass("tdCelularU");
            cache = td.html();
            td.empty();
            td.html('<input type="text" class="inputCacheCelular" value="'+cache+'">');
        });
        $("#tablaG").on("focusout",".inputCacheCelular",function()
        {
            if($(this).val().length == 0)
                revertirCelular($(this));
            else
                actualizarCelular($(this));
        });
        $("#tablaG").on("keydown",".inputCacheCelular",function(e)
        {
            if(e.keyCode == 13)
                if($(this).val().length == 0)
                    revertirCelular($(this));
                else
                    actualizarCelular($(this));
        });
////////////////// MODIFICAR EMAIL USUARIO  /////////////////////////
        $("#tablaG").on("dblclick",".tdEmail",function()
        {
            td = $(this);
            td.removeClass("tdEmail");
            cache = td.html();
            td.empty();
            td.html('<input type="text" class="inputCacheEmail" value="'+cache+'">');
        });
        $("#tablaG").on("focusout",".inputCacheEmail",function()
        {
            if($(this).val().length == 0)
                revertirEmail($(this));
            else
                actualizarEmail($(this));
        });
        $("#tablaG").on("keydown",".inputCacheEmail",function(e)
        {
            if(e.keyCode == 13)
                if($(this).val().length == 0)
                    revertirEmail($(this));
                else
                    actualizarEmail($(this));
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
    <div id="dialog-eliminar-usuario" title="ELIMINAR USUARIO DEL SISTEMA">
        <p>
            <div class="form-group">
                <label><i class="fa fa-question-circle" aria-hidden="true"></i> Est&aacute;s seguro que quieres eliminar el usuario seleccionado?</label>
                <p class="help-block">Solo el Administrador del Sistema podr&iacute;a deshacer esta acci&oacute;n</p>
            </div>
        </p>
    </div>
    <div id="dialog-cambiar-pwd" title="CAMBIAR EL PASSWORD">
        <p>
            <div class="form-group">
                <label><i class="fa fa-lock" aria-hidden="true"></i> Escribe el nuevo password</label>
                <input class="form-control" type="password" id="inputPassword1">
                <br>
                <label><i class="fa fa-lock" aria-hidden="true"></i> Una vez m&aacute;s</label>
                <input class="form-control" type="password" id="inputPassword2">
                <p class="help-block">Escribe el nuevo password dos veces para poder cambiarlo</p>
            </div>
        </p>
    </div>
    <div id="dialog-nuevo-usuario" title="DATOS DEL NUEVO USUARIO">
        <p>
            <div class="form-group">
                <label>Nombre</label>
                <input class="form-control" type="text" id="inputNombreUsuario">
                <p class="help-block">Nombre del nuevo usuario</p>
            </div>
            <div class="form-group">
                <label>Apellido Paterno</label>
                <input class="form-control" type="text" id="inputApellidoPUsuario">
                <p class="help-block">Apellido paterno</p>
            </div>
            <div class="form-group">
                <label>Apellido Materno</label>
                <input class="form-control" type="text" id="inputApellidoMUsuario">
                <p class="help-block">Apellido materno</p>
            </div>
            <div class="form-group">
                <label>Nick Name</label>
                <input class="form-control" type="text" id="inputNickUsuario">
                <p class="help-block">Como aparecer&aacute; en los tickets y para iniciar sesi&oacute;n</p>
            </div>
            <div class="form-group">
                <label>No. Celular</label>
                <input class="form-control" type="text" id="inputCelularUsuario">
                <p class="help-block">No. Celular</p>
            </div>
            <div class="form-group">
                <label>E mail</label>
                <input class="form-control" type="text" id="inputEmailUsuario">
                <p class="help-block">Correo electrónico</p>
            </div>
            <div class="form-group">
                <label>Tipo</label>
                <select class="form-control" id="selectTipoUsuario">
                    <option value="1">Administrador</option>
                    <option value="2">Cajero</option>
                </select>
                <p class="help-block">El grupo de usuarios al que pertenecer&aacute;</p>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input class="form-control" type="password" id="inputPassword3">
            </div>
            <div class="form-group">
                <label>Repite el Password</label>
                <input class="form-control" type="password" id="inputPassword4">
                <p class="help-block">&nbsp;</p>
            </div>
        </p>

    </div>
</body>

</html>
<?php } ?>
