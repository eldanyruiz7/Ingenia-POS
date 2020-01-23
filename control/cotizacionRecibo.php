<?php
function genReciboCotizacion($idVenta,$mysqli)
{
    $sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig   = $mysqli->query($sql);
    $rowConfig      = $resultConfig->fetch_assoc();
    $sql = "SELECT * FROM cotizaciones WHERE id = $idVenta LIMIT 1";
    $result = $mysqli->query($sql);
    $rowVenta = $result->fetch_assoc();
    $idUsuario      = $rowVenta['usuario'];
    $idCliente      = $rowVenta['cliente'];
    $sql            = "SELECT nombre, apellidop FROM usuarios WHERE id = $idUsuario LIMIT 1";
    $resultUsr      = $mysqli->query($sql);
    $rowUsr         = $resultUsr->fetch_assoc();
    $nombreUsuario  = $rowUsr['nombre']." ".$rowUsr['apellidop'];
    $sql            = "SELECT rsocial, representante FROM clientes WHERE id = $idCliente LIMIT 1";
    $resultNomCte   = $mysqli->query($sql);
    $rowNomCte      = $resultNomCte->fetch_assoc();
    $nombreCompletoCliente = $rowNomCte['rsocial']; //." ".$rowNomCte['apellidom'];

    $recibo =  " <table style='width:100%'>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'><h3>--".$rowConfig['nombreComercio']."--</h3></td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $fecha   = date('d/m/Y h:i:s a', strtotime($rowVenta['timestamp']));
    $recibo .=  " <table style='width:100%'>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center' colspan=4>$fecha</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Cliente:</td>";
    $recibo .= "        <td style='text-align:right' colspan=3>$nombreCompletoCliente</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>Le atendió:</td>";
    $recibo .= "        <td style='text-align:right' colspan=3> $nombreUsuario</td>";
    $recibo .= "    </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo .="     </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td colspan=4 style='text-align:center'><b>COTIZACIÓN</b></td>";
    $recibo .= "    </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo .="     </tr>";
    $recibo .= " </table>";
    $recibo .= " <table style='width:100%'>";
    $recibo .="     <tr>";
    $recibo .="         <th style='text-align:center'>Nombre</th>";
    $recibo .="         <th style='text-align:center'>Cant</th>";
    $recibo .="         <th style='text-align:center'>PUnit</th>";
    $recibo .="         <th style='text-align:center'>$</th>";
    $recibo .="     </tr>";
    $sql = "SELECT
                productos.nombrecorto       AS nombre,
                detallecotizacion.cantidad  AS cantidad,
                detallecotizacion.precio    AS precio,
                detallecotizacion.subtotal  AS subtotal
            FROM detallecotizacion
            INNER JOIN productos
            ON detallecotizacion.producto = productos.id
            WHERE detallecotizacion.cotizacion = $idVenta";
    $resultDetalle  = $mysqli->query($sql);
    $totProd = 0;
    while ($rowD    = $resultDetalle->fetch_assoc())
    {
        $nombre     = $rowD['nombre'];
        $cantidad   = $rowD['cantidad'];
        $precio     = $rowD['precio'];
        $subtotal   = $rowD['subtotal'];
        $recibo .="     <tr style='width:100px'>";
        $recibo .="         <td colspan=4>$nombre</td>";
        $recibo .="     </tr>";
        $recibo .="     <tr style='width:100px'>";
        $recibo .="         <td>&nbsp;</td>";
        $recibo .="         <td style='text-align:right'>".number_format($cantidad,3)."</td>";
        $recibo .="         <td style='text-align:right'>".number_format($precio,2,".","")."</td>";
        $recibo .="         <td style='text-align:right'>".number_format($subtotal,2,".","")."</td>";
        $recibo .="     </tr>";
        $totProd ++;
    }
    $recibo         .="     <tr>";
    $recibo         .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo         .="     </tr>";
    $recibo         .="     <tr>";
    $recibo         .="         <td>TOT. ARTS.:</td>";
    $recibo         .="         <td>$totProd</td>";
    $recibo         .="         <td><b>TOTAL:</b></td>";
    $totalVenta = $rowVenta['totalventa'];
    $totalVenta_f = number_format($totalVenta, 2);
    $recibo         .="         <td style='text-align:right'><b>$$totalVenta_f</b></td>";
    $recibo         .="     </tr>";
    $recibo         .="     <tr>";
    $recibo         .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo         .="     </tr>";
    $recibo         .=" </table>";

    $recibo         .=  " <table style='width:100%'>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'><svg id='code_svg'></svg></td>";
    $recibo         .= "    </tr>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>".$rowConfig['nombreRepresentante']."</td>";
    $recibo         .= "    </tr>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>".$rowConfig['rfcComercio']."</td>";
    $recibo         .= "    </tr>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>".$rowConfig['direccionComercio']."</td>";
    $recibo         .= "    </tr>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>".$rowConfig['direccionComercio2']."</td>";
    $recibo         .= "    </tr>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>".$rowConfig['fraseSaludo']."</td>";
    $recibo         .= "    </tr>";
    $recibo         .= " </table>";
    return $recibo;
}

?>
