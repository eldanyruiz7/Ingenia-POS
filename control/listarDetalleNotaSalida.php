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
                <th>P Lista</th>
                <th>P P&uacute;blico</th>
                <th>Sub Tot lista</th>
                <th>Sub Tot P&uacute;blico</th>
                <th>Fact</th>
            </tr>
        </thead>
        <tbody>
<?php
    $sql = "SELECT
                detallenotadesalida.cantidad      AS cantidad,
                detallenotadesalida.preciolista   AS pLista,
                detallenotadesalida.preciopublico      AS pPublico,
                detallenotadesalida.subtotallista       AS subtLista,
                detallenotadesalida.subtotalpublico AS subtPublico,
                detallenotadesalida.facturable AS facturable,
                detallenotadesalida.facturado AS facturado,
                productos.nombrecorto AS descripcion
            FROM detallenotadesalida
            INNER JOIN productos
            ON  detallenotadesalida.producto = productos.id
            WHERE detallenotadesalida.idnota = $idCompra";
    $result = $mysqli->query($sql);
    $arts = $result->num_rows;
    if ($arts == 0)
    {
?>
        <tr>
            <td colspan="4">
                <span class="text-muted small">
                    <em>No existen art&iacute;culos para la nota seleccionada.</em>
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
                <td><?php echo $rowDoc['descripcion'];?></td>
                <td><?php echo "$".number_format($rowDoc['pLista'],2,".",",");?></td>
                <td><?php echo "$".number_format($rowDoc['pPublico'],2,".",",");?></td>
                <td><?php echo "$".number_format($rowDoc['subtLista'],2,".",",");?></td>
                <td><?php echo "$".number_format($rowDoc['subtPublico'],2,".",",");?></td>
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
