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
        $sql = "SELECT id FROM notacredito WHERE venta = $id LIMIT 1";
        $resultExisteNota = $mysqli->query($sql);
        if($resultExisteNota->num_rows > 0)
        {
            $response["status"] = 0;
            $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
            $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Ya existe una nota de venta asociada al ticket no. <b>'.$id.'</b>.';
            $response["respuesta"].='</div>';
            responder($response, $mysqli);
        }
        $sql = "SELECT
                    ventas.id               AS      idVenta,
                    ventas.timestamp        AS      fechaHora,
                    ventas.cliente          AS      idCliente,
                    ventas.descuento        AS      descuento,
                    ventas.usuario          AS      idUsuario,
                    ventas.metododepago     AS      idMetodopago,
                    ventas.descuento        AS      descuento,
                    ventas.totalventa       AS      totalventa,
                    clientes.rsocial        AS      nombreCliente,
                    usuarios.nombre         AS      nombreUsuario,
                    usuarios.apellidop      AS      apellidopUsuario,
                    usuarios.apellidom      AS      apellidomUsuario,
                    metodosdepago.nombre    AS      metodopago
                FROM ventas
                INNER JOIN clientes
                ON ventas.cliente = clientes.id
                INNER JOIN usuarios
                ON ventas.usuario = usuarios.id
                INNER JOIN metodosdepago
                ON ventas.metododepago = metodosdepago.id
                WHERE ventas.id = $id LIMIT 1";
        if($result = $mysqli->query($sql))
        {
            if($result->num_rows == 0)
            {
                $response["status"] = 0;
                $response["respuesta"] ='<div class="alert alert-danger alert-dismissable fade in">';
                $response["respuesta"].='   <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
                $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <b>Sin registros.</b> No se encontr&oacute; el ticket no. <b>'.$id.'</b>.';
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
            $response["respuesta"].='   <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Formato inv&aacute;lido.';
            $response["respuesta"].='</div>';
            responder($response, $mysqli);
        }
 ?>
 <div class="col-lg-4 col-md-4" style="padding-left:0px;padding-right:0px">
     <div class="col-lg-12 col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <label>TICKET ENCONTRADO </label>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Ticket No.</label>
                            <p class="form-control-static"><?php echo $rowVenta['idVenta']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Fecha/Hora</label>
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
                            <p class="form-control-static"><?php echo $rowVenta['nombreUsuario']." ".$rowVenta['apellidopUsuario']." ".$rowVenta['apellidomUsuario']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Cliente</label>
                            <p class="form-control-static"><?php echo $rowVenta['nombreCliente'];?></p>
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
                        <h3>$<span id="spanTotal"><?php echo number_format($rowVenta['totalventa'],2,".",","); ?></span></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 text-left">
                        <div class="form-group">
                            <h3><label>Cr&eacute;dito:</label></h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 text-right">
                        <h3 class="text-danger">- $<span id="spanCredito">0.00</span></h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 text-left">
                        <div class="form-group">
                            <h3><label>Nuevo total:</label></h3>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 text-right">
                        <h3 class="text-success"> $<span id="spanNuevoTotal">0.00</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-8 col-md-8">
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
                            <th>id &Uacute;nico</th>
                            <th>Nombre</th>
                            <th class="text-right">Precio</th>
                            <th class="text-right">Cantidad</th>
                            <th class="text-right">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
        <?php
            $idVenta = $rowVenta['idVenta'];
            $sql = "SELECT
                        detalleventa.id AS id,
                        detalleventa.producto AS idProducto,
                        detalleventa.cantidad AS cantidad,
                        detalleventa.precio AS precio,
                        detalleventa.subTotal AS subTotal,
                        productos.nombrelargo AS nombreProducto
             FROM detalleventa
             INNER JOIN productos
             ON detalleventa.producto = productos.id
             WHERE detalleventa.venta = $idVenta";
             $cont = 0;
            if($resultArts = $mysqli->query($sql))
                while ($rowArt = $resultArts->fetch_assoc())
                {
        ?>
                        <tr class="trItemLista" name="<?php echo $cont;?>">
                            <td><button type="button" class="btn btn-danger btn-outline btn-circle btnEliminarItem" name="<?php echo $cont;?>"><i class="fa fa-times"></i></button></td>
                            <td class="tdId" name="<?php echo $rowArt['id'];?>"><?php echo $rowArt['id']; ?></td>
                            <td class="tdProducto" name="<?php echo $rowArt['idProducto'];?>"><?php echo $rowArt['nombreProducto']; ?></td>
                            <td class="text-right tdPrecio" name="<?php echo $rowArt['precio'];?>"><?php echo "$".number_format($rowArt['precio'],2,".",","); ?></td>
                            <td class="text-right tdCantidad" name="<?php echo $rowArt['cantidad'];?>"><input type="number" min="0" style="text-align:right; border-width:0;" class="inputCantidad" value="<?php echo number_format($rowArt['cantidad'],3,".",","); ?>"></td>
                            <td class="text-right tdSubTotal" name="<?php echo $rowArt['subTotal'];?>"><?php echo "$".number_format($rowArt['subTotal'],2,".",","); ?></td>
                        </tr>
        <?php
                    $cont++;
                }
        ?>
                    </tbody>
                </table>
                <input type="hidden" id="idCliente" value="<?php echo $rowVenta['idCliente']?>">
                <input type="hidden" id="idVenta" value="<?php echo $rowVenta['idVenta']?>">
            </div>
            <!-- /.table-responsive -->
        </div>
        <div class="col-lg-12 text-center" style="padding-top:8px">
            <button type="button" id="btnGuardar" disabled class="btn btn-lg btn-success"><i class="fa fa-floppy-o" aria-hidden="true"></i> Generar nota</button>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
    <!-- /.panel -->
<?php
}
?>
