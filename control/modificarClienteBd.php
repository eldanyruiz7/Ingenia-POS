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
    $arrayCliente = json_decode($_POST['arrayCliente']);
    foreach ($arrayCliente as $esteCliente)
    {
        $idCliente      =   $esteCliente    ->idCliente;
        $rsocial        =   $esteCliente    ->rsocial;
        $representante  =   $esteCliente    ->representante;
        $tipoPrecio     =   $esteCliente    ->tipoPrecio;
        $tipoPrecioText =   $esteCliente    ->tipoPrecioText;
        $calle          =   $esteCliente    ->calle;
        $numeroExt      =   $esteCliente    ->numeroExt;
        $numeroInt      =   $esteCliente    ->numeroInt;
        $poblacion      =   $esteCliente    ->poblacion;
        $municipio      =   $esteCliente    ->municipio;
        $colonia        =   $esteCliente    ->colonia;
        $cp             =   $esteCliente    ->cp;
        $estado         =   $esteCliente    ->estado;
        $telefono1      =   $esteCliente    ->telefono1;
        $telefono2      =   $esteCliente    ->telefono2;
        $celular        =   $esteCliente    ->celular;
        $rfc            =   $esteCliente    ->rfc;
        $email          =   $esteCliente    ->email;
        $dias           =   $esteCliente    ->dias;
    }
    $response = array(
        "status"        => 1,
        "queCliente"    => $idCliente,
        "rsocial"       => $rsocial,
        "representante" => $representante,
        "tipoPrecio"    => $tipoPrecio,
        "tipoPrecioText"=> $tipoPrecioText,
        "calle"         => $calle,
        "numeroExt"     => $numeroExt,
        "numeroInt"     => $numeroInt,
        "poblacion"     => $poblacion,
        "municipio"     => $municipio,
        "colonia"       => $colonia,
        "cp"            => $cp,
        "estado"        => $estado,
        "telefono1"     => $telefono1,
        "telefono2"     => $telefono2,
        "celular"       => $celular,
        "rfc"           => $rfc,
        "email"         => $email,
        "dias"          => $dias
    );
    if( strlen($rsocial) == 0)
    {
        $response["status"] = 0;
        $response["s_rsocial"] = 0;
    }
    // if( strlen($representante) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_representante"] = 0;
    // }
    // if( strlen($tipoPrecio) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_tipoPrecio"] = 0;
    // }
    // if( strlen($tipoPrecioText) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_tipoPrecioText"] = 0;
    // }
    // if( strlen($calle) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_calle"] = 0;
    // }
    // if( strlen($numeroExt) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_numeroExt"] = 0;
    // }
    // if( strlen($poblacion) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_poblacion"] = 0;
    // }
    // if( strlen($municipio) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_municipio"] = 0;
    // }
    // if( strlen($colonia) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_colonia"] = 0;
    // }
    // if( strlen($cp) != 5 && !is_numeric($cp))
    // {
    //     $response["status"] = 0;
    //     $response["s_cp"] = 0;
    // }
    if( strlen($cp) == 0 || is_numeric($cp) == false)
    {
        $cp = 0;
    }
    // if(!is_numeric($estado))
    // {
    //     $response["status"] = 0;
    //     $response["s_estado"] = 0;
    // }
    // if( strlen($telefono1) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_telefono1"] = 0;
    // }
    // if( strlen($rfc) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_rfc"] = 0;
    // }
    // if( strlen($email) == 0)
    // {
    //     $response["status"] = 0;
    //     $response["s_email"] = 0;
    // }
    if( strlen($dias) < 0 || is_numeric($dias) == FALSE)
    {
        $response["status"] = 0;
        $response["s_dias"] = 0;
    }
    if($response["status"] == 0)
    {
        $response["respuesta"]  =   "<div class='alert alert-danger alert-dismissable'>";
        $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
        $response["respuesta"] .=   "   <i class='fa fa-exclamation-triangle' aria-hidden='true'></i> <b>Error</b> Los campos marcados son obligatorios.";
        $response["respuesta"] .=   "</div>";
        responder($response, $mysqli);
    }
    $sql = "UPDATE  clientes
            SET     rsocial         = '$rsocial',
                    representante   = '$representante',
                    tipoprecio      = $tipoPrecio,
                    calle           = '$calle',
                    numeroext       = '$numeroExt',
                    numeroint       = '$numeroInt',
                    poblacion       = '$poblacion',
                    municipio       = '$municipio',
                    colonia         = '$colonia',
                    cp              = $cp,
                    estado          = $estado,
                    telefono1       = '$telefono1',
                    telefono2       = '$telefono2',
                    celular         = '$celular',
                    rfc             = '$rfc',
                    email           = '$email',
                    diasCredito     = $dias
            WHERE id = $idCliente
            LIMIT 1";
    if($mysqli->query($sql) === TRUE)
    {
        if ($mysqli->affected_rows ==1)
        {
            $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
            $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
            $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Cliente con id: <b>$idCliente</b> ha sido actualizado correctamente.";
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
