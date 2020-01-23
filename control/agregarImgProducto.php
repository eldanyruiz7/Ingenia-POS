<?php
	require_once ('../conecta/bd.php');
	require_once ("../conecta/sesion.class.php");
	$sesion = new sesion();
	require_once ("../conecta/cerrarOtrasSesiones.php");
	require_once ("../conecta/usuarioLogeado.php");
	$usuario= $sesion->get("id");
	$idSes  = $sesion->get("idsesion");
	if( logueado($idSes,$usuario,$mysqli) == FALSE)
	{
	    header("Location: /pventa_std/pages/login.php");
	}
	else
	{
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
			$idProducto = $_POST['idProducto'];
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
					$imagenBinario 		= base64_encode(file_get_contents($_FILES['inputFileImagen']['tmp_name']));
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
		    		}
		            $directorio = '../images/_productos_/';
					$nombreArchivo = str_pad($idProducto, 10, "0", STR_PAD_LEFT);
					$imagen_d   = base64_decode($imagenBinario); // decode an image
					$im         = imagecreatefromstring($imagen_d); // php function to create image from string
					// condition check if valid conversion
					if ($im     !== false)
					{
						$resp   = imagejpeg($im, $_SERVER['DOCUMENT_ROOT']."/pventa_std/images/_productos_/$nombreArchivo.jpg");
						if ($resp)
						{
							$sql = "UPDATE productos SET img = '$nombreArchivo' WHERE id = $idProducto LIMIT 1";
							$res = $mysqli->query($sql);
							if ($mysqli->affected_rows > 0)
							{
								imagedestroy($im);
								$response["exito"] = 1;
								$response["respuesta"] = '<div class="alert alert-info alert-dismissable">';
								$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
								$response["respuesta"] .= 	'La imagen ha sido agregada correctamente';
								$response["respuesta"] .='</div>';
								$response['src']		= "../images/_productos_/".$nombreArchivo.".jpg";
								responder($response, $mysqli);
							}
							else
							{
								$response["exito"] = 0;
								$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
								$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
								$response["respuesta"] .= 	'No se pudo guardar la imagen en la base de datos';
								$response["respuesta"] .='</div>';
								$response["sql"] 		= $sql;
								responder($response, $mysqli);
							}
						}
						else
						{
							$response["exito"] = 0;
							$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
							$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
							$response["respuesta"] .= 	'No se pudo guardar la imagen en el directorio selecionado';
							$response["respuesta"] .='</div>';
							responder($response, $mysqli);
						}
					}
					else
					{
						$response["exito"] = 0;
						$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
						$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
						$response["respuesta"] .= 	'No se pudo renderizar la imagen, intenta nuevamente o intenta con una imagen distinta';
						$response["respuesta"] .='</div>';
						responder($response, $mysqli);
					}
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
		// function responder($response, $mysqli)
	    // {
	    //     $response['error'] = $mysqli->error;
	    //     header('Content-Type: application/json');
	    //     echo json_encode($response, JSON_FORCE_OBJECT);
	    //     $mysqli->close();
	    //     exit;
	    // }
        // //$idUsr = $sesion->get("id");
        // $idProducto = $_POST['idProducto'];
		// if (is_numeric($idProducto) == FALSE)
		// {
		// 	$response["exito"] = 0;
		// 	$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
		// 	$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
		// 	$response["respuesta"] .= 	'El id del producto debe ser num&eacute;rico';
		// 	$response["respuesta"] .='</div>';
		// 	responder($response, $mysqli);
		// }
		// $sql = "UPDATE productos SET img = NULL WHERE id = $idProducto LIMIT 1";
		// $res = $mysqli->query($sql);
		// $dirImg = "../images/_productos_/".str_pad($idProducto, 10, "0", STR_PAD_LEFT).".jpg";
		// if($mysqli->affected_rows > 0)
	    // {
		// 	unlink($dirImg);
		// 	$response["exito"] = 1;
		// 	$response["respuesta"] = '<div class="alert alert-info alert-dismissable">';
		// 	$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
		// 	$response["respuesta"] .= 	'La imagen ha sido borrada correctamente';
		// 	$response["respuesta"] .='</div>';
		// 	responder($response, $mysqli);
		// }
		// else
		// {
		// 	$response["exito"] 		= 0;
		// 	$response["respuesta"] 	= '<div class="alert alert-warning alert-dismissable">';
		// 	$response["respuesta"] 	.='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
		// 	$response["respuesta"] 	.= 	'No se pudo borrar la imagen, int&eacute;ntalo nuevamente';
		// 	$response["respuesta"] 	.='</div>';
		// 	$response["sql"] 		=$dirImg;
		// 	responder($response, $mysqli);
		// }
 	}
 ?>
