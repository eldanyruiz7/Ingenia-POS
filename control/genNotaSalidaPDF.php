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
    $sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig   = $mysqli->query($sql);
    $rowConfig      = $resultConfig->fetch_assoc();
    $pdf = new PDF_Code128('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetMargins(8,20);
    $pdf->SetFont('Courier','',9);
    //$pdf->SetFillColor(225);
    $idNota = $_GET['idNota'];
    $sql            = "SELECT
                            notadesalida.id             AS idNota,
                            notadesalida.timestamp      AS fechaHora,
                            notadesalida.usuario        AS idUsuario,
                            notadesalida.montolista     AS montoLista,
                            notadesalida.montopublico   AS montoPublico,
                            notadesalida.observaciones  AS observaciones,
                            usuarios.nombre             AS nombreUsuario,
                            usuarios.apellidop          AS apellidoPUsuario
                            FROM notadesalida
                        INNER JOIN usuarios
                        ON notadesalida.usuario = usuarios.id
                        WHERE notadesalida.id= $idNota LIMIT 1";
    $resultNota     = $mysqli->query($sql);
    $rowNota        = $resultNota->fetch_assoc();
    $code           = str_pad($rowNota['idNota'], 12, "0", STR_PAD_LEFT);
    $idNota         = $rowNota['idNota'];
    $fechaHora      = date('d-m-Y / h:i:s a', strtotime($rowNota['fechaHora']));
    $cajero         = $rowNota['nombreUsuario']." ".$rowNota['apellidoPUsuario'];
    $montoLista     = $rowNota['montoLista'];
    $montoPublico   = $rowNota['montoPublico'];
    $observaciones  = $rowNota['observaciones'];
    $pdf->Code128(169,10," ".$code,38,12);
    $pdf->SetXY(174,20);
    $pdf->Cell(30,7,$code,0,1,'C');
    //$pdf->Write(5,$code);
    $pdf->SetXY(6,10);
    $pdf->SetFont('Courier','B',17);
    //Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
    //$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
    $pdf->Cell(200,7,utf8_decode($sesion->get("nombreComercio")),0,1,'C');
    //$pdf->SetXY(15,11);
    $pdf->SetFont('Courier','U',14);
    $pdf->Cell(200,6,utf8_decode('NOTA DE SALIDA'),0,1,'C');
    //$pdf->SetXY(180,20);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(16,7,utf8_decode('CAJERO:'),0,0,'L');
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(50,7,utf8_decode($cajero),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(27,7,utf8_decode('FECHA/HORA:'),0,0,'L');
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(30,7,utf8_decode($fechaHora),0,1,'L');
    $pdf->SetFont('Courier','B',10);
    $pdf->SetFillColor(034,113,179);
    $pdf->SetDrawColor(255);
    $pdf->SetTextColor(255);
    $pdf->Cell(100,4,"",0,1,'L',0);
    $pdf->Cell(27,4,utf8_decode('CÓDIGO'),1,0,'C',1);
    $pdf->Cell(72,4,utf8_decode('DESCRIPCIÓN DEL ARTÍCULO'),1,0,'L',1);
    $pdf->Cell(33,4,utf8_decode('CANT'),1,0,'C',1);
    //$pdf->Cell(22,4,utf8_decode('P LISTA'),1,0,'C',1);
    $pdf->Cell(33,4,utf8_decode('PRECIO'),1,0,'C',1);
    //$pdf->Cell(22,4,utf8_decode('MONTO'),1,0,'C',1);
    $pdf->Cell(33,4,utf8_decode('SUBTOT'),1,1,'C',1);
    //$pdf->SetFillColor(255);
    $pdf->SetFont('Courier','',9);
    $pdf->SetTextColor(0);
    $pdf->SetFillColor(0);
    $sql = "SELECT
                detallenotadesalida.producto        AS idProducto,
                detallenotadesalida.cantidad        AS cantidad,
                detallenotadesalida.preciolista     AS precioLista,
                detallenotadesalida.preciopublico   AS precioPublico,
                detallenotadesalida.subtotallista   AS subtotalLista,
                detallenotadesalida.subtotalpublico AS subtotalPublico,
                productos.nombrecorto               AS nombreProducto,
                productos.codigo                    AS codigo,
                productos.codigo2                   AS codigo2
            FROM detallenotadesalida
            INNER JOIN productos
            ON detallenotadesalida.producto = productos.id
            WHERE idnota = $idNota";
    $resultNotaDet = $mysqli->query($sql);
    $cantProductos = $resultNotaDet->num_rows;
    while ($rowDet = $resultNotaDet->fetch_assoc())
    {
        $codigo = (strlen($rowDet['codigo']) > 0) ? $rowDet['codigo'] : $rowDet['codigo2'];
        $pdf->Cell(27,4,utf8_decode($codigo),1,0,'R');
        $pdf->Cell(72,4,utf8_decode($rowDet['nombreProducto']),1,0,'L');
        $pdf->Cell(33,4,utf8_decode(number_format($rowDet['cantidad'],3,".",",")),1,0,'R');
        //$pdf->Cell(22,4,utf8_decode(number_format($rowDet['precioLista'],2,".",",")),1,0,'R');
        $pdf->Cell(33,4,utf8_decode(number_format($rowDet['precioPublico'],2,".",",")),1,0,'R');
        //$pdf->Cell(22,4,utf8_decode(number_format($rowDet['subtotalLista'],2,".",",")),1,0,'R');
        $pdf->Cell(33,4,utf8_decode(number_format($rowDet['subtotalPublico'],2,".",",")),1,1,'R');
    }
    $pdf->SetDrawColor(0);
    $pdf->Cell(100,4,"",0,1,'L');
    $pdf->Cell(198,0,"",1,1,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(40,6,utf8_decode('TOTAL ARTÍCULOS:'),1,0,'L');
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(20,6,utf8_decode($cantProductos),1,0,'R');
    $pdf->Cell(72,6,utf8_decode(''),0,0,'R');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(33,6,utf8_decode('TOTAL:'),1,0,'R',0);
    $pdf->SetFont('Courier','B',10);
    //$pdf->Cell(22,6,utf8_decode(number_format($montoLista,2,".",",")),1,0,'R',0);
    $pdf->Cell(33,6,utf8_decode('$ '.number_format($montoPublico,2,".",",")),1,1,'R',0);
    $pdf->SetFont('Courier','',10);
    $y = $pdf->GetY();
    if ($y > 205)
        $pdf->AddPage();
    $pdf->Cell(61,8,utf8_decode('OBSERVACIONES:'),0,1,'L');
    $pdf->SetFont('Courier','I',8);
    $pdf->MultiCell(0,3,utf8_decode('*'.$observaciones),0,'L');
    $pdf->Cell(73,1,utf8_decode(''),0,1,'R');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(200,8,utf8_decode('AUTORIZA:'),0,1,'C');
    $pdf->Cell(73,20,utf8_decode(''),0,1,'R');
    $pdf->Cell(66,0,"",0,0,'L');
    $pdf->Cell(66,0,"",1,1,'L');
    $pdf->SetFont('Courier','B',10);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $z = $pdf->GetPageHeight();
    $w = $pdf->GetPageWidth();
    $pdf->Cell(200,8,utf8_decode($rowConfig['nombreRepresentante']),0,0,'C');
    $pdf->Output('D','Nota-Salida-'.$code.'.pdf');
    //$pdf->Output();
    /*
        $coleccionTabla = json_decode($_POST['r']);
        print_r($coleccionTabla);
    */
}
 ?>
