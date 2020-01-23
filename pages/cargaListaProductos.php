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
    header("Location: /pventa_std/pages/salir.php");}
else
{
    $idCliente          = $_POST['idCliente'];
    $tipo               = $_POST['tipo'];
    $sql ="SELECT
                tipoprecio
            FROM
                clientes
            WHERE
                id = $idCliente
            LIMIT 1";
    $resultadoCliente   = $mysqli->query($sql);
    $tipoCliente        = $resultadoCliente->fetch_assoc();
    $tipoprecio         = $tipoCliente['tipoprecio'];
     ?>
    <div class="col-lg-12">
        <table id="dataTable" class="display compact table table-striped table-bordered table-hover" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Departamento</th>
                    <th>C&oacute;digo barras</th>
                    <th>C&oacute;digo corto</th>
                    <th>Nombre</th>
                    <th>Descripci&oacute;n</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $sql = "SELECT
                productos.id AS id,
                productos.codigo AS codigo,
                productos.codigo2 AS codigo2,
                productos.nombrecorto AS nombrecorto,
                productos.nombrelargo AS descripcion,
                detalleprecios.precioXunidad AS precioventa,
                departamentos.nombre AS departamento
            FROM productos
            INNER JOIN detalleprecios
            ON productos.id = detalleprecios.producto
            INNER JOIN departamentos
            ON productos.departamento = departamentos.id
            WHERE
                detalleprecios.tipoprecio = $tipoprecio
            ORDER BY
                productos.nombrecorto ASC";
    $result = $mysqli->query($sql);
    while($arrayProductos = $result->fetch_assoc())
    {
        if(strlen($arrayProductos['codigo']) > 0)
            $codigo = $arrayProductos['codigo'];
        else
            $codigo = $arrayProductos['codigo2'];
    ?>

                <tr class="trProductoElegible">
                    <td>
                        <?php echo $arrayProductos['departamento'];?>
                    </td>
                    <td>
                        <span class="tdCodigoElegible" style="display:none"><?php echo $codigo; ?></span>
                        <?php echo $arrayProductos['codigo'];?>
                    </td>
                    <td>
                        <?php echo $arrayProductos['codigo2'];?>
                    </td>
                    <td>
                        <?php echo $arrayProductos['nombrecorto'];?>
                    </td>
                    <td>
                        <?php echo $arrayProductos['descripcion'];?>
                    </td>
                    <td class="text-center">
                        <?php echo $arrayProductos['precioventa'];?>
                    </td>
                </tr>
    <?php
    }
     ?>
                </tbody>
            </table>


    </div>
    <script>
    $('#dataTable').DataTable(
     {
        "lengthMenu": [[10], [10]],
        "language":
        {
             "url": "../startbootstrap/vendor/datatables-plugins/Spanish.json"
         },
         responsive: true,
         "initComplete": function(settings, json)
         {
             <?php
                switch ($tipo)
                {
                    case 1:
                        $s = '<i class="fa fa-cart-arrow-down" aria-hidden="true"></i> Punto de venta';
                        break;
                    case 2:
                        $s = '<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Compras';
                        break;
                    case 3:
                        $s = '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar cotización';
                        break;
                }
             ?>
              $("#spanCargando").html('<?php echo $s;?>');
              $("#divListaProducto").show();

         }
    });
    </script>
<?php
}
?>
