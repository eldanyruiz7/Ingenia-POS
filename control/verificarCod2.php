<?php
require_once ('../conecta/bd.php');
function responder($response, $mysqli)
{
    //$response['respuesta'].=$mysqli->error;
    header('Content-Type: application/json');
    echo json_encode($response, JSON_FORCE_OBJECT);
    $mysqli->close();
    exit;
}
$response['existe'] = 0;
$response['respuesta'] = '<p class="help-block">&nbsp;</p>';
$response['class'] = '';
$cod                = $_POST['codigo2'];
if(strlen($cod) == 0)
responder($response, $mysqli);

$sql                = "SELECT id FROM productos WHERE codigo2 = '$cod' AND activo = 1 LIMIT 1";
$result             = $mysqli->query($sql);
if($result->num_rows == 1)
{
    $response['existe'] = 1;
    $response['respuesta'] = '<p class="help-block"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Clave ya asignada. Elige otra</p>';
    $response['class'] = 'has-error';
}
else
{
    $response['existe'] = 0;
    $response['respuesta'] = '<p class="help-block"><i class="fa fa-check-circle-o" aria-hidden="true"></i> C&oacute;digo disponible</p>';
    $response['class'] = 'has-success';
}

responder($response, $mysqli);
//$tipoCliente        = $resul->fetch_assoc();






?>
