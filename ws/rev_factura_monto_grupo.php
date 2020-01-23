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
        function responder($response, $mysqli)
        {
            $response['error'] = $mysqli->error;
            header('Content-Type: application/json');
            echo json_encode($response, JSON_FORCE_OBJECT);
            $mysqli->close();
            exit;
        }
        $coleccionVentas = json_decode($_POST['coleccionVentas']);
        //print_r($coleccionVentas);
        //$idVenta    = $_POST['idVenta'];
        //$monto      = $_POST['monto'];
        $response = array(
            "status"        => 1
        );
        // if (is_numeric($monto) == FALSE && $monto <= 0)
        // {
        //     $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        //     $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        //     $response['respuesta'] .='  Indica el monto del abono.';
        //     $response['respuesta'] .='</div>';
        //     $response['montoPago']  = 0;
        //     $response['status']     = 0;
        //     responder($response,$mysqli);
        // }
        $saldoAcumulado             = 0;
        $detFacturables             = 0;
        $det_noFacturables          = 0;
        $cadenaVentas               = "";
        $cont                       = 0;
        $cont_tot_facturables       = 0;
        $total_Ventas               = 0;
        $total_Pagado               = 0;
        $array_ventas_Fact          = [];
        $array_ventas_NO_Fact       = [];
        // $array_ventas_F_det         = [];
        $acumulado_Venta            = 0;
        $acumulado_no_facturable    = 0;
        $acumulado_facturable       = 0;
        $pagado                     = 0;
        foreach ($coleccionVentas as $idVenta)
        {
            // if ($acumulado_Venta    >= $monto)
            //     continue;
            $idVenta_F = $idVenta ->idVenta;
            $sql = "SELECT
                        ventas.totalventa           AS totalVenta,
                        ventas.esCredito            AS esCredito,
                        ventas.pagado               AS pagado,
                        ventas.cliente              AS idCliente,
                        clientes.rsocial            AS rSocial,
                        clientes.email              AS emailCliente,
                        (SELECT
                            IFNULL(SUM(pagosrecibidos.monto),0)
                        FROM pagosrecibidos
                        WHERE idventa = $idVenta_F) AS montoPago,
                        (SELECT COUNT(detalleventa.id)
                        FROM detalleventa
                        WHERE facturable = 1 AND facturado = 0 AND venta = $idVenta_F) AS totFacturables,
                        (SELECT COUNT(detalleventa.id)
                        FROM detalleventa
                        WHERE venta = $idVenta_F) AS detTotal
                    FROM ventas
                    INNER JOIN clientes
                    ON ventas.cliente = clientes.id
                    WHERE ventas.id = $idVenta_F LIMIT 1";
            $result_V               = $mysqli->query($sql);
            $row_V                  = $result_V->fetch_assoc();
            $idCliente              = $row_V['idCliente'];
            $nombreCliente          = $row_V['rSocial'];
            $esteSaldo              = $row_V['totalVenta'] - $row_V['montoPago'];
            $esteSaldo              = ($esteSaldo < 0) ? 0 : $esteSaldo;
            $pagado                 += $row_V['montoPago'];
            $saldoAcumulado         += $esteSaldo;
            $emailCliente           = $row_V['emailCliente'];
            if ($cont               == 0)
            {
                $idCliente_ant      = $idCliente;
                $cadenaVentas       .= $idVenta_F;
            }
            else
            {
                $cadenaVentas       .= ", ".$idVenta_F;
                if ($idCliente      != $idCliente_ant)
                {
                    $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
                    $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                    $response['respuesta'] .='  La lista de documentos seleccionados deben pertenecer al mismo cliente.';
                    $response['respuesta'] .='</div>';
                    $response['clienteDif'] = 1;
                    $response['status']     = 0;
                    responder($response,$mysqli);
                }
            }
            // Cantidad de items facturables (3)
            $sql = "SELECT
                        detalleventa.id             AS idSubVenta,
                        detalleventa.producto       AS idProducto,
                        detalleventa.cantidad       AS cantidad,
                        detalleventa.precio         AS precioU,
                        detalleventa.subTotal       AS subTotal,
                        detalleventa.facturable     AS facturable,
                        detalleventa.facturado      AS facturado,
                        detalleventa.nombrecorto    AS nombreCorto,
                        productos.claveSHCP         AS claveSAT,
                        productos.IVA               AS iva,
                        productos.IEPS              AS ieps,
                        unidadesventa.c_ClaveUnidad AS claveUnidad,
                        unidadesventa.nombre        AS nombreUnidadVenta
                    FROM detalleventa
                    INNER JOIN productos
                    ON detalleventa.producto = productos.id
                    INNER JOIN unidadesventa
                    ON productos.unidadventa = unidadesventa.id
                    WHERE detalleventa.activo = 1 AND detalleventa.venta = $idVenta_F";
            $res_det_fact       = $mysqli->query($sql);
            $arr_no_f           = [];
            $arr_f              = [];
            $contInt_No_Fact    = 0;
            $contIntFact        = 0;
            while ($row_det     = $res_det_fact->fetch_assoc())
            {
                if ($row_det['facturable'] == 0 || $row_det['facturado'] == 1)
                {
                    $arr_no_f[$contInt_No_Fact]['idVenta']          = $idVenta_F;
                    $arr_no_f[$contInt_No_Fact]['idSubVenta']       = $row_det['idSubVenta'];
                    $arr_no_f[$contInt_No_Fact]['cantidad']         = $row_det['cantidad'];
                    $arr_no_f[$contInt_No_Fact]['precioU']          = $row_det['precioU'];
                    $arr_no_f[$contInt_No_Fact]['subTotal']         = $row_det['subTotal'];
                    $arr_no_f[$contInt_No_Fact]['idProducto']       = $row_det['idProducto'];
                    $arr_no_f[$contInt_No_Fact]['nombreCorto']      = $row_det['nombreCorto'];
                    $arr_no_f[$contInt_No_Fact]['claveSAT']         = $row_det['claveSAT'];
                    $arr_no_f[$contInt_No_Fact]['claveUnidad']      = $row_det['claveUnidad'];
                    $arr_no_f[$contInt_No_Fact]['nombreUnidadVenta']= $row_det['nombreUnidadVenta'];
                    $arr_no_f[$contInt_No_Fact]['iva']              = $row_det['iva'];
                    $arr_no_f[$contInt_No_Fact]['ieps']             = $row_det['ieps'];
                    $arr_no_f[$contInt_No_Fact]['contabilizado']    = 0;
                    $arr_no_f[$contInt_No_Fact]['eliminado']        = 0;
                    $acumulado_Venta  +=  $row_det['subTotal'];
                    $acumulado_no_facturable  += $row_det['subTotal'];
                    // if ($acumulado_Venta >= $monto)
                    // {
                    //     break;
                    // }
                    $contInt_No_Fact++;
                }
                else
                {
                    $arr_f[$contIntFact]['idVenta']                 = $idVenta_F;
                    $arr_f[$contIntFact]['idSubVenta']              = $row_det['idSubVenta'];
                    $arr_f[$contIntFact]['cantidad']                = $row_det['cantidad'];
                    $arr_f[$contIntFact]['precioU']                 = $row_det['precioU'];
                    $arr_f[$contIntFact]['subTotal']                = $row_det['subTotal'];
                    $arr_f[$contIntFact]['idProducto']              = $row_det['idProducto'];
                    $arr_f[$contIntFact]['nombreCorto']             = $row_det['nombreCorto'];
                    $arr_f[$contIntFact]['claveSAT']                = $row_det['claveSAT'];
                    $arr_f[$contIntFact]['claveUnidad']             = $row_det['claveUnidad'];
                    $arr_f[$contIntFact]['nombreUnidadVenta']       = $row_det['nombreUnidadVenta'];
                    $arr_f[$contIntFact]['iva']                     = $row_det['iva'];
                    $arr_f[$contIntFact]['ieps']                    = $row_det['ieps'];
                    $arr_f[$contIntFact]['contabilizado']           = 0;
                    $arr_f[$contIntFact]['eliminado']               = 0;

                    $acumulado_Venta    +=  $row_det['subTotal'];
                    $acumulado_facturable   += $row_det['subTotal'];
                    // if ($acumulado_Venta >= $monto)
                    // {
                    //     break;
                    // }
                    $contIntFact++;
                }
            }
            $array_ventas_Fact[$cont] = $arr_f;
            $array_ventas_NO_Fact[$cont++] = $arr_no_f;
            unset($arr_f);
            unset($arr_no_f);
        }
        // if ($monto > $saldoAcumulado)
        // {
        //     $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
        //     $response['respuesta'] .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
        //     $response['respuesta'] .='  El monto del pago (<b>$'.number_format($monto,2,".",",").')</b> no puede ser mayor que el monto m&aacute;ximo.';
        //     $response['respuesta'] .='</div>';
        //     $response['saldoMayor'] = 0;
        //     $response['status']     = 0;
        //     $response['saldo']      = $saldoAcumulado;
        //     responder($response,$mysqli);
        // }
        if ($detFacturables         == 1 && $monto < $saldoAcumulado)
        {
            $response['respuesta']  = '<div class="alert alert-danger alert-dismissable">';
            $response['respuesta']  .='  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response['respuesta']  .='  No se puede procesar. El monto para liquidar esta venta debe ser de <b> $'.number_format($saldoAcumulado,2,".",",").'</b> .';
            $response['respuesta']  .='</div>';
            $response['saldoMenor'] = 0;
            $response['status']     = 0;
            $response['saldo']      = $saldoAcumulado;
            responder($response,$mysqli);
        }

