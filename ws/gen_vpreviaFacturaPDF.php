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
require '../startbootstrap/vendor/fpdf/code128.php';
require '../startbootstrap/vendor/fpdf/qrcode/qrcode.class.php';

if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    $idF        = $_GET['id'];
    $vista      = 1;
    $sql        = "SELECT tipoCFDI FROM facturas WHERE id = $idF LIMIT 1";
    $result     = $mysqli->query($sql);
    $row        = $result->fetch_assoc();
    $tipoCFDI   = $row['tipoCFDI'];
    //echo "TIPO CFDI=".$tipoCFDI;
    if ($tipoCFDI == 'P')
    {
        require 'genComplementoPDF.php';
        genComplemento($idF,$mysqli,$vista);
    }
    else
    {
        require 'genFacturaPDF.php';
        genFactura($idF,$mysqli,$vista);
    }
}

?>
