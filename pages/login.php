<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
date_default_timezone_set('America/Mexico_City');
require ('../conecta/bd.php');
require ("../conecta/sesion.class.php");
$sesion = new sesion();
require ('../conecta/validarUsuario.php');
if( isset($_POST["btnIniciar"]) )
{
    $usuario                = $_POST["usuario"];
    $password               = $_POST["password"];
    $validar                = validarUsuario($usuario, $password, $mysqli);
    if($validar             != false)
    {
        $idUsuario          = $validar['id'];
        $fechaLogin         = date("Y-m-d H:i:s");
        $sql                = "SELECT id, saldoinicial FROM sesionescontrol WHERE usuario = $idUsuario AND timestampsalida IS NULL AND activo = 1 LIMIT 1";
        $result             = $mysqli->query($sql);
        $saldoinicial       = 'NULL';
        $sesionCambiar      = 0;
        if($result->num_rows > 0)
        {
            $rowUsr         = $result->fetch_assoc();
            $saldoinicial   = $rowUsr['saldoinicial'];
            $sesionCambiar  = $rowUsr['id'];
            $sql            = "UPDATE sesionescontrol SET timestampsalida = '$fechaLogin', activo = 0, saldoinicial = $saldoinicial, estado = 0 WHERE usuario = $idUsuario AND activo = 1";
            $mysqli->query($sql);
        }
        $sql                = "INSERT INTO sesionescontrol (timestampentrada, usuario, saldoinicial, activo)
                                VALUES ('$fechaLogin', $idUsuario, $saldoinicial, 1)";
        $mysqli->query($sql);
        $idSesion           =       $mysqli->insert_id;
        /*if($sesionCambiar != 0)
        {*/

        $sql        = "UPDATE retiros SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "UPDATE compras SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "UPDATE ventas SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "UPDATE pagosrecibidos SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "UPDATE pagosemitidos SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "UPDATE notacredito SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "UPDATE notadesalida SET sesion = $idSesion WHERE usuario = $idUsuario AND corte = 0";
        $mysqli->query($sql);
        $sql        = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
        $resConfig  = $mysqli->query($sql);
        $rowConfig  = $resConfig->fetch_assoc();

        //}
        $sesion->set("id",                  $idUsuario);
        $sesion->set("idsesion",            $idSesion);
        $sesion->set("nombre",              $validar['nombre']);
        $sesion->set("apellidop",           $validar['apellidop']);
        $sesion->set("apellidom",           $validar['apellidom']);
        $sesion->set("tipousuario",         $validar['tipousuario']);
        $sesion->set("nick",                $validar['nick']);
        $sesion->set("email",               $validar['email']);
        $sesion->set("celular",             $validar['celular']);
        $sesion->set("nombreComercio",      $rowConfig['nombreComercio']);
        $sesion->set("direccionComercio",   $rowConfig['direccionComercio']);
        $sesion->set("telefono1Comercio",   $rowConfig['telefono1Comercio']);
        $sesion->set("telefono2Comercio",   $rowConfig['telefono2Comercio']);
        $sesion->set("emailComercio",       $rowConfig['emailComercio']);

        header("location: /pventa_std/pages/index.php");
    }
}
if ($sesion->get("nick")!=false)
    header("location: /pventa_std/pages/index.php");
 ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Iniciar sesi&oacute;n</title>
        <!-- Bootstrap Core CSS -->
        <link href="../startbootstrap/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <!-- MetisMenu CSS -->
        <link href="../startbootstrap/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="../startbootstrap/dist/css/sb-admin-2.css" rel="stylesheet">
        <!-- Custom Fonts -->
        <link href="../startbootstrap/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            body
            {
                background: url("../images/tienda.jpg");
                background-size: cover;
                background-repeat: no-repeat;
            }
        </style>
    </head>
    <body>

        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                    <div class='<?php echo (isset($_POST["btnIniciar"])) ? "login-panel panel panel-red" : "login-panel panel panel-primary";?>' style="background: rgba(255,255,255,0.4);">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo (isset($_POST["btnIniciar"])) ? "Nombre de usuario o contraseña incorrectos" : "Ingresa tus datos para iniciar";?></h3>
                        </div>
                        <div class="panel-body">
                            <form role="form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                                <fieldset>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Nombre usuario" name="usuario" type="text" autofocus>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                    </div>

                                    <!-- Change this to a button or input when using this as a form -->
                                    <input type="submit" name="btnIniciar" value="Entrar" class="btn btn-lg btn-success btn-block">
                                    <!--<a href="index.html" class="btn btn-lg btn-success btn-block"><i class="fa fa-sign-in" aria-hidden="true"></i> Entrar</a>-->
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery -->
        <script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="../startbootstrap/vendor/bootstrap/js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="../startbootstrap/vendor/metisMenu/metisMenu.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="../startbootstrap/dist/js/sb-admin-2.js"></script>
    </body>
</html>