?>
    <div class="row">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7">
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>No. Venta(s):</label>
                <p class="form-control-static"><?php echo $cadenaVentas; //var_dump($array_ventas_NO_Fact); ?><input type="hidden" id="hiddenCadenaVentas" value="<?php echo $cadenaVentas;?>"></input></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>Cliente:</label>
                <p class="form-control-static"><?php echo $nombreCliente; //var_dump($array_ventas_Fact);?></p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:5px;">
                <input type="checkbox" id="chkEmail_grupo" checked style="width:20px;height:20px;position:absolute;margin-top:6px"><label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enviar a:</label>
                <input id="inputEmail_grupo" type="email" autofocus class="form-control" placeholder="email@ejemplo.com" value="<?php echo $emailCliente?>" style="width:300px">
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>M&eacute;todo pago:</label>
                <p class="form-control-static">
                    <select class="form-control" id="selectMetodo_grupo" style="width:292px">
                        <option value="PUE">PUE - Pago en una sola exhibición</option>
                        <option value="PPD">PPD - Pago diferido o en parcialidades</option>
                    </select>
                </p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>Forma de pago:</label>
                <p class="form-control-static">
                    <select id="selectFormaPago-grupo" class="form-control" style="width:280px">
            <?php
                    $sql = "SELECT * FROM metodosdepago";
                    $res_m = $mysqli->query($sql);
                    while ($row_m   = $res_m->fetch_assoc())
                    {
                        $bancarizado= $row_m['bancarizado'];
                        $id_m_sat   = $row_m['c_FormaPago'];
                        $nombre_m   = $row_m['nombre'];
                        echo "<option value='$bancarizado' name='$id_m_sat'>$id_m_sat - $nombre_m</option>";
                    }
            ?>
                    </select>
                </p>
            </div>
            <div class="form-group form-inline" style="margin-bottom:0px;margin-top:-5px">
                <label>Uso CFDI:</label>
                <p class="form-control-static">
                    <select class="form-control" id="selectUso" style="max-width:319px">
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
            <iframe id="ifram_monto_grupo" style="width:100%;height:100px;border-style:none;"></iframe>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive" style="max-height:60rem">
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
    ////////////////////////////////CORREGIR DUPLICIDAD EN PRODUCTOS ANTES DE FACTURAR ///////////////////////////////////////////
            for ($x=0; $x < sizeof($array_ventas_NO_Fact) ; $x++)
            {
                for ($y=0; $y < sizeof($array_ventas_NO_Fact[$x]) ; $y++)
                {
                    $array_ventas_NO_Fact[$x][$y]['contabilizado'] = 1;
                    $idProducto_ = $array_ventas_NO_Fact[$x][$y]['idProducto'];
                    for ($a=0; $a < sizeof($array_ventas_NO_Fact) ; $a++)
                    {
                        for ($b=0; $b < sizeof($array_ventas_NO_Fact[$a]) ; $b++)
                        {
                            if ($array_ventas_NO_Fact[$a][$b]['contabilizado'] == 0)
                            {
                                if ($idProducto_    == $array_ventas_NO_Fact[$a][$b]['idProducto'])
                                {
                                    $esteSubTot_    = $array_ventas_NO_Fact[$a][$b]['subTotal'] + $array_ventas_NO_Fact[$x][$y]['subTotal'];
                                    $esteCant_      = $array_ventas_NO_Fact[$a][$b]['cantidad'] + $array_ventas_NO_Fact[$x][$y]['cantidad'];
                                    $array_ventas_NO_Fact[$x][$y]['subTotal'] = $esteSubTot_;
                                    $array_ventas_NO_Fact[$x][$y]['cantidad'] = $esteCant_;
                                    $array_ventas_NO_Fact[$x][$y]['precioU'] = $esteSubTot_ / $esteCant_;
                                    $array_ventas_NO_Fact[$a][$b]['contabilizado'] = 1;
                                    $array_ventas_NO_Fact[$a][$b]['eliminado'] = 1;
                                }
                            }
                        }
                    }
                }
            }
            $totalRows_fact = 0;
            for ($x=0; $x < sizeof($array_ventas_Fact) ; $x++)
            {
                for ($y=0; $y < sizeof($array_ventas_Fact[$x]) ; $y++)
                {
                    $array_ventas_Fact[$x][$y]['contabilizado'] = 1;
                    $idProducto_ = $array_ventas_Fact[$x][$y]['idProducto'];
                    for ($a=0; $a < sizeof($array_ventas_Fact) ; $a++)
                    {
                        for ($b=0; $b < sizeof($array_ventas_Fact[$a]) ; $b++)
                        {
                            if ($array_ventas_Fact[$a][$b]['contabilizado'] == 0)
                            {
                                if ($idProducto_    == $array_ventas_Fact[$a][$b]['idProducto'])
                                {
                                    $esteSubTot_    = $array_ventas_Fact[$a][$b]['subTotal'] + $array_ventas_Fact[$x][$y]['subTotal'];
                                    $esteCant_      = $array_ventas_Fact[$a][$b]['cantidad'] + $array_ventas_Fact[$x][$y]['cantidad'];
                                    $array_ventas_Fact[$x][$y]['subTotal'] = $esteSubTot_;
                                    $array_ventas_Fact[$x][$y]['cantidad'] = $esteCant_;
                                    $array_ventas_Fact[$x][$y]['precioU'] = $esteSubTot_ / $esteCant_;
                                    $array_ventas_Fact[$a][$b]['contabilizado'] = 1;
                                    $array_ventas_Fact[$a][$b]['eliminado'] = 1;
                                }
                            }
                        }
                    }
                }
            }
            ////////////////////////////////////////// CONTAR ROWS A FACTURAR PARA DIVIDIR EL SALDO NO FACTURABLE ////////////////////////////////////////////////////
            $cant_tot_rows_facturar = 0;
            $nuevo_sub_tot = 0;
            for ($x=0; $x < sizeof($array_ventas_Fact) ; $x++)
            {
                for ($y=0; $y < sizeof($array_ventas_Fact[$x]) ; $y++)
                {
                    $eliminado          = $array_ventas_Fact[$x][$y]['eliminado'];
                    if ($eliminado      == 1)
                        continue;
                    $sTot_ = $array_ventas_Fact[$x][$y]['subTotal'];
                    $cant_ = $array_ventas_Fact[$x][$y]['cantidad'];
                    //$pUnit_= $array_ventas_Fact[$x][$y]['precioU'];
                    $porcentaje = $sTot_ * 100 / $acumulado_facturable;

                    $montoIndividual_sumar_X_item = $porcentaje * $acumulado_no_facturable / 100;
                    $nuevo_sub_tot += $array_ventas_Fact[$x][$y]['subTotal'] = $sTot_ + $montoIndividual_sumar_X_item;
                    $array_ventas_Fact[$x][$y]['precioU'] = $array_ventas_Fact[$x][$y]['subTotal'] / $cant_;
                    $cant_tot_rows_facturar++;
                }
            }
            //$montoIndividual_sumar_X_item = $acumulado_no_facturable / $cant_tot_rows_facturar;
            ///////////////////////////RESTAR PAGOS QUE YA SE HAN EFECTUADO (DIVIDIR ENTRE EL NO. DE ITEMS FACTURABLES) //////////////////////////////////////////////
            //$montoIndividual_sumar_X_item = $pagado / $cant_tot_rows_facturar;
            for ($x=0; $x < sizeof($array_ventas_Fact); $x++)
            {
                for ($y=0; $y < sizeof($array_ventas_Fact[$x]) ; $y++)
                {
                    $eliminado          = $array_ventas_Fact[$x][$y]['eliminado'];
                    if ($eliminado      == 1)
                        continue;
                    $sTot_ = $array_ventas_Fact[$x][$y]['subTotal'];
                    $cant_ = $array_ventas_Fact[$x][$y]['cantidad'];
                    $porcentaje = $sTot_ * 100 / $nuevo_sub_tot;

                    $montoIndividual_sumar_X_item = $porcentaje * $pagado / 100;
                    //echo 'montoInd: '.$montoIndividual_sumar_X_item.'porcentaje: '.$porcentaje;
                    $array_ventas_Fact[$x][$y]['subTotal'] = $sTot_ - $montoIndividual_sumar_X_item;
                    $array_ventas_Fact[$x][$y]['precioU'] = $array_ventas_Fact[$x][$y]['subTotal'] / $cant_;
                }
            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // echo "Facturables:</br>";var_dump($array_ventas_Fact);
            // echo "No Facturables:</br>";var_dump($array_ventas_NO_Fact);
            $sumatoriaIVA           = 0;
            $sumatoriaSinIva        = 0;
            $sumatoriaIEPS          = 0;
            $sumatoriaSinIeps       = 0;
            $sumatoriaSinImpuestos  = 0;
            $saldo                  = $total_Ventas - $total_Pagado;
            for ($x=0; $x < sizeof($array_ventas_NO_Fact) ; $x++)
            {
                for ($y=0; $y < sizeof($array_ventas_NO_Fact[$x]) ; $y++)
                {
                    $eliminado          = $array_ventas_NO_Fact[$x][$y]['eliminado'];
                    if ($eliminado == 1)
                        continue;
                    $idVenta            = $array_ventas_NO_Fact[$x][$y]['idVenta'];
                    $idSubVenta         = $array_ventas_NO_Fact[$x][$y]['idSubVenta'];
                    $esteCantidad       = $array_ventas_NO_Fact[$x][$y]['cantidad'];
                    $estePrecioU        = $array_ventas_NO_Fact[$x][$y]['precioU'];
                    $esteSubTotal       = $array_ventas_NO_Fact[$x][$y]['subTotal'];
                    $idProducto         = $array_ventas_NO_Fact[$x][$y]['idProducto'];
                    $nombreCorto        = $array_ventas_NO_Fact[$x][$y]['nombreCorto'];
                    $claveSAT           = $array_ventas_NO_Fact[$x][$y]['claveSAT'];
                    $claveUnidad        = $array_ventas_NO_Fact[$x][$y]['claveUnidad'];
                    $nombreUnidadVenta  = $array_ventas_NO_Fact[$x][$y]['nombreUnidadVenta'];
                    $iva                = $array_ventas_NO_Fact[$x][$y]['iva'];
                    $ieps               = $array_ventas_NO_Fact[$x][$y]['ieps'];

                        ?>
                            <tr class="trRow inactive" name="<?php echo $idProducto;?>" idSubVenta="<?php echo $idSubVenta?>" idVenta="<?php echo $idVenta;?>">
                                <td><?php echo $nombreCorto;?></td>
                                <td class="text-center">
                                    <input name="<?php echo $idProducto;?>" disabled class="inputSAT inputGrilla" size="10" value="<?php echo $claveSAT;?>" style="border-style:none;background-color:transparent;text-align:right">
                                </td>
                                <td><?php echo $claveUnidad."-".$nombreUnidadVenta;?></td>
                                <td class="text-right">
                                    <input type="number" class="inputIva inputGrilla" disabled min="0" value="<?php echo ($iva != 0) ? number_format($iva,2,".",",") : "0";?>" style="border-style:none;background-color:transparent;text-align:right;width:55px"></td>
                        <?php


                        ?>
                                <td class="text-right">
                                    <input type="number" class="inputIeps inputGrilla" disabled min="0" value="<?php echo ($ieps != 0) ? number_format($ieps,2,".",",") : "0";?>" style="border-style:none;background-color:transparent;text-align:right;width:55px">
                                </td>
                                <td class="text-right"><span class="spanCantidad"><?php echo number_format($esteCantidad,3,".","");?></span></td>
                                <td class="text-right"><span class="spanPrecioU"><?php echo number_format($estePrecioU,3,".","");?></span></td>
                                <td class="text-right"><span class="spanSubTotal"><?php echo number_format($esteSubTotal,3,".","")?></span>
                                </td>
                            </tr>
                        <?php
                }
            }

            for ($x=0; $x < sizeof($array_ventas_Fact) ; $x++)
            {
                for ($y=0; $y < sizeof($array_ventas_Fact[$x]) ; $y++)
                {
            // $arr_no_f[$contInt_No_Fact]['idVenta']          = $idVenta_F;
            // $arr_no_f[$contInt_No_Fact]['idSubVenta']       = $row_det['idSubVenta'];
            // $arr_no_f[$contInt_No_Fact]['cantidad']         = $row_det['cantidad'];
            // $arr_no_f[$contInt_No_Fact]['precioU']          = $row_det['precioU'];
            // $arr_no_f[$contInt_No_Fact]['subTotal']         = $row_det['subTotal'];
            // $arr_no_f[$contInt_No_Fact]['idProducto']       = $row_det['idProducto'];
            // $arr_no_f[$contInt_No_Fact]['nombreCorto']      = $row_det['nombreCorto'];
            // $arr_no_f[$contInt_No_Fact]['claveSAT']         = $row_det['claveSAT'];
            // $arr_no_f[$contInt_No_Fact]['claveUnidad']      = $row_det['claveUnidad'];
            // $arr_no_f[$contInt_No_Fact]['nombreUnidadVenta']= $row_det['nombreUnidadVenta'];
            // $arr_no_f[$contInt_No_Fact]['iva']              = $row_det['iva'];
            // $arr_no_f[$contInt_No_Fact]['ieps']             = $row_det['ieps'];
                    $eliminado          = $array_ventas_Fact[$x][$y]['eliminado'];
                    if ($eliminado == 1)
                        continue;
                    $idVenta            = $array_ventas_Fact[$x][$y]['idVenta'];
                    $idSubVenta         = $array_ventas_Fact[$x][$y]['idSubVenta'];
                    $esteCantidad       = $array_ventas_Fact[$x][$y]['cantidad'];
                    $esteSubTotal       = $array_ventas_Fact[$x][$y]['subTotal'];
                    $estePrecioU        = $array_ventas_Fact[$x][$y]['precioU'];
                    $idProducto         = $array_ventas_Fact[$x][$y]['idProducto'];
                    $nombreCorto        = $array_ventas_Fact[$x][$y]['nombreCorto'];
                    $claveSAT           = $array_ventas_Fact[$x][$y]['claveSAT'];
                    $claveUnidad        = $array_ventas_Fact[$x][$y]['claveUnidad'];
                    $nombreUnidadVenta  = $array_ventas_Fact[$x][$y]['nombreUnidadVenta'];
                    $iva                = $array_ventas_Fact[$x][$y]['iva'];
                    $ieps               = $array_ventas_Fact[$x][$y]['ieps'];
                            ?>
                                <tr class="trRowFactura" name="<?php echo $idProducto;?>" idSubVenta="<?php echo $idSubVenta?>" idVenta="<?php echo $idVenta;?>">
                                    <td><?php echo $nombreCorto;?></td>
                                    <td class="text-center">
                                        <input name="<?php echo $idProducto;?>" class="inputSAT inputGrilla" size="10" value="<?php echo $claveSAT;?>" style="border-style:none;background-color:transparent;text-align:right">
                                    </td>
                                    <td><?php echo $claveUnidad."-".$nombreUnidadVenta;?></td>
                                    <td class="text-right">
                                        <input type="number" class="inputIva inputGrilla" min="0" value="<?php echo ($iva != 0) ? number_format($iva,2,".",",") : "0";?>" style="border-style:none;background-color:transparent;text-align:right;width:55px"></td>
                            <?php


                            ?>
                                    <td class="text-right">
                                        <input type="number" class="inputIeps inputGrilla" min="0" value="<?php echo ($ieps != 0) ? number_format($ieps,2,".",",") : "0";?>" style="border-style:none;background-color:transparent;text-align:right;width:55px">
                                    </td>
                                    <td class="text-right"><span class="spanCantidad"><?php echo number_format($esteCantidad,3,".","");?></span></td>
                                    <td class="text-right"><span class="spanPrecioU"><?php echo number_format($estePrecioU,3,".","");?></span></td>
                                    <td class="text-right"><span class="spanSubTotal"><?php echo number_format($esteSubTotal,3,".","")?></span>
                                    </td>
                                </tr>

                    <?php
                    if ($iva > 0 && $ieps == 0)
                    {
                        $factorIva          = $iva / 100;
                        $factorIva++;
                        $rowSinIva          = $esteSubTotal / $factorIva;
                        $rowIVA             = $esteSubTotal - $rowSinIva;
                        $sumatoriaIVA       += $rowIVA;
                        $sumatoriaSinIva    += $rowSinIva;
                    }elseif ($iva == 0 && $ieps > 0)
                    {
                        $factorIeps         = $ieps / 100;
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
                    //$cont_rows_facturables++;
                }

            }

?>
                        <tr class="trRowFacturaSubt">
                            <td colspan="6" rowspan="4" class="">
                                <h2 style="" id="tdMsgInd_g"></h2>
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
                            <td class="text-right"><label>$<span id="spanTotal"><?php echo number_format($sumatoriaSinIva + $sumatoriaSinIeps + $sumatoriaSinImpuestos + $sumatoriaIVA + $sumatoriaIEPS,2); ?></span></label>
                                <?php
                                    for ($x=0; $x < sizeof($array_ventas_Fact) ; $x++)
                                    {
                                        for ($y=0; $y < sizeof($array_ventas_Fact[$x]) ; $y++)
                                        {
                                            $idSV_         = $array_ventas_Fact[$x][$y]['idSubVenta'];
                                            echo "<input type='hidden' class='subVentaHidden' value='$idSV_'>";
                                        }
                                    }

                                 ?>
                                <!--
                                <br>cont:<?php echo $cont;?>
                                <br>monto:<?php echo $monto;?>
                                <br>esteSubTotal:<?php echo $esteSubTotal;?>
                                <br>totalRows:<?php echo $totalRows;?>
                                <br>total_Ventas:<?php echo $total_Ventas;?>
                                <br>det_noFacturables:<?php echo $det_noFacturables;?>-->

                            </td>

                            <input type="hidden" id="hiddenIdCliente" value="<?php echo $idCliente;?>">

                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
<?php
    }
?>
