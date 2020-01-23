<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require ('../conecta/bd.php');
require ("../conecta/sesion.class.php");
$sesion = new sesion();
require ("../conecta/cerrarOtrasSesiones.php");
require ("../conecta/usuarioLogeado.php");
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    function chkProdUtil()
    {

    }
    function responder($response, $mysqli)
    {
        $response['error'] = $mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $response = array(
        "status"        => 1
    );
    $existe = 0;
    $utilMin = 3;
    $sql = "SELECT
                precios.preciolista AS pLista,
                precios.producto AS idProducto,
                detalleprecios.tipoprecio AS tipoPrecio,
                detalleprecios.precioXpaquete AS pPaquete,
                detalleprecios.utilidadXpaquete AS uPaquete,
                detalleprecios.precioXunidad AS pUnidad,
                detalleprecios.utilidadXunidad AS uUnidad,
                productos.factorconversion AS fConversion,
                productos.balanza AS balanza
            FROM precios
            INNER JOIN detalleprecios ON precios.producto = detalleprecios.producto
            INNER JOIN productos ON precios.producto = productos.id";
    $resultProd = $mysqli->query($sql);
    while ($rowProd = $resultProd->fetch_assoc())
    {
        $idProducto = $rowProd['idProducto'];
        $pLista     = $rowProd['pLista'];
        $pPaquete   = $rowProd['pPaquete'];
        $uPaquete   = $rowProd['uPaquete'];
        $pUnidad    = $rowProd['pUnidad'];
        $uUnidad    = $rowProd['uUnidad'];
        $fConversion= $rowProd['fConversion'];
        $x          = $pUnidad * $fConversion;
        $response['idProducto'] = $idProducto;
        $response['x'] = $x;
        if($rowProd['balanza'] == 1)
        {
            if( $pLista >= $x )
                $existe = 1;
        }
        else
        {
            if($pLista >= $pPaquete || $pLista >= $x )
            {
                $existe = 1;
                break;
            }
        }
        if($rowProd['balanza'] == 1)
        {
            // Utilidad precio X pieza
            $util   = ($x * 100) / $pLista;
            if($util < $utilMin)
            {
                $existe = 1;
                break;
            }
        }
        else
        {
            $dif    = $pPaquete - $pLista;
            $util   = ($dif * 100) / $pLista;
            if($util < $utilMin)
            {
                $existe = 1;
                break;
            }
            // Utilidad precio X pieza
            $util   = ($x * 100) / $pLista;
            if($util < $utilMin)
            {
                $existe = 1;
                break;
            }
        }
        /*if($pLista >= $pPaquete || $pLista >= $x )
        {
            $existe = 1;
            break;
        }
        else
        {
            // Utilidad precio X paquete
            $dif    = $pPaquete - $pLista;
            $util   = ($dif * 100) / $pLista;
            if($util < $utilMin)
            {
                $existe = 1;
                break;
            }
            // Utilidad precio X pieza
            $util   = ($x * 100) / $pLista;
            if($util < $utilMin)
            {
                $existe = 1;
                break;
            }
        }*/
    }
    if($existe == 0)
        $response["status"] = 0;
    else {
        $response['msg'] = '<li>';
        $response['msg'] .='    <a href="listarProductoMenUtil.php">';
        $response['msg'] .='        <i class="fa fa-shopping-basket" aria-hidden="true"></i> Tienes productos con ';
        $response['msg'] .='        <span class="text-danger"><b>- 3%</b></span> de utilidad';
        $response['msg'] .='    </a>';
        $response['msg'] .='</li>';
        $response ['status']= 1;
    }
    responder($response,$mysqli);
}
     ?>
