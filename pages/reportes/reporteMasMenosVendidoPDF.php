<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
require "../../startbootstrap/vendor/fpdf/fpdf.php";
$parts = explode('-',$_POST['fi']);
$fi = $parts[2]."-".$parts[1]."-".$parts[0];
$parts = explode('-',$_POST['ff']);
$ff = $parts[2]."-".$parts[1]."-".$parts[0];
$pdf = new FPDF('L','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(10,20);
$pdf->SetFillColor(225);
$pdf->Image('../../images/logo.jpg',9,4,-760);
$pdf->SetXY(15,5);
$pdf->SetFont('Courier','B',17);
require_once ('../../conecta/bd.php');
$sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
$resultConfig   = $mysqli->query($sql);
$rowConfig      = $resultConfig->fetch_assoc();
//Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
//$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
$pdf->Cell(249,10,utf8_decode($rowConfig['nombreComercio']),0,0,'C');
$pdf->SetXY(15,11);
$pdf->SetFont('Courier','U',14);
$pdf->Cell(249,10,utf8_decode('REPORTE ARTÍCULOS MAS VENDIDOS'),0,1,'C');
//$pdf->SetXY(180,20);
$pdf->SetFont('Courier','',10);
$coleccionTabla = json_decode($_POST['r']);
$cuantos = sizeof($coleccionTabla);
$pdf->Cell(27,12,utf8_decode('REGISTROS:'),0,0,'L');
$pdf->Cell(13,12,utf8_decode($cuantos),0,0,'L');
$pdf->Cell(105,12,utf8_decode(''),0,0,'R');
$pdf->Cell(30,12,utf8_decode('PERIODO:'),0,0,'R');
$pdf->SetFont('Courier','',8);
$pdf->Cell(8,12,utf8_decode('del'),0,0,'C');
$pdf->SetFont('Courier','UI',10);
$pdf->Cell(25,12,utf8_decode($fi),0,0,'C');
$pdf->SetFont('Courier','',8);
$pdf->Cell(8,12,utf8_decode('al'),0,0,'C');
$pdf->SetFont('Courier','UI',10);
$pdf->Cell(25,12,utf8_decode($ff),0,1,'C');
//$pdf->SetXY(8,30);
$pdf->SetFont('Courier','B',10);
$pdf->SetFillColor(034,113,179);
$pdf->SetDrawColor(255);
$pdf->SetTextColor(255);
$pdf->Cell(20,6,utf8_decode('ID.'),1,0,'C',1);
$pdf->Cell(40,6,utf8_decode('CODIGO'),1,0,'C',1);
$pdf->Cell(110,6,utf8_decode('NOMBRE'),1,0,'C',1);
$pdf->Cell(30,6,utf8_decode('DEPARTAMENTO'),1,0,'C',1);
$pdf->Cell(30,6,utf8_decode('UNIDAD'),1,0,'C',1);
$pdf->Cell(30,6,utf8_decode('CANTIDAD'),1,1,'C',1);
//$pdf->SetFillColor(255);
$pdf->SetFont('Courier','',8);
$pdf->SetTextColor(0);
//$pdf->SetFillColor(255);
$cont = 1;
$totalCant = 0;
$sumaDesc = 0;
$granTotal = 0;
foreach ($coleccionTabla as $esteRow)
{
    if($cont%2==0)
        //$pdf->SetFillColor(207,252,255);
        $pdf->SetFillColor(228, 238, 255);
    else
        $pdf->SetFillColor(255);
    $id             = $esteRow  ->id;
    $codigo         = $esteRow  ->codigo;
    $nombre         = $esteRow  ->nombre;
    $departamento   = $esteRow  ->departamento;
    $unidadVenta    = $esteRow  ->unidadVenta;
    $cantidad       = $esteRow  ->cantidad;

    $pdf->Cell(20,4,utf8_decode($id),0,0,'R',1);
    $pdf->Cell(40,4,utf8_decode($codigo),0,0,'R',1);
    $pdf->Cell(110,4,utf8_decode($nombre),0,0,'L',1);
    $pdf->Cell(30,4,utf8_decode($departamento),0,0,'L',1);
    $pdf->Cell(30,4,utf8_decode($unidadVenta),0,0,'L',1);
    $pdf->Cell(30,4,utf8_decode($cantidad),0,1,'R',1);

    $cont++;
    // $sumaDesc += floatval(str_replace(',','',substr($descuento,1)));
    $totalCant += floatval(str_replace(',','',$cantidad));
}
//$granTotal = $totalCant - $sumaDesc;
$pdf->SetFont('Courier','',10);
$pdf->Cell(219,6,utf8_decode('CANT. TOTAL VENDIDOS:'),0,0,'R',0);
$totalCant = number_format($totalCant, 2, ".", ",");
$pdf->Cell(43,6,utf8_decode($totalCant),0,1,'R',0);
$pdf->Output();
/*
    $coleccionTabla = json_decode($_POST['r']);
    print_r($coleccionTabla);
*/

 ?>
