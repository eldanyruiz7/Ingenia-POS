<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
$usuario = $sesion->get("nick");
if( $usuario == false )
{
    header("Location: /pventa_std/pages/login.php");
}
else
{
    function responder($response, $mysqli)
    {
        $response['respuesta'].=$mysqli->error;
        //print_r($response);
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    //$esteProducto = json_decode($_POST['arrayProducto']);
    $response = array(
        "nombreCorto"       =>1,
        "nombreLargo"       =>1,
        "departamento"      =>1,
        "codigoBarras"      =>1,
        "codigo2"           =>1,
        "precioLista"       =>1,
        "factor"            =>1,
        "unidadVenta"       =>1,
        "iva"               =>1,
        "ieps"              =>1,
        "balanza"           =>1,
        "img"               =>1,
        "listaPrecios"      =>1,
        "status"            =>1
    );
    $response["respuesta"]  ='<div class="alert alert-danger alert-dismissable">';
    $response["respuesta"]  .='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    $response["respuesta"]  .='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Los campos marcados son obligatorios. ';
    $response["respuesta"]  .='</div>';
    $nombreCorto            = $mysqli->real_escape_string($_POST['nombreCorto']);
    $nombreLargo            = $mysqli->real_escape_string($_POST['nombreLargo']);
    $departamento           = $mysqli->real_escape_string($_POST['departamento']);
    $codigoBarras           = $mysqli->real_escape_string($_POST['codigoBarras']);
    $codigo2                = $mysqli->real_escape_string($_POST['codigo2']);
    $claveSHCP              = $mysqli->real_escape_string($_POST['claveSHCP']);
    $balanza                = $_POST['balanza'];
    $unidadVenta            = $mysqli->real_escape_string($_POST['unidadVenta']);
    $precioLista            = $mysqli->real_escape_string($_POST['precioLista']);
    $factor                 = $mysqli->real_escape_string($_POST['factor']);
    $iva                    = $mysqli->real_escape_string($_POST['iva']);
    $ieps                   = $mysqli->real_escape_string($_POST['ieps']);
    $inputMenPxP            = $mysqli->real_escape_string($_POST['inputMenPxP']);
    $inputMenDxP            = $mysqli->real_escape_string($_POST['inputMenDxP']);
    $inputMenUxP            = $mysqli->real_escape_string($_POST['inputMenUxP']);
    $inputMenPxU            = $mysqli->real_escape_string($_POST['inputMenPxU']);
    $inputMenDxU            = $mysqli->real_escape_string($_POST['inputMenDxU']);
    $inputMenUxU            = $mysqli->real_escape_string($_POST['inputMenUxU']);

    $inputMedPxP            = $mysqli->real_escape_string($_POST['inputMedPxP']);
    $inputMedDxP            = $mysqli->real_escape_string($_POST['inputMedDxP']);
    $inputMedUxP            = $mysqli->real_escape_string($_POST['inputMedUxP']);
    $inputMedPxU            = $mysqli->real_escape_string($_POST['inputMedPxU']);
    $inputMedDxU            = $mysqli->real_escape_string($_POST['inputMedDxU']);
    $inputMedUxU            = $mysqli->real_escape_string($_POST['inputMedUxU']);

    $inputMayPxP            = $mysqli->real_escape_string($_POST['inputMayPxP']);
    $inputMayDxP            = $mysqli->real_escape_string($_POST['inputMayDxP']);
    $inputMayUxP            = $mysqli->real_escape_string($_POST['inputMayUxP']);
    $inputMayPxU            = $mysqli->real_escape_string($_POST['inputMayPxU']);
    $inputMayDxU            = $mysqli->real_escape_string($_POST['inputMayDxU']);
    $inputMayUxU            = $mysqli->real_escape_string($_POST['inputMayUxU']);

    $inputEspPxP            = $mysqli->real_escape_string($_POST['inputEspPxP']);
    $inputEspDxP            = $mysqli->real_escape_string($_POST['inputEspDxP']);
    $inputEspUxP            = $mysqli->real_escape_string($_POST['inputEspUxP']);
    $inputEspPxU            = $mysqli->real_escape_string($_POST['inputEspPxU']);
    $inputEspDxU            = $mysqli->real_escape_string($_POST['inputEspDxU']);
    $inputEspUxU            = $mysqli->real_escape_string($_POST['inputEspUxU']);
    $imagenBinario          = $_POST['imagenBinario'];
    $tipoimagen             = $_POST['imagenTipo'];

    if( strlen($nombreCorto) == 0)
    {
        $response["nombreCorto"] = 0;
        $response["status"] = 0;
    }
    if( strlen($nombreLargo) == 0)
    {
        $response["nombreLargo"] = 0;
        $response["status"] = 0;
    }
    if( strlen($departamento) == 0 || !(is_numeric($departamento)))
    {
        $response["departamento"] = 0;
        $response["status"] = 0;
    }
    /*if( strlen($codigoBarras) == 0)
    {
        $response["codigoBarras"] = 0;
        $response["status"] = 0;
    }
    if( strlen($codigo2) == 0)
    {
        $response["codigo2"] = 0;
        $response["status"] = 0;
    }*/
    if( strlen($precioLista) == 0 || !(is_numeric($precioLista)))
    {
        $response["precioLista"] = 0;
        $response["status"] = 0;
    }
    if ((strlen($factor) == 0) || !(is_numeric($factor)))
    {
        $response["factor"] = 0;
        $response["status"] = 0;
    }
    // if( strlen($unidadVenta) == 0 || !(is_numeric($unidadVenta)))
    // {
    //     $response["unidadVenta"] = 0;
    //     $response["status"] = 0;
    // }
    if( !(is_numeric($iva)))//if( strlen($iva) == 0 || !(is_numeric($iva)))
    {
        $response["iva"] = 0;
        $response["status"] = 0;
    }
    if( !(is_numeric($ieps)))//if( strlen($ieps) == 0 || !(is_numeric($ieps)))
    {
        $response["ieps"] = 0;
        $response["status"] = 0;
    }
    // if( strlen($balanza) == 0 || !(is_numeric($balanza)))
    // {
    //     $response["ieps"] = 0;
    //     $response["status"] = 0;
    // }
    if($response["status"] == 0)
        responder($response, $mysqli);

    if( strlen($codigoBarras) == 0 && strlen($codigo2) == 0)
    {
        $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
        $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Cada producto debe tener asignado al menos una clave corta o un c&oacute;digo de barras.';
        $response["respuesta"].='</div>';
        $response["codigoBarras"] = 0;
        $response["codigo2"] = 0;
        $response["status"] = 0;
        responder($response, $mysqli);
    }
    if( strlen($codigoBarras) > 0)
    {
        $sql = "SELECT codigo FROM productos WHERE codigo = '$codigoBarras' AND activo = 1";
        $resCod = $mysqli->query($sql);
        $row_cnt = $resCod->num_rows;
        if($row_cnt > 0)
        {
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Ya existe otro producto con el mismo c&oacute;digo de barras.';
            $response["respuesta"].='</div>';
            $response["codigoBarras"] = 0;
            $response["status"] = 0;
            responder($response, $mysqli);
        }
    }
    if( strlen($codigo2) > 0)
    {
        $sql = "SELECT codigo2 FROM productos WHERE codigo2 = '$codigo2' AND activo = 1";
        $resCod2 = $mysqli->query($sql);
        $row_cnt = $resCod2->num_rows;
        if($row_cnt > 0)
        {
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Ya existe otro producto con la misma clave corta.';
            $response["respuesta"].='</div>';
            $response["codigo2"] = 0;
            $response["status"] = 0;
            responder($response, $mysqli);
        }
    }
    if (strlen($inputMenPxU) == 0
        || !(is_numeric($inputMenPxU))
            || strlen($inputMenDxU) == 0
                || !(is_numeric($inputMenDxU))
                    ||strlen($inputMenUxU) == 0
                        || !(is_numeric($inputMenUxU))
                            ||strlen($inputMedPxU) == 0
                                || !(is_numeric($inputMedPxU))
                                    ||strlen($inputMedDxU) == 0
                                        || !(is_numeric($inputMedDxU))
                                            ||strlen($inputMedUxU) == 0
                                                || !(is_numeric($inputMedUxU))
                                                    ||strlen($inputMayPxU) == 0
                                                        || !(is_numeric($inputMayPxU))
                                                            ||strlen($inputMayDxU) == 0
                                                                || !(is_numeric($inputMayDxU))
                                                                    ||strlen($inputMayUxU) == 0
                                                                        || !(is_numeric($inputMayUxU))
                                                                            ||strlen($inputEspPxU) == 0
                                                                                || !(is_numeric($inputEspPxU))
                                                                                    ||strlen($inputEspDxU) == 0
                                                                                        || !(is_numeric($inputEspDxU))
                                                                                            ||strlen($inputEspUxU) == 0
                                                                                                || !(is_numeric($inputEspUxU)))
    {
        $response['listaPrecios'] = 0;
        $response["status"] = 0;
        responder($response, $mysqli);
    }
    if ($balanza == 0)
    {
        if (strlen($inputMenPxP) == 0
            || !(is_numeric($inputMenPxP))
                || strlen($inputMenDxP) == 0
                    || !(is_numeric($inputMenDxP))
                        ||strlen($inputMenUxP) == 0
                            || !(is_numeric($inputMenUxP))
                                ||strlen($inputMedPxP) == 0
                                    || !(is_numeric($inputMedPxP))
                                        ||strlen($inputMedDxP) == 0
                                            || !(is_numeric($inputMedDxP))
                                                ||strlen($inputMedUxP) == 0
                                                    || !(is_numeric($inputMedUxP))
                                                        ||strlen($inputMayPxP) == 0
                                                            || !(is_numeric($inputMayPxP))
                                                                ||strlen($inputMayDxP) == 0
                                                                    || !(is_numeric($inputMayDxP))
                                                                        ||strlen($inputMayUxP) == 0
                                                                            || !(is_numeric($inputMayUxP))
                                                                                ||strlen($inputEspPxP) == 0
                                                                                    || !(is_numeric($inputEspPxP))
                                                                                        ||strlen($inputEspDxP) == 0
                                                                                            || !(is_numeric($inputEspDxP))
                                                                                                ||strlen($inputEspUxP) == 0
                                                                                                    || !(is_numeric($inputEspUxP)))
        {
            $response['listaPrecios'] = 0;
            $response["status"] = 0;
            responder($response, $mysqli);
        }
    }
    else
    {
        $inputMenPxP            = 0;
        $inputMenDxP            = 0;
        $inputMenUxP            = 0;
        $inputMedPxP            = 0;
        $inputMedDxP            = 0;
        $inputMedUxP            = 0;
        $inputMayPxP            = 0;
        $inputMayDxP            = 0;
        $inputMayUxP            = 0;
        $inputEspPxP            = 0;
        $inputEspDxP            = 0;
        $inputEspUxP            = 0;
        $balanza                = 1;
    }
    // Procesar imagen, si existe
    // $imagenBinaria              = 'NULL';
    // if(isset($_FILES["img"]))
    // {
    //     $nombreArchivo          = $_FILES['img']['name'];
    //     $extensiones            = array('jpg', 'jpeg', 'png', 'bmp');
    //     $tmp                    = explode('.', $nombreArchivo);
    //     $extension              = end($tmp);
    //     if(!in_array($extension, $extensiones))//&&($mTipo != IMAGETYPE_JPEG) && ($mTipo != IMAGETYPE_PNG) && ($mTipo != IMAGETYPE_BMP))
    //     {
    //         $response["status"] = 0;
    //         $response["img"]    = 0;
    //         $response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
    //         $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
    //         $response["respuesta"] .= 	'Sólo se permiten archivos de imagen (Tipo: JPG, PNG o BMP)';
    //         $response["respuesta"] .='</div>';
    //         responder($response, $mysqli);
    //     }
    //     $imagenBinaria          = addslashes(file_get_contents($_FILES['img']['tmp_name']));
	// 	$tamañoArchivo          = $_FILES['img']['size']; //Obtenemos el tamaño del archivo en Bytes
	// 	$tamañoArchivoKB        = round(intval(strval( $tamañoArchivo / 1024 ))); //Pasamos el tamaño del archivo a KB
	// 	$tamañoMaximoKB         = "5120"; //Tamaño máximo expresado en KB
	// 	$tamañoMaximoBytes      = $tamañoMaximoKB * 1024; // -> 2097152 Bytes -> 2 MB
	// 	//Comprobamos el tamaño del archivo, y mostramos un mensaje si es mayor al tamaño expresado en Bytes
	// 	if($tamañoArchivo > $tamañoMaximoBytes)
    //     {
	// 		$response["status"] = 0;
	// 		$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
    //         $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
	// 		$response["respuesta"] .=	"El archivo <b>";
	// 		$response["respuesta"] .=	$nombreArchivo;
	// 		$response["respuesta"] .=" 	</b>es demasiado grande. El tamaño máximo del archivo es de ";
	// 		$response["respuesta"] .=	$tamañoMaximoKB;
	// 		$response["respuesta"] .=	"Kb. ";
	// 		$response["respuesta"] .=	"Inténtalo con una imagen de menor tamaño";
	// 		$response["respuesta"] .='</div>';
	// 		responder($response, $mysqli);
	// 	}
	// }
    $sql = "INSERT INTO productos (
                codigo,
                codigo2,
                nombrecorto,
                nombrelargo,
                balanza,
                departamento,
                unidadventa,
                factorconversion,
                claveSHCP,
                existencia,
                IVA,
                IEPS,
                img,
                imgTipo)
            VALUES (
                '$codigoBarras',
                '$codigo2',
                '$nombreCorto',
                '$nombreLargo',
                0,
                $departamento,
                1,
                $factor,
                '$claveSHCP',
                0,
                $iva,
                $ieps,
                '',
                '$tipoimagen')";
    if($mysqli->query($sql)!= TRUE)
    {
        $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
        $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Ocurri&oacute; un error con la Base de datos';
        $response["respuesta"].='</div>';
        $response["status"] = 0;
        responder($response, $mysqli);
    }
    else
    {
        $idProductoInsertado    =   $mysqli->insert_id;
        $sql = "INSERT INTO precios (
                    producto, preciolista)
                VALUES
                    ($idProductoInsertado, $precioLista)";
        if($mysqli->query($sql)!= TRUE)
        {
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Ocurri&oacute; un error al intentar almacenar el precio de lista';
            $response["respuesta"].='</div>';
            $response["status"] = 0;
            responder($response, $mysqli);
        }
        else
        {
            $sql = "INSERT INTO detalleprecios (tipoprecio, producto, precioXpaquete, descuentoXpaquete, utilidadXpaquete, precioXunidad, descuentoXunidad, utilidadXunidad)
                    VALUES ( 1, $idProductoInsertado, $inputMenPxP, $inputMenDxP, $inputMenUxP, $inputMenPxU, $inputMenDxU, $inputMenUxU);";
            $sql .= "INSERT INTO detalleprecios (tipoprecio, producto, precioXpaquete, descuentoXpaquete, utilidadXpaquete, precioXunidad, descuentoXunidad, utilidadXunidad)
                    VALUES ( 2, $idProductoInsertado, $inputMedPxP, $inputMedDxP, $inputMedUxP, $inputMedPxU, $inputMedDxU, $inputMedUxU);";
            $sql .= "INSERT INTO detalleprecios (tipoprecio, producto, precioXpaquete, descuentoXpaquete, utilidadXpaquete, precioXunidad, descuentoXunidad, utilidadXunidad)
                    VALUES ( 3, $idProductoInsertado, $inputMayPxP, $inputMayDxP, $inputMayUxP, $inputMayPxU, $inputMayDxU, $inputMayUxU);";
            $sql .= "INSERT INTO detalleprecios (tipoprecio, producto, precioXpaquete, descuentoXpaquete, utilidadXpaquete, precioXunidad, descuentoXunidad, utilidadXunidad)
                    VALUES ( 4, $idProductoInsertado, $inputEspPxP, $inputEspDxP, $inputEspUxP, $inputEspPxU, $inputEspDxU, $inputEspUxU);";
                if($mysqli->multi_query($sql)== TRUE)
                {
                    do {
                    } while ($mysqli->more_results() && $mysqli->next_result());
                    if (strlen($tipoimagen) > 0)
                    {
                        $insert_id_left = str_pad($idProductoInsertado, 10, "0", STR_PAD_LEFT);

                        //$imagen         = $paciente ->imagen;
                        $sql            = "UPDATE productos SET img = '$insert_id_left' WHERE id = $idProductoInsertado LIMIT 1";
                        // try
                        // {
                            if ($mysqli     ->query($sql))
                            {
                                $imagen_d   = base64_decode($imagenBinario); // decode an image
                                $im         = imagecreatefromstring($imagen_d); // php function to create image from string
                                // condition check if valid conversion
                                if ($im     !== false)
                                {
                                    $resp   = imagejpeg($im, $_SERVER['DOCUMENT_ROOT']."/pventa_std/images/_productos_/$insert_id_left.jpg");
                                    imagedestroy($im);
                                }
                            }
                        // } catch (Exception $e) {
                        //     echo 'Excepción capturada: ',  $e->getMessage(), "\n";
                        // }
                    }
                    $response["respuesta"]  =   "<div class='alert alert-success alert-dismissable'>";
                    $response["respuesta"] .=   "   <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>";
                    $response["respuesta"] .=   "   <i class='fa fa-check-circle' aria-hidden='true'></i> <b>&Eacute;xito</b> Producto con id: <b>$idProductoInsertado</b> ha sido agregado correctamente.";
                    $response["respuesta"] .=   "   <br/><b>-Nombre corto:</b> $nombreCorto";
                    $response["respuesta"] .=   "   <br/><b>-Nombre largo:</b> $nombreLargo";
                    $response["respuesta"] .=   "   <br/><b>-C&oacute;digo corto:</b> $codigo2";
                    $response["respuesta"] .=   "   <br/><b>-C&oacute;digo de barras:</b> $codigoBarras";
                    $response["respuesta"] .=   "</div>";
                    $response["status"] = 1;
                    $response["sql"] = $sql;

                    responder($response, $mysqli);
                    $mysqli->close();
                }
                else
                {
                    $response["respuesta"] ='<div class="alert alert-danger alert-dismissable">';
                    $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                    $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>No se pudo guardar.</b> Error inesperado al intentar almacenar la lista de precios. Por favor, consulta con el administrador.';
                    $response["respuesta"].='</div>';
                    $response["status"] = 0;
                    responder($response, $mysqli);

                }
        }
    }
    responder($response, $mysqli);
            //unlink($_SERVER["SERVER_ROOT"].$directorio.$nombreArchivo);
}
?>
