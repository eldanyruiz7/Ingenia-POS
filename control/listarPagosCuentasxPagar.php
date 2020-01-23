<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require ('../conecta/bd.php');
require ("../conecta/sesion.class.php");
$sesion = new sesion();
require ("../conecta/cerrarOtrasSesiones.php");
require ("../conecta/usuarioLogeado.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $response = array(
        "status"        => 1
    );
    $idCompra = $_POST['idCompra'];
?>
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
<?php
    $sql = "SELECT
                fechahora AS fechaPago,
                monto AS monto,
                usuarios.nombre AS nombreUsuario
            FROM pagosemitidos
            INNER JOIN usuarios
            ON usuarios.id = pagosemitidos.usuario
            WHERE idCompra = $idCompra";
    $result = $mysqli->query($sql);
    if ($result->num_rows == 0)
    {
?>
        <tr>
            <td colspan="4">
                <span class="text-muted small"><em>No se ha hecho ning&uacute;n pago a este documento.</em>
                                    </span>
            </td>
        </tr>
<?php
    }
    else
    {
        $contPago = 1;
        while ($rowDoc = $result->fetch_assoc())
        {
?>
            <tr>
                <td><?php echo $contPago;?></td>
                <td><?php echo $rowDoc['fechaPago'];?></td>
                <td class="text-right"><?php echo "$".number_format($rowDoc['monto'],2,".",",");?></td>
            </tr>
<?php
            $contPago++;
        }
    }

?>
        </tbody>
    </table>
</div>
<!-- /.table-responsive -->
<?php
//    responder($response,$mysqli);
}
?>
