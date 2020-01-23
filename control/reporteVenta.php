<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
require_once ('../conecta/bd.php');
$sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
$resultConfig   = $mysqli->query($sql);
$rowConfig      = $resultConfig->fetch_assoc();
require "../startbootstrap/vendor/fpdf/fpdf.php";
$parts = explode('-',$_POST['fi']);
$fi = $parts[2]."-".$parts[1]."-".$parts[0];
$parts = explode('-',$_POST['ff']);
$ff = $parts[2]."-".$parts[1]."-".$parts[0];
$pdf = new FPDF('L','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetMargins(4,20);
$pdf->SetFillColor(225);
$pdf->SetXY(15,5);
$pdf->SetFont('Courier','B',17);
//Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
//$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
$pdf->Cell(249,10,utf8_decode($rowConfig['nombreComercio']),0,0,'C');
$pdf->SetXY(15,11);
$pdf->SetFont('Courier','U',14);
$pdf->Cell(249,10,utf8_decode('REPORTE DE VENTAS'),0,1,'C');
//$pdf->SetXY(180,20);
$pdf->SetFont('Courier','',10);
$coleccionTabla = json_decode($_POST['r']);
$cuantos = sizeof($coleccionTabla);
$pdf->Cell(27,12,utf8_decode('REGISTROS:'),0,0,'L');
$pdf->Cell(13,12,utf8_decode($cuantos),0,0,'L');
$pdf->Cell(135,12,utf8_decode(''),0,0,'R');
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
$pdf->Cell(20,6,utf8_decode('NO.'),1,0,'C',1);
$pdf->Cell(23,6,utf8_decode('FECHA'),1,0,'C',1);
$pdf->Cell(23,6,utf8_decode('HORA'),1,0,'C',1);
$pdf->Cell(60,6,utf8_decode('CLIENTE'),1,0,'C',1);
$pdf->Cell(30,6,utf8_decode('DESC'),1,0,'C',1);
$pdf->Cell(60,6,utf8_decode('CAJERO'),1,0,'C',1);
$pdf->Cell(23,6,utf8_decode('METODO'),1,0,'C',1);
$pdf->Cell(8,6,utf8_decode('T'),1,0,'C',1);
$pdf->Cell(25,6,utf8_decode('MONTO'),1,1,'C',1);
//$pdf->SetFillColor(255);
$pdf->SetFont('Courier','',8);
$pdf->SetTextColor(0);
//$pdf->SetFillColor(255);
$cont = 1;
$subTotal = 0;
$sumaDesc = 0;
$granTotal = 0;
foreach ($coleccionTabla as $esteRow)
{
    if($cont%2==0)
        //$pdf->SetFillColor(207,252,255);
        $pdf->SetFillColor(228, 238, 255);
    else
        $pdf->SetFillColor(255);
    $numero     = $esteRow  ->numero;
    $fecha      = $esteRow  ->fecha;
    $hora       = $esteRow  ->hora;
    $cliente    = $esteRow  ->cliente;
    $descuento  = $esteRow  ->descuento;
    $cajero     = $esteRow  ->cajero;
    $metodo     = $esteRow  ->metodo;
    $tipo       = $esteRow  ->tipo;
    $total      = $esteRow  ->total;
    $pdf->Cell(20,4,utf8_decode($numero),0,0,'L',1);
    $pdf->Cell(23,4,utf8_decode($fecha),0,0,'C',1);
    $pdf->Cell(23,4,utf8_decode($hora),0,0,'C',1);
    $pdf->Cell(60,4,utf8_decode($cliente),0,0,'C',1);
    $pdf->Cell(30,4,utf8_decode($descuento),0,0,'R',1);
    $pdf->Cell(60,4,utf8_decode($cajero),0,0,'C',1);
    $pdf->Cell(23,4,utf8_decode($metodo),0,0,'C',1);
    $pdf->Cell(8,4,utf8_decode($tipo),0,0,'C',1);
    $pdf->Cell(25,4,utf8_decode($total),0,1,'R',1);
    $cont++;
    $sumaDesc += floatval(str_replace(',','',substr($descuento,1)));
    $subTotal += floatval(str_replace(',','',substr($total,1)));
}
$granTotal = $subTotal - $sumaDesc;
$pdf->SetFont('Courier','',10);
$pdf->Cell(239,6,utf8_decode('SUBTOTAL:'),0,0,'R',0);
$subTotal = number_format($subTotal, 2, ".", ",");
$pdf->Cell(33,6,utf8_decode('$'.$subTotal),0,1,'R',0);
$pdf->Cell(239,6,utf8_decode('TOTAL DESC:'),0,0,'R',0);
$sumaDesc = number_format($sumaDesc, 2, ".", ",");
$pdf->Cell(33,6,utf8_decode('$'.$sumaDesc),0,1,'R',0);
$pdf->SetFont('Courier','B',10);
$pdf->Cell(239,6,utf8_decode('TOTAL:'),0,0,'R',0);
$granTotal = number_format($granTotal, 2, ".", ",");
$pdf->Cell(33,6,utf8_decode('$'.$granTotal),0,0,'R',0);

$pdf->Output();
/*
    $coleccionTabla = json_decode($_POST['r']);
    print_r($coleccionTabla);
*/

 ?>
