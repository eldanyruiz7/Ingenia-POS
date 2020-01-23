<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
//require_once ("../startbootstrap/vendor/fpdf/fpdf.php");
require_once ('../startbootstrap/vendor/fpdf/code128.php');
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
if(!isset($_SESSION))
{

        $sesion = new sesion();
}
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
    if (isset($enviar_) == FALSE)
    {
        $enviar_ = 0;
    }
    if ($enviar_ == 1)
    {
        $idCotizacion   = $idCotizacion_;
        $eMail          = $eMail_;
    }else
    {
        $guardarDisco   = 0;
        $idCotizacion   = $_GET['idCotizacion'];
    }
    $sql            = "SELECT * FROM cotizaciones WHERE id = $idCotizacion LIMIT 1";
    $result         = $mysqli->query($sql);
    $rowCompra      = $result->fetch_assoc();
    $idUsuario      = $rowCompra['usuario'];
    $idCliente      = $rowCompra['cliente'];
    $sql            = "SELECT nombre, apellidop FROM usuarios WHERE id = $idUsuario LIMIT 1";
    $resultUsr      = $mysqli->query($sql);
    $rowUsr         = $resultUsr->fetch_assoc();
    $sql            = "SELECT rsocial FROM clientes WHERE id = $idCliente LIMIT 1";
    $resultNomCte   = $mysqli->query($sql);
    $rowNomCte      = $resultNomCte->fetch_assoc();
    $nombreCliente  = $rowNomCte['rsocial']; //." ".$rowNomCte['apellidom'];
    $idVenta        = $rowCompra['idVenta'];
    $estadoVenta    = ($idVenta == NULL) ? '--' : $idVenta;
    // $tipoDoc        = ($rowCompra['esfactura'] == 1) ? "FACTURA" : "REMISIÓN";
    // $noDocto        = $rowCompra['nodocumento'];
    // $estado         = ($rowCompra['pagado'] == 1) ? "SALDADO" : "PENDIENTE";
    $pdf->SetXY(11,10);
    $pdf->SetFillColor(243, 230, 255);
    $pdf->SetDrawColor(102, 0, 204);
    $pdf->Cell(157,14.5,"",0,0,'L',1);
    $pdf->SetFillColor(0);
    $pdf->Image('../images/logo.png',5.7,5,-720);
    $code           = str_pad($rowCompra['id'], 12, "0", STR_PAD_LEFT);
    $fechaHora      = date('d-m-Y / h:i:s a', strtotime($rowCompra['timestamp']));
    //$fechaExpira    = ($rowCompra['contado'] == 0) ? date('d-m-Y', strtotime($rowCompra['fechaexpira'])) : "N/A";
    $cajero         = $rowUsr['nombre']." ".$rowUsr['apellidop'];
    $pdf->Code128(169,10," ".$code,38,12);
    $pdf->SetXY(174,20);
    $pdf->Cell(30,7,$code,0,1,'C');
    //$pdf->Write(5,$code);
    $pdf->SetXY(6,10);
    $pdf->SetFont('Courier','B',20);
    $pdf->SetTextColor(40, 0, 77);
    //Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
    //$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
    $pdf->Cell(200,7,utf8_decode($sesion->get("nombreComercio")),0,1,'C');
    $pdf->SetTextColor(0);
    //$pdf->SetXY(15,11);
    $pdf->SetFont('Courier','U',16);
    $pdf->Cell(200,8,utf8_decode('COTIZACIÓN'),0,1,'C');
    //$pdf->SetXY(180,20);
    $pdf->SetFillColor(228);
    $pdf->SetDrawColor(255);
    $pdf->Cell(20,2,utf8_decode(""),0,1,'L');

    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(35,3.5,utf8_decode('FECHA ELABORACIÓN:'),0,0,'L');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(106,3.5,utf8_decode($fechaHora),0,0,'L');
    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(16,3.5,utf8_decode("ELABORÓ:"),0,0,'L');
    $pdf->SetFont('Courier','',8);
    $pdf->Cell(42,3.5,utf8_decode($cajero),0,1,'C');
    //$pdf->Cell(20,2,utf8_decode(""),0,1,'L');
    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(22,3.5,utf8_decode("ESTIMADO(A):"),0,0,'L');
    $pdf->SetFont('Courier','U',8);
    $pdf->Cell(20,3.5,utf8_decode($nombreCliente),0,1,'L');
    $pdf->SetFont('Courier','',7.5);
    $pdf->Cell(20,3.5,utf8_decode("En atención a su solicitud, me permito enviarle la siguiente cotización correspondiente a los productos de su intertés:"),0,1,'L');


