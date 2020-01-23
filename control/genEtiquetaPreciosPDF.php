<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
//require_once ("../startbootstrap/vendor/fpdf/fpdf.php");
require_once ('../startbootstrap/vendor/fpdf/code128_sinfooter.php');
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

    $pdf = new PDF_Code128('L','mm',array(29,62));
    //$pdf->AliasNbPages();
    $pdf->SetMargins(0.1,0.1,0.1);
    $pdf->SetAutoPageBreak(false);
    //$pdf->SetFillColor(225);
    $sql        = "SELECT
                        productos.id                    AS idProducto,
                        productos.nombrecorto           AS nombre,
                        productos.codigo                AS codigo,
                        productos.codigo2               AS codigo2,
                        detalleprecios.precioXunidad    AS precio
                    FROM productos
                    INNER JOIN detalleprecios
                    ON productos.id = detalleprecios.producto
                    WHERE detalleprecios.tipoprecio = 1
                    ORDER BY productos.nombrecorto ASC";
    $result     = $mysqli->query($sql);
    //$pdf->SetTextColor(255,0,0);
    $cont = 0;
    while ($row = $result->fetch_assoc())
    {
        $pdf->AddPage();
        $cont++;
        $pdf->SetFont('Times','U',11.2);
        $pdf->Image('../images/logo_etiqueta.jpg',3,3.5,-940);
        $pdf->SetXY(1.9,2.3);
        $pdf->MultiCell(0,3.2,utf8_decode($row['nombre']),0,'C',false);

        $pdf->SetXY(15.5,10);
        $pdf->SetFont('Times','',21);
        $pdf->Cell(9.5,3,utf8_decode('$'),0,0,'L');
        $pdf->SetFont('Times','B',39);
        $pdf->Cell(36,6,utf8_decode(number_format($row['precio'],2,".",",")),0,1,'R');
        $codigo = (string)(strlen($row['codigo'])>0) ? $row['codigo'] : $row['codigo2'];
        $pdf->Code128(32,18.5,$codigo,27,3.4);
        $pdf->SetXY(1.5,22.8);
        $pdf->SetFont('Times','',6.1);
        $pdf->Cell(3,2,"E:",0,0,'L');
        $pdf->Cell(30,2,$cont,0,0,'L');
        $pdf->SetXY(30,22.8);
        //$pdf->SetFont('Times','',6.1);
        $pdf->Cell(30,2,"id:".$row['idProducto']."    ".$codigo,0,0,'R');
        $pdf->Rect(2,2,58.2,20.4);

    }
    // $pdf->Output('D','etiquetaPrecios.pdf');
    $pdf->Output();
    /*
        $coleccionTabla = json_decode($_POST['r']);
        print_r($coleccionTabla);
    */
}
 ?>
