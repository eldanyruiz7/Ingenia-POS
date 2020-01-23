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
        //$response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $arrayProveedor = json_decode($_POST['arrayProveedor']);
    foreach ($arrayProveedor as $esteProveedor)
    {
        $idProveedor    =   $esteProveedor    ->idProveedor;
        $rsocial        =   $esteProveedor    ->rsocial;
        $representante  =   $esteProveedor    ->representante;
        $direccion      =   $esteProveedor    ->direccion;
        $telefono       =   $esteProveedor    ->telefono;
        $rfc            =   $esteProveedor    ->rfc;
        $email          =   $esteProveedor    ->email;
    }
    $response = array(
        "status"        => 1,
        "queProveedor"  => $idProveedor,
        "rsocial"       => $rsocial,
        "representante" => $representante,
        "direccion"     => $direccion,
        "telefono"      => $telefono,
        "rfc"           => $rfc,
        "email"         => $email
    );
    if( strlen($rsocial) == 0)
        $response["status"] = 0;
    // if( strlen($representante) == 0)
    //     $response["status"] = 0;
    // if( strlen($apellidom) == 0)
    //     $response["status"] = 0;
    // if( strlen($direccion) == 0)
    //     $response["status"] = 0;
    // if( strlen($telefono) == 0)
    //     $response["status"] = 0;
    // if( strlen($rfc) == 0)
    //     $response["status"] = 0;
    // if( strlen($email) == 0)
    //     $response["status"] = 0;
    if($response["status"] == 0)
    {
        $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se modific&oacute; nada.";
        $response["respuesta"] .=   "</div>";
        responder($response, $mysqli);
    }
    $sql = "UPDATE  proveedores
            SET     rsocial         = '$rsocial',
                    representante   = '$representante',
                    direccion       = '$direccion',
                    telefono        = '$telefono',
                    rfc             = '$rfc',
                    email           = '$email'
            WHERE id                = $idProveedor
            LIMIT 1";
    if($mysqli->query($sql) === TRUE)
    {
        if ($mysqli->affected_rows ==1)
        {
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Proveedor con id: <b>$idProveedor</b> ha sido actualizado correctamente.";
            $response["respuesta"] .=   "</div>";
            responder($response,$mysqli);
        }
        else
        {
            $response["respuesta"]  =   "<div class='alert alert-warning alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se modific&oacute; nada.";
            $response["respuesta"] .=   "</div>";
            $response["status"] = 0;
            responder($response,$mysqli);
        }
    }
    else
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> No se pudo modificar. Consulta con el administrador del sistema.";
        $response["respuesta"] .=   "</div>";
        $response["status"] = 0;
        responder($response,$mysqli);
    }
}
?>
