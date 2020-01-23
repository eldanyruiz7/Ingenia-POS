<?php
function genReciboVenta($idVenta, $mysqli)
{
    $sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig   = $mysqli->query($sql);
    $rowConfig      = $resultConfig->fetch_assoc();
    $existeNota     = 0;
    $sql            = "SELECT * FROM notacredito WHERE venta = $idVenta LIMIT 1";
    $resultNota     = $mysqli->query($sql);
    if($resultNota->num_rows > 0)
    {
        $existeNota = 1;
        $rowNota    = $resultNota->fetch_assoc();
        $idNota     = $rowNota['id'];
        $credito    = $rowNota['credito'];
        $nTotal     = $rowNota['nuevototalventa'];
        $tipoNota   = $rowNota['tipo']; // 2 = regresar dinero en efectivo
        $obsNota    = $rowNota['observaciones'];
    }
    $sql            = "SELECT * FROM ventas WHERE id = $idVenta LIMIT 1";
    $result         = $mysqli->query($sql);
    $rowVenta       = $result->fetch_assoc();
    $idUsuario      = $rowVenta['usuario'];
    $idCliente      = $rowVenta['cliente'];
    $sql            = "SELECT nombre FROM usuarios WHERE id = $idUsuario LIMIT 1";
    $resultUsr      = $mysqli->query($sql);
    $rowUsr         = $resultUsr->fetch_assoc();
    $nombreUsuario  = $rowUsr['nombre'];
    $sql            = "SELECT rsocial FROM clientes WHERE id = $idCliente LIMIT 1";
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
    $recibo .= "        <td style='text-align:center' colspan=4>Cliente: $nombreCompletoCliente</td>";
    $recibo .= "    </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td style='text-align:center' colspan=4>Le atendió: $nombreUsuario</td>";
    $recibo .= "    </tr>";
    $recibo .="     <tr>";
    $recibo .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo .="     </tr>";
    $recibo .= "    <tr>";
    $recibo .= "        <td colspan=4 style='text-align:center'><b>TICKET DE VENTA</b></td>";
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
                productos.nombrecorto AS nombre,
                detalleventa.nombrecorto AS nombre_det,
                detalleventa.cantidad AS cantidad,
                detalleventa.precio AS precio,
                detalleventa.subtotal AS subtotal,
                detalleventa.id AS idSubVenta
            FROM detalleventa
            INNER JOIN productos
            ON detalleventa.producto = productos.id
            WHERE detalleventa.venta = $idVenta AND detalleventa.activo = 1";
    $resultDetalle  = $mysqli->query($sql);
    $totProd = 0;
    while ($rowD    = $resultDetalle->fetch_assoc())
    {
        $nombre     = (strlen($rowD['nombre_det']) <= 1) ? $rowD['nombre'] : $rowD['nombre_det'] ;
        $cantidad   = $rowD['cantidad'];
        $precio     = $rowD['precio'];
        $subtotal   = $rowD['subtotal'];
        $recibo .="     <tr style='width:100px'>";
        $recibo .="         <td colspan=4>$nombre</td>";
        $recibo .="     </tr>";
        $recibo .="     <tr style='width:100px'>";
        $recibo .="         <td>&nbsp;</td>";
        $recibo .="         <td style='text-align:right'>".number_format($cantidad,3)."</td>";
        $recibo .="         <td style='text-align:right'>".number_format($precio,2,".",",")."</td>";
        $recibo .="         <td style='text-align:right'>".number_format($subtotal,2,".",",")."</td>";
        $recibo .="     </tr>";
        $totProd ++;
        if($existeNota == 1 && $tipoNota == 2) // $tipoNota = 2 ** Regresar efectivo
        {
            $idSubVenta = $rowD['idSubVenta'];
            $sql = "SELECT cantidad, subTotal FROM notacreditocambio WHERE idsubventa = $idSubVenta LIMIT 1";
            $resultDetalleNota = $mysqli->query($sql);
            if($resultDetalleNota->num_rows > 0)
            {
                $rowDetalleNota = $resultDetalleNota->fetch_assoc();
                $c_n = $rowDetalleNota['cantidad'];
                $s_n = $rowDetalleNota['subTotal'];
                $recibo .="     <tr style='width:100px'>";
                $recibo .="         <td>&nbsp;</td>";
                $recibo .="         <td style='text-align:right'> <b>- ".number_format($c_n,3)."</b></td>";
                $recibo .="         <td style='text-align:right'></td>";
                $recibo .="         <td style='text-align:right'> <b>- ".number_format($s_n,2,".",",")."</b></td>";
                $recibo .="     </tr>";
            }

        }
    }
    $recibo         .="     <tr>";
    $recibo         .="         <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
    $recibo         .="     </tr>";
    if($existeNota == 0 || $tipoNota == 1) // $tipoNota = 1 ** Cambio por articulo igual
    {
        $recibo         .="     <tr>";
        $recibo         .="         <td>Arts.:</td>";
        $recibo         .="         <td>$totProd</td>";
        $recibo         .="         <td><b>TOTAL:</b></td>";
        $totalVenta = $rowVenta['totalventa'];
        $totalVenta_f = number_format($totalVenta, 2);
        $recibo         .="         <td style='text-align:right'><b>$$totalVenta_f</b></td>";
        $recibo         .="     </tr>";
    }
    elseif($existeNota == 1 && $tipoNota == 2) // $tipoNota = 2 ** Regresar efectivo
    {
        $recibo         .="     <tr>";
        $recibo         .="         <td></td>";
        $recibo         .="         <td></td>";
        $recibo         .="         <td>SUMA:</td>";
        $totalVenta = $rowVenta['totalventa'];
        $totalVenta_f = number_format($totalVenta, 2);
        $recibo         .="         <td style='text-align:right'>$$totalVenta_f</td>";
        $recibo         .="     </tr>";
        $recibo         .="     <tr>";
        $recibo         .="         <td></td>";
        $recibo         .="         <td colspan = 2><b>DEVOLUCION:</b></td>";
        $credito = number_format($credito, 2);
        $recibo         .="         <td style='text-align:right'><b>- $$credito</b></td>";
        $recibo         .="     </tr>";
        $recibo         .="     <tr>";
        $recibo         .="         <td></td>";
        $recibo         .="         <td></td>";
        $recibo         .="         <td>TOTAL:</td>";
        //$totalVenta = $rowVenta['totalventa'];
        $nTotal = number_format($nTotal, 2);
        $recibo         .="         <td style='text-align:right'>$$nTotal</td>";
        $recibo         .="     </tr>";
        $recibo         .= "</table>";
    }
    if ($existeNota == 1)
    {
        $sql = "SELECT * FROM tiponotacredito WHERE id = $tipoNota LIMIT 1";
        $resultNombreNota = $mysqli->query($sql);
        $rowNombreNota = $resultNombreNota->fetch_assoc();
        $motivoNota = $rowNombreNota['nombre'];
        $recibo         .= "<table style='width:100%'>";
        $recibo         .= "    <tr>";
        $recibo         .= "        <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
        $recibo         .= "    </tr>";
        $recibo         .= "    <tr>";
        $recibo         .= "        <td style='text-align:center' colspan=4><b>$motivoNota</b></td>";
        $recibo         .= "    </tr>";
        $recibo         .= "</table>";
        if($tipoNota == 1) // $tipoNota = 1 ** Cambio por otro artículo igual
        {
            $recibo     .= "<table style='width:100%'>";
            $recibo     .= "    <tr>";
            $recibo     .= "        <td style='text-align:center' colspan=4>----------------------------------------------------</td>";
            $recibo     .= "    </tr>";
            $recibo     .= "    <tr>";
            $recibo     .= "        <td style='text-align:center' colspan=4>Recibí cambio por los siguientes artículos:</td>";
            $recibo     .= "    </tr>";
            $recibo     .="     <tr>";
            $recibo     .="         <td style='text-align:center'><b>NOMBRE</b></td>";
            $recibo     .="         <td style='text-align:center'><b>CANT</b></td>";
            $recibo     .="         <td style='text-align:center'><b>PUNIT</b></td>";
            $recibo     .="         <td style='text-align:center'><b>$</b></td>";
            $recibo     .="     </tr>";
            $sql = "SELECT
                        productos.nombrecorto       AS nombre,
                        notacreditocambio.cantidad  AS cantidad,
                        notacreditocambio.precio    AS precio,
                        notacreditocambio.subtotal  AS subtotal
                    FROM notacreditocambio
                    INNER JOIN productos
                    ON notacreditocambio.idproducto = productos.id
                    WHERE notacreditocambio.idnotacredito = $idNota";
            $resultDetalleCredito = $mysqli->query($sql);
            while ($rowC    = $resultDetalleCredito->fetch_assoc())
            {
                $nombre     = $rowC['nombre'];
                $cantidad   = $rowC['cantidad'];
                $precio     = $rowC['precio'];
                $subtotal   = $rowC['subtotal'];
                $recibo .="     <tr>";
                $recibo .="         <td colspan=4><b>$nombre</b></td>";
                $recibo .="     </tr>";
                $recibo .="     <tr>";
                $recibo .="         <td>&nbsp;</td>";
                $recibo .="         <td style='text-align:right'><b>".number_format($cantidad,3)."</b></td>";
                $recibo .="         <td style='text-align:right'><b>".number_format($precio,2,".",",")."</b></td>";
                $recibo .="         <td style='text-align:right'><b>".number_format($subtotal,2,".",",")."</b></td>";
                $recibo .="     </tr>";
                //$recibo .= "</table>";
                $totProd ++;
            }
            $recibo     .= "</table>";
        }
        if($tipoNota == 2)
        {
            $recibo     .= "<table style='width:100%'>";
            $recibo     .= "    <tr>";
            $recibo     .= "        <td style='text-align:center' colspan=4>Recibí la cantidad de: <b>$$credito</b></td>";
            $recibo     .= "    </tr>";
            $recibo     .= "</table>";
        }
        $recibo         .= "<table style='width:100%'>";
        $recibo         .= "    </tr>";
        $recibo         .= "    <tr>";
        $recibo         .= "        <td style='text-align:center' colspan=4></br></br></br></br></br></td>";
        $recibo         .= "    </tr>";
        $recibo         .= "    <tr>";
        $recibo         .= "        <td style='text-align:center'>_______________________________</td>";
        $recibo         .= "    </tr>";
        $recibo         .= "    <tr>";
        $recibo         .= "        <td style='text-align:center' colspan=4>Firma cliente: $nombreCompletoCliente</td>";
        $recibo         .= "    </tr>";
        $recibo         .= "</table>";
    }
    else
    {
        //$recibo         .= "</table>";
    }
    $recibo         .= "<table style='width:100%'>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>----------------------------------------------------</td>";
    $recibo         .= "    </tr>";
    if ($existeNota == 1 && isset($obsNota) && strlen($obsNota) > 0)
    {
        $recibo     .= "    <tr>";
        $recibo     .= "        <td style='text-align:center'><h6 style='margin:2px'>*Obs: $obsNota</h6></td>";
        $recibo     .= "    </tr>";
    }
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
    // $recibo         .= "    <tr>";
    // $recibo         .= "        <td style='text-align:center'>Devoluciones y aclaraciones </br>con este ticket de venta</td>";
    // $recibo         .= "    </tr>";
    $recibo         .= "    <tr>";
    $recibo         .= "        <td style='text-align:center'>".$rowConfig['fraseSaludo']."</td>";
    $recibo         .= "    </tr>";
    $recibo         .= " </table>";
    return $recibo;
}

?>
