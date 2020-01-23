<?php
function genReciboRetiro($idRecibo,$mysqli)
{
    $sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig   = $mysqli->query($sql);
    $rowConfig      = $resultConfig->fetch_assoc();
    $sql = "SELECT * FROM retiros WHERE id = $idRecibo LIMIT 1";
    $result = $mysqli->query($sql);
    $fila = $result->fetch_assoc();
    $id = $fila['id'];
    $idTipo = $fila['tipo'];
    $tipoMovMay = ($idTipo == 0) ? "RETIRO" : "INGRESO";
    $tipoMovMin = ($idTipo == 0) ? "retiro" : "ingreso";
    $usuario = $fila['usuario'];
    $sql = "SELECT nombre, apellidop, apellidom FROM usuarios WHERE id = $usuario LIMIT 1";
    $resultUsr = $mysqli->query($sql);
    $filaUsr = $resultUsr->fetch_assoc();
    $nombreUsuario = $filaUsr['nombre']." ".$filaUsr['apellidop']." ".$filaUsr['apellidom'];

    $idCajero = $fila['cajero'];
    $sql = "SELECT nombre, apellidop, apellidom FROM usuarios WHERE id = $idCajero LIMIT 1";
    $resultCaj = $mysqli->query($sql);
    $filaCaj = $resultCaj->fetch_assoc();
    $nombreCajero = $filaCaj['nombre']." ".$filaCaj['apellidop']." ".$filaCaj['apellidom'];
    $fecha = date('d/m/Y', time());
    $hora = date('h:i:s a', time());
    $fechaRet = date('d/m/Y',strtotime($fila['timestamp']));
    $horaRet = date('h:i:s a',strtotime($fila['timestamp']));
    $monto = $fila['monto'];
    $monto = number_format($monto,2);
    $obs = $fila['observaciones'];
    $recibo =  " <table>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'><h3>".$rowConfig['nombreComercio']."</h3></td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'><b>COMPROBANTE $tipoMovMay DE EFECTIVO</b></td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .=  " <table>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'><b>#</b> $id</td>";
    $recibo .= "        <td style='text-align:right'></td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Fecha impresi&oacute;n</td>";
    $recibo .= "        <td style='text-align:right'>$fecha</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Hora impresi&oacute;n</td>";
    $recibo .= "        <td style='text-align:right'> $hora</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Fecha $tipoMovMin:</td>";
    $recibo .= "        <td style='text-align:right'>$fechaRet</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Hora $tipoMovMin</td>";
    $recibo .= "        <td style='text-align:right'> $horaRet</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Cajero: </td>";
    $recibo .= "        <td style='text-align:right'>$nombreCajero</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Autoriza </td>";
    $recibo .= "        <td style='text-align:right'>$nombreUsuario</td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .= "___________________________________";
    $recibo .=  " <table>";
    $recibo .= "    <tr>";
    $recibo .= "        <th style='text-align:left'><h3><b>MONTO $tipoMovMay:</b></h3></th>";
    $recibo .= "        <th style='text-align:right'><h3><b>$$monto</b></h3></th>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .= "___________________________________";
    $recibo .= " </br>";
    $recibo .=  " <table>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left;width:100%'>Observaciones: $obs</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center;width:100%'></br>Firma:</br></br></br></br></br></br></br></br></td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center;width:100%'>___________________________________</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center;width:100%'><h4>$nombreUsuario</h4></td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .= " </br>";
    $recibo .= " </br>";
    return $recibo;
}




 ?>
