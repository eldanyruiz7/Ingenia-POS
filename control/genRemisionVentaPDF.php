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
    function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
    //
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                            } else {
                                $key = (int) substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                }
                                else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int) substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int) substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                }
                                else {
                                    $key = (int) substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                            } else {
                                $key = (int) substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena.= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena.= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN BILLON ";
                        else
                            $xcadena.= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena.= "UN MILLON ";
                        else
                            $xcadena.= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO PESOS $xdecimales/100 M.N.";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN PESO $xdecimales/100 M.N. ";
                        }
                        if ($xcifra >= 2) {
                            $xcadena.= " PESOS $xdecimales/100 M.N. "; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }
    function subfijo($xx)
    { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    }
    $pdf = new PDF_Code128('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetMargins(8,20);
    $pdf->SetFont('Courier','',9);
    //$pdf->SetFillColor(225);
    $idVenta = $_GET['idVenta'];
    $existeNota = 0;
    $sql = "SELECT * FROM notacredito WHERE venta = $idVenta LIMIT 1";
    $resultNota = $mysqli->query($sql);
    if($resultNota->num_rows > 0)
    {
        $existeNota = 1;
        $rowNota    = $resultNota->fetch_assoc();
        $idNota     = $rowNota['id'];
        $credito    = $rowNota['credito'];
        $nTotal     = $rowNota['nuevototalventa'];
        $tipoNota   = $rowNota['tipo']; // 2 = regresar dinero en efectivo
        $obsNota    = $rowNota['observaciones'];
    }
    $sql            = "SELECT * FROM ventas WHERE id = $idVenta LIMIT 1";
    $result         = $mysqli->query($sql);
    $rowVenta       = $result->fetch_assoc();
    $idUsuario      = $rowVenta['usuario'];
    $idCliente      = $rowVenta['cliente'];
    $ocultarPU      = $rowVenta['ocultarPU'];
    $totalVenta     = number_format($rowVenta['totalventa'],2,".",",");
    $sql            = "SELECT nombre, apellidop FROM usuarios WHERE id = $idUsuario LIMIT 1";
    $resultUsr      = $mysqli->query($sql);
    $rowUsr         = $resultUsr->fetch_assoc();
    $nombreUsuario  = $rowUsr['nombre'];
    $sql            = "SELECT rsocial, calle, numeroext, numeroint, colonia, municipio, cp, telefono1 FROM clientes WHERE id = $idCliente LIMIT 1";
    $resultNomCte   = $mysqli->query($sql);
    $rowNomCte      = $resultNomCte->fetch_assoc();
    $nombreCliente  = $rowNomCte['rsocial']; //." ".$rowNomCte['apellidom'];
    $calleCliente   = $rowNomCte['calle'];
    $numExtCliente  = $rowNomCte['numeroext'];
    $numIntCliente  = $rowNomCte['numeroint'];
    $coloniaCliente = $rowNomCte['colonia'];
    $municCliente   = $rowNomCte['municipio'];
    $cpCliente      = $rowNomCte['cp'];
    $telCliente     = $rowNomCte['telefono1'];
    $code           = str_pad($rowVenta['id'], 12, "0", STR_PAD_LEFT);
    $fechaHora      = date('d-m-Y / h:i:s a', strtotime($rowVenta['timestamp']));
    $soloFecha      = date('d/m/Y', strtotime($rowVenta['timestamp']));
    $cajero         = $rowUsr['nombre']." ".$rowUsr['apellidop'];
    // $pagare         = "DEBO Y PAGARÉ Incondicionalmente a la orden de  El día ___ de ______________ del ______ en la Ciudad y Puerto de Manzanillo, Col. o donde elija el tenedor, la Cantidad arriba escrita mercancía que he recibido a mi entera satisfacción. Si no fuera cubierta dicha cantidad el día precisamente de su vencimiento pagaré intereses moratoria al 05% mensual desde la fecha de su vencimiento. Este pagaré es mercantil y está regido por la Ley General de Títulos y Operaciones de Crédito en su Art. 150, 151, 173 y 174 parte final y demás correlativos por no ser pagaré domiciliado.";
    $pagare         = "En Manzanillo, Col. a $soloFecha, por este pagaré reconozco(emos) deber y me(nos) obligo(amos) a pagar incondicionalmente el día $soloFecha a favor de _________________, en la ciudad y puerto de Manzanillo, Colima, o en cualquier otra que se nos requiera de pago, la cantidad de $$totalVenta (".numtoletras($totalVenta).") Valor recibido en mercancía a mi entera satisfacción. La falta de paggo puntual de este documento obliga al aceptante a cubrir intereses moratorios a razón del 5% mensual, más el I.V.A. respectivo hasta su pago insoluto. Este pagaré es mercantil y está regido por la ley general de títulos y operaciones de crédito en su artículo 173 parte final y artículos correlativos.";
    /*$montoLista     = $rowNota['montoLista'];
    $montoPublico   = $rowNota['montoPublico'];
    $observaciones  = $rowNota['observaciones'];*/
    $pdf->Code128(169,10," ".$code,38,12);
    $pdf->SetXY(174,20);
    $pdf->Cell(30,7,$code,0,1,'C');
    //$pdf->Write(5,$code);
    $pdf->SetXY(6,10);
    $pdf->SetFont('Courier','B',20);
    $pdf->SetTextColor(0,0,100);
    //Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
    //$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
    $pdf->Cell(200,7,utf8_decode('EL NOMBRE DE TU NEGOCIO AQUÍ'),0,1,'C');
    $pdf->SetTextColor(0);
    //$pdf->SetXY(15,11);
    $pdf->SetFont('Courier','U',16);
    $pdf->Cell(200,8,utf8_decode('REMISIÓN'),0,1,'C');
    //$pdf->SetXY(180,20);
    $pdf->SetFillColor(228);
    $pdf->SetDrawColor(255);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(26,4,utf8_decode("LE ATENDIÓ:"),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(50,4,utf8_decode($cajero),1,1,'L');
    $pdf->SetFont('Courier','B',10);
    $pdf->SetLineWidth(1.5);
    $pdf->Cell(16,6,utf8_decode('NOMBRE:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(100,6,utf8_decode($nombreCliente),1,0,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(24,6,utf8_decode('FECHA/HORA:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(58,6,utf8_decode($fechaHora),1,1,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(16,6,utf8_decode('CALLE:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(112,6,utf8_decode($calleCliente),1,0,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(16,6,utf8_decode('NºEXT.:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(19,6,utf8_decode($numExtCliente),1,0,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(16,6,utf8_decode('NºINT.:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(19,6,utf8_decode($numIntCliente),1,1,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(18,6,utf8_decode('COLONIA:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(39,6,utf8_decode($coloniaCliente),1,0,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(16,6,utf8_decode('CIUDAD:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(39,6,utf8_decode($municCliente),1,0,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(13,6,utf8_decode('C.P.:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(18,6,utf8_decode($cpCliente),1,0,'C',true);
    $pdf->SetFont('Courier','B',10);
    $pdf->Cell(12,6,utf8_decode('TEL.:'),0,0,'L');
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(43,6,utf8_decode($telCliente),1,1,'C',true);
    $pdf->SetLineWidth(0.2);
    //$pdf->Cell(198,0,"",1,1,'L');
    $pdf->Cell(100,2,"",0,1,'L',0);
    $pdf->SetFillColor(034,113,179);
    $pdf->SetTextColor(255);
    $pdf->SetFont('Courier','B',9.5);
    $pdf->Cell(22,4,utf8_decode('CANT.'),0,0,'C',1);
    $pdf->Cell(30,4,utf8_decode('UNIDAD'),0,0,'C',1);
    $pdf->Cell(72,4,utf8_decode('DESCRIPCIÓN DEL ARTÍCULO'),0,0,'C',1);
    $pdf->Cell(10,4,utf8_decode('IVA'),0,0,'C',1);
    $pdf->Cell(10,4,utf8_decode('IEPS'),0,0,'C',1);
    if ($ocultarPU == 0)
        $pdf->Cell(29,4,utf8_decode('P. UNITARIO'),0,0,'R',1);
    else
        $pdf->Cell(29,4,utf8_decode(''),0,0,'R',1);
    $pdf->Cell(25,4,utf8_decode('IMPORTE'),0,1,'C',1);
    //$pdf->SetFillColor(255);
    $pdf->SetFont('Courier','',9);
    $pdf->SetTextColor(0);
    $pdf->SetFillColor(0);

    $sql = "SELECT
                productos.nombrecorto           AS nombre,
                detalleventa.nombreCorto        AS nombre_det,
                productos.IVA                   AS IVA,
                productos.IEPS                  AS IEPS,
                productos.claveSHCP             AS claveSAT,
                detalleventa.cantidad           AS cantidad,
                detalleventa.precio             AS precio,
                detalleventa.subtotal           AS subtotal,
                detalleventa.id                 AS idSubVenta,
                unidadesventa.nombre            AS unidadVenta
            FROM detalleventa
            INNER JOIN productos
            ON detalleventa.producto = productos.id
            INNER JOIN unidadesventa
            ON productos.unidadventa = unidadesventa.id
            WHERE detalleventa.venta = $idVenta AND detalleventa.activo = 1";
    $resultDetalle  = $mysqli->query($sql);
    $totProd        = 0;
    while ($rowD    = $resultDetalle->fetch_assoc())
    {
        $nombre     = (strlen($rowD['nombre_det']) <= 1) ? $rowD['nombre'] : $rowD['nombre_det'];
        $iva        = $rowD['IVA'];
        $ieps       = $rowD['IEPS'];
        $claveSAT   = $rowD['claveSAT'];
        $cantidad   = $rowD['cantidad'];
        $precio     = $rowD['precio'];
        $subtotal   = $rowD['subtotal'];
        $unidad     = $rowD['unidadVenta'];
        $pdf->SetFont('Courier','',9.6);
        $pdf->Cell(22,4,utf8_decode(number_format($cantidad,3)),0,0,'R',0);
        $pdf->Cell(30,4,utf8_decode($unidad),0,0,'C',0);
        $pdf->Cell(72,4,utf8_decode($nombre),0,0,'L',0);
        $pdf->Cell(10,4,utf8_decode($iva."%"),0,0,'R',0);
        $pdf->Cell(10,4,utf8_decode($ieps."%"),0,0,'R',0);
        if ($ocultarPU == 0)
            $pdf->Cell(29,4,utf8_decode(number_format($precio,2,".",",")),0,0,'R',0);
        else
            $pdf->Cell(29,4,"",0,0,'R',0);
        $pdf->Cell(25,4,utf8_decode(number_format($subtotal,2,".",",")),0,1,'R',0);
        $totProd ++;
        if($existeNota == 1 && $tipoNota == 2) // $tipoNota = 2 ** Regresar efectivo
        {
            $pdf->SetFont('Courier','B',10);
            $idSubVenta = $rowD['idSubVenta'];
            $sql = "SELECT cantidad, subTotal FROM notacreditocambio WHERE idsubventa = $idSubVenta LIMIT 1";
            $resultDetalleNota = $mysqli->query($sql);
            if($resultDetalleNota->num_rows > 0)
            {
                $rowDetalleNota = $resultDetalleNota->fetch_assoc();
                $c_n = $rowDetalleNota['cantidad'];
                $s_n = $rowDetalleNota['subTotal'];
                $pdf->Cell(22,4,utf8_decode("-".number_format($c_n,3)),1,0,'R',0);
                $pdf->Cell(30,4,"",0,0,'C',0);
                $pdf->Cell(72,4,utf8_decode($nombre),0,0,'L',0);
                $pdf->Cell(49,4,"",1,0,'C',0);
                $pdf->Cell(26,4,utf8_decode("-".number_format($s_n,2,".",",")),1,1,'R',0);
            }
        }
    }
    $pdf->SetDrawColor(0);
    $pdf->Cell(198,2,"",0,1,'L');
    $pdf->Cell(198,0,"",1,1,'L');
    if($existeNota == 0 || $tipoNota == 1) // $tipoNota = 1 ** Cambio por articulo igual
    {
        //$pdf->Cell(100,20,"",0,1,'L');
        $pdf->Cell(198,0,"",1,1,'L');
        $totalVenta     = $rowVenta['totalventa'];
        $totalVenta_f   = number_format($totalVenta, 2,".",",");
        $pdf->SetFont('Courier','',10);
        $pdf->Cell(40,6,utf8_decode('TOTAL ARTS.:'),0,0,'R');
        $pdf->SetFont('Courier','B',10);
        $pdf->Cell(20,6,utf8_decode($totProd),0,0,'L');
        $pdf->Cell(67,6,utf8_decode(''),0,0,'R');
        $pdf->SetFont('Courier','',10);
        $pdf->Cell(33,6,utf8_decode('TOTAL:'),1,0,'R',0);
        $pdf->SetFont('Courier','B',10);
        //$pdf->Cell(22,6,utf8_decode(number_format($montoLista,2,".",",")),1,0,'R',0);
        $pdf->Cell(38,6,utf8_decode('$ '.$totalVenta_f),1,1,'R',0);
    }
    elseif($existeNota == 1 && $tipoNota == 2) // $tipoNota = 2 ** Regresar efectivo
    {
        $totalVenta = $rowVenta['totalventa'];
        $totalVenta_f = number_format($totalVenta, 2,".",",");
        $credito = number_format($credito, 2,".",",");
        $nTotal = number_format($nTotal, 2,".",",");
        $pdf->SetFont('Courier','',10);
        $pdf->Cell(127,4,utf8_decode(''),0,0,'R');
        $pdf->Cell(33,4,utf8_decode('SUMA:'),0,0,'R',0);
        $pdf->Cell(38,4,utf8_decode(''.$totalVenta_f),1,1,'R',0);

        $pdf->SetFont('Courier','',10);
        $pdf->Cell(127,4,utf8_decode(''),0,0,'R');
        $pdf->Cell(33,4,utf8_decode('DEVOLUCIÓN:'),0,0,'R',0);
        $pdf->Cell(38,4,utf8_decode('-'.$credito),1,1,'R',0);

        $pdf->Cell(127,4,utf8_decode(''),0,0,'R');
        $pdf->Cell(33,4,utf8_decode('TOTAL:'),0,0,'R',0);
        $pdf->SetFont('Courier','B',10);
        $pdf->Cell(38,4,utf8_decode('$'.$nTotal),1,1,'R',0);
    }
    if ($existeNota == 1)
    {
        $sql = "SELECT * FROM tiponotacredito WHERE id = $tipoNota LIMIT 1";
        $resultNombreNota = $mysqli->query($sql);
        $rowNombreNota = $resultNombreNota->fetch_assoc();
        $motivoNota = $rowNombreNota['nombre'];
        $pdf->SetFont('Courier','B',10);
        //$pdf->Cell(127,6,utf8_decode(''),0,0,'R');
        if (isset($obsNota) && strlen($obsNota) > 0)
        {
            $pdf->SetFont('Courier','',10);
            $pdf->Cell(40,4,"OBSERVACIONES:",0,0,'R',0);
            $pdf->SetFont('Courier','I',8);
            $pdf->MultiCell(120,4,utf8_decode("*"."$obsNota"),0,'L');
        }
        $y = $pdf->GetY();
        if ($y > 205)
            $pdf->AddPage();
        $pdf->SetFont('Courier','',10);
        $pdf->Cell(198,7,utf8_decode("<< ".$motivoNota." >>"),0,1,'C',0);
        if($tipoNota == 1) // $tipoNota = 1 ** Cambio por otro artículo igual
        {
            $pdf->SetFont('Courier','B',10);
            $pdf->Cell(127,6,utf8_decode(''),0,1,'R');
            $pdf->Cell(0,6,utf8_decode('RECIBÍ CAMBIO POR LOS SIGUIENTES ARTÍCULOS:'),0,1,'C',0);
            $sql = "SELECT
                        productos.nombrecorto       AS nombre,
                        notacreditocambio.cantidad  AS cantidad,
                        notacreditocambio.precio    AS precio,
                        notacreditocambio.subtotal  AS subtotal,
                        unidadesventa.nombre        AS unidadVenta
                    FROM notacreditocambio
                    INNER JOIN productos
                    ON notacreditocambio.idproducto = productos.id
                    INNER JOIN unidadesventa
                    ON productos.unidadventa = unidadesventa.id
                    WHERE notacreditocambio.idnotacredito = $idNota";
            $resultDetalleCredito = $mysqli->query($sql);
            while ($rowC    = $resultDetalleCredito->fetch_assoc())
            {
                $nombre     = $rowC['nombre'];
                $cantidad   = $rowC['cantidad'];
                $precio     = $rowC['precio'];
                $subtotal   = $rowC['subtotal'];
                $unidad     = $rowC['unidadVenta'];
                $pdf->SetFont('Courier','',10);
                $pdf->Cell(22,4,utf8_decode(number_format($cantidad,3)),0,0,'R',0);
                $pdf->Cell(30,4,utf8_decode($unidad),0,0,'C',0);
                $pdf->Cell(72,4,utf8_decode($nombre),0,0,'L',0);
                $pdf->Cell(20,4,"",0,0,'L',0);
                $pdf->Cell(29,4,utf8_decode(number_format($precio,2,".",",")),0,0,'R',0);
                $pdf->Cell(25,4,utf8_decode("$ ".number_format($subtotal,2,".",",")),0,1,'R',0);
                $totProd ++;
            }
        }
        if($tipoNota == 2)
        {
            $y = $pdf->GetY();
            if ($y > 205)
                $pdf->AddPage();
            $pdf->SetFont('Courier','',10);
            $pdf->Cell(113,3,utf8_decode("RECIBÍ LA CANTIDAD DE:"),0,0,'R');
            $pdf->SetFont('Courier','B',10);
            $pdf->Cell(30,3,utf8_decode("$$credito"),0,1,'C');
            //$pdf->Cell(33,6,,1,1,'R',0);
        }
        $pdf->SetFont('Courier','',6.5);
        $pdf->Cell(100,5,"",0,1,'L',0);
        $pdf->MultiCell(0,2.3,utf8_decode($pagare),0,'J');
        $pdf->Cell(100,14,"",0,1,'L',0);
        $pdf->SetFont('Courier','',10);
        $pdf->Cell(198,4,"_______________________________________",0,1,'C',0);
        $pdf->Cell(198,3,'FIRMA',0,1,'C',0);

    }
    else
    {
        $y = $pdf->GetY();
        if ($y > 205)
            $pdf->AddPage();
        $pdf->Cell(100,5,"",0,1,'L',0);
        $pdf->SetFont('Courier','',6.5);
        $pdf->MultiCell(0,2.3,utf8_decode($pagare),0,'J');
        $pdf->Cell(100,14,"",0,1,'L',0);
        $pdf->Cell(198,4,"_______________________________________",0,1,'C',0);
        $pdf->Cell(198,3,'FIRMA',0,1,'C',0);
        //$recibo         .= "</table>";
    }
    $pdf->Image('../images/logo.jpg',6,4,-370);
    //$pdf->Output('D','Remision-'.$code.'.pdf');
    $pdf->Output();
}
 ?>
