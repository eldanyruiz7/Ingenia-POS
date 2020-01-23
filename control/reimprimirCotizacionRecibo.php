<?php
require "../conecta/bd.php";
require "cotizacionRecibo.php";
function responder($response, $mysqli)
{
    $response['error'] = $mysqli->error;
    header('Content-Type: application/json');
    echo json_encode($response, JSON_FORCE_OBJECT);
    $mysqli->close();
    exit;
}
$id = $_POST['id'];
$reciboHTML = genReciboCotizacion($id, $mysqli);
$response['recibo']     =   $reciboHTML;
$response['codigo']     =  str_pad($id, 12, "0", STR_PAD_LEFT);
responder($response,$mysqli);
 ?>
