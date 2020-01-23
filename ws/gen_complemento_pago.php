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
require 'genComplementoPDF.php';
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
    //$coleccionFactura = json_decode($_GET['coleccionFacturaJSON']);
    $idFactura          = $_GET['idFactura'];
    $montoPago          = $_GET['montoEmitir'];
    $montoPago          = number_format($montoPago,2,".","");
    $sql                = "SELECT * FROM facturas WHERE idRelacion = $idFactura"; // Numero de pagos realizados
    $result_cont        = $mysqli->query($sql);
    $totalPagos         = $result_cont->num_rows;
    $numParcialidad     = $totalPagos + 1;

    $sql                = "SELECT * FROM facturas WHERE id = $idFactura LIMIT 1"; // Datos de la factura PPD
    $resultFactura      = $mysqli->query($sql);
    $rowFact            = $resultFactura->fetch_assoc();
    $UUID_dr            = $rowFact['folioFiscal'];
    $moneda_dr          = $rowFact['moneda'];
    $metodoPago_dr      = $rowFact['metodoPago'];
    $idCliente          = $rowFact['idReceptor'];
    $totalFact          = $rowFact['total'];
    $idVentasRel        = $rowFact['idVentasRelacion'];

    $sql_sumaPagos      = "SELECT IFNULL(SUM(montoPago),0) AS sumaPagos FROM facturas WHERE idRelacion = $idFactura"; // Suma monto de los pagos realizados
    $result_sumaPagos   = $mysqli->query($sql_sumaPagos);
    $row_sumaPagos      = $result_sumaPagos->fetch_assoc();
    $montoPagado        = $row_sumaPagos['sumaPagos'];
    $saldoAnterior      = $totalFact - $montoPagado;
    $saldoAnterior      = number_format($saldoAnterior,2,".","");
    $saldoInsoluto      = $saldoAnterior - $montoPago;
    $saldoInsoluto      = number_format($saldoInsoluto,2,".","");
    //var_dump($coleccionFactura);
    $enviarA            = $_GET['enviarA'];
    $enviar             = $_GET['enviar'];
    ////// Datos del emisor ///////////
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
    $result_cliente     = $mysqli->query($sql);
    $row_Cliente           = $result_cliente->fetch_assoc();
    $rfcReceptor        = $row_Cliente['rfcCliente'];
    $razonReceptor      = $row_Cliente['razonCliente'];
    $aliasReceptor      = "";
    $regimenReceptor    = "";
    $calleReceptor      = $row_Cliente['calleCliente'];
    $numeroExtReceptor  = $row_Cliente['numeroExtCliente'];
    $numeroIntReceptor  = $row_Cliente['numeroIntCliente'];
    $coloniaReceptor    = $row_Cliente['coloniaCliente'];
    $poblacionReceptor  = $row_Cliente['poblacionCliente'];
    $municipioReceptor  = $row_Cliente['municipioCliente'];
    $cpReceptor         = $row_Cliente['cpCliente'];
    $entidadReceptor    = $row_Cliente['estadoCliente'];
    $emailReceptor      = ($enviar == 0) ? $row_Cliente['emailCliente'] : $enviarA;
    $sql_det            = ($enviar == 1) ? "UPDATE clientes SET email = '$emailReceptor' WHERE id = $idCliente LIMIT 1;" : "";
    ///////////// Datos del CFDI ///////////////
    //$metodoPago         = "PUE";
    $metodoPago         = $metodoPago_dr;
    $pagado             = 1;
    $tipoComprobante    = "P";
    $moneda             = "MXN";
    // $moneda             = "XXX";
    $formaDePago        = $_GET['formaPago'];
    $fecha              = date('Y-m-d');
    $hora               = date('H:i:s');
    $fechaHora          = $fecha."T".$hora;
    $vigencia           = 'NULL';
    $version            = "3.3";
    // $UsoCFDI            = 'G03';
    $UsoCFDI            = 'P01';
    $nombreBanco        = "NULL";
    $cuentaBancaria     = "NULL";
    $clabeInterbancaria = "NULL";
    $numeroCheque       = "NULL";
    //$noCertificado      = '30001000000300023707';
    // $noCertificado      = '0000100000040930875';

    progreso(10,"Creando XML", 0);
    usleep(500000);
    ///////WHILE///////////

    $xmlCFDI_c               = '<cfdi:Conceptos>';
    $xmlCFDI_c              .= '    <cfdi:Concepto ClaveProdServ="84111506" ClaveUnidad="ACT" Cantidad="1" Descripcion="Pago" ValorUnitario="0.00" Importe="0.00">';
    $xmlCFDI_c              .= '    </cfdi:Concepto>';
    $xmlCFDI_c              .= '</cfdi:Conceptos>';
    $xmlCFDI_c              .= '<cfdi:Complemento>';
    $xmlCFDI_c              .= '    <pago10:Pagos xmlns:pago10="http://www.sat.gob.mx/Pagos" Version="1.0">';
    $xmlCFDI_c              .= '        <pago10:Pago FechaPago="'.$fechaHora.'" FormaDePagoP="'.$formaDePago.'" MonedaP="MXN" Monto="'.$montoPago.'">';
    $xmlCFDI_c              .= '            <pago10:DoctoRelacionado IdDocumento="'.$UUID_dr.'" MonedaDR="'.$moneda_dr.'" MetodoDePagoDR="'.$metodoPago_dr.'" NumParcialidad="'.$numParcialidad.'" ImpSaldoAnt="'.$saldoAnterior.'" ImpPagado="'.$montoPago.'" ImpSaldoInsoluto="'.$saldoInsoluto.'"/>';
    $xmlCFDI_c              .= '        </pago10:Pago>';
    $xmlCFDI_c              .= '    </pago10:Pagos>';
    $xmlCFDI_c              .= '</cfdi:Complemento>';
    $xmlCFDI_c              .= '</cfdi:Comprobante>';
    //echo utf8_decode($xmlCFDI);
    $xmlCFDI            = '<?xml version="1.0" encoding="utf-8"?><cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
    $xmlCFDI            .= 'LugarExpedicion="'.$cpEmisor.'" TipoDeComprobante="'.$tipoComprobante.'" Total="0" Moneda="XXX" Certificado="" SubTotal="0" NoCertificado="00000000000000000000" Sello="" Fecha="'.$fechaHora.'" Version="'.$version.'">';
    $xmlCFDI            .= '<cfdi:Emisor Rfc="'.$rfcEmisor.'" Nombre="'.$razonEmisor.'" RegimenFiscal="'.$regimenEmisor.'"></cfdi:Emisor>';
    $xmlCFDI            .= '<cfdi:Receptor Rfc="'.$rfcReceptor.'" Nombre="'.$razonReceptor.'" UsoCFDI="'.$UsoCFDI.'"></cfdi:Receptor>';
    $xmlCFDI            .= $xmlCFDI_c;
    $archivoXML_     = fopen("error_complemento.xml", "w");
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
    $certificadoEmisor  = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD01_AAA010101AAA.cer");
    $llavePrivadaEmisor = file_get_contents("certEmisorPrueba/Certificados de pruebas/CSD01_AAA010101AAA.key");
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
    $url_QR .= "&tt=0";
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
                selloDigitalSAT, cadenaOriginal, cadenaOriginalCumplimiento, codigoQR, fechaCertificacion, totalIVA, totalIEPS, subTotal, total, descuento, version, usuario, pagado, idRelacion, montoPago)
            VALUES(
                '$rfcEmisor', '$razonEmisor', '$regimenEmisor', '$calleEmisor', '$coloniaEmisor', '$municipioEmisor',
                '$numeroExtEmisor', '$numeroIntEmisor', '$poblacionEmisor', $entidadEmisor, $cpEmisor, $idCliente,
                '$rfcReceptor', '$razonReceptor', '$aliasReceptor', '$calleReceptor', '$coloniaReceptor', '$municipioReceptor',
                '$numeroExtReceptor', '$numeroIntReceptor', '$poblacionReceptor', '$entidadReceptor', $cpReceptor,
                '$regimenReceptor', '$emailReceptor', $vigencia, '$fechaHora', $cpEmisor, '$UsoCFDI', '$tipoComprobante', '$moneda', '$formaDePago',
                '$metodoPago', '$nombreBanco', '$cuentaBancaria', '$clabeInterbancaria', '$numeroCheque', '$xmlTIMBRE', '$folioFiscal', '$noCertificado', '$noCertificadoSAT', '$selloDigitalEmisor',
                '$selloDigitalSAT', '$cadenaOriginal','$cadOrig_cump_v11', '$url_QR', '$fechaCertificacion', 0, 0, 0, 0, 0, $version, $idUsuario, $pagado, $idFactura, $montoPago)";
    if ($mysqli->query($sql))
    {
        $idFactura_ = $mysqli->insert_id;
        $sql_det .= "INSERT INTO detalleFactura (
                        idFactura, idProducto, claveSHCP, cantidad,
                        claveUnidad, nombreUnidad, descripcion, precioU,
                        iva, ieps, importe, descuento)
                    VALUES ( $idFactura_, 0, '84111506', 1,
                        'ACT', 'Actividad', 'Pago', 0,
                        0, 0, 0, 0);";
        if ($saldoInsoluto <= 0)
        {
            $sql_det .= "UPDATE facturas
                         SET pagado = 1
                         WHERE id = $idFactura LIMIT 1;";
        }
        $sql_det     .= "INSERT INTO pagosrecibidos
                                (idFactura, monto, usuario, cliente, metodoPago, sesion)
                            VALUES
                                ($idFactura_, $montoPago, $idUsuario, $idCliente, $formaDePago, $idSesion);";
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $esteIdVenta            = 0;
        $saldo_restar           = $montoPago;
        $array_ventas           = explode(", ",$idVentasRel);
        for ($y=0; $y < sizeof($array_ventas); $y++)
        {
            $idVenta_           = $array_ventas[$y];
            if ($idVenta_       == $esteIdVenta)
                continue;
            $esteIdVenta        = $idVenta_;
            $sql_liq            = "SELECT
                                    ventas.totalventa AS totalVenta,
                                    (SELECT IFNULL(SUM(pagosrecibidos.monto), 0) FROM pagosrecibidos WHERE idventa = $idVenta_) AS pagado
                                FROM ventas
                                    WHERE ventas.id = $idVenta_ LIMIT 1";
            $result_liq     = $mysqli->query($sql_liq);
            $rowRes_liq     = $result_liq->fetch_assoc();
            $pagado         = $rowRes_liq['pagado'];
            $montoVenta     = $rowRes_liq['totalVenta'];
            $esteSaldo      = $montoVenta - $pagado;
            if ($saldo_restar >= $esteSaldo)
            {
                $montoPago  = $esteSaldo;
                $sql_det   .= "UPDATE ventas SET pagado = 1 WHERE id = $idVenta_ LIMIT 1;";
                //$sql_det   .= "INSERT INTO relventafactura (idVenta, idFactura) VALUES ($idVenta_, $idFactura_);";
            }
            else
                $montoPago  = $saldo_restar;
            // $sql_det       .= "INSERT INTO pagosrecibidos
            //                         (idventa, monto, usuario, cliente, metodoPago, sesion)
            //                     VALUES
            //                         ($idVenta_, $montoPago, $idUsuario, $idCliente, $formaDePago, $idSesion);";
            $saldo_restar   = $saldo_restar - $montoPago;
            if ($saldo_restar <= 0)
                break;

        }
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
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
    $archivoXML     = fopen("XML/".str_pad($idFactura_, 13, "0", STR_PAD_LEFT).".xml", "w");
    if($archivoXML == false)
        progreso(0, "Error al descargar el XML, pero la factura SÍ fue timbrada", 1);
    fwrite($archivoXML, $xmlTIMBRE);
    fclose($archivoXML);
    genComplemento($idFactura_,$mysqli,0);
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
        $mail->Subject = utf8_decode('Envío del complemento del pago!');
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        //Replace the plain text body with one created manually
        //$mail->AltBody = 'This is a plain-text message body';
        $msg = "<p>Envío del Complemento de Pago.</p> <p> Folio fiscal documento relacionado: <b>$UUID_dr</b></p><p>Id Factura: <b>$idFactura</b></p><p>Gracias por su preferencia!</p>";

        $mail->IsHTML(true); // El correo se envía como HTML
        $mail->Body    = $msg;
        //Attach an image file
        $mail->addAttachment("XML/".str_pad($idFactura_, 13, "0", STR_PAD_LEFT).".xml");
        $mail->addAttachment("XML/".str_pad($idFactura_, 13, "0", STR_PAD_LEFT).".pdf");
        //send the message, check for errors
        if (!$mail->send())
        {
            progreso(100, "Pago emitido correctamente. No se envió e-mail!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.msgFactura(1,'Tu factura ha sido timbrada con éxito. (No se pudo enviar e-mail)');";
            $script         .= 'window.parent.refrescarPagina();';
            $script         .= '</script>';
            echo $script;
        } else
        {
            progreso(100, "Pago emitido correctamente. E-mail enviado correctamente!", 0);
            $script         =  "<script>";
            $script         .= "window.parent.msgFactura(1,'Tu pago ha sido timbrado y enviado con éxito!');";
            $script         .= 'window.parent.refrescarPagina();';
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
        progreso(50, "Pago emitido correctamente. SIN envío de e-mail!", 0);
        $script         =  "<script>";
        $script         .= "window.parent.msgFactura(1,'Tu factura ha sido timbrada correctamente. NO se envió por e-mail');";
        $script         .= 'window.parent.refrescarPagina();';
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
//mail($to, $subject, $message, $headers);*/

}
?>
