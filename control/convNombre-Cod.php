<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
    $request = $_POST["nombre"];
    $query = "SELECT codigo, codigo2 FROM productos WHERE activo = 1 AND nombrelargo = '$request' LIMIT 1";
    $result = mysqli_query($mysqli, $query);
    //$data = array();
    if(mysqli_num_rows($result) > 0)
    {
        $row = $result->fetch_assoc();
        if(strlen($row['codigo']) > 0 )
            echo $row['codigo'];
        else if(strlen($row['codigo2']) > 0 )
            echo $row['codigo2'];
            //$data[] = $row["nombrelargo"];

        //echo json_encode($data);
    }
}
?>
