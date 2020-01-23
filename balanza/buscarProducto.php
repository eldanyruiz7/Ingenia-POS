<?php

require "../conecta/bd.php";

$cod                                        = $_POST['datos'];
$idCliente                                  = $_POST['idCliente'];
if(strlen($cod)==0)
{
    echo "Sin registro";
    exit;
}
$sql ="SELECT tipoprecio FROM clientes WHERE id = $idCliente LIMIT 1";
$resultadoCliente                           = $mysqli->query($sql);
$tipoCliente                                = $resultadoCliente->fetch_assoc();
$tipoprecio                                 = $tipoCliente['tipoprecio'];
//echo "Tipo precio: ".$tipoprecio;
$sql = "SELECT
            productos.id                    AS  id,
            productos.codigo                AS  codigo,
            productos.codigo2               AS  codigo2,
            productos.nombrecorto           AS  nombrecorto,
            productos.nombrelargo           AS  descripcion,
            productos.img                   AS  imagen,
            productos.imgTipo               AS  imagenTipo,
            productos.factorconversion      AS  factorconversion,
            detalleprecios.precioXpaquete   AS  precioXpaquete,
            detalleprecios.precioXunidad    AS  precio,
            productos.balanza               AS  balanza,
            unidadesventa.id                AS  unidadventaid,
            unidadesventa.nombre            AS  unidadventanombre
        FROM        productos
        INNER JOIN  detalleprecios
        ON          productos.id            = detalleprecios.producto
        INNER JOIN  unidadesventa
        ON          productos.unidadventa   = unidadesventa.id
        WHERE       (codigo                 = '$cod' OR codigo2 = '$cod')
        AND         productos.activo        = 1
        AND         detalleprecios.tipoprecio= $tipoprecio LIMIT 1";
if($resultado                               = $mysqli->query($sql))
{
    if($resultado->num_rows                 > 0)
    {
        $producto                           = $resultado->fetch_assoc();
        if(strlen($producto['codigo'])      > 0)
            $codigo                         = $producto['codigo'];
        else
            $codigo                         = $producto['codigo2'];
        //Seteamos el header de "content-type" como "JSON" para que jQuery lo reconozca como tal
        header('Content-Type: application/json');
        //Guardamos los datos en un array
        $precioXpaquete = ($producto['balanza'] == 1) ? $producto['precio'] : $producto['precioXpaquete'];
        $srcImagen = (strlen($producto['imagen'])>0) ? "../images/_productos_/".$producto['imagen'].".jpg" : "";
        $response                           = array(
                        'id'                =>  $producto['id'],
                        'codigo'            =>  $codigo,
                        'nombre'            =>  $producto['nombrecorto'],
                        'nombreCache'       =>  $producto['nombrecorto'],
                        'descripcion'       =>  $producto['descripcion'],
                        'precio'            =>  $producto['precio'],
                        'precioXunidad'     =>  $producto['precio'],
                        'balanza'           =>  $producto['balanza'],
                        'unidadventaid'     =>  $producto['unidadventaid'],
                        'unidadventanombre' =>  $producto['unidadventanombre'],
                        'factorconversion'  =>  $producto['factorconversion'],
                        'precioXpaquete'    =>  $precioXpaquete,
                        'imagen'            =>  $srcImagen
        );
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