//     $pdf->SetLineWidth(1);
//     $pdf->SetFont('Courier','B',10);
//     $pdf->Cell(20,6,utf8_decode("RECIBIÓ:"),0,0,'L');
//     $pdf->SetFont('Courier','',10);
//     $pdf->Cell(60,6,utf8_decode($cajero),0,0,'L');
//     $pdf->SetFont('Courier','B',10);
//     $pdf->Cell(22,6,utf8_decode('NO DOCTO:'),0,0,'L');
//     $pdf->SetFont('Courier','',10);
//     $pdf->Cell(42,6,utf8_decode('$noDocto'),1,0,'C',true);
//     $pdf->SetFont('Courier','B',10);
//     $pdf->Cell(13,6,utf8_decode('TIPO:'),0,0,'L');
//     $pdf->SetFont('Courier','',10);
//     $pdf->Cell(43,6,utf8_decode('$tipoDoc'),1,1,'C',true);
//
//
//     $pdf->SetFont('Courier','',10);
//
// //    $pdf->SetFont('Courier','B',10);
//     $pdf->SetFont('Courier','B',10);
//     $pdf->Cell(29,6,utf8_decode('FECHA EXPIRA:'),0,0,'L');
//     $pdf->SetFont('Courier','',10);
//     $pdf->Cell(33,6,utf8_decode('$fechaExpira'),1,0,'C',true);
//     $pdf->SetFont('Courier','B',10);
//     $pdf->Cell(17,6,utf8_decode('PAGO:'),0,0,'L');
//     $pdf->SetFont('Courier','',10);
//     $pdf->Cell(37,6,utf8_decode('$estado'),1,1,'C',true);
    $pdf->SetLineWidth(0.2);
    //$pdf->Cell(198,0,"",1,1,'L');
    $pdf->Cell(100,2,"",0,1,'L',0);
    $pdf->SetFillColor(102, 0, 204);
    $pdf->SetTextColor(255);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(22,4,utf8_decode('CANT.'),0,0,'C',1);
    $pdf->Cell(34,4,utf8_decode('UNIDAD'),0,0,'C',1);
    $pdf->Cell(72,4,utf8_decode('DESCRIPCIÓN DEL ARTÍCULO'),0,0,'C',1);
    $pdf->Cell(33,4,utf8_decode('P. UNITARIO'),0,0,'C',1);
    $pdf->Cell(37,4,utf8_decode('IMPORTE'),0,1,'C',1);
    //$pdf->SetFillColor(255);
    $pdf->SetFont('Courier','',9);
    $pdf->SetTextColor(0);
    $pdf->SetFillColor(0);

    $sql = "SELECT
                productos.nombrelargo           AS nombre,
                detallecotizacion.cantidad      AS cantidad,
                detallecotizacion.precio        AS precio,
                detallecotizacion.subTotal      AS subtotal,
                detallecotizacion.descripcion   AS descripcion,
                unidadesventa.nombre            AS unidadVenta
            FROM detallecotizacion
            INNER JOIN productos
            ON detallecotizacion.producto = productos.id
            INNER JOIN unidadesventa
            ON productos.unidadventa = unidadesventa.id
            WHERE detallecotizacion.cotizacion = $idCotizacion AND detallecotizacion.activo = 1";
    $resultDetalle  = $mysqli->query($sql);
    $totProd = 0;
    while ($rowD    = $resultDetalle->fetch_assoc())
    {
        $nombre     = ($rowD['descripcion'] == '0') ? $rowD['nombre'] : $rowD['descripcion'];
        $cantidad   = $rowD['cantidad'];
        $precio     = $rowD['precio'];
        $subtotal   = $rowD['subtotal'];
        $unidad     = $rowD['unidadVenta'];
        $pdf->SetFont('Courier','',10);
        $pdf->Cell(22,4,utf8_decode(number_format($cantidad,3)),0,0,'R',0);
        $pdf->Cell(34,4,utf8_decode($unidad),0,0,'C',0);
        $pdf->Cell(72,4,utf8_decode($nombre),0,0,'L',0);
        $pdf->Cell(33,4,utf8_decode(number_format($precio,2,".",",")),0,0,'R',0);
        $pdf->Cell(37,4,utf8_decode(number_format($subtotal,2,".",",")),0,1,'R',0);
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
    $totalCompra = $rowCompra['totalventa'];
    //$totalCompra_f = number_format($totalCompra, 2);
    $pdf->Cell(38,6,utf8_decode('$ '.number_format($totalCompra,2,".",",")),1,1,'R',0);
    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(27,3.5,utf8_decode('OBSERVACIONES:'),0,0,'L');
    $pdf->SetFont('Courier','',7.5);
    $pdf->Cell(20,3.5,utf8_decode("-Todos nuestros precios incluyen IVA"),0,1,'L');
    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(27,3.5,utf8_decode(''),0,0,'L');
    $pdf->SetFont('Courier','',7.5);
    $pdf->Cell(20,3.5,utf8_decode("-Vigencia de la presente: 2 días naturales a partir de la fecha de elaboración"),0,1,'L');
    $pdf->SetFont('Courier','B',8);
    $pdf->Cell(27,3.5,utf8_decode(''),0,0,'L');
    $pdf->SetFont('Courier','',7.5);
    $pdf->Cell(20,3.5,utf8_decode("-Precios pueden cambiar sin previo aviso"),0,1,'L');
    $pdf->SetFont('Courier','',8.5);
    $pdf->Cell(200,2,utf8_decode(""),0,1,'C');
    $pdf->Cell(200,3.5,utf8_decode("ATENTAMENTE"),0,1,'C');
    $pdf->Cell(200,12,utf8_decode(""),0,1,'C');
    $pdf->Cell(200,3.5,utf8_decode("_________________________________________"),0,1,'C');
    $pdf->Cell(200,3.5,utf8_decode($rowConfig['nombreRepresentante']),0,1,'C');
    //$pdf->Output('D','Remision-'.$code.'.pdf');
    if ($enviar_ == 1)
    {
        $pdf->Output('F',"../ws/XML/".str_pad($idCotizacion, 13, "0", STR_PAD_LEFT).".pdf");
    }
    else
    {
        $pdf->Output();
    }

}
 ?>
