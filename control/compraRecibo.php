<?php
function genReciboCompra($idCompra,$mysqli)
{
    $sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig   = $mysqli->query($sql);
    $rowConfig      = $resultConfig->fetch_assoc();
    $sql = "SELECT * FROM compras WHERE id = $idCompra LIMIT 1";
    $result = $mysqli->query($sql);
    $rowCompra = $result->fetch_assoc();
    $idProveedor = $rowCompra['proveedor'];
    $sql = "SELECT nombres, apellidop, apellidom FROM proveedores WHERE id = $idProveedor LIMIT 1";
    $resultP = $mysqli->query($sql);
    $filaProveedor = $resultP->fetch_assoc();
    $nombreProveedor = $filaProveedor["nombres"]." ".$filaProveedor["apellidop"]." ".$filaProveedor["apellidom"];
    $idUsuario = $rowCompra['usuario'];
    $sql = "SELECT nombre, apellidop FROM usuarios WHERE id = $idUsuario LIMIT 1";
    $resultUsr = $mysqli->query($sql);
    $filaUsr = $resultUsr->fetch_assoc();
    $nombreUsuario = $filaUsr['nombre']." ".$filaUsr['apellidop'];
    $fecha   = date('d/m/Y h:i:s a', strtotime($rowCompra['timestamp']));
    $recibo =  " <table style='width:100%'>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center'><h3>".$rowConfig['nombreComercio']."</h3></td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .=  " <table style='width:100%'>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center' colspan=2>$fecha</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center' colspan='2'><b>RECIBO DE COMPRA</b></td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>RECIBIÓ:</td>";
    $recibo .= "        <td style='text-align:right'>$nombreUsuario</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:left'>PROV.:</td>";
    $recibo .= "        <td style='text-align:right'>$nombreProveedor</td>";
    $recibo .= "    </tr>";
    $recibo .= " </table>";
    $recibo .= " <table style='width:100%'>";
    $recibo .="     <tr>";
    $recibo .="         <th style='text-align:center'>Nombre</th>";
    $recibo .="         <th style='text-align:center'>Cant</th>";
    $recibo .="         <th style='text-align:center'>PUnit</th>";
    $recibo .="         <th style='text-align:center'>$</th>";
    $recibo .="     </tr>";
    $sql = "SELECT
                productos.nombrecorto AS nombre,
                detallecompra.cantidad AS cantidad,
                detallecompra.preciolista AS precio,
                detallecompra.subTotal AS subtotal
            FROM detallecompra
            INNER JOIN productos
            ON detallecompra.producto = productos.id
            WHERE detallecompra.compra = $idCompra";
    $resultDetalle  = $mysqli->query($sql);
    $totProd = 0;
    while ($rowD    = $resultDetalle->fetch_assoc())
    {
        $nombre     = $rowD['nombre'];
        $cantidad   = $rowD['cantidad'];
        $precio     = $rowD['precio'];
        $subtotal   = $rowD['subtotal'];
        $recibo     .="     <tr>";
        $recibo     .="         <td colspan=4>$nombre</td>";
        $recibo     .="     </tr>";
        $recibo     .="     <tr>";
        $recibo     .="         <td>&nbsp;</td>";
        $recibo     .="         <td style='text-align:right'>".number_format($cantidad,3)."</td>";
        $recibo     .="         <td style='text-align:right'>".number_format($precio,2,".",",")."</td>";
        $recibo     .="         <td style='text-align:right'>".number_format($subtotal,2,".",",")."</td>";
        $recibo     .="     </tr>";
        $totProd ++;
    }
    $recibo         .="     <tr>";
    $recibo         .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo         .="     </tr>";
    $recibo         .="     <tr>";
    $recibo         .="         <td>TOT. ARTS.:</td>";
    $recibo         .="         <td>$totProd</td>";
    $recibo         .="         <td><b>TOTAL:</b></td>";
    $totalCompra = $rowCompra['monto'];
    $totalCompra_f = number_format($totalCompra, 2);
    $recibo         .="         <td style='text-align:right'><b>$$totalCompra_f</b></td>";
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
    $recibo         .= "        <td style='text-align:center'>.$rowConfig['direccionComercio2'].</td>";
    $recibo         .= "    </tr>";
    $recibo         .= " </table>";

    return $recibo;
}

?>
