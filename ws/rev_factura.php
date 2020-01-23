<?php
    /*error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL);*/
    date_default_timezone_set('America/Mexico_City');
    $ipserver = $_SERVER['SERVER_ADDR'];
    require_once ('../conecta/bd.php');
    require_once ("../conecta/sesion.class.php");
    $sesion = new sesion();
    require_once ("../conecta/cerrarOtrasSesiones.php");
    require_once ("../conecta/usuarioLogeado.php");
    if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
    {
        header("Location: /pventa_std/pages/salir.php");
    }
    else
    {
        $idVenta = $_POST['idVenta'];
        $sql = "SELECT
                    ventas.id           AS idVenta,
                    ventas.totalventa   AS totalVenta,
                    clientes.rsocial    AS nombreCliente,
                    clientes.email      AS emailCliente,
                    (SELECT
                        IFNULL(SUM(pagosrecibidos.monto),0)
                    FROM pagosrecibidos
                    WHERE pagosrecibidos.idventa = $idVenta)
                                        AS totalAbonado
                FROM ventas
                INNER JOIN clientes
                ON ventas.cliente = clientes.id
                WHERE ventas.id = $idVenta LIMIT 1";
        $result = $mysqli->query($sql);
        $rowVenta = $result->fetch_assoc();
        $sql = "SELECT
                    detalleventa.producto       AS idProducto,
                    detalleventa.cantidad       AS cantidad,
                    detalleventa.precio         AS precioU,
                    detalleventa.subTotal       AS subTotal,
                    detalleventa.facturable     AS facturable,
                    detalleventa.facturado      AS facturado,
                    detalleventa.nombrecorto    AS nombreProducto,
                    productos.nombrelargo       AS nombreLargo,
                    productos.unidadventa       AS idUnidadVenta,
                    productos.IVA               AS IVA,
                    productos.IEPS              AS IEPS,
                    productos.claveSHCP         AS claveSHCP,
                    (SELECT
                        unidadesventa.nombre
                    FROM unidadesventa
                    WHERE unidadesventa.id  = idUnidadVenta
                    LIMIT 1)                    AS nombreUnidadVenta,
                    (SELECT
                        unidadesventa.c_ClaveUnidad
                    FROM unidadesventa
                    WHERE unidadesventa.id  = idUnidadVenta
                    LIMIT 1)                AS c_ClaveUnidad
                FROM detalleventa
                INNER JOIN productos
                ON detalleventa.producto    = productos.id
                WHERE detalleventa.venta    = $idVenta
                ORDER BY facturable ASC, facturado DESC";
        $resultadoDet = $mysqli->query($sql);
?>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>No. Venta:</label>
                <p class="form-control-static"><?php echo $idVenta; ?></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>Cliente:</label>
                <p class="form-control-static"><?php echo $rowVenta['nombreCliente']; ?></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:5px;">
                <input type="checkbox" id="chkEmail" checked style="width:20px;height:20px;position:absolute;margin-top:6px"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enviar a:</label>
                <input id="inputEmail" type="email" autofocus class="form-control" placeholder="email@ejemplo.com" value="<?php echo $rowVenta['emailCliente']; ?>" style="width:300px">
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>M&eacute;todo pago:</label>
                <p class="form-control-static">
                    <select class="form-control" id="selectMetodo" style="max-width:294px">
                        <option value="PUE">PUE - Pago en una sola exhibición</option>
                        <option value="PPD">PPD - Pago diferido o en parcialidades</option>
                    </select>
                </p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>Uso CFDI:</label>
                <p class="form-control-static">
                    <select class="form-control" id="selectUso" style="max-width:325px">
<?php
        $sqlUsoCFDI = "SELECT * FROM usoCFDI ORDER BY id ASC";
        $resUsoCFDI = $mysqli->query($sqlUsoCFDI);
        while ($rowUsoCFDI = $resUsoCFDI->fetch_assoc())
        {
?>
                        <option value="<?php echo $rowUsoCFDI['c_UsoCFDI'];?>"><?php echo $rowUsoCFDI['c_UsoCFDI'].' - '.$rowUsoCFDI['Descripcion'];?></option>
<?php
        }
 ?>
                    </select>
                </p>
            </div>
        </div>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5" style="margin-left:0px;padding-left:0px;">
            <iframe id="ifram" style="width:100%;height:100px;border-style:none;"></iframe>
        </div>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-condensed table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>claveSAT</th>
                            <th>Unidad</th>
                            <th>IVA %</th>
                            <th>IEPS %</th>
                            <th>Cant</th>
                            <th>PrecioU</th>
                            <th>Importe</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php
            $totalRows              = $resultadoDet->num_rows;
            $cont                   = 0;
            $sumatoriaIVA           = 0;
            $sumatoriaSinIva        = 0;
            $sumatoriaIEPS          = 0;
            $sumatoriaSinIeps       = 0;
            $sumatoriaSinImpuestos  = 0;
            $totalSubTotal_fact     = 0;
            $saldoMaxFacturar       = $rowVenta['totalVenta'] - $rowVenta['totalAbonado'];
            while ($rowDet          = $resultadoDet->fetch_assoc())
            {
                $cont++;
                $classTr = ($rowDet['facturable'] == 0 || $rowDet['facturado'] == 1) ? "trRow inactive" : "trRowFactura";
                $disabled = ($rowDet['facturable'] == 0 || $rowDet['facturado'] == 1) ? "disabled" : "";

                if ($cont == $totalRows)
                {
                    $esteSubTotal = $saldoMaxFacturar - $totalSubTotal_fact;
                    $estePrecioU = $esteSubTotal / $rowDet['cantidad'];
                }
                else
                {
                    $estePrecioU = $rowDet['precioU'];
                    $esteSubTotal = $rowDet['subTotal'];
                }
                if ($rowDet['facturable'] == 1)
                {
                    $totalSubTotal_fact += $esteSubTotal;
                }
    ?>

                        <tr class="<?php echo $classTr;?>" name="<?php echo $rowDet['idProducto'];?>">
                            <td><?php echo (strlen($rowDet['nombreProducto'])>1 ? $rowDet['nombreProducto'] : $rowDet['nombreLargo']);?></td>
                            <td class="text-center">
                                <input name="<?php echo $rowDet['idProducto'];?>" <?php echo $disabled;?> class="inputSAT inputGrilla" size="10" value="<?php echo $rowDet['claveSHCP'];?>" style="border-style:none;background-color:transparent;text-align:right">
                            </td>
                            <td><?php echo $rowDet['c_ClaveUnidad']."-".$rowDet['nombreUnidadVenta'];?></td>
                            <td class="text-right">
                                <input type="number" <?php echo $disabled;?> class="inputIva inputGrilla" min="0" value="<?php echo ($rowDet['IVA'] != 0) ? number_format($rowDet['IVA'],2,".",",") : "0";?>" style="border-style:none;background-color:transparent;text-align:right;width:55px"></td>
    <?php
                if ($rowDet['facturable'])
                {
                    if ($rowDet["IVA"] > 0 && $rowDet['IEPS'] == 0)
                    {
                        $factorIva          = $rowDet['IVA'] / 100;
                        $factorIva++;
                        $rowSinIva          = $esteSubTotal / $factorIva;
                        $rowIVA             = $esteSubTotal - $rowSinIva;
                        $sumatoriaIVA       += $rowIVA;
                        $sumatoriaSinIva    += $rowSinIva;
                    }elseif ($rowDet["IVA"] == 0 && $rowDet['IEPS'] > 0)
                    {
                        $factorIeps         = $rowDet['IEPS'] / 100;
                        $factorIeps++;
                        $rowSinIeps         = $esteSubTotal / $factorIeps;
                        $rowIEPS            = $esteSubTotal - $rowSinIeps;
                        $sumatoriaIEPS      += $rowIEPS;
                        $sumatoriaSinIeps   += $rowSinIeps;
                    }
                    else
                    {
                        $rowSinImpuestos    = $esteSubTotal;
                        $sumatoriaSinImpuestos += $rowSinImpuestos;
                    }
                }
    ?>
                            <td class="text-right">
                                <input type="number" <?php echo $disabled;?> class="inputIeps inputGrilla" min="0" value="<?php echo ($rowDet['IEPS'] != 0) ? number_format($rowDet['IEPS'],2,".",",") : "0";?>" style="border-style:none;background-color:transparent;text-align:right;width:55px">
                            </td>
                            <td class="text-right"><span class="spanCantidad"><?php echo number_format($rowDet['cantidad'],3,".","");?></span></td>
                            <td class="text-right"><span class="spanPrecioU"><?php echo number_format($estePrecioU,2,".","");?></span></td>
                            <td class="text-right"><span class="spanSubTotal"><?php echo number_format($esteSubTotal,2,".","");?></span></td>
                        </tr>
    <?php
            }
     ?>
                        <tr class="trRowFacturaSubt">
                            <td colspan="6" rowspan="4" class="">
                                <h2 style="" id="tdMsg"></h2>
                            </td>
                            <td class="text-right">SubTotal:</td>
                            <td class="text-right">$<span id="spanSumatoria"><?php echo number_format($sumatoriaSinIva + $sumatoriaSinIeps + $sumatoriaSinImpuestos,2); ?></span></td>
                        </tr>

                        <tr class="trRowFacturaSubt">
                            <td class="text-right">IVA (16%):</td>
                            <td class="text-right">$<span id="spanIva"><?php echo number_format($sumatoriaIVA,2); ?></span></td>
                        </tr>
                        <tr class="trRowFacturaSubt">
                            <td class="text-right">IEPS (08%):</td>
                            <td class="text-right">$<span id="spanIeps"><?php echo number_format($sumatoriaIEPS,2); ?></span></td>
                        </tr>
                        <tr class="trRowFacturaSubt">
                            <td class="text-right"><label>Total:</label></td>
                            <td class="text-right"><label>$<span id="spanTotal"><?php echo number_format($saldoMaxFacturar,2); ?></span></label></td>
                            <input type="hidden" id="hiddenIdVenta" value="<?php echo $idVenta;?>">
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
<?php
    }
?>
