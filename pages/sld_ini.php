<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
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
        $error = 0;
        $sql = "SELECT saldoinicial FROM sesionescontrol WHERE id = $idSesion LIMIT 1";
        $res_saldo = $mysqli->query($sql);
        $row_saldo = $res_saldo->fetch_assoc();
        $saldoinicial = $row_saldo['saldoinicial'];
        if($saldoinicial != null)
            header("Location: /pventa_std/pages/index.php");
        elseif(isset($_POST['inputSaldoInicial']))
        {
            $saldoInicial = $_POST['inputSaldoInicial'];
            if(is_numeric($saldoInicial) && $saldoInicial >= 0)
            {
                $sql = "UPDATE sesionescontrol SET saldoinicial = $saldoInicial WHERE id = $idSesion LIMIT 1";
                $mysqli->query($sql);
                if ($mysqli->affected_rows == 1)
                {
                    header("Location: /pventa_std/pages/index.php");
                }
            }
            else
            {
                $error = 1;
            }
        }
        function validarFecha($date, $format)
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }
        $consultar = 1;
   // Aquí va el contenido de la pagina qu se mostrara en caso de que se haya iniciado sesion
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Saldo inicial</title>
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
    </style>
</head>
<body>

    <div class="row">
    </br>
    </br>
    </br>
        <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-8 col-xs-offset-2">
            <div class="panel panel-success">
<?php
                //$mañana = new DateTime('00:00:00');//fecha inicial
                //$noche = new DateTime('20:00:00');
                $nombreUsuario = $sesion->get("nombre");
                $horaActual = date('H:i:s');
                $horaActual = strtotime($horaActual);
                $manana_ini = strtotime('00:00:00');
                $manana_fin = strtotime('11:59:59');
                $tarde_ini = strtotime('12:00:00');
                $tarde_fin = strtotime('19:59:59');
                $noche_ini = strtotime('20:00:00');
                $noche_fin = strtotime('23:59:59');
                if($horaActual >= $manana_ini && $horaActual <= $manana_fin)
                    $saludo = 'Buenos d&iacute;as';
                elseif($horaActual >= $tarde_ini && $horaActual <= $tarde_fin)
                    $saludo = 'Buenas tardes';
                elseif($horaActual >= $noche_ini && $horaActual <= $noche_fin)
                    $saludo = 'Buenas noches';
                        ?>
                <div class="panel-heading">
                    <label class="lead" style="margin-bottom:5px"><i class="fa fa-user-circle" aria-hidden="true"></i> <?php echo "¡".$saludo." ".$nombreUsuario.", bienvenid@!"; ?></label>
                </div>
                <div class="panel-body">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <img src="../images/logo.jpg" width="100%" class="rounded float-left" alt="...">
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                        <p class="lead" style="text-align:center">Para iniciar por favor ingresa en el recuadro de abajo la cantidad en efectivo con el que abres caja y presiona "Continuar"</p>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12 text-center">
                        </br>
                        <i class="fa fa-5x fa-arrow-circle-down text-primary" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="panel-footer">
                    <form role="form" method="POST">
                        <div class="form-group input-group <?php echo ($error == 1) ? "has-error" : "";?>">
                            <span class="input-group-addon">$</span>
                            <input type="number" name="inputSaldoInicial" min="0" class="form-control" required style="text-align:right" autofocus>
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Continuar
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

    </div>
    <!-- /.row -->


    <!-- jQuery -->
    <script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../startbootstrap/vendor/bootstrap/js/bootstrap.min.js"></script>
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
    <script src="../startbootstrap/vendor/myJs/keyF1.js"></script>
    <script>
    </script>
</body>

</html>
<?php
}
?>
