<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
//phpinfo();
	//require_once('../startbootstrap/vendor/nusoap/lib/nusoap.php');
	header ('Content-type: text/html; charset=utf-8');
	/*try {

		//set_time_limit(0);

		/* carga archivo xml */
		//$xml = simplexml_load_file ("C:\\xmls\\08F72325-37B4-47C1-8B5F-354D04FA7DF5.xml");

		/* Esto es para cargar el xml en una sola cadena, tal como lo necesita el web service
		   en esta parte se recomienda que los caracteres raros y acentuados se metan con
		   secuencia de escape para xml.
		   aqui se pueden dar una idea... http://xml.osmosislatina.com/curso/basico.htm
		*/
/*		$filename="20120329163751_145.xml";

		/*$output="";
		$file = fopen($filename, "r");
		while(!feof($file)) {
			//read file line by line into variable
		  $output = $output . fgets($file, 4096);
		}
		fclose ($file);
		echo $output;

		*/
/*		$XML = new DOMDocument();
		$XML->load( $filename );
		//set_time_limit(60);


		/* conexion al web service */
/*		$client = new SoapClient('http://dev33.facturacfdi.mx/WSForcogsaService?wsdl');
		/* esto es solo para informativo */
/*		print_r($client->__getFunctions());

		/* se le pasan los datos de acceso */
/*		$autentica = new Autenticar();
		$autentica->usuario = "pruebasWS";
		$autentica->contrasena = "pruebasWS";

		/* se cacha la respuesta de la autenticacion */
/*		$responseAutentica = $client->Autenticar($autentica);
		var_dump($responseAutentica);
		//echo $responseAutentica->return->mensaje . "<br>";
	/*	echo $responseAutentica->return->token . "<br>";

		/* se manda el xml a timbrar */
/*		$timbrar = new Timbrar();
		$timbrar->cfd = $XML->saveXML();
		$timbrar->token = $responseAutentica->return->token;

		/* cacha la respuesta */
/*		$responseTimbre = $client->Timbrar($timbrar);
		print_r($responseTimbre);
		echo "<br><br><br>MSG SOAP REQUEST:<br><br>" . $client->__getLastRequest() . "\n";
		echo "<br><br><br>MSG SOAP REQUEST:<br><br>" . $client->__getLastResponse() . "\n";


		/* solo informativo... muestra el codigo de error en caso de existir y resultados */
	/*	echo "codigoErr:" . $responseTimbre->return->codigo . "<br>";
		echo $responseTimbre->return->mensaje . "<br>";
		echo $responseTimbre->return->cfdi . "<br>";



	} catch (SoapFault $e) {
		print("Auth Error:::: $e");
	}


class Autenticar{
	public $usuario;
	public $contraseña;
}

class Timbrar{
	public $cfd;
	public $token;
}
*/
//<?php

$fname = "20120329163751_145.xml";
if(!file_exists($fname)){
 die(PHP_EOL . "File not found" . PHP_EOL . PHP_EOL);
}

$handle = fopen($fname, "r");
$sData = '';
$usuario = "testing@solucionfactible.com";
$password = "timbrado.SF.16672";

while(!feof($handle))
    $sData .= fread($handle, filesize($fname));
fclose($handle);
$b64 = base64_encode($sData);

$response = '';
/*
Porfavor note:
    Este ejemplo está basado en el WSDL De timbrado
    cada WSDL tiene su propia estructura y deberá modificarse la petición
    acorde al webservice que se esté conectando.*/
try {
        $client = new SoapClient("http://pacfdisat.com:8080/CMM/InterconectaWs?wsdl");
		var_dump($client->__getFunctions());
        $params = array('usuario' => $usuario, 'password' => $password, 'cfdiBase64'=>$b64, 'zip'=>False);
        $response = $client->__soapCall('timbrarBase64', array('parameters' => $params));
} catch (SoapFault $fault) {
        echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
}

$ret = $response->return;

print_r("Estatus request: " . $ret->status . PHP_EOL);
print_r("Mensjae request: " . $ret->mensaje . PHP_EOL);

if($ret->status == 200) {
        print_r("Contenido resultados: " . PHP_EOL);
        print_r($ret->resultados);
}
?>
