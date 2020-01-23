<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/Mexico_City');

//phpinfo();
	//require_once('../startbootstrap/vendor/nusoap/lib/nusoap.php');
header ('Content-type: text/html; charset=utf-8');
$fname = "xml_ejemplo.xml";
if(!file_exists($fname)){
 die(PHP_EOL . "File not found" . PHP_EOL . PHP_EOL);
}

$handle = fopen($fname, "r");
$sData = '';
$xmlCFDI = "";
while(!feof($handle))
    $xmlCFDI .= fread($handle, filesize($fname));
fclose($handle);
//$b64 = base64_encode($sData);

$response = '';
/*
Porfavor note:
    Este ejemplo está basado en el WSDL De timbrado
    cada WSDL tiene su propia estructura y deberá modificarse la petición
    acorde al webservice que se esté conectando.*/
$user = "AIRY820226UXA_3";
$userPassword = "184829659849329157062420";
$llavePrivadaEmisorPassword = "12345678a";
$certificadoEmisor = file_get_contents("certEmisorPrueba/CSD01_AAA010101AAA.cer");
$llavePrivadaEmisor = file_get_contents("certEmisorPrueba/CSD01_AAA010101AAA.key");
//$user = "AIRY820226UXA_10";
//$userPassword = "58996256596700145927542";
try {
        $client = new SoapClient("http://mieses.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl");
	//	var_dump($client->__getFunctions());
        $params = array(
			'user' => $user,
			'userPassword' => $userPassword,
			'certificadoEmisor'=> $certificadoEmisor,
			'llavePrivadaEmisor'=> $llavePrivadaEmisor,
			'llavePrivadaEmisorPassword' => $llavePrivadaEmisorPassword,
			'xmlCFDI'	=> $xmlCFDI,
			'versionCFDI' => '3.3'
		);
        $response = $client->__soapCall('sellaTimbraCFDI', array('parameters' => $params));
} catch (SoapFault $fault) {
        echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
}
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
$ret = $response->return;

//print_r("Estatus request: " . $ret->status . PHP_EOL);
//print_r("Mensjae request: " . $ret->mensaje . PHP_EOL);
var_dump($response);
/*if($ret->status == 200) {
        print_r("Contenido resultados: " . PHP_EOL);
        print_r($ret->resultados);
}*/
print_r($ret);
echo "XML====>".$ret->XML;
?>
