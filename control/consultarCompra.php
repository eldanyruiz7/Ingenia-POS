<?php
    // error_reporting(E_ALL);
    // ini_set('display_errors', '1');
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
            //$response['respuesta'].=$mysqli->error;
            //print_r($response);
            header('Content-Type: application/json');
            echo json_encode($response, JSON_FORCE_OBJECT);
            $mysqli->close();
            exit;
        }
        $response = array(
            "status"        =>1
        );
        $id = $_POST['id'];
        $sql = "SELECT id FROM compras WHERE id = $id LIMIT 1";
        $resultExisteCompra = $mysqli->query($sql);
        if($resultExisteCompra->num_rows == 0)
        {
            $response["status"] = 0;
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se encontr&oacute; ninguna compra con el Id. <b>'.$id.'</b>.';
            $response["respuesta"].='</div>';
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    compras.id                  AS id,
                    compras.usuario             AS idUsuario,
                    compras.fechaexpira         AS fechaExpira,
                    compras.timestamp           AS fechaHora,
                    compras.metododepago        AS metododepago,
                    compras.activo              AS activo,
                    compras.proveedor           AS idProveedor,
                    compras.monto               AS monto,
                    compras.contado             AS esContado,
                    compras.motivo              AS motivo,
                    compras.corte               AS corte,
                    compras.esfactura           AS esFactura,
                    compras.nodocumento         AS noDocumento,
                    compras.esCredito           AS esCredito,
                    compras.pagado              AS pagado,
                    proveedores.rsocial         AS nombreProveedor,
                    usuarios.nombre             AS nombreUsuario,
                    usuarios.apellidop          AS apellidopUsuario,
                    metodosdepago.nombre        AS metodopago
                FROM compras
                INNER JOIN proveedores
                ON compras.proveedor = proveedores.id
                INNER JOIN usuarios
                ON compras.usuario = usuarios.id
                INNER JOIN metodosdepago
                ON compras.metododepago = metodosdepago.id
                WHERE compras.id = $id LIMIT 1";
        if($result = $mysqli->query($sql))
        {
            if($result->num_rows == 0)
            {
                $response["status"] = 0;
                $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
                $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>Sin registros.</b> No se encontr&oacute; la compra con Id. <b>'.$id.'</b>.';
                $response["respuesta"].='</div>';
                responder($response, $mysqli);
            }
            $rowVenta = $result->fetch_assoc();
        }elseif(strlen($id) == 0)
        {
            $response["status"] = 0;
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se recibi&oacute; ning&uacute;n dato.';
            $response["respuesta"].='</div>';
            responder($response, $mysqli);
        } elseif (!is_numeric($id))
        {
            $response["status"] = 0;
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Formato de n&uacute;mero inv&aacute;lido.';
            $response["respuesta"].='</div>';
            responder($response, $mysqli);
        }
 ?>
 <div class="col-lg-3 col-md-3" style="padding-left:0px;padding-right:0px">
     <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <label>COMPRA ENCONTRADA </label>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Compra No.</label>
                            <p class="form-control-static"><?php echo $rowVenta['id']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Fecha creaci&oacute;n</label>
                            <p class="form-control-static"><?php echo $rowVenta['fechaHora']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Estatus pago</label>
                            <?php $estadoPago = ($rowVenta['pagado'] == 1) ? '<span class="label label-primary"><i class="fa fa-check" aria-hidden="true"></i> Pagado</span></p>' : '<span class="label label-danger"><i class="fa fa-times" aria-hidden="true"></i> Pendiente</span></p>'; ?>
                            <p class="form-control-static"><?php echo $estadoPago;?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Tipo documento</label>
                            <select id="selectTipoDoc" name="selectTipoDoc" class="form-control">
                            <?php
                                if ($rowVenta['esFactura'] == 1)
                                {
                                    echo "<option value='1' selected>Factura</option>";
                                    echo "<option value='0'>Remisión</option>";
                                }
                                else
                                {
                                    echo "<option value='1'>Factura</option>";
                                    echo "<option value='0' selected>Remisión</option>";
                                }
                             ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Condiciones pago</label>
                            <select id="selectCondiciones" name="selectCondiciones" class="form-control">
                            <?php
                                if ($rowVenta['esCredito'] == 1)
                                {
                                    echo "<option value='1' selected>Crédito</option>";
                                    echo "<option value='0'>Contado</option>";
                                }
                                else
                                {
                                    echo "<option value='1'>Crédito</option>";
                                    echo "<option value='0' selected>Contado</option>";
                                }
                             ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Fecha expiración</label>
                            <p class="form-control-static"><?php echo $rowVenta['fechaExpira']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Cajero</label>
                            <p class="form-control-static"><?php echo $rowVenta['nombreUsuario']." ".$rowVenta['apellidopUsuario']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Proovedor</label>
                            <select id="selectProveedor" name="selectProveedor" class="form-control">
                            <?php
                                $sql = "SELECT
                                            id AS id,
                                            rsocial AS rsocial
                                        FROM proveedores
                                        WHERE activo = 1
                                        ORDER BY rsocial ASC";
                                if ($resultProveedores = $mysqli->query($sql))
                                {
                                    while($filaProveedor = $resultProveedores->fetch_assoc())
                                    {
                                        $idProveedor = $filaProveedor['id'];
                                        $nombreProveedor = $filaProveedor['rsocial'];
                                        if ($idProveedor == $rowVenta['idProveedor'])
                                            echo "<option value='$idProveedor' selected>$nombreProveedor</option>";
                                        else
                                            echo "<option value='$idProveedor'>$nombreProveedor</option>";
                                    }
                                }
                             ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>M&eacute;todo pago</label>
                            <p class="form-control-static"><?php echo $rowVenta['metodopago']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.panel -->
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>MONTO <i class="fa fa-usd" aria-hidden="true"></i></h4>
            </div>
            <div class="panel-body" class="text-right">
                <div class="row">
                    <div class="col-lg-6 col-md-12 text-left">
                        <div class="form-group">
                            <h3><label>Total:</label></h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 text-right">
                        <h3>$<span id="spanTotal"><?php echo number_format($rowVenta['monto'],2,".",","); ?></span></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 text-left">
                        <div class="form-group">
                            <h3><label>Nuevo total:</label></h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 text-right">
                        <h3 class="text-success"> $<span id="spanNuevoTotal"><?php echo number_format($rowVenta['monto'],2,".",","); ?></span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-9 col-md-9">
   <div id="divGroup" class="input-group custom-search-form input-group-lg" style="margin-bottom:10px;">
       <input type="text" class="form-control" placeholder="Clave o código de barras" id="inputBuscarProducto" autocomplete="off">
       <span class="input-group-btn">
           <button class="btn btn-info" type="button" id="btnBuscarProducto">
               <i class="fa fa-check" aria-hidden="true"></i> Agregar producto
           </button>
       </span>
   </div>
   <div class="panel panel-default">
       <div class="panel-heading">
           <label>LISTA DE ART&Iacute;CULOS:</label>
       </div>
       <!-- /.panel-heading -->
       <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Eliminar</th>
                            <th>C&oacute;digo</th>
                            <th>Nombre</th>
                            <th>Unidad</th>
                            <th>Clave Prod.</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">IVA%</th>
                            <th class="text-right">IEPS%</th>
                            <th class="text-right">Precio</th>
                            <th class="text-right">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductos">
        <?php
            $idCompra = $rowVenta['id'];
            $sql            = "SELECT
                                    detallecompra.id            AS idSubCompra,
                                    detallecompra.producto      AS idProducto,
                                    detallecompra.cantidad      AS cantidad,
                                    detallecompra.preciolista   AS precio,
                                    detallecompra.subTotal      AS subTotal,
                                    productos.nombrecorto       AS descripcion,
                                    productos.codigo            AS codigo,
                                    productos.codigo2           AS codigo2,
                                    productos.unidadventa       AS unidadVenta,
                                    productos.claveSHCP         AS claveSat,
                                    productos.IVA               AS iva,
                                    productos.IEPS              AS ieps
                         FROM detallecompra
                         INNER JOIN productos
                         ON detallecompra.producto = productos.id
                         WHERE detallecompra.compra = $idCompra AND detallecompra.activo = 1
                         ORDER BY detallecompra.id DESC";
            if($resultArts              = $mysqli->query($sql))
                $cont                   = 0;
                while ($rowArt          = $resultArts->fetch_assoc())
                {
                    $htmlSelectU        = "";
                    $codigoProd         = (strlen($rowArt['codigo']) > 0) ? $rowArt['codigo'] : $rowArt['codigo2'];
                    $unidadVenta        = $rowArt['unidadVenta'];
                    $sql                = "SELECT * FROM unidadesventa";
                    $res_u_venta        = $mysqli->query($sql);
                    while ($row_u_venta = $res_u_venta->fetch_assoc())
                    {
                        $idUnidad       = $row_u_venta['id'];
                        $nombreUnidad   = $row_u_venta['nombre'];
                        $claveUSat      = $row_u_venta['c_ClaveUnidad'];
                        if ($unidadVenta== $idUnidad)
                            $htmlSelectU    .= "<option selected value='$idUnidad'>$claveUSat</option>";
                        else
                            $htmlSelectU    .= "<option value='$idUnidad'>$claveUSat</option>";

                    }

        ?>
                        <tr class="trItemLista" name="<?php echo $cont;?>" idsubcompra="<?php echo $rowArt['idSubCompra'];?>" codigo="" idproducto preciou cantidad subtotal unidadsat clavesat iva ieps>
                            <td class="text-center"><button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem" name="<?php echo $cont;?>"><i class="fa fa-times"></i></button></td>
                            <td class="tdId" name="<?php echo $codigoProd;?>"><?php echo $codigoProd; ?></td>
                            <td class="tdProducto" name="<?php echo $rowArt['idProducto'];?>"><?php echo $rowArt['descripcion']; ?></td>
                            <td class="text-center">
                                <select style="text-align:center; border-width:0; max-width: 80px; background-color: white;" class="selectUSat">
                                    <?php echo $htmlSelectU;?>
                                </select>
                            </td>
                            <td class="text-right">
                                <input type="number" min="0" style="text-align:right; border-width:0; max-width: 80px;" class="inputClaveSat" value="<?php echo $rowArt['claveSat'];?>">
                            </td>
                            <td class="text-right tdCantidad" name="<?php echo $rowArt['cantidad'];?>">
                                <input type="number" min="0" style="text-align:right;border-width:0px;width:100px;" class="inputCantidad" value="<?php echo number_format($rowArt['cantidad'],3,".",","); ?>">
                            </td>
                            <td class="text-right">
                                <input type="number" min="0" style="text-align:right; border-width:0; max-width: 80px;" class="inputIVA" value="<?php echo number_format($rowArt['iva'],2,".","");?>">
                            </td>
                            <td class="text-right">
                                <input type="number" min="0" style="text-align:right; border-width:0; max-width: 80px;" class="inputIEPS" value="<?php echo number_format($rowArt['ieps'],2,".","");?>">
                            </td>
                            <td class="text-right tdPrecio" name="<?php echo $rowArt['precio'];?>">
                                <input type="number" min="0" style="text-align:right;border-width:0px;width:100px;" class="inputPrecioU" value="<?php echo number_format($rowArt['precio'],2,".",""); ?>">
                            </td>
                            <td class="text-right tdSubTotal" name="<?php echo $rowArt['subTotal'];?>"><?php echo "$".number_format($rowArt['subTotal'],2,".",","); ?></td>
                        </tr>
        <?php
                    $cont++;
                }
        ?>
                    </tbody>
                </table>
                <input type="hidden" id="idProveedor" value="<?php echo $rowVenta['idProveedor']?>">
                <input type="hidden" id="idCompra" value="<?php echo $idCompra;?>">
            </div>
            <!-- /.table-responsive -->
        </div>
        <div class="col-lg-12 text-center" style="padding-top:8px">
            <button type="button" id="btnGuardar" disabled class="btn btn-lg btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar compra</button>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
    <!-- /.panel -->
<?php
}
?>
