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
            $script     .= 'window.parent.msgFactura(0,"'.$respHTML.'");'; // msgFactura($hayError,$mensaje)
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
    $coleccionFactura       = json_decode($_GET['coleccionFacturaJSON']);
    //$coleccionidSubVenta    = json_decode($_GET['coleccionIdSubVentaJSON']);
    //echo sizeof($coleccionFactura);
    //var_dump($coleccionFactura);
    $idCliente            = $_GET['idCliente'];
    //var_dump($idCliente);
    $enviarA            = $_GET['enviarA'];
    $enviar             = $_GET['enviar'];
    $formaPago         = $_GET['formaPago'];
    //$rfcEmisor          = "SACJ830819U62";
    $rfcEmisor          = "AAA010101AAA";
    //$rfcEmisor          = "AIRY820226UXA";
    $razonEmisor        = "===FACTURA DE EJEMPLO===";
    //$razonEmisor        = "JORGE IVAN SALAZAR CHAVEZ";
    //$razonEmisor        = "YADIRA JANNETTE ARIAS ROJAS";
    $regimenEmisor      = "601";
    //$regimenEmisor      = "612";
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
                            clientes.id                 AS idCliente,
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
                            clientes.email              AS emailCliente
                        FROM clientes
                        WHERE clientes.id = $idCliente LIMIT 1";
    $result             = $mysqli->query($sql);
    $rowCliente         = $result->fetch_assoc();
    $idReceptor         = $rowCliente['idCliente'];
    $rfcReceptor        = $rowCliente['rfcCliente'];
    $razonReceptor      = $rowCliente['razonCliente'];
    $aliasReceptor      = "";
    $regimenReceptor    = "";
    $calleReceptor      = $rowCliente['calleCliente'];
    $numeroExtReceptor  = $rowCliente['numeroExtCliente'];
    $numeroIntReceptor  = $rowCliente['numeroIntCliente'];
    $coloniaReceptor    = $rowCliente['coloniaCliente'];
    $poblacionReceptor  = $rowCliente['poblacionCliente'];
    $municipioReceptor  = $rowCliente['municipioCliente'];
    $cpReceptor         = $rowCliente['cpCliente'];
    $entidadReceptor    = $rowCliente['estadoCliente'];
    //$totalRows          = $rowCliente['totalFacturables'];
    $emailReceptor      = ($enviar == 0) ? $rowCliente['emailCliente'] : $enviarA;
    $sql_det            = ($enviar == 1) ? "UPDATE clientes SET email = '$emailReceptor' WHERE id = $idReceptor LIMIT 1;" : "";
    ///////////// Datos del CFDI ///////////////
    $metodoPago         = ($_GET['metodoPago'] == "PPD") ? "PPD" : "PUE";
    $pagado             = ($_GET['metodoPago'] == "PPD") ? 0 : 1;
    $cadenaVentas       = explode(", ",$_GET['cadenaVentas']);
    //$metodoPago         = "PUE";
    $tipoComprobante    = "I";
    $moneda             = "MXN";
    //$formaDePago      = "99";
    $formaDePago        = $formaPago;
    $fecha              = date('Y-m-d');
    $hora               = date('H:i:s');
    $fechaHora          = $fecha."T".$hora;
    $vigencia           = 'NULL';
    $version            = "3.3";
    //$folio              = "08766";
    $UsoCFDI            = $_GET['usoCFDI'];//'P01';
    $nombreBanco        = "NULL";
    $cuentaBancaria     = "NULL";
    $clabeInterbancaria = "NULL";
    $numeroCheque       = "NULL";
    $noCertificado      = '‎30001000000300023707';

    progreso(10,"Creando XML", 0);
    usleep(500000);
    $cont               = 0;
    $cont_ventas        = 0;
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
    $banderaFacturbles  = 0; // 0 = No hay facturables; 1 = Si hay facturables
    $stringVentas_rel   = "";
    $array_ventas       = [];
    $esteIdVenta_rel    = 0;
    $xmlCFDI_c          = '<cfdi:Conceptos>';
    foreach ($coleccionFactura as $rowFact)
    {
        $id_F           =   $rowFact ->idProducto;
        $idSubVenta_F   =   $rowFact ->idSubVenta;
        $idVenta_F      =   $rowFact ->idVenta;
        $claveSAT_F     =   $rowFact ->claveSAT;
        $iva_F          =   $rowFact ->iva;
        $ieps_F         =   $rowFact ->ieps;
        $cantidad_F     =   $rowFact ->cantidad;
        $precioU_F      =   $rowFact ->precioU;
        $subTotal_F     =   $rowFact ->subTotal;
        // $subTotal_F     =   $cantidad_F * $precioU_F;
        // $subTotal_F     =   number_format($subTotal_F,2,".","");
        //$subTotal_F     =   $subTotal_F;
        if ($esteIdVenta_rel != $idVenta_F)
        {
            if (strlen($stringVentas_rel) > 0)
                $stringVentas_rel .= ", ".$idVenta_F;
            else
                $stringVentas_rel .= $idVenta_F;
            $esteIdVenta_rel = $idVenta_F;
            //$array_ventas[$cont_ventas] = $esteIdVenta_rel;
            //$cont_ventas++;
        }
        if(in_array($esteIdVenta_rel, $array_ventas)== false)
            $array_ventas[] = $esteIdVenta_rel;

        $sql = "SELECT
                    detalleventa.facturable     AS facturable,
                    detalleventa.facturado      AS facturado,
                    productos.nombrelargo       AS descripcion,
                    unidadesventa.nombre        AS unidad,
                    unidadesventa.c_ClaveUnidad AS claveUnidad
                FROM detalleventa
                INNER JOIN productos
                ON detalleventa.producto = productos.id
                INNER JOIN unidadesventa
                ON productos.unidadventa = unidadesventa.id
                WHERE detalleventa.id = $idSubVenta_F LIMIT 1";
        $result_Facturable = $mysqli->query($sql);
        $row_Facturable = $result_Facturable->fetch_assoc();
        if ($row_Facturable['facturable'] == 1 && $row_Facturable['facturado'] == 0)
            $banderaFacturbles = 1;
        $cont++;
        $unidad         = $row_Facturable['unidad'];
        $claveUnidad    = $row_Facturable['claveUnidad'];
        $descripcion    = $row_Facturable['descripcion'];
        $idSubVenta     = $idSubVenta_F;
        $idProducto     = $id_F;
        //Calcular factoriva y ieps (1.16 y 1.08)
        $factorIva  = $iva_F / 100;
        $factorIva++;
        $factorIeps  = $ieps_F / 100;
        $factorIeps++;
        if ($iva_F > 0 && $ieps_F == 0)
        {
            $importe            = $subTotal_F / $factorIva;
            $importeImpuestoIVA = $subTotal_F - $importe;
            $importeImpuestoIEPS= 0;
            $sumatoriaSinIva    += $importe;
            $tasaOCuotaIVATotal = $iva_F / 100;
            $valorUnitario      = $precioU_F / $factorIva;
        }
        elseif ($iva_F == 0 && $ieps_F > 0)
        {
            $importe            = $subTotal_F / $factorIeps;
            $importeImpuestoIVA = 0;
            $importeImpuestoIEPS= $subTotal_F - $importe;
            $sumatoriaSinIeps   += $importe;
            $tasaOCuotaIEPSTotal= $ieps_F / 100;
            $valorUnitario      = $precioU_F / $factorIeps;
        }
        elseif  ($iva_F == 0 && $ieps_F == 0)
        {
            $importe            = $subTotal_F;//round($subTotal_F, 2);
            $valorUnitario      = $precioU_F;
            $importeImpuestoIVA = 0;
            $importeImpuestoIEPS= 0;
            $sumatoriaSinIva    += $importe;
        }
        $importeImpuestoIVA     = number_format($importeImpuestoIVA,3,".","");
        $importeImpuestoIEPS    = number_format($importeImpuestoIEPS,3,".","");
        $impuestoIVA            = "002";
        $tipoFactorIVA          = "Tasa";
        $impuestoIEPS           = "003";
        $tipoFactorIEPS         = 'Tasa';
        $tasaOCuotaIVA          = $iva_F / 100;
        $tasaOCuotaIEPS         = $ieps_F / 100;
        $sumatoriaIVA           += $importeImpuestoIVA;
        $sumatoriaIEPS          += $importeImpuestoIEPS;
        $valorUnitario          = number_format($valorUnitario,3,".","");
        $importe                = number_format($importe,3,".","");
        $TOTAL                  += $importe;
        if ($id_F != -1 || $idSubVenta_F != -1)
        {
            $xmlCFDI_c              .= '<cfdi:Concepto ClaveProdServ="'.$claveSAT_F.'" ';
            $xmlCFDI_c              .= 'ClaveUnidad="'.$claveUnidad.'" Cantidad="'.number_format($cantidad_F,3,".","").'" Unidad="'.$unidad.'" Descripcion="'.$descripcion.'" ValorUnitario="'.$valorUnitario.'" Importe="'.$importe.'" Descuento="0.00">';
            $xmlCFDI_c              .= '   <cfdi:Impuestos>';
            $xmlCFDI_c              .= '       <cfdi:Traslados>';
            $xmlCFDI_c              .= '           <cfdi:Traslado Base="'.$importe.'" Impuesto="'.$impuestoIVA.'" TipoFactor="'.$tipoFactorIVA.'" TasaOCuota="'.number_format($tasaOCuotaIVA,6,".","").'" Importe="'.$importeImpuestoIVA.'"/>';
            $xmlCFDI_c              .= '           <cfdi:Traslado Base="'.$importe.'" Impuesto="'.$impuestoIEPS.'" TipoFactor="'.$tipoFactorIEPS.'" TasaOCuota="'.number_format($tasaOCuotaIEPS,6,".","").'" Importe="'.$importeImpuestoIEPS.'"/>';
            $xmlCFDI_c              .= '       </cfdi:Traslados>';
            $xmlCFDI_c              .= '   </cfdi:Impuestos>';
            $xmlCFDI_c              .= '</cfdi:Concepto>';
            $detalle_factura[$x]['idSubVenta']  = $idSubVenta;
            $detalle_factura[$x]['idVenta']     = $idVenta_F;
            $detalle_factura[$x]['idProducto']  = $idProducto;
            $detalle_factura[$x]['claveSHCP']   = $claveSAT_F;
            $detalle_factura[$x]['cantidad']    = $cantidad_F;
            $detalle_factura[$x]['claveUnidad'] = $claveUnidad;
            $detalle_factura[$x]['nombreUnidad']= $unidad;
            $detalle_factura[$x]['descripcion'] = $descripcion;
            $detalle_factura[$x]['precioU']     = $valorUnitario;
            $detalle_factura[$x]['iva']         = $iva_F;
            $detalle_factura[$x]['ieps']        = $ieps_F;
            $detalle_factura[$x]['importe']     = $importe;
            $x++;
            echo $descripcion.": ".$importe."</br>";
        }
        //$detalle_factura[$x]['descuento']   = $descuento;
    }
    if($banderaFacturbles == 0)
        progreso(0, "Error. No existen artículos que se puedan facturar.", 1);
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
    $xmlCFDI            = '<?xml version="1.0" encoding="utf-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
    $xmlCFDI            .= 'LugarExpedicion="'.$cpEmisor.'" MetodoPago="'.$metodoPago.'" TipoDeComprobante="'.$tipoComprobante.'" Total="'.$TOTAL.'" Descuento="0.00" Moneda="'.$moneda.'" Certificado="" SubTotal="'.number_format($subTotal,2,".","").'" NoCertificado="00000000000000000000" FormaPago="'.$formaDePago.'" Sello="" Fecha="'.$fechaHora.'" Version="'.$version.'">';
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
    $user               = "AIRY820226UXA_3";
    $userPassword       = "184829659849329157062420";
    $llavePrivadaEmisorPassword = "12345678a";
    //$llavePrivadaEmisorPassword = "Ve123456";
    //$llavePrivadaEmisorPassword = "supersalazar123";
    //$llavePrivadaEmisorPassword = "salazar830819";

    //$llavePrivadaEmisorPassword = "sole1982";
    $certificadoEmisor  = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD05_AAA010101AAA.cer");
    $llavePrivadaEmisor = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD05_AAA010101AAA.key");
    //$certificadoEmisor  = file_get_contents("sellos/nuevo/SACJ830819U62.cer");
    //$llavePrivadaEmisor = file_get_contents("sellos/nuevo/SACJ830819U62.key");
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
            $client     = new SoapClient("http://mieses.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl");
    	//	var_dump($client->__getFunctions());
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
            //echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
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
    var_dump($detalle_factura);
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
    /////////////////////////////////////////////////////////////////////////////////////////////////////////
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
                '$selloDigitalSAT', '$cadenaOriginal', '$cadOrig_cump_v11', '$url_QR', '$fechaCertificacion', $sumatoriaIVA, $sumatoriaIEPS, $subTotal, $TOTAL, 0, $version, $idUsuario, $pagado, '$stringVentas_rel')";
    if ($mysqli->query($sql))
    {
        $idFactura_             = $mysqli->insert_id;
        $actualizarPagos        = 0;
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
                            $iva_, $ieps_, $importe_, 0);";
            $sql_det .= "UPDATE productos
                            SET claveSHCP   = $claveProdServ_,
                                IVA         = $iva_,
                                IEPS        = $ieps_
                            WHERE id = $idProducto_ LIMIT 1;";
            $sql_det .= "UPDATE detalleventa
                            SET facturado = 1
                            WHERE id = $idSubVenta_ LIMIT 1;";
        }
        // foreach ($coleccionidSubVenta as $idSubV_)
        // {
        //     $idS = $idSubV_->idSubVenta;
        //     $sql_det .= "UPDATE detalleventa
        //                     SET facturado = 1
        //                     WHERE id = $idS LIMIT 1;";
        // }
        if($mysqli->multi_query($sql_det))
        {
            $actualizarPagos    = 1;
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
    $sql_insert             = "";
    //$array_ventas           = [];
    $esteIdVenta            = 0;
    $saldo_restar           = $TOTAL;
    for ($y=0; $y < sizeof($cadenaVentas); $y++)
    {
    // foreach ($coleccionFactura as $rowFact)
    // {
        //$idVenta_           =   $rowFact ->idVenta;
        $idVenta_           = $cadenaVentas[$y];
        // if ($idVenta_       == $esteIdVenta)
        //     continue;
        $esteIdVenta        = $idVenta_;
        $sql_liq            = "SELECT
                                    ventas.totalventa AS totalVenta,
                                    (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta_) AS pagado
                                FROM ventas
                                WHERE ventas.id = $idVenta_ LIMIT 1";
        $result_liq         = $mysqli->query($sql_liq);
        $rowRes_liq         = $result_liq->fetch_assoc();
        $pagado             = $rowRes_liq['pagado'];
        $montoVenta         = $rowRes_liq['totalVenta'];
        $esteSaldo          = $montoVenta - $pagado;
        // if ($saldo_restar   >= $esteSaldo)
        // {
        $montoPago          = $esteSaldo;
        $sql_insert         .= "UPDATE ventas SET pagado = 1 WHERE id = $idVenta_ LIMIT 1;";
        // }
        // else
        // {
        //     $montoPago      = $saldo_restar;
        // }
        // foreach ($coleccionidSubVenta as $idSubV_)
        // {
        //     $idS            = $idSubV_->idSubVenta;
        //     $sql_det        .= "UPDATE detalleventa
        //                         SET facturado = 1
        //                         WHERE id = $idS LIMIT 1;";
        // }
        if ($metodoPago == 'PUE')
        {
            $sql_insert         .= "INSERT INTO pagosrecibidos
            (idFactura, monto, usuario, cliente, metodoPago, sesion)
            VALUES
            ($idFactura_, $montoPago, $idUsuario, $idReceptor, $formaDePago, $idSesion);";
        }
        $sql_insert         .= "INSERT INTO relventafactura (idVenta, idFactura) VALUES ($idVenta_, $idFactura_);";
        $sql_insert         .= "UPDATE detalleventa SET facturado = 1 WHERE venta = $idVenta_ AND facturable = 1 AND facturado = 0 AND activo = 1;";
        // $sql_insert         .= "INSERT INTO relventafactura (
        //                             idVenta, idFactura)
        //                         VALUES
        //                         ($idVenta, $idFactura_, );";
        // $saldo_restar       = $saldo_restar - $montoPago;
        // if ($saldo_restar   <= 0)
        //     break;

    }
    if($mysqli->multi_query($sql_insert))
    {
        do {
            # code...
        } while ($mysqli->next_result());
    }
    echo "sql_insert:".$sql_insert;

    /*////////////////////////////////////////////// REGISTRAR PAGO(S) ///////////////////////////////////////////////////////
    foreach ($coleccionVentas as $idVenta)
    {
        $idVenta_F      = $idVenta->idVenta;
        $sql            = "SELECT
                                ventas.totalventa AS totalVenta,
                                (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta_F) AS pagado
                            FROM ventas
                                WHERE ventas.id = $idVenta_F";
        $result_        = $mysqli->query($sql);
        $rowRes_        = $result_->fetch_assoc();
        $pagado         = $rowRes_['pagado'];
        $montoVenta     = $rowRes_['totalVenta'];
        $esteSaldo      = $montoVenta - $pagado;
        if ($saldo_restar >= $esteSaldo)
        {
            $montoPago  = $esteSaldo;
            $sql_insert .= "UPDATE ventas SET pagado = 1 WHERE id = $idVenta_F LIMIT 1;";
            $response['rowBorrar'][$cont]= $idVenta_F;
            $response['borrar']         = 1;
            $cont++;
        }
        else
        {
            $montoPago                  = $saldo_restar;
            $response['rowActualizar']  = $idVenta_F;
            $response['actualizar']     = 1;
            $response['nuevoPagado']    = $pagado + $montoPago;
            $response['nuevoSaldo']     = number_format($montoVenta - $response['nuevoPagado'],2,".",",");
            $response['nuevoPagado']    = number_format($response['nuevoPagado'],2,".",",");
        }
        $sql_insert     .= "INSERT INTO pagosrecibidos
                                (idventa, monto, usuario, cliente, sesion)
                            VALUES
                                ($idVenta_F, $montoPago, $idUsuario, $idCliente_ant, $idSesion);";
        $saldo_restar   = $saldo_restar - $montoPago;
        if ($saldo_restar <= 0)
        {
            break;
        }
    }
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////*/
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
            $script         =  "<script>";
            $script         .= 'window.parent.refrescarPagina();';
            $script         .= '</script>';
            echo $script;
            flush();
            ob_flush();
        } else
        {
            progreso(100, "Factura creada correctamente. E-mail enviado correctamente!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.msgFactura(1,'Tu factura ha sido timbrada y enviada con éxito!');";
            $script         .= '</script>';
            $script         .=  "<script>";
            $script         .= 'window.parent.refrescarPagina();';
            $script         .= '</script>';
            echo $script;
            flush();
            ob_flush();
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
        $script         .= 'window.parent.refrescarPagina();';
        $script         .= '</script>';
        echo $script;
        flush();
        ob_flush();
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
