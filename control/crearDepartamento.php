<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
$usuario = $sesion->get("nick");
if( $usuario == false )
{
    header("Location: /pventa_std/pages/login.php");
}
else
{
    function responder($response, $mysqli)
    {
        //$response['respuesta'].=$mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $nombre = $_POST['nombre'];
    $response = array(
        "status"            =>1
    );
    $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
    $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Inténtalo de nuevo. ';
    $response["respuesta"].='</div>';
    // foreach ($esteDepartamento as $departamento)
    // {
    //     $nombre            = $mysqli->real_escape_string($departamento->nombre);
    // }
    if( strlen($nombre)    == 0)
    {
        $response["nombre"]  = 0;
        $response["status"] = 0;
    }
    if($response["status"] == 0)
        responder($response, $mysqli);

    $sql = "INSERT INTO
                departamentos (
                    nombre)
            VALUES ('$nombre')";
    if($mysqli->query($sql)!= TRUE)
    {
        $response["status"] = 0;
        responder($response, $mysqli);
    }
    else {
        $idDepartamento         =   $mysqli->insert_id;
        $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Departamento con id: <b>$idDepartamento</b> ha sido agregado correctamente.";
        $response["respuesta"] .=   "   <br/><b>-Nombre:</b> $nombre";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 1;
        responder($response, $mysqli);

    }
}
?>
