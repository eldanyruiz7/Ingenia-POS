<?php
function validarUsuario($usuario, $password, $mysqli)
{
    //require ('../conecta/bd.php');
    $sql = "SELECT
                *
            FROM
                usuarios
            WHERE
                nick = '$usuario'
            LIMIT 1";
    $result = $mysqli->query($sql);
    if($result->num_rows == 1)
    {
        $fila = $result->fetch_assoc();
        if( strcmp($password, $fila["cntrsn"]) === 0 )
            return $fila;
        else
            return false;
    }
    else
        return false;
}
?>
