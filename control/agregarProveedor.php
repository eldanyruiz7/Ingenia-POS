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
    $esteProveedor = json_decode($_POST['arrayProveedor']);
    $response = array(
        "razon"             =>1,
        "representante"     =>1,
        "direccion"         =>1,
        "telefono"          =>1,
        "rfc"               =>1,
        "email"             =>1,
        "status"            =>1
    );
    $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
    $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Los campos marcados son obligatorios. ';
    $response["respuesta"].='</div>';
    foreach ($esteProveedor as $proveedor)
    {
        $rSocial            = $mysqli->real_escape_string($proveedor->rSocial);
        $representante      = $mysqli->real_escape_string($proveedor->representante);
        $direccion          = $mysqli->real_escape_string($proveedor->direccion);
        $telefono           = $mysqli->real_escape_string($proveedor->telefono);
        $rfc                = $mysqli->real_escape_string($proveedor->rfc);
        $email              = $mysqli->real_escape_string($proveedor->email);
    }
    if( strlen($rSocial)    == 0)
    {
        $response["razon"]  = 0;
        $response["status"] = 0;
    }
    // if( strlen($representante) == 0)
    // {
    //     $response["representante"] = 0;
    //     $response["status"] = 0;
    // }
    // if( strlen($apellidom) == 0)
    // {
    //     $response["apellidom"] = 0;
    //     $response["status"] = 0;
    // }
    // if( strlen($direccion) == 0)
    // {
    //     $response["direccion"] = 0;
    //     $response["status"] = 0;
    // }
    // if( strlen($telefono) == 0)
    // {
    //     $response["telefono"] = 0;
    //     $response["status"] = 0;
    // }
    // if( strlen($rfc) == 0)
    // {
    //     $response["rfc"] = 0;
    //     $response["status"] = 0;
    // }
    // if( strlen($email) == 0)
    // {
    //     $response["email"] = 0;
    //     $response["status"] = 0;
    // }
    if($response["status"] == 0)
        responder($response, $mysqli);

    $sql = "INSERT INTO
                proveedores (
                    rsocial,
                    representante,
                    direccion,
                    telefono,
                    rfc,
                    email)
            VALUES ('$rSocial',
                    '$representante',
                    '$direccion',
                    '$telefono',
                    '$rfc',
                    '$email')";
    if($mysqli->query($sql)!= TRUE)
    {
        $response["status"] = 0;
        responder($response, $mysqli);
    }
    else {
        $idProveedor              =   $mysqli->insert_id;
        $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Proveedor con id: <b>$idProveedor</b> ha sido agregado correctamente.";
        $response["respuesta"] .=   "   <br/><b>-Nombre o R Social:</b> $rSocial";
        $response["respuesta"] .=   "   <br/><b>-Direcci&oacute;n:</b> $direccion";
        $response["respuesta"] .=   "   <br/><b>-Tel&eacute;fono:</b> $telefono";
        $response["respuesta"] .=   "   <br/><b>-RFC:</b> $rfc";
        $response["respuesta"] .=   "   <br/><b>-E-mail:</b> $email";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 1;
        responder($response, $mysqli);

    }
}
?>
