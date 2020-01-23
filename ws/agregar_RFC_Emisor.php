<?php
$user               = "AIRY820226UXA_3";
$userPassword       = "184829659849329157062420";
try {
        $client = new SoapClient("http://mieses.from-la.net:8083/CMM_Pruebas/InterconectaWs?wsdl");
    //	var_dump($client->__getFunctions());
        $params = array(
            'user' => $user,
            'userPassword' => $userPassword,
            'contribuyenteRFC'=> "SACJ830819U62",
            'contribuyenteRazonSocial'=> "JORGE IVAN SALAZAR CHAVEZ",

        );
        $response = $client->__soapCall('otorgarAccesoContribuyente', array('parameters' => $params));
} catch (SoapFault $fault) {
        echo "SOAPFault: ".$fault->faultcode."-".$fault->faultstring."\n";
}
$ret                = $response->return;
//print_r("Estatus request: " . $ret->status . PHP_EOL);
//print_r("Mensjae request: " . $ret->mensaje . PHP_EOL);
/*if($ret->status == 200) {
        print_r("Contenido resultados: " . PHP_EOL);
        print_r($ret->resultados);
}*/
var_dump($ret);
?>
