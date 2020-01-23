<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
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
    $request = mysqli_real_escape_string($mysqli, $_POST["query"]);
    $query = "SELECT nombrelargo FROM productos WHERE activo = 1 AND nombrelargo LIKE '%".$request."%'";
    $result = mysqli_query($mysqli, $query);
    $data = array();
    if(mysqli_num_rows($result) > 0)
    {
        while($row = mysqli_fetch_assoc($result))
        {
            $data[] = $row["nombrelargo"];
        }
        echo json_encode($data);
    }
}
?>
