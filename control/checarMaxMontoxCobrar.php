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
    $idVenta    = $_POST["idVenta"];
    $sql        = "SELECT cliente FROM ventas WHERE id = $idVenta LIMIT 1";
    $result     = $mysqli->query($sql);
    $row        = $result->fetch_assoc();
    $idCliente  = $row['cliente'];

    $sql        = "SELECT
                        ventas.id AS idVenta_,
                        ventas.totalventa AS totalVenta_,
                        (SELECT IFNULL(SUM(monto), 0)
                        FROM pagosrecibidos
                        WHERE pagosrecibidos.idventa = idVenta_) AS esteMontoPagado_
                    FROM ventas
                    WHERE ventas.cliente = $idCliente AND esCredito = 1 AND pagado = 0";
    $result_suma = $mysqli->query($sql);
    $maxPago = 0;
    if ($result_suma->num_rows == 0)
    {
        $response["status"] = 0;
        responder($response,$mysqli);
    }
    while ($row_suma    = $result_suma->fetch_assoc())
    {
        $totVenta       = $row_suma['totalVenta_'];
        $pagado         = $row_suma['esteMontoPagado_'];
        $maxPago        += $totVenta - $pagado;
    }

    $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
    $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
    $response["status"]     =   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se guard&oacute; la operaci&oacute;n.";
    $response["maxPago"]    =   $maxPago;

}
?>
