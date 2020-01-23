<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
//require_once ("../startbootstrap/vendor/fpdf/fpdf.php");
require_once ('../startbootstrap/vendor/fpdf/code128.php');
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

    $pdf = new PDF_Code128('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetMargins(6,10);
    $pdf->SetFont('Courier','',9);
    $pdf->SetXY(6,10);
    $sql = "SELECT codigo2, nombrelargo FROM productos WHERE length(codigo2) > 0 AND length(codigo) = 0 ORDER BY nombrelargo ASC";
    $resultado = $mysqli->query($sql);
    $pdf->SetFont('Courier','B',18);
    $saltoLinea = 0;
    while ($row = $resultado->fetch_assoc())
    {
        if ($saltoLinea == 1)
        {
            //$pdf->MultiCell(80,8,utf8_decode($row['nombrelargo']),0,'L');
            $pdf->Cell(95,57,"",1,1,'L');
            ///////////////
            $x_cache = $x = $pdf->GetX();
            $y_cache = $y = $pdf->GetY();
            $pdf->SetXY($x-100,$y-50);
            $pdf->MultiCell(83,6.5,utf8_decode($row['nombrelargo']),0,'C');
            $code = $row['codigo2'];
            $pdf->Code128($x+136,$y-30,$code,46,15);
            $pdf->SetXY($x-65,$y-11);
            $pdf->Write(5,$code);
            $pdf->SetXY($x,$y);
            ///////////////
            $pdf->Cell(188,5,"",0,1,'L');
            $saltoLinea = 0;;
            //continue;
        }
        else
        {
            $pdf->Cell(95,57,"",1,0,'L');
            ////////////////
            $x_cache = $x = $pdf->GetX();
            $y_cache = $y = $pdf->GetY();
            $pdf->SetXY($x-90,$y+7);
            $pdf->MultiCell(83,6.5,utf8_decode($row['nombrelargo']),0,'C');
            $code = $row['codigo2'];
            $pdf->Code128($x-69,$y+27,$code,46,15);
            $pdf->SetXY($x-54,$y+45);
            $pdf->Write(5,$code);
            $pdf->SetXY($x,$y);
            ////////////////
            $pdf->Cell(15,57,"",0,0,'L');
            $saltoLinea++;
        }
        //$pdf->Cell(10,10,"",0,1,'L');
        //echo $row['codigo2']."--".$row['nombrelargo']."</br>";
    }

    //$pdf->Image('../images/logo.jpg',6,4,-720);
    //$pdf->Output('D','Remision-'.$code.'.pdf');
    $pdf->Output();
}
 ?>
