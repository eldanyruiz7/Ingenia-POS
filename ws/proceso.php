<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/Mexico_City');
$pp = 0;
function progreso($x)
{
    //$p = 0;
    global $pp;
    $pp += $x;
    //$pp = $x;
    $script =  "<script>";
    $script .= "$('#progress-p').css('width','$pp%');";
    $script .= '</script>';
    //echo '<span style="width:10px;" class="avance"></span>';
    echo $script;
    flush();
    ob_flush();
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
$html = "<div>";
$html .="    <p>";
$html .="        <strong>Task 1></strong>";
$html .="        <span class='pull-right text-muted'>40% Complete</span>";
$html .="    </p>";
$html .='    <div class="progress progress-striped active">';
$html .='        <div id="progress-p" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">';
$html .='            <span class="sr-only">40% Complete (success)</span>';
$html .='        </div>';
$html .='    </div>';
$html .='</div>';
echo $html;
flush();
ob_flush();


progreso(10);
//sleep(1);
$fname = "xml_ejemplo.xml";
if(!file_exists($fname)){
 die(PHP_EOL . "File not found" . PHP_EOL . PHP_EOL);
}
progreso(10);
//sleep(1);
$handle = fopen($fname, "r");
$sData = '';
$xmlCFDI = "";
while(!feof($handle))
    $xmlCFDI .= fread($handle, filesize($fname));
fclose($handle);
//$b64 = base64_encode($sData);
progreso(20);
//sleep(1);
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
progreso(30);
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
progreso(20);
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
progreso(10);
print_r($ret);
echo "XML====>".$ret->XML;
    /*for($i=0;$i<=10;$i++)
    {
        $p = $i*10;
        $script =  "<script>";
        $script .= "$('#progress-p').css('width','$p%');";
        $script .= '</script>';
        //echo '<span style="width:10px;" class="avance"></span>';
        echo $script;
        flush();
        ob_flush();
        sleep(1);
    }*/
    $script =  "<script>";
    $script .= "window.parent.ocultariframe();";
    $script .= '</script>';
    echo $script;
?>
