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
        $sql = "SELECT id FROM cotizaciones WHERE id = $id LIMIT 1";
        $resultExisteCotizacion = $mysqli->query($sql);
        if($resultExisteCotizacion->num_rows == 0)
        {
            $response["status"] = 0;
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> No se encontr&oacute; ninguna cotizaci&oacute;n con el Id. <b>'.$id.'</b>.';
            $response["respuesta"].='</div>';
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    cotizaciones.id                 AS      id,
                    cotizaciones.idVenta            AS      idVenta,
                    cotizaciones.timestamp          AS      fechaHora,
                    cotizaciones.cliente            AS      idCliente,
                    cotizaciones.descuento          AS      descuento,
                    cotizaciones.usuario            AS      idUsuario,
                    cotizaciones.metododepago       AS      metododepago,
                    cotizaciones.totalventa         AS      totalVenta,
                    clientes.rsocial                AS      nombreCliente,
                    usuarios.nombre                 AS      nombreUsuario,
                    usuarios.apellidop              AS      apellidopUsuario,
                    metodosdepago.nombre            AS      metodopago
                FROM cotizaciones
                INNER JOIN clientes
                ON cotizaciones.cliente = clientes.id
                INNER JOIN usuarios
                ON cotizaciones.usuario = usuarios.id
                INNER JOIN metodosdepago
                ON cotizaciones.metododepago = metodosdepago.id
                WHERE cotizaciones.id = $id LIMIT 1";
        if($result = $mysqli->query($sql))
        {
            if($result->num_rows == 0)
            {
                $response["status"] = 0;
                $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
                $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>Sin registros.</b> No se encontr&oacute; la cotizaci&oacute;n con Id. <b>'.$id.'</b>.';
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
 <div class="col-lg-4 col-md-4" style="padding-left:0px;padding-right:0px">
     <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <label>COTIZACI&Oacute;N ENCONTRADA </label>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Cotizaci&oacute;n No.</label>
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
                            <label>M&eacute;todo pago</label>
                            <p class="form-control-static"><?php echo $rowVenta['metodopago']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Descuento</label>
                            <p class="form-control-static"><?php echo "$ ".$rowVenta['descuento']; ?></p>
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
                            <label>Cliente</label>
                            <select id="selectCliente" name="selectCliente" class="form-control">
                            <?php
                                $sql = "SELECT
                                            id AS id,
                                            rsocial AS rsocial,
                                            tipoprecio AS tipoprecio
                                        FROM clientes
                                        ORDER BY rsocial ASC";
                                if ($resultClientes = $mysqli->query($sql))
                                {
                                    while($filaCliente = $resultClientes->fetch_assoc())
                                    {
                                        $idCliente = $filaCliente['id'];
                                        $nombreCliente = $filaCliente['rsocial'];
                                        if ($idCliente == $rowVenta['idCliente'])
                                            echo "<option value='$idCliente' selected>$nombreCliente</option>";
                                        else
                                            echo "<option value='$idCliente'>$nombreCliente</option>";
                                    }
                                }
                             ?>
                            </select>
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
                        <h3>$<span id="spanTotal"><?php echo number_format($rowVenta['totalVenta'],2,".",","); ?></span></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 text-left">
                        <div class="form-group">
                            <h3><label>Nuevo total:</label></h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 text-right">
                        <h3 class="text-success"> $<span id="spanNuevoTotal"><?php echo number_format($rowVenta['totalVenta'],2,".",","); ?></span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-8 col-md-8">
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
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Precio</th>
                            <th class="text-right">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody id="listaProductos">
        <?php
            $idCotizacion = $rowVenta['id'];
            $sql            = "SELECT
                                    detallecotizacion.id            AS idSubCotizacion,
                                    detallecotizacion.producto      AS idProducto,
                                    detallecotizacion.cantidad      AS cantidad,
                                    detallecotizacion.precio        AS precio,
                                    detallecotizacion.subTotal      AS subTotal,
                                    detallecotizacion.descripcion   AS descripcion,
                                    productos.codigo                AS codigo,
                                    productos.codigo2               AS codigo2
                         FROM detallecotizacion
                         INNER JOIN productos
                         ON detallecotizacion.producto = productos.id
                         WHERE detallecotizacion.cotizacion = $idCotizacion AND detallecotizacion.activo = 1
                         ORDER BY detallecotizacion.id DESC";
            if($resultArts = $mysqli->query($sql))
                $cont          = 0;
                while ($rowArt = $resultArts->fetch_assoc())
                {
                    $codigoProd = (strlen($rowArt['codigo']) > 0) ? $rowArt['codigo'] : $rowArt['codigo2'];
        ?>
                        <tr class="trItemLista" name="<?php echo $cont;?>" idSubCotizacion="<?php echo $rowArt['idSubCotizacion'];?>">
                            <td class="text-center"><button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem" name="<?php echo $cont;?>"><i class="fa fa-times"></i></button></td>
                            <td class="tdId" name="<?php echo $codigoProd;?>"><?php echo $codigoProd; ?></td>
                            <td class="tdProducto" name="<?php echo $rowArt['idProducto'];?>"><?php echo $rowArt['descripcion']; ?></td>
                            <td class="text-right tdCantidad" name="<?php echo $rowArt['cantidad'];?>">
                                <input type="number" min="0" style="text-align:right;border-width:0px;width:100px;" class="inputCantidad" value="<?php echo number_format($rowArt['cantidad'],3,".",","); ?>">
                            </td>
                            <td class="text-right tdPrecio" name="<?php echo $rowArt['precio'];?>">
                                <input type="number" min="0" style="text-align:right;border-width:0px;width:100px;" class="inputPrecio" value="<?php echo number_format($rowArt['precio'],2,".",""); ?>">
                            </td>
                            <td class="text-right tdSubTotal" name="<?php echo $rowArt['subTotal'];?>"><?php echo "$".number_format($rowArt['subTotal'],2,".",","); ?></td>
                        </tr>
        <?php
                    $cont++;
                }
        ?>
                    </tbody>
                </table>
                <input type="hidden" id="idCliente" value="<?php echo $rowVenta['idCliente']?>">
                <input type="hidden" id="idCotizacion" value="<?php echo $rowVenta['id'];?>">
            </div>
            <!-- /.table-responsive -->
        </div>
        <div class="col-lg-12 text-center" style="padding-top:8px">
            <button type="button" id="btnGuardar" disabled class="btn btn-lg btn-success" style="background-color:mediumpurple;border-color:mediumpurple"><i class="fa fa-floppy-o" aria-hidden="true"></i> Actualizar cotizaci&oacute;n</button>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
    <!-- /.panel -->
<?php
}
?>
