<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');     //displays php errors*/
require "../conecta/bd.php";

$cod                = $_POST['datos'];
//$idCliente          = $_POST['idProveedor'];
if(strlen($cod)==0)
{
    echo "Sin registro";
    exit;
}
/*$sql ="SELECT
            tipoprecio
        FROM
            clientes
        WHERE
            id = $idCliente
        LIMIT 1";
$resultadoCliente   = $mysqli->query($sql);
$tipoCliente        = $resultadoCliente->fetch_assoc();
$tipoprecio         = $tipoCliente['tipoprecio'];*/
//echo "Tipo precio: ".$tipoprecio;
$sql = "SELECT
            productos.id                AS  id,
            productos.codigo            AS  codigo,
            productos.codigo2           AS  codigo2,
            productos.nombrecorto       AS  nombrecorto,
            productos.unidadventa       AS  unidadVenta,
            productos.IVA               AS  IVA,
            productos.IEPS              AS  IEPS,
            productos.factorconversion  AS  factorconversion,
            productos.balanza           AS  balanza,
            productos.claveSHCP         AS  claveSAT,
            productos.unidadventa       AS  unidadventa,
            precios.preciolista         AS  preciolista,
            unidadesventa.nombre        AS  unidadventanombre
        FROM        productos
        INNER JOIN  precios
        ON          productos.id = precios.producto
        INNER JOIN  unidadesventa
        ON          productos.unidadventa = unidadesventa.id
        WHERE       (codigo = '$cod' OR codigo2 = '$cod')
        AND         productos.activo         = 1
        LIMIT 1";
if($resultado = $mysqli->query($sql))
{
    if($resultado->num_rows > 0)
    {
        $htmlSelectU    = "";
        $producto   = $resultado->fetch_assoc();
        $sql = "SELECT * FROM unidadesventa";
        $res_u_venta = $mysqli->query($sql);
        while ($row_u_venta = $res_u_venta->fetch_assoc())
        {
            $idUnidad       = $row_u_venta['id'];
            $nombreUnidad   = $row_u_venta['nombre'];
            $claveUSat      = $row_u_venta['c_ClaveUnidad'];

            $htmlSelectU    .= "<option value='$idUnidad'>$claveUSat</option>";
        }
        if(strlen($producto['codigo']) > 0)
            $codigo = $producto['codigo'];
        else
            $codigo = $producto['codigo2'];
        /*if($producto['factorconversion'] > 1)
            $conversionarray = array(
            0               =>  $producto['unidadventanombre'],
            1               =>  'Contenedores');
        else
            $conversionarray = array(
            0               =>  $producto['unidadventanombre']);*/
        //Seteamos el header de "content-type" como "JSON" para que jQuery lo reconozca como tal
        header('Content-Type: application/json');
        //Guardamos los datos en un array
        $response = array(
        'id'                =>  $producto['id'],
        'codigo'            =>  $codigo,
        'nombre'            =>  $producto['nombrecorto'],
        'claveSAT'          =>  $producto['claveSAT'],
        'IVA'               =>  $producto['IVA'],
        'IEPS'              =>  $producto['IEPS'],
        'htmlSelectU'       =>  $htmlSelectU,
        'unidadVenta'       =>  $producto["unidadVenta"],
        //'precio'            =>  $producto['precio'],
        'balanza'           =>  $producto['balanza'],
        'preciolista'     =>  number_format($producto['preciolista'], 2),
        'unidadventanombre' =>  $producto['unidadventanombre'],
        'factorconversion'  =>  $producto['factorconversion']);
        //Devolvemos el array pasado a JSON como objeto
        echo json_encode($response, JSON_FORCE_OBJECT);
    }
    else
        echo "Sin registro";
}
else {
    echo "Error de conexion";
}


?>
