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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../startbootstrap/vendor/PHPMailer-master/src/Exception.php';
require '../startbootstrap/vendor/PHPMailer-master/src/PHPMailer.php';
require '../startbootstrap/vendor/PHPMailer-master/src/SMTP.php';
require '../startbootstrap/vendor/fpdf/code128.php';
require '../startbootstrap/vendor/fpdf/qrcode/qrcode.class.php';
require 'genFacturaPDF.php';
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    $pp = 0;
    function progreso($x, $status, $error)
    {
        //$p = 0;
        global $pp;
        $pp += $x;
        $pp = ($pp > 100) ? 100 : $pp;
        if($error       == 0)
        {
            $script     =  "<script>";
            $script     .= "$('#progress-p').css('width','$pp%');";
            $script     .= "$('#span-completo').text('$pp% completado');";
            $script     .= "$('#strong-tarea').text('$status');";
            if($pp >= 100)
                $script .= "$('#progress-div').removeClass('active');";
            $script     .= '</script>';
            echo $script;
            flush();
            ob_flush();
        }
        else
        {
            $script     =  "<script>";
            $script     .= "$('#progress-p').removeClass('progress-bar-info');";
            $script     .= "$('#progress-p').addClass('progress-bar-danger');";
            $script     .= "$('#progress-div').removeClass('active');";
            $script     .= "$('#span-completo').text('$pp% completado');";
            $script     .= '$("#strong-tarea").text("No se pudo timbrar. Inténtalo nuevamente");';
            $script     .= '</script>';
            $respHTML   = str_replace("\"", "", $status);
            $script     .=  "<script>";
            $script     .= 'window.parent.msgFactura(0,"'.$respHTML.'");';
            $script     .= '</script>';
            echo $script;
            flush();
            ob_flush();
            exit(0);
        }
    }
    ?>
    <link href="../startbootstrap/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../startbootstrap/dist/css/sb-admin-2.css" rel="stylesheet">
    <link href="../startbootstrap/vendor/morrisjs/morris.css" rel="stylesheet">
    <link href="../startbootstrap/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet" type="text/css">
    <link href="../startbootstrap/vendor/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css">
    <script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="../startbootstrap/vendor/bootstrap/js/bootstrap.min.js"></script>
    <?php
    $html               = "<div>";
    $html               .="    <p>";
    $html               .="        <strong id='strong-tarea'>Inicializando...</strong>";
    $html               .="        <span class='pull-right text-muted' id='span-completo'>0% Completo</span>";
    $html               .="    </p>";
    $html               .='    <div id="progress-div" class="progress progress-striped active">';
    $html               .='        <div id="progress-p" class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">';
    $html               .='            <span class="sr-only">40% Complete (success)</span>';
    $html               .='        </div>';
    $html               .='    </div>';
    $html               .='</div>';
    echo $html;
    flush();
    ob_flush();
    progreso(10, "Recolectando datos", 0);
    usleep(500000);
    ////// Datos del emisor ///////////
    $coleccionFactura = json_decode($_GET['coleccionFacturaJSON']);
    //var_dump($coleccionFactura);
    $idVenta            = $_GET['idVenta'];
    $enviarA            = $_GET['enviarA'];
    $enviar             = $_GET['enviar'];
    // $rfcEmisor          = "SACJ830819U62";
    $rfcEmisor          = "AAA010101AAA";
    //$rfcEmisor          = "AIRY820226UXA";
    $razonEmisor        = "===FACTURA DE EJEMPLO===";
    // $razonEmisor        = "JORGE IVAN SALAZAR CHAVEZ";
    //$razonEmisor        = "YADIRA JANNETTE ARIAS ROJAS";
    $regimenEmisor      = "601";
    // $regimenEmisor      = "612";
    $calleEmisor        = "Eusebio Hernandez";
    $coloniaEmisor      = "Salagua";
    $municipioEmisor    = "Manzanillo";
    $numeroExtEmisor    = "510";
    $numeroIntEmisor    = "";
    $poblacionEmisor    = "Manzanillo";
    $entidadEmisor      = "9";
    $cpEmisor           = "28869";
    /////// Datos del receptor //////////
    $sql                = "SELECT
                            ventas.id                   AS idVenta,
                            ventas.cliente              AS idCliente,
                            ventas.metododepago         AS idFormaPago,
                            ventas.totalventa           AS totalVenta,
                            ventas.esCredito            AS esCredito,
                            clientes.rfc                AS rfcCliente,
                            clientes.rsocial            AS razonCliente,
                            clientes.calle              AS calleCliente,
                            clientes.numeroext          AS numeroExtCliente,
                            clientes.numeroint          AS numeroIntCliente,
                            clientes.colonia            AS coloniaCliente,
                            clientes.poblacion          AS poblacionCliente,
                            clientes.municipio          AS municipioCliente,
                            clientes.cp                 AS cpCliente,
                            clientes.estado             AS estadoCliente,
                            clientes.email              AS emailCliente,
                                (SELECT COUNT(*)
                                FROM detalleventa
                                WHERE detalleventa.venta = idVenta
                                AND detalleventa.facturable = 1) AS totalFacturables
                        FROM ventas
                        INNER JOIN clientes
                        ON ventas.cliente = clientes.id
                        WHERE ventas.id = $idVenta LIMIT 1";
    $result             = $mysqli->query($sql);
    $rowVenta           = $result->fetch_assoc();
    $idReceptor         = $rowVenta['idCliente'];
    $rfcReceptor        = $rowVenta['rfcCliente'];
    $razonReceptor      = $rowVenta['razonCliente'];
    $aliasReceptor      = "";
    $regimenReceptor    = "";
    $calleReceptor      = $rowVenta['calleCliente'];
    $numeroExtReceptor  = $rowVenta['numeroExtCliente'];
    $numeroIntReceptor  = $rowVenta['numeroIntCliente'];
    $coloniaReceptor    = $rowVenta['coloniaCliente'];
    $poblacionReceptor  = $rowVenta['poblacionCliente'];
    $municipioReceptor  = $rowVenta['municipioCliente'];
    $cpReceptor         = $rowVenta['cpCliente'];
    $entidadReceptor    = $rowVenta['estadoCliente'];
    $totalRows          = $rowVenta['totalFacturables'];
    $emailReceptor      = ($enviar == 0) ? $rowVenta['emailCliente'] : $enviarA;
    $sql_det            = ($enviar == 1) ? "UPDATE clientes SET email = '$emailReceptor' WHERE id = $idReceptor LIMIT 1;" : "";
    ///////////// Datos del CFDI ///////////////
    $metodoPago         = ($_GET['metodoPago'] == "PPD") ? "PPD" : "PUE";
    $pagado             = ($_GET['metodoPago'] == "PPD") ? 0 : 1;
    //$metodoPago         = "PUE";
    // $metodoPago         = "PPD";
    $pagado             = 1;
    if ($metodoPago == "PUE")
        $pagado         = 1;
    if ($metodoPago == "PPD")
        $pagado         = 0;
    $tipoComprobante    = "I";
    // $tipoComprobante    = "P";
    $moneda             = "MXN";
    // $moneda             = "XXX";
    $formaDePago        = "99";
    $fecha              = date('Y-m-d');
    $hora               = date('H:i:s');
    $fechaHora          = $fecha."T".$hora;
    $vigencia           = 'NULL';
    $version            = "3.3";
    $folio              = "08766";
    // $UsoCFDI            = 'G03';
    $UsoCFDI            = $_GET['usoCFDI'];//'P01';
    $nombreBanco        = "NULL";
    $cuentaBancaria     = "NULL";
    $clabeInterbancaria = "NULL";
    $numeroCheque       = "NULL";
    //$noCertificado      = '30001000000300023707';
    // $noCertificado      = '0000100000040930875';

    progreso(10,"Creando XML", 0);
    usleep(500000);
    ///////WHILE///////////
    $sql = "SELECT
                detalleventa.id             AS idSubVenta,
                detalleventa.cantidad       AS cantidad,
                detalleventa.precio         AS valorUnitario,
                detalleventa.subTotal       AS importe,
                detalleventa.facturable     AS facturable,
                detalleventa.facturado      AS facturado,
                detalleventa.nombrecorto    AS descripcion,
                unidadesventa.nombre        AS unidad,
                unidadesventa.c_ClaveUnidad AS claveUnidad,
                productos.id                AS idProducto,
                productos.claveSHCP         AS claveSHCP,
                productos.IVA               AS tasaOCuotaIVA,
                productos.IEPS              AS tasaOCuotaIEPS
            FROM productos
            INNER JOIN detalleventa
            ON productos.id = detalleventa.producto
            INNER JOIN unidadesventa
            ON productos.unidadventa = unidadesventa.id
            WHERE detalleventa.venta = $idVenta
            ORDER BY facturable ASC";
    $result = $mysqli->query($sql);
    $totalRows          = $result->num_rows;
    $cont               = 0;
    $sumatoriaIVA       = 0;
    $sumatoriaSinIva    = 0;
    $sumatoriaIEPS      = 0;
    $sumatoriaSinIeps   = 0;
    $TOTAL              = 0;
    $subTotal           = 0;
    $tasaOCuotaIVATotal = 0;
    $tasaOCuotaIEPSTotal= 0;
    $x                  = 0;
    $totalSubTotal_fact = 0;
    $xmlCFDI_c          = '<cfdi:Conceptos>';
    while ($row = $result->fetch_assoc())
    {
        $cont++;
        //Calcular factoriva y ieps (1.16 y 1.08)

        $idSubVenta         = $row['idSubVenta'];
        $idProducto         = $row['idProducto'];
        $claveProdServ      = $row['claveSHCP'];
        $claveUnidad        = $row['claveUnidad'];
        $cantidad           = $row['cantidad'];
        $unidad             = $row['unidad'];
        $descripcion        = $row['descripcion'];
        $porCientoIVA       = $row['tasaOCuotaIVA'];
        $porCientoIEPS      = $row['tasaOCuotaIEPS'];
        $facturable         = $row['facturable'];
        $facturado          = $row['facturado'];
        if ($cont == $totalRows)
        {
            $esteSubTotal = $rowVenta['totalVenta'] - $totalSubTotal_fact;
            $estePrecioU = $esteSubTotal / $cantidad;
        }
        else
        {
            $estePrecioU = $row['valorUnitario'];
            $esteSubTotal = $row['importe'];
        }
        if ($facturable == 1)
        {
            $totalSubTotal_fact += $esteSubTotal;
        }
        $descuento          = 0;
        foreach ($coleccionFactura as $rowFact)
        {
            $id_F           =   $rowFact ->idProducto;
            $claveSAT_F     =   $rowFact ->claveSAT;
            $iva_F          =   $rowFact ->iva;
            $ieps_F         =   $rowFact ->ieps;
            //$descuento_F    =   $rowFact ->descuento;
            if ($id_F == $idProducto)
            {
                $claveProdServ  = $claveSAT_F;
                $porCientoIVA   = $iva_F;
                $porCientoIEPS  = $ieps_F;
                //DESPUÉS---->> $descuento = $descuento_F;
            }
        }
        if ($facturable == 1 && $facturado == 0)
        {

            $factorIva  = $porCientoIVA / 100;
            $factorIva++;
            $factorIeps  = $porCientoIEPS / 100;
            $factorIeps++;
            if ($porCientoIVA > 0 && $porCientoIEPS == 0)
            {
                $importe            = round($esteSubTotal / $factorIva, 2);
                $importeImpuestoIVA = round($esteSubTotal - $importe, 2);
                $importeImpuestoIEPS= 0;
                $sumatoriaSinIva    += $importe;
                $tasaOCuotaIVATotal = $porCientoIVA / 100;
                $valorUnitario      = round($estePrecioU / $factorIva, 2);
            }
            elseif ($porCientoIVA == 0 && $porCientoIEPS > 0)
            {
                $importe            = round($esteSubTotal / $factorIeps, 2);
                $importeImpuestoIVA = 0;
                $importeImpuestoIEPS= round($esteSubTotal - $importe, 2);
                $sumatoriaSinIeps   += $importe;
                $tasaOCuotaIEPSTotal= $porCientoIEPS / 100;
                $valorUnitario      = round($estePrecioU / $factorIeps, 2);
            }
            elseif  ($porCientoIVA == 0 && $porCientoIEPS == 0)
            {
                $importe            = round($esteSubTotal, 2);//round($esteSubTotal, 2);
                $valorUnitario      = round($estePrecioU, 2);
                $importeImpuestoIVA = 0;
                $importeImpuestoIEPS= 0;
                $sumatoriaSinIva    += $importe;
            }
            $impuestoIVA        = "002";
            $tipoFactorIVA      = "Tasa";
            $impuestoIEPS       = "003";
            $tipoFactorIEPS     = 'Tasa';
            $tasaOCuotaIVA      = $porCientoIVA / 100;
            $tasaOCuotaIEPS     = $porCientoIEPS / 100;
            $sumatoriaIVA       += number_format($importeImpuestoIVA,2,".","");
            $sumatoriaIEPS      += number_format($importeImpuestoIEPS,2,".","");
            $TOTAL              += $importe;
            $xmlCFDI_c            .= '<cfdi:Concepto ClaveProdServ="'.$claveProdServ.'" ';
            $xmlCFDI_c            .= 'ClaveUnidad="'.$claveUnidad.'" Cantidad="'.number_format($cantidad,3,".","").'" Unidad="'.$unidad.'" Descripcion="'.$descripcion.'" ValorUnitario="'.number_format($valorUnitario,2,".","").'" Importe="'.number_format($importe,2,".","").'" Descuento="0.00">';
            $xmlCFDI_c            .= '   <cfdi:Impuestos>';
            $xmlCFDI_c            .= '       <cfdi:Traslados>';
            $xmlCFDI_c            .= '           <cfdi:Traslado Base="'.number_format($importe,2,".","").'" Impuesto="'.$impuestoIVA.'" TipoFactor="'.$tipoFactorIVA.'" TasaOCuota="'.number_format($tasaOCuotaIVA,2,".","").'" Importe="'.number_format($importeImpuestoIVA,2,".","").'"/>';
            $xmlCFDI_c            .= '           <cfdi:Traslado Base="'.number_format($importe,2,".","").'" Impuesto="'.$impuestoIEPS.'" TipoFactor="'.$tipoFactorIEPS.'" TasaOCuota="'.number_format($tasaOCuotaIEPS,2,".","").'" Importe="'.number_format($importeImpuestoIEPS,2,".","").'"/>';
            $xmlCFDI_c            .= '       </cfdi:Traslados>';
            $xmlCFDI_c            .= '   </cfdi:Impuestos>';
            $xmlCFDI_c            .= '</cfdi:Concepto>';

            $detalle_factura[$x]['idSubVenta']  = $idSubVenta;
            $detalle_factura[$x]['idProducto']  = $idProducto;
            $detalle_factura[$x]['claveSHCP']   = $claveProdServ;
            $detalle_factura[$x]['cantidad']    = $cantidad;
            $detalle_factura[$x]['claveUnidad'] = $claveUnidad;
            $detalle_factura[$x]['nombreUnidad']= $unidad;
            $detalle_factura[$x]['descripcion'] = $descripcion;
            $detalle_factura[$x]['precioU']     = $valorUnitario;
            $detalle_factura[$x]['iva']         = $porCientoIVA;
            $detalle_factura[$x]['ieps']        = $porCientoIEPS;
            $detalle_factura[$x]['importe']     = $importe;
            //$detalle_factura[$x]['descuento']   = $descuento;
            $x++;
        }
    }
    $subTotal                   = $sumatoriaSinIva + $sumatoriaSinIeps;
    $totalImpuestosTrasladados  = $sumatoriaIVA + $sumatoriaIEPS;
    $TOTAL                      = number_format($subTotal + $totalImpuestosTrasladados,2,".","");
    $xmlCFDI_c            .= '</cfdi:Conceptos>';
    $xmlCFDI_c            .= '<cfdi:Impuestos TotalImpuestosTrasladados="'.number_format($totalImpuestosTrasladados,2,".","").'">';
    $xmlCFDI_c            .= '   <cfdi:Traslados>';
    $xmlCFDI_c            .= '       <cfdi:Traslado Impuesto="'.$impuestoIVA.'" TipoFactor="'.$tipoFactorIVA.'" TasaOCuota="'.number_format($tasaOCuotaIVATotal,2,".","").'" Importe="'.number_format($sumatoriaIVA,2,".","").'"/>';
    $xmlCFDI_c            .= '       <cfdi:Traslado Impuesto="'.$impuestoIEPS.'" TipoFactor="'.$tipoFactorIEPS.'" TasaOCuota="'.number_format($tasaOCuotaIEPSTotal,2,".","").'" Importe="'.number_format($sumatoriaIEPS,2,".","").'"/>';
    $xmlCFDI_c            .= '   </cfdi:Traslados>';
    $xmlCFDI_c            .= '</cfdi:Impuestos>';
    $xmlCFDI_c            .= '</cfdi:Comprobante>';
    //echo utf8_decode($xmlCFDI);
    $xmlCFDI            = '<?xml version="1.0" encoding="utf-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
    $xmlCFDI            .= 'LugarExpedicion="'.$cpEmisor.'" MetodoPago="'.$metodoPago.'" TipoDeComprobante="'.$tipoComprobante.'" Total="'.$TOTAL.'" Descuento="0.00" Moneda="'.$moneda.'" Certificado="" SubTotal="'.$subTotal.'" NoCertificado="00000000000000000000" FormaPago="'.$formaDePago.'" Sello="" Fecha="'.$fechaHora.'" Version="'.$version.'">';
    $xmlCFDI            .= '<cfdi:Emisor Rfc="'.$rfcEmisor.'" Nombre="'.$razonEmisor.'" RegimenFiscal="'.$regimenEmisor.'"></cfdi:Emisor>';
    $xmlCFDI            .= '<cfdi:Receptor Rfc="'.$rfcReceptor.'" Nombre="'.$razonReceptor.'" UsoCFDI="'.$UsoCFDI.'"></cfdi:Receptor>';
    $xmlCFDI            .= $xmlCFDI_c;
    $archivoXML_     = fopen("error.xml", "w");
    if($archivoXML_ == false)
    {
        progreso(0, "Error al crear archivo XML. Error de escritura", 1);
    }
    fwrite($archivoXML_, $xmlCFDI);
    fclose($archivoXML_);

    progreso(20, "Conectando al SAT", 0);
    usleep(500000);
    $response           = '';
    // $user               = "AIRY820226UXA_10";
    // $userPassword       = "58996256596700145927542";
    $user               = "AIRY820226UXA_3";
    $userPassword       = "184829659849329157062420";
    // $llavePrivadaEmisorPassword = "supersalazar123";
    $llavePrivadaEmisorPassword = "12345678a";

    //$llavePrivadaEmisorPassword = "sole1982";
    $certificadoEmisor  = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD05_AAA010101AAA.cer");
    $llavePrivadaEmisor = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD05_AAA010101AAA.key");
    // $certificadoEmisor  = file_get_contents("sellos/nuevo/SACJ830819U62.cer");
    // $llavePrivadaEmisor = file_get_contents("sellos/nuevo/SACJ830819U62.key");
    //$certificadoEmisor  = file_get_contents("sellos/nuevo/SACJ830819U62.cer");
    //$llavePrivadaEmisor = file_get_contents("sellos/nuevo/SACJ830819U62.key");
    //$certificadoEmisor  = file_get_contents("sellos/airy/csd/AIRY820226UXA.cer");
    //$llavePrivadaEmisor = file_get_contents("sellos/airy/csd/AIRY820226UXA.key");
    //$certificadoEmisor  = file_get_contents("certEmisorPrueba/airy820226uxa.cer");
    //$llavePrivadaEmisor = file_get_contents("certEmisorPrueba/airy820226uxa.key");
    //$user = "AIRY820226UXA_10";
    //$userPassword = "58996256596700145927542";
    progreso(10, "Descargando información",0);
    usleep(500000);
    try {
            $client     = new SoapClient("http://mieses.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl"); /////////////PRUEBAS
            // $client     = new SoapClient("http://pacfdisat.com:8080/CMM/InterconectaWs?wsdl"); //////////////////PRODUCCION
    		//var_dump($client->__getFunctions());
            $params     = array(
        			'user'                       => $user,
        			'userPassword'               => $userPassword,
        			'certificadoEmisor'          => $certificadoEmisor,
        			'llavePrivadaEmisor'         => $llavePrivadaEmisor,
        			'llavePrivadaEmisorPassword' => $llavePrivadaEmisorPassword,
        			'xmlCFDI'	                 => $xmlCFDI,
        			'versionCFDI'                => '3.3'
    		);
            $response   = $client->__soapCall('sellaTimbraCFDI', array('parameters' => $params));
    } catch (SoapFault $fault) {
            echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
            progreso(0, "Revisa tu conexión de internet y vuelve a intentarlo. ", 1);
//var_dump($ret);
            /*echo $script;
            exit(0);*/
    }
    progreso(10, "Generando factura", 0);
    usleep(500000);
    $ret                = $response->return;
    //var_dump($ret);
    /*try {
            $client = new SoapClient("http://mieses.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl");
    	//	var_dump($client->__getFunctions());
            $params = array(
    			'user' => $user,
    			'userPassword' => $userPassword,
    			'contribuyenteRFC'=> "AAA010101AAA",
    			'contribuyenteRazonSocial'=> "ACCEM SERVICIOS EMPRESARIALES SC",

    		);
            $response = $client->__soapCall('otorgarAccesoContribuyente', array('parameters' => $params));
    } catch (SoapFault $fault) {
            echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
    }*/
    //print_r("Estatus request: " . $ret->status . PHP_EOL);
    //print_r("Mensjae request: " . $ret->mensaje . PHP_EOL);
    /*if($ret->status == 200) {
            print_r("Contenido resultados: " . PHP_EOL);
            print_r($ret->resultados);
    }*/
    if ($ret->isError)
    {
        $respuesta      = $ret->errorMessage;
        ///////////////////////////////////////////
        progreso(0, $respuesta, 1);
    }
    $folioFiscal        = $ret->folioUUID;
    $selloDigitalEmisor = $ret->selloDigitalEmisor;
    $selloDigitalSAT    = $ret->selloDigitalTimbreSAT;
    $cadenaOriginal     = $ret->cadenaOriginal;
    $fechaCertificacion = $ret->fechaHoraTimbrado;
    $xmlTIMBRE          = $ret->XML;
    /////////////////////////////////// Obtener NoCertificadoSAT //////////////////////////////////////////
    $delimiter_Cert     = "NoCertificadoSAT=";
    $divideXML_Cert     = explode($delimiter_Cert, $xmlTIMBRE);
    $delimiter_Dash     = "\"";
    $divideXML_Dash     = explode($delimiter_Dash, $divideXML_Cert[1]);
    $noCertificadoSAT   = $divideXML_Dash[1];
    /////////////////////////////////// Obtener NoCertificadoEmisor ////////////////////////////////////////
    $delimiter_Cert     = "NoCertificado=";
    $divideXML_Cert     = explode($delimiter_Cert, $xmlTIMBRE);
    $delimiter_Dash     = "\"";
    $divideXML_Dash     = explode($delimiter_Dash, $divideXML_Cert[1]);
    $noCertificado      = $divideXML_Dash[1];
    ///////////////// Obtener RfcProvCertif y generar cadenaOriginalCumplimiento V1.1 //////////////////////
    $delimiter_Cert     = "RfcProvCertif=";
    $divideXML_Cert     = explode($delimiter_Cert, $xmlTIMBRE);
    $delimiter_Dash     = "\"";
    $divideXML_Dash     = explode($delimiter_Dash, $divideXML_Cert[1]);
    $RfcProvCertif      = $divideXML_Dash[1];
    $cadOrig_cump_v11   = "||1.1|";
    $cadOrig_cump_v11   .= $folioFiscal."|";
    $cadOrig_cump_v11   .= $fechaCertificacion."|";
    $cadOrig_cump_v11   .= $RfcProvCertif."|";
    $cadOrig_cump_v11   .= $selloDigitalSAT."|";
    $cadOrig_cump_v11   .= $noCertificadoSAT."||";
    /////////////////////////////////////// Generar codigoQR ///////////////////////////////////////////////
    $url_QR =  "https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx";
    $url_QR .= "?id=".$folioFiscal;
    $url_QR .= "&re=".$rfcEmisor;
    $url_QR .= "&rr=".$rfcReceptor;
    $url_QR .= "&tt=".$TOTAL;
    $subSDig = substr($selloDigitalEmisor, -8);
    $url_QR .= "&fe=".$subSDig;
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    $sql = "INSERT INTO facturas (
                rfcEmisor, razonEmisor, regimenEmisor, calleEmisor, coloniaEmisor, municipioEmisor,
                numeroExtEmisor, numeroIntEmisor, poblacionEmisor, entidadEmisor, cpEmisor, idReceptor,
                rfcReceptor, razonReceptor, aliasReceptor, calleReceptor, coloniaReceptor, municipioReceptor,
                numeroExtReceptor, numeroIntReceptor, poblacionReceptor, entidadReceptor, cpReceptor,
                regimenReceptor, emailReceptor, vigencia, fechaEmision, cpExpedicion, usoCFDI, tipoCFDI, moneda, formaPago,
                metodoPago, nombreBanco, cuentaBancaria, clabeInterbancaria, numeroCheque, xml, folioFiscal, noCertificado, noCertificadoSAT, selloDigitalEmisor,
                selloDigitalSAT, cadenaOriginal, cadenaOriginalCumplimiento, codigoQR, fechaCertificacion, totalIVA, totalIEPS, subTotal, total, descuento, version, usuario, pagado, idVentasRelacion)
            VALUES(
                '$rfcEmisor', '$razonEmisor', '$regimenEmisor', '$calleEmisor', '$coloniaEmisor', '$municipioEmisor',
                '$numeroExtEmisor', '$numeroIntEmisor', '$poblacionEmisor', $entidadEmisor, $cpEmisor, $idReceptor,
                '$rfcReceptor', '$razonReceptor', '$aliasReceptor', '$calleReceptor', '$coloniaReceptor', '$municipioReceptor',
                '$numeroExtReceptor', '$numeroIntReceptor', '$poblacionReceptor', '$entidadReceptor', $cpReceptor,
                '$regimenReceptor', '$emailReceptor', $vigencia, '$fechaHora', $cpEmisor, '$UsoCFDI', '$tipoComprobante', '$moneda', '$formaDePago',
                '$metodoPago', '$nombreBanco', '$cuentaBancaria', '$clabeInterbancaria', '$numeroCheque', '$xmlTIMBRE', '$folioFiscal', '$noCertificado', '$noCertificadoSAT', '$selloDigitalEmisor',
                '$selloDigitalSAT', '$cadenaOriginal', '$cadOrig_cump_v11', '$url_QR', '$fechaCertificacion', $sumatoriaIVA, $sumatoriaIEPS, $subTotal, $TOTAL, 0, $version, $idUsuario, $pagado, '$idVenta')";
    if ($mysqli->query($sql))
    {
        $idFactura_ = $mysqli->insert_id;
        for ($y=0; $y < sizeof($detalle_factura); $y++)
        {
            $idProducto_        = $detalle_factura[$y]['idProducto'];
            $claveProdServ_     = $detalle_factura[$y]['claveSHCP'];
            $cantidad_          = $detalle_factura[$y]['cantidad'];
            $claveUnidad_       = $detalle_factura[$y]['claveUnidad'];
            $nombreUnidad_      = $detalle_factura[$y]['nombreUnidad'];
            $descripcion_       = $detalle_factura[$y]['descripcion'];
            $valorUnitario_     = $detalle_factura[$y]['precioU'];
            $iva_               = $detalle_factura[$y]['iva'];
            $ieps_              = $detalle_factura[$y]['ieps'];
            $importe_           = $detalle_factura[$y]['importe'];
            $idSubVenta_        = $detalle_factura[$y]['idSubVenta'];
            //$descuento_         = $detalle_factura[$y]['descuento'];
            $sql_det .= "INSERT INTO detalleFactura (
                            idFactura, idProducto, claveSHCP, cantidad,
                            claveUnidad, nombreUnidad, descripcion, precioU,
                            iva, ieps, importe, descuento)
                        VALUES ( $idFactura_, $idProducto_, '$claveProdServ_', $cantidad_,
                            '$claveUnidad_', '$nombreUnidad_', '$descripcion_', '$valorUnitario_',
                            $iva_, $ieps_, $importe_, $descuento);";
            $sql_det .= "UPDATE productos
                            SET claveSHCP   = $claveProdServ_,
                                IVA         = $iva_,
                                IEPS        = $ieps_
                            WHERE id = $idProducto_ LIMIT 1;";
            $sql_det .= "UPDATE detalleventa
                            SET facturado = 1
                            WHERE id = $idSubVenta_ LIMIT 1;";
        }
        $sql_det     .= "INSERT INTO relventafactura (
                            idVenta, idFactura)
                         VALUES
                            ($idVenta, $idFactura_);";
        if($mysqli->multi_query($sql_det))
        {
            do {
                # code...
            } while ($mysqli->next_result());
        }
    }
    else
    {
        progreso(0, "Factura timbrada, pero no se pudo guardar en la base de datos. Error: ".$mysqli->error, 1);
    }
    ////////////////////////////////////////////// REGISTRAR PAGO ///////////////////////////////////////////////////////
    $sql_p  = "";
    $sql_p  .= "INSERT INTO pagosrecibidos
                    (idVenta, idFactura, monto, usuario, cliente, metodoPago, sesion)
               VALUES
                    ($idVenta, $idFactura_, $TOTAL, $idUsuario, $idReceptor, $formaDePago, $idSesion);";
    $sql_rev_pag    = "SELECT IFNULL(SUM(monto),0) FROM pagosrecibidos WHERE idventa= $idVenta";
    $res_rev_pag    = $mysqli->query($sql_rev_pag);
    $row_rev_pag    = $res_rev_pag->fetch_assoc();
    $sql_p      .= "UPDATE ventas SET pagado = 1 WHERE id = $idVenta LIMIT 1;";

    if($mysqli->multi_query($sql_p))
    {
        do {

        } while ($mysqli->next_result());
    }

    $archivoXML     = fopen("XML/".str_pad($idFactura_, 13, "0", STR_PAD_LEFT).".xml", "w");
    if($archivoXML == false)
        progreso(0, "Error al descargar el XML, pero la factura SÍ fue timbrada", 1);
    fwrite($archivoXML, $xmlTIMBRE);
    fclose($archivoXML);
    genFactura($idFactura_,$mysqli,0);
    if($enviar == 1)
    {
        progreso(10, "Enviando correo-e al cliente", 0);
        usleep(1000000);

        $mail = new PHPMailer();
        $mail->isSMTP();
        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = "gamb2006@gmail.com";
        //Password to use for SMTP authentication
        $mail->Password = "dvrselvsclpssasF";
        //Set who the message is to be sent from
        $mail->From='gamb2006@gmail.com';
        $mail->FromName = "SUPER DON ALEX"; //A RELLENAR Nombre a mostrar del remitente.
        //Set an alternative reply-to address
        //$mail->addReplyTo('replyto@example.com', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress($emailReceptor);
        //Set the subject line
        $mail->Subject = 'Su factura, gracias!';
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        //Replace the plain text body with one created manually
        //$mail->AltBody = 'This is a plain-text message body';
        $msg = "<p>Envío del comprobante fiscal digital.</p></br> <p>Gracias por su preferencia!</p>";

        $mail->IsHTML(true); // El correo se envía como HTML
        $mail->Body    = $msg;
        //Attach an image file
        $mail->addAttachment("XML/".str_pad($idFactura_, 13, "0", STR_PAD_LEFT).".xml");
        $mail->addAttachment("XML/".str_pad($idFactura_, 13, "0", STR_PAD_LEFT).".pdf");
        //send the message, check for errors
        if (!$mail->send())
        {
            progreso(100, "Factura creada correctamente. No se envió e-mail!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.msgFactura(1,'Tu factura ha sido timbrada con éxito. (No se pudo enviar e-mail)');";
            $script         .= '</script>';
            echo $script;
        } else
        {
            progreso(100, "Factura creada correctamente. E-mail enviado correctamente!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.msgFactura(1,'Tu factura ha sido timbrada y enviada con éxito!');";
            $script         .= '</script>';
            echo $script;
            //Section 2: IMAP
            //Uncomment these to save your message in the 'Sent Mail' folder.
            #if (save_mail($mail)) {
            #    echo "Message saved!";
            #}
        }
    }
    else
    {
        progreso(50, "Factura creada correctamente. SIN envío de e-mail!", 0);
        $script         =  "<script>";
        $script         .= "window.parent.msgFactura(1,'Tu factura ha sido timbrada correctamente. NO se envió por e-mail');";
        $script         .= '</script>';
        echo $script;
    }
    //Luego tenemos que iniciar la validación por SMTP:
    /*$mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "smtp.gmail.com"; // A RELLENAR. Aquí pondremos el SMTP a utilizar. Por ej. mail.midominio.com
    $mail->Username = "gamb2006@gmail.com"; // A RELLENAR. Email de la cuenta de correo. ej.info@midominio.com La cuenta de correo debe ser creada previamente.
    $mail->Password = "dvrselvsclpssasF"; // A RELLENAR. Aqui pondremos la contraseña de la cuenta de correo
    $mail->Port = 465; // Puerto de conexión al servidor de envio.
    $mail->From = "gamb2006@gmail.com"; // A RELLENARDesde donde enviamos (Para mostrar). Puede ser el mismo que el email creado previamente.

    $mail->AddAddress("abarrotesalagua@gmail.com"); // Esta es la dirección a donde enviamos
    $mail->Subject = "Titulo"; // Este es el titulo del email.
    $body = "Hola mundo. Esta es la primer línea";
    $body .= "Aquí continuamos el mensaje";
    $mail->Body = $body; // Mensaje a enviar.
    $exito = $mail->Send(); // Envía el correo.*/
//mail($to, $subject, $message, $headers);

}
?>
