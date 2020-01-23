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
        "status"                    => 1
    );
    $listaDoctos                    = json_decode($_POST['listaDoctos']);
    $cont                           = 0;
    $saldoAcumulado                 = 0;
    $esteSaldoAc                    = 0;
    foreach ($listaDoctos as $doc)
    {
        $idVenta                    = $doc ->idVenta;
        $sql                        = "SELECT
                                            ventas.pagado AS pagado,
                                            ventas.cliente AS cliente,
                                            ventas.totalventa AS totalVenta,
                                            clientes.rsocial AS rSocial,
                                            (SELECT
                                                IFNULL(SUM(pagosrecibidos.monto),0)
                                            FROM pagosrecibidos
                                            WHERE pagosrecibidos.idventa = $idVenta) AS montoPagado
                                        FROM ventas
                                        INNER JOIN clientes
                                        ON ventas.cliente = clientes.id
                                        WHERE ventas.id = $idVenta LIMIT 1";
        $result                     = $mysqli->query($sql);
        $row                        = $result->fetch_assoc();
        $cliente                    = $row['cliente'];
        $pagado                     = $row['montoPagado'];
        $totalVenta                 = $row['totalVenta'];
        $rSocial                    = $row['rSocial'];
        $esteSaldoAc                = $totalVenta - $pagado;
        $saldoAcumulado             += $esteSaldoAc;
        if ($cont++                 == 0)
            $cliente_tmp            = $cliente;
        if ($row['pagado']          == 1)
        {
            $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
            $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response['respuesta'] .='  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Uno o m&aacute;s documentos ya ha sido liquidado. Actualiza esta p&aacute;gina e int&eacute;ntalo nuevamente.';
            $response['respuesta'] .='</div>';
            $response['status'] = 0;
            responder($response,$mysqli);
        }
        if ($cliente_tmp != $cliente)
        {
            $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
            $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response['respuesta'] .='  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> La lista de documentos seleccionados deben pertenecer al mismo cliente.';
            $response['respuesta'] .='</div>';
            $response['status'] = 0;
            responder($response,$mysqli);
        }
    }
    // if (sizeof($listaDoctos)    <= 1)
    // {
    //     $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
    //     $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    //     $response['respuesta'] .='  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Debes seleccionar al menos 2 cuentas por pagar para realizar esta acci&oacute;n.';
    //     $response['respuesta'] .='</div>';
    //     $response['status']     = 0;
    // }
    // else
    // {
        $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response['respuesta'] .='  Hecho.';
        $response['respuesta'] .='</div>';
        $response['saldoAcumulado'] = $saldoAcumulado;
        $response['rSocial']    = $rSocial;
    // }
    responder($response,$mysqli);
}
?>
