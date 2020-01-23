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
<div class="table-responsive" style="max-height:550px !important;">
    <table class="table table-bordered table-condensed table-hover table-striped">
        <thead>
            <tr>
                <th>Cant</th>
                <th>Nombre</th>
                <th>PUnit</th>
                <th>SubTot</th>
            </tr>
        </thead>
        <tbody>
<?php
    $sql = "SELECT
                detallecompra.cantidad      AS cantidad,
                detallecompra.preciolista   AS pUnit,
                detallecompra.subTotal      AS subTot,
                productos.nombrecorto       AS nombre
            FROM detallecompra
            INNER JOIN productos
            ON  detallecompra.producto = productos.id
            WHERE detallecompra.compra = $idCompra AND detallecompra.activo = 1";
    $result = $mysqli->query($sql);
    $arts = $result->num_rows;
    if ($arts == 0)
    {
?>
        <tr>
            <td colspan="4">
                <span class="text-muted small">
                    <em>No existen art&iacute;culos para la compra seleccionada.</em>
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
                <td class="text-right"><?php echo "$".number_format($rowDoc['pUnit'],2,".",",");?></td>
                <td class="text-right"><?php echo "$".number_format($rowDoc['subTot'],2,".",",");?></td>
            </tr>
<?php
        }
    }

?>
        </tbody>
    </table>
</div>
<div class="col-lg-12" style="margin-bottom:15px;padding-right:0px">
    </br>
    <div class="pull-right list-group-item">
        Art&iacute;culos: <label><?php echo $arts;?> </label>
    </div>
</div>
<!-- /.table-responsive -->
<?php
//    responder($response,$mysqli);
}
?>
