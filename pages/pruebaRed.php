<?php
function redondearDec ($dec)
{
    $dec                =   (string)$dec;
    $partes             =   explode(".",$dec);
    $parteEntera        =   $partes[0];
    $parteDecimal       =   $partes[1];
    if($parteDecimal    ==  "")
        $parteDecimal   =   "00";
    if(strlen($parteDecimal)== 1)
        $parteDecimal   =   $parteDecimal . "0";
    $parteDecimal       =   substr($parteDecimal, 0, 2);
    $dec                =   $parteEntera . "." . $parteDecimal;
    $str                =   (string)$dec;
    $str                =   explode(".",$str);
    $segundoDec         =   $str[1];
    $segundoDec         =   (int)substr($segundoDec, 1, 1);
    if($segundoDec      ==  0)
        return $dec;
    elseif ($segundoDec >   0)
    {
        $dec            =   number_format($dec, 1);
        if($segundoDec  <   5)
            $dec        =   $dec + 0.1;
        return number_format($dec, 2);
    }
}
$totalVenta = 0;
$cantidad = 1.1;
$precio = 21.00;
$totalVenta     +=   redondearDec($cantidad * $precio);
$cantidad = 1.1;
$precio = 12.69;
$totalVenta     +=   redondearDec($cantidad * $precio);
print_r($totalVenta);
?>
