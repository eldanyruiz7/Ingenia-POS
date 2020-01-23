<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
//$response['exito'] = 1;
if(isset($_FILES["inputFileImagen"]))
{
	require "../conecta/bd.php";
	function responder($response, $mysqli)
	{
	    //$response['respuesta'].=$mysqli->error;
	    header('Content-Type: application/json');
	    echo json_encode($response);
	    $mysqli->close();
	    exit;
	}
	$error = $_FILES["inputFileImagen"]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData()
    if($error == 0)
    {
		/*$mTmpFile = $_FILES['inputFileImagen']['tmp_name'];
		$mTipo = exif_imagetype($mTmpFile);*/
        $nombreArchivo = $_FILES['inputFileImagen']['name'];
        $extensiones = array('jpg', 'jpeg', 'png', 'bmp', 'JPG', 'JPEG', 'PNG', 'BMP');
		$tmp = explode('.', $nombreArchivo);
		$extension = end($tmp);
        if(!in_array($extension, $extensiones))//&&($mTipo != IMAGETYPE_JPEG) && ($mTipo != IMAGETYPE_PNG) && ($mTipo != IMAGETYPE_BMP))
        {
			$response["exito"] = 0;
			$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
			$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
			$response["respuesta"] .= 	'Sólo se permiten archivos de imagen';
			$response["respuesta"] .='</div>';
			responder($response, $mysqli);
        }
        else
        {
		//Si la extensión es correcta, procedemos a comprobar el tamaño del archivo subido
		//Y definimos el máximo que se puede subir
		//Por defecto el máximo es de 2 MB, pero se puede aumentar desde el .htaccess o en la directiva 'upload_max_filesize' en el php.ini
        //Convertimos la información de la imagen en binario para insertarla en la BBDD
		$imagenBinaria 		= base64_encode(file_get_contents($_FILES['inputFileImagen']['tmp_name']));
		$tipoImagen			= $_FILES['inputFileImagen']['type'];
		$tamañoArchivo 		= $_FILES['inputFileImagen']['size']; //Obtenemos el tamaño del archivo en Bytes
		$tamañoArchivoKB 	= round(intval(strval( $tamañoArchivo / 1024 ))); //Pasamos el tamaño del archivo a KB

		$tamañoMaximoKB 	= "5120"; //Tamaño máximo expresado en KB
		$tamañoMaximoBytes 	= $tamañoMaximoKB * 1024; // -> 2097152 Bytes -> 2 MB
    		//Comprobamos el tamaño del archivo, y mostramos un mensaje si es mayor al tamaño expresado en Bytes
    		if($tamañoArchivo > $tamañoMaximoBytes)
            {
				$response["exito"] = 0;
				$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
                $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
				$response["respuesta"] .=	"El archivo <b>";
				$response["respuesta"] .=	$nombreArchivo;
				$response["respuesta"] .=" 	</b>es demasiado grande. El tamaño máximo del archivo es de ";
				$response["respuesta"] .=	$tamañoMaximoKB;
				$response["respuesta"] .=	"Kb. ";
				$response["respuesta"] .=	"Inténtalo con una imagen de menor tamaño";
				$response["respuesta"] .='</div>';
				responder($response, $mysqli);
    		};
            $directorio = '../pages/imagenes/';
            // Muevo la imagen desde el directorio temporal a nuestra ruta indicada anteriormente
            if(move_uploaded_file($_FILES['inputFileImagen']['tmp_name'],$directorio.$nombreArchivo))
			{
				$response["exito"] = 1;
				$response["respuesta"] 	= '<img class="vistaPrevia" width="100%" height="100%" src="data:'.$tipoImagen.';base64,'.$imagenBinaria.'" >';
				$response["src"] = $directorio.$nombreArchivo;
				$response["binario"] = $imagenBinaria;
				$response['tipo'] 		= $tipoImagen;
				unlink($directorio.$nombreArchivo);
				responder($response, $mysqli);
			}
            else
			{
				$response["exito"] = 0;
				$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
                $response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
				$response["respuesta"] .= 	"No se pudo adjuntar la imagen. Favor de consultarlo con el Administrador del Sistema";
				$response["respuesta"] .='</div>';
				responder($response, $mysqli);
            //unlink($_SERVER["SERVER_ROOT"].$directorio.$nombreArchivo);
			}
    	}
    }
    else
        echo $error;
 }
 ?>
