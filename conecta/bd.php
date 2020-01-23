<?php
//error_reporting(E_ALL);
ini_set('display_errors', '1');     //displays php errors
//equire "conecta/bd.php"

    $servidor = 'localhost';
    $usr = 'root';
    $contrasena = 'admin';
    $bd = 'tiendadb';

    $mysqli = new mysqli($servidor , $usr , $contrasena , $bd);
    $mysqli->set_charset("utf8");
    if($mysqli->connect_errno)
    {
        echo "Error de Base de datos\n";
        echo "Errno: ". $mysqli->connect_errno . "\n";
        echo "Error: ". $mysqli->connect_error . "\n";
        exit;
    }
 ?>
