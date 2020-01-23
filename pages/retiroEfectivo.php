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
    $pagina = 8;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Retiro de efectivo</title>
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
    </style>
</head>
<body>
    <div id="wrapper">
<?php include "nav.php" ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-xs-6 col-xs-6">
                    <h1 class="page-header"> <i class="fa fa-arrow-left" aria-hidden="true"></i> Retiro de efectivo</h1>
                </div>
                <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
                <div id="divRespuesta" class="col-lg-6 col-md-6 col-xs-6 col-xs-6">
                </div>
            </div>
            <!-- /.row -->
            <div class="row" id="Cnt">
                <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                    <div class='login-panel panel panel-primary' style="background: rgba(255,255,255,0.4);">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-key fa-pull-left" aria-hidden="true"></i> <?php echo (isset($_POST["btnIniciar"])) ? "Nombre de usuario o contraseña incorrectos" : "Ingresa como administrador para continuar";?></h3>
                        </div>
                        <div class="panel-body">
                            <form role="form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" id="formAcceso">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Nombre usuario" name="usuario" type="text" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" id="password" placeholder="Password" name="password" type="password" value="">
                                    </div>
                                    <input type="button" id="btnIniciar" name="btnIniciar" value="Entrar" class="btn btn-lg btn-success btn-block">
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery.hotkeys.js"></script>
    <script src="../startbootstrap/vendor/myJs/hotKeys.js"></script>
    <script>
        $(document).ready(function()
        {
            $("#password").keydown(function(e)
            {
                if(e.keyCode == 13)
                    $("#btnIniciar").click();
            });
            $("#btnIniciar").click(function()
            {
                $.ajax(
                    {
                        type: "POST",
                        url: "../control/retiroEfectivoCtrl.php",
                        data: $("#formAcceso").serialize(),
                    }).done(function(p)
                    {
                        console.log(p);
                        if(p.status == 0)
                        {
                            $("#page-wrapper > div:nth-child(2) > div > div > div.panel-heading > h3").html(p.respuesta);
                        }
                        else
                        {
                            $("#Cnt").html(p);
                        }
                    }).fail(function()
                    {
                        alert("Servidor no disponible. Favor de consultarlo con el administrador del sistema");
                    });
            });

        });

    </script>

</body>

</html>


<?php
}
?>
