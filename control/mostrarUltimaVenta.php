<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
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
    require ("ventaRecibo.php");
    function responder($response, $mysqli)
    {
        //$response['respuesta'].=$mysqli->error;
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $idVentaActual      = $_POST['idVentaActual'];
    $orden              = $_POST['orden'];
    if ($idVentaActual <= 1)
    {
        $sql = "SELECT
                    ventas.id                   AS idVenta,
                    ventas.timestamp            AS fechaHora,
                    ventas.cliente              AS idCliente,
                    ventas.usuario              AS idUsuario,
                    ventas.metododepago         AS metododepago,
                    ventas.totalventa           AS totalVenta,
                    ventas.corte                AS corteCaja,
                    ventas.pagacon              AS pagaCon,
                    ventas.pagado               AS pagado,
                    ventas.remision             AS remision,
                    clientes.rsocial            AS nombreCliente,
                    usuarios.nombre             AS nombreUsuario,
                    usuarios.apellidop          AS apellidopUsuario,
                    metodosdepago.c_FormaPago   AS c_FormaPago,
                    metodosdepago.nombre        AS nombrFormadePago
                FROM ventas
                INNER JOIN clientes
                ON ventas.cliente = clientes.id
                INNER JOIN usuarios
                ON ventas.usuario = usuarios.id
                INNER JOIN metodosdepago
                ON ventas.metododepago = metodosdepago.id
                ORDER BY ventas.id DESC LIMIT 1";
    }
    else
    {
        if ($orden == 1)
            $idVentaActual++;
        else
            $idVentaActual--;

        $sql = "SELECT
                    ventas.id                   AS idVenta,
                    ventas.timestamp            AS fechaHora,
                    ventas.cliente              AS idCliente,
                    ventas.usuario              AS idUsuario,
                    ventas.metododepago         AS metododepago,
                    ventas.totalventa           AS totalVenta,
                    ventas.corte                AS corteCaja,
                    ventas.pagacon              AS pagaCon,
                    ventas.pagado               AS pagado,
                    ventas.remision             AS remision,
                    clientes.rsocial            AS nombreCliente,
                    usuarios.nombre             AS nombreUsuario,
                    usuarios.apellidop          AS apellidopUsuario,
                    metodosdepago.c_FormaPago   AS c_FormaPago,
                    metodosdepago.nombre        AS nombrFormadePago
                FROM ventas
                INNER JOIN clientes
                ON ventas.cliente = clientes.id
                INNER JOIN usuarios
                ON ventas.usuario = usuarios.id
                INNER JOIN metodosdepago
                ON ventas.metododepago = metodosdepago.id
                WHERE ventas.id = $idVentaActual LIMIT 1";
    }
    $result         = $mysqli->query($sql);
    if ($result->num_rows < 1)
    {
        $response['pageWrapper'] = 1;
        responder($response, $mysqli);
    }
    $row            = $result->fetch_assoc();
    $iconoVenta     = ($row['remision'] == 1) ? '<i class="fa fa-print" aria-hidden="true"></i> Remisi&oacute;n' : '<i class="fa fa-ticket" aria-hidden="true"></i> Ticket';
    $idVenta        = str_pad($row['idVenta'], 12, "0", STR_PAD_LEFT);
    $fecha          = date('H:i:s/d-m-Y',strtotime($row['fechaHora']));
    $nombreCliente  = $row['nombreCliente'];
    $formaPago      = $row['c_FormaPago']."-".$row['nombrFormadePago'];
    $corte          = ($row['corteCaja'] == 1) ? '<i class="fa fa-check-square-o" aria-hidden="true"></i> Hecho' : '<i class="fa fa-times" aria-hidden="true"></i> Pendiente';
    $nombreUsuario  = $row['nombreUsuario']." ".$row['apellidopUsuario'];
    $statusPago     = ($row['pagado'] == 1) ? 'Pagado' : 'Pendiente de pago';
    $totalVenta     = $row['totalVenta'];
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><?php echo $iconoVenta."# ".$idVenta;?></h1> <input type="hidden" id="hiddenIdMostrarVenta" value="<?php echo $idVenta;?>">
    </div>
</div>
<div class="col-lg-4 col-md-4" style="padding-left:0px;padding-right:0px">
    <div class="col-lg-12 col-md-12">
       <div class="panel panel-info">
           <div class="panel-heading">
               <label>INFORMACI&Oacute;N DE LA VENTA </label>
           </div>
           <!-- /.panel-heading -->
           <div class="panel-body">
               <div class="row">
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Venta No.</label>
                           <p class="form-control-static"><?php echo $idVenta; ?></p>
                       </div>
                   </div>
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Fecha/Hora</label>
                           <p class="form-control-static"><?php echo $fecha; ?></p>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Cajero</label>
                           <p class="form-control-static"><?php echo $nombreUsuario;?></p>
                       </div>
                   </div>
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Cliente</label>
                           <p class="form-control-static"><?php echo $nombreCliente;?></p>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Estatus pago</label>
                           <p class="form-control-static"><?php echo $statusPago;?></p>
                       </div>
                   </div>
                   <div class="row">
                       <div class="col-lg-6">
                           <div class="form-group">
                               <label>Forma de pago</label>
                               <p class="form-control-static"><?php echo $formaPago; ?></p>
                           </div>
                       </div>
                   </div>



               </div>
               <div class="row">
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Tipo:</label>
                           <p class="form-control-static"><?php echo $iconoVenta;?></p>
                       </div>
                   </div>
                   <div class="col-lg-6">
                       <div class="form-group">
                           <label>Corte de caja</label>
                           <p class="form-control-static"><?php echo $corte;?></p>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <!-- /.panel -->

</div>
<div class="col-lg-8 col-md-8">
  <div class="panel panel-info">
      <div class="panel-heading">
          <label>LISTA DE ART&Iacute;CULOS:</label>
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
           <div class="table-responsive">
               <table class="table table-hover">
                   <thead>
                       <tr>
                           <th></th>
                           <th>C&oacute;digo</th>
                           <th>Nombre</th>
                           <th class="text-right">Precio</th>
                           <th class="text-right">Cantidad</th>
                           <th class="text-right">Sub Total</th>
                       </tr>
                   </thead>
                   <tbody>
       <?php
           $idVenta = $row['idVenta'];
           $sql = "SELECT
                       detalleventa.id          AS id,
                       detalleventa.producto    AS idProducto,
                       detalleventa.cantidad    AS cantidad,
                       detalleventa.precio      AS precio,
                       detalleventa.subTotal    AS subTotal,
                       productos.nombrelargo    AS nombreProducto,
                       productos.codigo         AS codigo,
                       productos.codigo2        AS codigo2
            FROM detalleventa
            INNER JOIN productos
            ON detalleventa.producto = productos.id
            WHERE detalleventa.venta = $idVenta";
            $cont = 1;
           if($resultArts               = $mysqli->query($sql))
               while ($rowArt           = $resultArts->fetch_assoc())
               {
                   $codigo              = ($rowArt['codigo2'] == NULL) ? $rowArt['codigo'] : $rowArt['codigo2'];
       ?>
                       <tr class="trItemList" name="<?php echo $cont;?>">
                           <td><?php echo $cont.".";?></td>
                           <td class="tdId" name="<?php echo $rowArt['id'];?>"><?php echo $codigo; ?></td>
                           <td class="tdProducto" name="<?php echo $rowArt['idProducto'];?>"><?php echo $rowArt['nombreProducto']; ?></td>
                           <td class="text-right tdPrecio" name="<?php echo $rowArt['precio'];?>"><?php echo "$".number_format($rowArt['precio'],2,".",","); ?></td>
                           <td class="text-right tdCantidad" name="<?php echo $rowArt['cantidad'];?>"><input type="number" min="0" style="text-align:right; border-width:0;" class="inputCant" value="<?php echo number_format($rowArt['cantidad'],3,".",","); ?>"></td>
                           <td class="text-right tdSubTot" name="<?php echo $rowArt['subTotal'];?>"><?php echo "$".number_format($rowArt['subTotal'],2,".",","); ?></td>
                       </tr>
       <?php
                   $cont++;
               }
       ?>
                   </tbody>
               </table>
           </div>
           <!-- /.table-responsive -->
       </div>
       <div class="panel-footer">
       <h3 class="text-right" style="margin-top:2px;"><span>Total $<?php echo number_format($totalVenta,2,".",","); ?></span></span></h3>
        </div>
   </div>
   <!-- /.panel-body -->
</div>
   <!-- /.panel -->


<?php

}
?>
