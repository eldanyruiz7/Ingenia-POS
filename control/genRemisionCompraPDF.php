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
    $pdf->SetMargins(8,20);
    $pdf->SetFont('Courier','',9);
    //$pdf->SetFillColor(225);
    $idCompra = $_GET['idCompra'];
    $sql            = "SELECT * FROM compras WHERE id = $idCompra LIMIT 1";
    $result         = $mysqli->query($sql);
    $rowCompra      = $result->fetch_assoc();
    $idUsuario      = $rowCompra['usuario'];
    $idProveedor    = $rowCompra['proveedor'];
    $sql            = "SELECT nombre, apellidop FROM usuarios WHERE id = $idUsuario LIMIT 1";
    $resultUsr      = $mysqli->query($sql);
    $rowUsr         = $resultUsr->fetch_assoc();
    $sql            = "SELECT rsocial FROM proveedores WHERE id = $idProveedor LIMIT 1";
    $resultNomProv  = $mysqli->query($sql);
    $rowNomProv     = $resultNomProv->fetch_assoc();
    $nombreProveedor= $rowNomProv['rsocial']; //." ".$rowNomCte['apellidom'];
    $tipoDoc        = ($rowCompra['esfactura'] == 1) ? "FACTURA" : "REMISIÓN";
    $noDocto        = $rowCompra['nodocumento'];
    $estado         = ($rowCompra['pagado'] == 1) ? "SALDADO" : "PENDIENTE";
    $pdf->Image('../images/logo.jpg',6,4,-720);
    $code           = str_pad($rowCompra['id'], 12, "0", STR_PAD_LEFT);
    $fechaHora      = date('d-m-Y / h:i:s a', strtotime($rowCompra['timestamp']));
    $fechaExpira    = ($rowCompra['contado'] == 0) ? date('d-m-Y', strtotime($rowCompra['fechaexpira'])) : "N/A";
    $cajero         = $rowUsr['nombre']." ".$rowUsr['apellidop'];
    $pdf->Code128(169,10," ".$code,38,12);
    $pdf->SetXY(174,20);
    $pdf->Cell(30,7,$code,0,1,'C');
    //$pdf->Write(5,$code);
    $pdf->SetXY(6,10);
    $pdf->SetFont('Courier','B',20);
    $pdf->SetTextColor(0,0,100);
    //Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
    //$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
    $pdf->Cell(200,7,utf8_decode($sesion->get("nombreComercio")),0,1,'C');
    $pdf->SetTextColor(0);
    //$pdf->SetXY(15,11);
    $pdf->SetFont('Courier','U',16);
    $pdf->Cell(200,8,utf8_decode('NOTA DE COMPRA'),0,1,'C');
    //$pdf->SetXY(180,20);
    $pdf->SetFillColor(233);
    $pdf->SetDrawColor(255);
    $pdf->SetLineWidth(0.6);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(20,4,utf8_decode("RECIBIÓ:"),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(60,4,utf8_decode($cajero),0,0,'L');
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(22,4,utf8_decode('NO DOCTO:'),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(42,4,utf8_decode($noDocto),1,0,'C',true);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(13,4,utf8_decode('TIPO:'),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(43,4,utf8_decode($tipoDoc),1,1,'C',true);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(26,4,utf8_decode('FECHA/HORA:'),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(58,4,utf8_decode($fechaHora),1,0,'C',true);
//    $pdf->SetFont('Courier','B',9.5);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(29,4,utf8_decode('FECHA EXPIRA:'),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(33,4,utf8_decode($fechaExpira),1,0,'C',true);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(17,4,utf8_decode('PAGO:'),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(37,4,utf8_decode($estado),1,1,'C',true);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(24,4,utf8_decode("PROVEEDOR:"),0,0,'L');
    $pdf->SetFont('Courier','',9.5);
    $pdf->Cell(100,4,utf8_decode($nombreProveedor),1,1,'L',true);
    $pdf->SetLineWidth(0.2);
    //$pdf->Cell(198,0,"",1,1,'L');
    $pdf->Cell(100,0.8,"",0,1,'L',0);
    $pdf->SetFillColor(0, 155, 0);
    $pdf->SetTextColor(255);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(20,4,utf8_decode('CANT.'),0,0,'C',1);
    $pdf->Cell(30,4,utf8_decode('UNIDAD'),0,0,'C',1);
    $pdf->Cell(87,4,utf8_decode('DESCRIPCIÓN DEL ARTÍCULO'),0,0,'C',1);
    $pdf->Cell(10,4,utf8_decode('IVA'),0,0,'C',1);
    $pdf->Cell(10,4,utf8_decode('IEPS'),0,0,'C',1);
    $pdf->Cell(20,4,utf8_decode('P. UNIT'),0,0,'C',1);
    $pdf->Cell(22,4,utf8_decode('IMPORTE'),0,1,'C',1);
    //$pdf->SetFillColor(255);
    $pdf->SetTextColor(0);
    $pdf->SetFillColor(0);

    $sql = "SELECT
                productos.nombrecorto       AS nombre,
                productos.IVA               AS IVA,
                productos.IEPS              AS IEPS,
                productos.claveSHCP         AS claveSAT,
                detallecompra.cantidad      AS cantidad,
                detallecompra.preciolista   AS precio,
                detallecompra.subTotal      AS subtotal,
                unidadesventa.nombre        AS unidadVenta
            FROM detallecompra
            INNER JOIN productos
            ON detallecompra.producto = productos.id
            INNER JOIN unidadesventa
            ON productos.unidadventa = unidadesventa.id
            WHERE detallecompra.compra = $idCompra AND detallecompra.activo = 1
            ORDER BY detallecompra.id DESC";
    $resultDetalle  = $mysqli->query($sql);
    $totProd = 0;
    while ($rowD    = $resultDetalle->fetch_assoc())
    {
        $nombre     = $rowD['nombre'];
        $iva        = ($rowD['IVA'] == 0)? "" : $rowD['IVA']."%";
        $ieps       = ($rowD['IEPS'] == 0)? "" : $rowD['IEPS']."%";
        //$ieps       = $rowD['IEPS'];
        $claveSAT   = $rowD['claveSAT'];
        $nombre    .= (strlen($claveSAT)>0) ? " (".$claveSAT.")" : "";
        $cantidad   = $rowD['cantidad'];
        $precio     = $rowD['precio'];
        $subtotal   = $rowD['subtotal'];
        $unidad     = $rowD['unidadVenta'];
        $pdf->SetFont('Courier','',9.2);
        $pdf->Cell(20,4,utf8_decode(number_format($cantidad,3)),0,0,'R',0);
        $pdf->Cell(30,4,utf8_decode($unidad),0,0,'C',0);
        $pdf->Cell(87,4,utf8_decode($nombre),0,0,'L',0);
        $pdf->Cell(10,4,utf8_decode($iva),0,0,'R',0);
        $pdf->Cell(10,4,utf8_decode($ieps),0,0,'R',0);
        $pdf->Cell(20,4,utf8_decode(number_format($precio,2,".",",")),0,0,'R',0);
        $pdf->Cell(22,4,utf8_decode(number_format($subtotal,2,".",",")),0,1,'R',0);
        $totProd ++;
    }
    $pdf->SetDrawColor(0);
    $pdf->Cell(198,2,"",0,1,'L');
    $pdf->Cell(198,0,"",1,1,'L');
    //$totalVenta     = $rowVenta['totalventa'];
    //$totalVenta_f   = number_format($totalVenta, 2);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(40,6,utf8_decode('TOTAL ARTS.:'),0,0,'R');
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(20,6,utf8_decode($totProd),0,0,'L');
    $pdf->Cell(67,6,utf8_decode(''),0,0,'R');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(33,6,utf8_decode('TOTAL:'),1,0,'R',0);
    $pdf->SetFont('Courier','B',10);
    //$pdf->Cell(22,6,utf8_decode(number_format($montoLista,2,".",",")),1,0,'R',0);
    $totalCompra = $rowCompra['monto'];
    //$totalCompra_f = number_format($totalCompra, 2);
    $pdf->Cell(38,6,utf8_decode('$ '.number_format($totalCompra,2,".",",")),1,1,'R',0);
    //$pdf->Output('D','Remision-'.$code.'.pdf');
    $pdf->Output();

}
 ?>
