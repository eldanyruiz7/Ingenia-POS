<?php
    function genFactura($idF,$mysqli,$vista)
    {
        $sql_f              = "SELECT * FROM facturas WHERE id = $idF LIMIT 1";
        $result             = $mysqli->query($sql_f);
        $row                = $result->fetch_assoc();
        $rfcEmisor          = $row['rfcEmisor'];
        $razonEmisor        = $row['razonEmisor'];
        $regimenEmisor      = $row['regimenEmisor'];
        $calleEmisor        = $row['calleEmisor'];
        $coloniaEmisor      = $row['coloniaEmisor'];
        $municipioEmisor    = $row['municipioEmisor'];
        $numeroExtEmisor    = $row['numeroExtEmisor'];
        $numeroIntEmisor    = $row['numeroIntEmisor'];
        $poblacionEmisor    = $row['poblacionEmisor'];
        $entidadEmisor      = $row['entidadEmisor'];
        $cpEmisor           = $row['cpEmisor'];
        $idReceptor         = $row['idReceptor'];
        $rfcReceptor        = $row['rfcReceptor'];
        $razonReceptor      = $row['razonReceptor'];
        $aliasReceptor      = $row['aliasReceptor'];
        $calleReceptor      = $row['calleReceptor'];
        $coloniaReceptor    = $row['coloniaReceptor'];
        $municipioReceptor  = $row['municipioReceptor'];
        $numeroExtReceptor  = $row['numeroExtReceptor'];
        $numeroIntReceptor  = $row['numeroIntReceptor'];
        $poblacionReceptor  = $row['poblacionReceptor'];
        $entidadReceptor    = $row['entidadReceptor'];
        $cpReceptor         = $row['cpReceptor'];
        $regimenReceptor    = $row['regimenReceptor'];
        $emailReceptor      = $row['emailReceptor'];
        $vigencia           = $row['vigencia'];
        $fechaEmision       = $row['fechaEmision'];
        $cpExpedicion       = $row['cpExpedicion'];
        $usoCFDI            = $row['usoCFDI'];
        $sql                = "SELECT Descripcion FROM usoCFDI WHERE c_UsoCFDI = '$usoCFDI' LIMIT 1";
        $res_usoCFDI        = $mysqli->query($sql);
        $row_usoCFDI        = $res_usoCFDI->fetch_assoc();
        $usoCFDI            = $usoCFDI." - ".$row_usoCFDI['Descripcion'];
        $tipoCFDI           = $row['tipoCFDI'];
        $moneda             = $row['moneda'];
        $formaPago          = $row['formaPago'];
        $sql                = "SELECT nombre FROM metodosdepago WHERE c_FormaPago = $formaPago LIMIT 1";
        $res_fPago          = $mysqli->query($sql);
        $row_fPago          = $res_fPago->fetch_assoc();
        $formaPago          = $formaPago." - ".$row_fPago['nombre'];
        $metodoPago         = $row['metodoPago'];
        $nombreBanco        = $row['nombreBanco'];
        $cuentaBancaria     = $row['cuentaBancaria'];
        $clabeInterbancaria = $row['clabeInterbancaria'];
        $numeroCheque       = $row['numeroCheque'];
        $serieCertCFDI      = $row['noCertificado'];
        $folioFiscalCFDI    = $row['noCertificadoSAT'];
        $folioFiscal        = $row['folioFiscal'];
        $selloDigitalEmisor = $row['selloDigitalEmisor'];
        $selloDigitalSAT    = $row['selloDigitalSAT'];
        $cadenaOriginal     = $row['cadenaOriginal'];
        $cadenaOriginal_C   = $row['cadenaOriginalCumplimiento'];
        $codigo_QR          = $row['codigoQR'];
        $fechaCertificacion = $row['fechaCertificacion'];
        $totalIva           = $row['totalIVA'];
        $totalIeps          = $row['totalIEPS'];
        $subTotal           = number_format($row['subTotal'],2,".",",");
        $total              = $row['total'];
        $descuento          = $row['descuento'];
        $version            = $row['version'];
        $pdf                = new PDF_Code128('P','mm','Letter');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetMargins(8,20);
        $pdf->SetFont('Courier','B',9);
        //$pdf->SetFillColor(225);
        $pdf->Image('../images/logo.jpg',9,8,-560);
        //$pdf->Image('certEmisorPrueba/codigoqr.png',173,25,-285);
        $qrcode = new QRcode($codigo_QR, 'H'); // error level : L, M, Q, H
        $qrcode->disableBorder();
        $qrcode->displayFPDF($pdf, 173, 25, 30);
        //$idFactura = $_GET['idFactura'];
        $mB                 = 0; // Mostrar ocultar bordes cell
        $code               = "Folio: ";
        $code               .=  $code_ = str_pad($idF, 12, "0", STR_PAD_LEFT);
        $pdf->Code128(169,10," ".$code_,38,12);
        $pdf->SetXY(174,20);
        $pdf->Cell(30,7,$code,0,1,'C');
        //$pdf->Write(5,$code);
        $pdf->SetXY(8,10);
        $pdf->SetFont('Arial','B',15);
        $pdf->SetTextColor(0,0,100);
        $pdf->Cell(200,7,utf8_decode('"SUPER DON ALEX"'),$mB,1,'C');
        $pdf->SetFont('Arial','',10);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(200,4,utf8_decode($razonEmisor),$mB,1,'C');
        $pdf->Cell(200,4,utf8_decode("$calleEmisor No. $numeroExtEmisor"),$mB,1,'C');
        $pdf->Cell(200,4,utf8_decode("$coloniaEmisor, $municipioEmisor, $entidadEmisor"),$mB,1,'C');
        $pdf->SetFont('Arial','B',12);
        $pdf->SetTextColor(0,0,100);
        $pdf->Cell(200,4,utf8_decode("RFC: $rfcEmisor"),$mB,1,'C');
        $pdf->SetFont('Arial','B',10);
        //$pdf->Cell(200,4,utf8_decode($regimenEmisor),$mB,1,'C');
        $pdf->Cell(200,4,utf8_decode("Régimen de incorporación Fiscal"),$mB,1,'C');
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Nombre Receptor: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($razonReceptor),$mB,0,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Método de pago: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($metodoPago),$mB,1,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('RFC Receptor: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($rfcReceptor ),$mB,0,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Forma de pago: '),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($formaPago),$mB,1,'L');

        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(30,3,utf8_decode('Domicilio Receptor:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->MultiCell(70,3,utf8_decode("$calleReceptor No. $numeroIntReceptor Int. $numeroIntReceptor. Col. $coloniaReceptor, $municipioReceptor, $entidadReceptor, C.P. $cpReceptor"),$mB,'L');

        $pdf->SetXY(8,43);
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(100,3,utf8_decode(''),$mB,0,'L');
        $pdf->Cell(30,3,utf8_decode('Tipo comprobante:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($tipoCFDI),$mB,1,'L');
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(100,3,utf8_decode(''),$mB,0,'L');
        $pdf->Cell(30,3,utf8_decode('Moneda:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($moneda),$mB,1,'L');
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(100,3,utf8_decode(''),$mB,0,'L');

        $pdf->Cell(30,3,utf8_decode('Fecha-hr emisión:'),$mB,0,'L');
        $pdf->SetFont('Arial','',7.5);
        $pdf->Cell(70,3,utf8_decode($fechaEmision),$mB,1,'L');
        $pdf->SetFont('Arial','B',7.5);
        $pdf->Cell(200,3,utf8_decode(''),$mB,1,'L');
        $pdf->Cell(200,2,utf8_decode(''),$mB,1,'L');
        $pdf->Cell(16,3.5,utf8_decode('CLAVE'),1,0,'L');
        $pdf->Cell(18,3.5,utf8_decode('CANT'),1,0,'C');
        $pdf->Cell(18,3.5,utf8_decode('UNIDAD'),1,0,'C');
        $pdf->Cell(69,3.5,utf8_decode('DESCRIPCIÓN'),1,0,'C');
        $pdf->Cell(23,3.5,utf8_decode('PRECIOU'),1,0,'C');
        $pdf->Cell(16,3.5,utf8_decode('002 IVA'),1,0,'C');
        $pdf->Cell(16,3.5,utf8_decode('003 IEPS'),1,0,'C');
        $pdf->Cell(24,3.5,utf8_decode('IMPORTE'),1,1,'C');

        $pdf->SetFont('Arial','',7.5);
        $sql_det = "SELECT * FROM detalleFactura WHERE idFactura = $idF";
        $result_d = $mysqli->query($sql_det);
        while ($row_d = $result_d->fetch_assoc())
        {
            $pdf->Cell(16,3.5,utf8_decode($row_d['claveSHCP']),$mB,0,'L');
            $pdf->Cell(18,3.5,utf8_decode(number_format($row_d['cantidad'],3,".","")),$mB,0,'R');
            $pdf->Cell(18,3.5,utf8_decode($row_d['claveUnidad']."-".$row_d['nombreUnidad']),$mB,0,'L');
            $pdf->Cell(69,3.5,utf8_decode($row_d['descripcion']),$mB,0,'L');
            $pdf->Cell(23,3.5,utf8_decode(number_format($row_d['precioU'],2,".",",")),$mB,0,'R');
            $pdf->Cell(16,3.5,utf8_decode(number_format($row_d['iva'],2,".","")),$mB,0,'C');
            $pdf->Cell(16,3.5,utf8_decode(number_format($row_d['ieps'],2,".","")),$mB,0,'C');
            $pdf->Cell(24,3.5,utf8_decode(number_format($row_d['importe'],2,".",",")),$mB,1,'R');
        }

        $pdf->Cell(200,1.5,utf8_decode(''),$mB,1,'R');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,3.5,utf8_decode('SERIE DEL CERTIFICADO DEL EMISOR:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($serieCertCFDI),$mB,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(24,3.5,utf8_decode('SUB TOTAL:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode($subTotal),$mB,1,'R');

        $pdf->Cell(60,3.5,utf8_decode('FOLIO FISCAL:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($folioFiscal),$mB,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(24,3.5,utf8_decode('16% IVA:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode(number_format($totalIva,2,".",",")),$mB,1,'R');

        $pdf->Cell(60,3.5,utf8_decode('NO. DE SERIE DEL CERTIFICADO DEL SAT:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($folioFiscalCFDI),$mB,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(24,3.5,utf8_decode('8% IEPS:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode(number_format($totalIeps,2,".",",")),$mB,1,'R');

        $pdf->Cell(60,3.5,utf8_decode('FECHA Y HORA DE CERTIFICACIÓN:'),$mB,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(92,3.5,utf8_decode($fechaCertificacion),$mB,0,'L');
        $pdf->SetFont('Arial','B',9.5);
        $pdf->Cell(24,3.5,utf8_decode('TOTAL:'),$mB,0,'R');
        $pdf->Cell(24,3.5,utf8_decode("$".number_format($total,2,".",",")),$mB,1,'R');
        $pdf->Cell(200,2,utf8_decode(''),$mB,1,'R');
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(200,3.5,utf8_decode('ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI'),$mB,1,'C');
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(200,3.5,utf8_decode("VERSIÓN $version"),$mB,1,'C');
        $pdf->Cell(200,3.5,utf8_decode("USOCFDI: $usoCFDI"),$mB,1,'C');
        $pdf->Cell(200,1.5,utf8_decode(''),$mB,1,'R');
        $pdf->SetFont('Arial','B',5.5);
        $pdf->Cell(200,3.5,utf8_decode('SELLO DIGITAL DEL CFDI'),$mB,1,'C');
        $pdf->SetFont('Arial','',4.5);
        $pdf->MultiCell(200,2.5,$selloDigitalEmisor,$mB,'L');
        $pdf->SetFont('Arial','B',5.5);
        $pdf->Cell(200,3.5,utf8_decode('SELLO DIGITAL DEL SAT'),$mB,1,'C');
        $pdf->SetFont('Arial','',4.5);
        $pdf->MultiCell(200,2.2,$selloDigitalSAT,$mB,'L');
        $pdf->SetFont('Arial','B',5.5);
        $pdf->Cell(200,3.5,utf8_decode('CADENA ORIGINAL DEL COMPLEMENTO DE CERTIFICACIÓN DIGITAL DEL SAT'),$mB,1,'C');
        $pdf->SetFont('Arial','',4.5);
        $pdf->MultiCell(200,2.2,utf8_decode($cadenaOriginal_C),$mB,'L');
        if ($vista == 1)
            $pdf->Output();
        else
            $pdf->Output('F',"XML/".str_pad($idF, 13, "0", STR_PAD_LEFT).".pdf");
    }

?>
