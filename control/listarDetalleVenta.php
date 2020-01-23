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
    $idVenta = $_POST['idVenta'];
?>
<div class="table-responsive" style="max-height:550px !important;">
    <table class="table table-bordered table-condensed table-hover table-striped">
        <thead>
            <tr>
                <th>Cant</th>
                <th>Nombre</th>
                <th>PUnit</th>
                <th>SubTot</th>
                <th>Fact</th>
            </tr>
        </thead>
        <tbody>
<?php
    $sql = "SELECT
                detalleventa.cantidad       AS cantidad,
                detalleventa.precio         AS pUnit,
                detalleventa.subTotal       AS subTot,
                detalleventa.facturable     AS facturable,
                detalleventa.facturado      AS facturado,
                productos.nombrecorto       AS nombre
            FROM detalleventa
            INNER JOIN productos
            ON detalleventa.producto = productos.id
            WHERE detalleventa.venta = $idVenta AND detalleventa.activo = 1";
    $result = $mysqli->query($sql);
    $arts = $result->num_rows;
    if ($arts == 0)
    {
?>
        <tr>
            <td colspan="4">
                <span class="text-muted small">
                    <em>No existen art&iacute;culos para la venta seleccionada.</em>
                </span>
            </td>
        </tr>
<?php
    }
    else
    {
        while ($rowDoc = $result->fetch_assoc())
        {
?>
            <tr>
                <td><?php echo number_format($rowDoc['cantidad'],3,".",",");?></td>
                <td><?php echo $rowDoc['nombre'];?></td>
                <td><?php echo "$".number_format($rowDoc['pUnit'],2,".",",");?></td>
                <td><?php echo "$".number_format($rowDoc['subTot'],2,".",",");?></td>
<?php
            if ($rowDoc['facturado'] == 1)
                $checkFact = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            else
                $checkFact = ($rowDoc['facturable'] == 1) ? '<i class="fa fa-square-o" aria-hidden="true"></i>' : '<i class="fa fa-ban" aria-hidden="true"></i>';
?>
                <td class="text-center"><?php echo $checkFact; ?></td>
            </tr>
<?php
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
