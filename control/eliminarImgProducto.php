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
		function responder($response, $mysqli)
	    {
	        $response['error'] = $mysqli->error;
	        header('Content-Type: application/json');
	        echo json_encode($response, JSON_FORCE_OBJECT);
	        $mysqli->close();
	        exit;
	    }
        //$idUsr = $sesion->get("id");
        $idProducto = $_POST['idProducto'];
		if (is_numeric($idProducto) == FALSE)
		{
			$response["exito"] = 0;
			$response["respuesta"] = '<div class="alert alert-danger alert-dismissable">';
			$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
			$response["respuesta"] .= 	'El id del producto debe ser num&eacute;rico';
			$response["respuesta"] .='</div>';
			responder($response, $mysqli);
		}
		$sql = "UPDATE productos SET img = NULL WHERE id = $idProducto LIMIT 1";
		$res = $mysqli->query($sql);
		$dirImg = "../images/_productos_/".str_pad($idProducto, 10, "0", STR_PAD_LEFT).".jpg";
		if($mysqli->affected_rows > 0)
	    {
			unlink($dirImg);
			$response["exito"] = 1;
			$response["respuesta"] = '<div class="alert alert-info alert-dismissable">';
			$response["respuesta"] .='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
			$response["respuesta"] .= 	'La imagen ha sido borrada correctamente';
			$response["respuesta"] .='</div>';
			responder($response, $mysqli);
		}
		else
		{
			$response["exito"] 		= 0;
			$response["respuesta"] 	= '<div class="alert alert-warning alert-dismissable">';
			$response["respuesta"] 	.='	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
			$response["respuesta"] 	.= 	'No se pudo borrar la imagen, int&eacute;ntalo nuevamente';
			$response["respuesta"] 	.='</div>';
			$response["sql"] 		=$dirImg;
			responder($response, $mysqli);
		}
 	}
 ?>
