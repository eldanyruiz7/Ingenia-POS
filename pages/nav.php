<?php
if( $sesion->get("nick")!= false )
{
?>
<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom:0px;">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index.php"><?php echo $sesion->get("nombreComercio"); ?></a>
    </div>
    <!-- /.navbar-header -->
    <ul class="nav navbar-nav visible-lg pull-right">
<?php
    if(!(isset($pagina)))
        $pagina = 10;
    switch ($pagina)
    {
        case 4:
        case 6:
?>
            <!-- <li><button id="btnCtrlD" class="btn btn-link btn-outline"><h6><b>Ctrl+D</b> Menú</h6></button></li> -->
            <li><a href="/pventa_std/pages/index.php" id="btnf1" class="btn btn-link btn-outline" style="padding-top:12px"><b>F1</b> Regresar al punto de venta</a></li>
            <li><button id="btnf2" class="btn btn-link btn-outline"><h6><b>F2</b> Listar todo</h6></button></li>
            <li><button id="btnf3" class="btn btn-link btn-outline"><h6><b>F3</b> Buscar por nombre</h6></button></li>
            <li><button id="btnf12" class="btn btn-link btn-outline"><h6><b>F12</b> Terminar compra</h6></button></li>
<?php
            break;
        case 7:
?>
            <!-- <li><button id="btnCtrlD" class="btn btn-link btn-outline"><h6><b>Ctrl+D</b> Menú</h6></button></li> -->
            <li><a href="/pventa_std/pages/index.php" id="btnf1" class="btn btn-link btn-outline" style="padding-top:12px"><b>F1</b> Regresar al punto de venta</a></li>
            <li><button id="btnf2" class="btn btn-link btn-outline"><h6><b>F2</b> Listar todo</h6></button></li>
            <li><button id="btnf3" class="btn btn-link btn-outline"><h6><b>F3</b> Buscar por nombre</h6></button></li>
<?php
            break;
        case 8:
        case 10:
?>
            <!-- <li><button id="btnCtrlD" class="btn btn-link btn-outline"><h6><b>Ctrl+D</b> Menú</h6></button></li> -->
            <li><a href="/pventa_std/pages/index.php" id="btnf1" class="btn btn-link btn-outline" style="padding-top:12px"><b>F1</b> Punto de venta</a></li>
<?php

            break;
        case 1:
?>
            <!-- <li><button id="btnCtrlD" class="btn btn-link btn-outline"><h6><b>Ctrl+D</b> Menú</h6></button></li> -->
            <li><button id="btnf2" class="btn btn-link btn-outline"><h6><b>F2</b> Listar todo</h6></button></li>
            <!--<button  type="button" class="btn btn-link btn-outline"></button>-->
            <li><button id="btnf3" class="btn btn-link btn-outline"><h6><b>F3</b> Buscar por nombre</h6></button></li>
            <li><button id="btnf4" class="btn btn-link btn-outline"><h6><b>F4</b> Nota cr&eacute;dito</h6></button></li>
            <!--<button id="btnf5" type="button" class="btn btn-link btn-outline"><b>F5</b> Limpiar</button>-->
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
            <li><button id="btnf5" class="btn btn-link btn-outline"><h6><b>F5</b> Nota de salida</h6></button></li>
<?php
    }
?>
            <li><button id="btnf6" class="btn btn-link btn-outline"><h6><b>F6</b> Comprar</h6></button></li>
            <li><button id="btnf7" class="btn btn-link btn-outline"><h6><b>F7</b> Corte caja</h6></button></li>
            <li><button id="btnf8" class="btn btn-link btn-outline"><h6><b>F8</b> Retiro</h6></button></li>
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
            <li><button id="btnf10" class="btn btn-link btn-outline"><h6><b>F10</b> Cotizaci&oacute;n</h6></button></li>
<?php
    }
?>
            <li><button id="btnf11" class="btn btn-link btn-outline"><h6><b>F11</b> Remisi&oacute;n</h6></button></li>
            <li><button id="btnf12" class="btn btn-link btn-outline"><h6><b>F12</b> Venta</h6></button></li>
<?php
            break;
    }

 ?>
            <li>
               <button style="color:#337ab7" type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                    <h6><i class="fa fa-user fa-fw"></i> <?php echo $sesion->get("nombre");?></h6>
               </button>
           </li>
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
             <div class="dropdown pull-right" style="margin-right:18px;margin-top:6px">
                 <button id="btnMensajes" class="btn btn-link dropdown-toggle" type="button" data-toggle="dropdown">
                     <!--<button id="btnMensajes" class="btn btn-link dropdown-toggle" type="button" data-toggle="dropdown">-->
                     <!---->
                     <i class="fa fa-check" aria-hidden="true"></i>
                 </button>
                 <ul class="dropdown-menu" id="msgDesplegable">

                 </ul>
             </div>
<?php
    }
?>
            </ul>
        </div>
    </div>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->
    <div class="navbar-default sidebar" role="navigation" style="margin-top:10px;z-index:2">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li>
                    <a href="index.php"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Punto de venta</a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-shopping-basket" aria-hidden="true"></i> Productos <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="compras.php">Comprar</a>
                        </li>
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                        <li>
                            <a href="buscarProducto.php">Buscar</a>
                        </li>
                        <li>
                            <a href="listarProducto.php">Listar</a>
                        </li>
                        <li>
                            <a href="agregarProducto.php">Agregar</a>
                        </li>
<?php
    }
?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                <li>
                    <a href="#"><i class="fa fa-address-card-o" aria-hidden="true"></i> Clientes <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="listarCliente.php">Listar</a>
                        </li>
                        <li>
                            <a href="agregarCliente.php">Agregar</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="#"><i class="fa fa-truck" aria-hidden="true"></i> Proveedores<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="listarProveedor.php">Listar</a>
                        </li>
                        <li>
                            <a href="agregarProveedor.php">Agregar</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
<?php
    }
 ?>
                <li>
                    <a href="#"><i class="fa fa-desktop" aria-hidden="true"></i> Caja <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="corteCaja.php">Realizar corte de caja</a>
                        </li>
                        <li>
                            <a href="retiroEfectivo.php">Retirar efectivo</a>
                        </li>
                        <li>
                            <a href="ingresoEfectivo.php">Agregar efectivo</a>
                        </li>
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                        <li>
                            <a href="listarCortes.php">Listar cortes de caja</a>
                        </li>
<?php
    }
?>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="#"><i class="fa fa-certificate" aria-hidden="true"></i>  Notas de cr&eacute;dito <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                        <li>
                            <a href="reporteNotasCredito.php">Listar notas de cr&eacute;dito</a>
                        </li>
<?php
    }
?>
                        <li>
                            <a href="notaCredito.php">Agregar nota de cr&eacute;dito</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
<?php
    if($sesion->get('tipousuario') == 1)
    {
?>
                <li>
                    <a href="#"><i class="fa fa-indent" aria-hidden="true"></i> Cotizaciones <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="reporteCotizaciones.php">Listar cotizaciones</a>
                        </li>
                        <li>
                            <a href="editarCotizacion.php">Editar cotizaci&oacute;n</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="#"><i class="fa fa-list-ol" aria-hidden="true"></i> Inventario <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <!-- <li>
                            <a href="listarAjustesInventario.php">Listar ajustes</a>
                        </li> -->
                        <li>
                            <a href="ajusteInventario.php">Ajuste de inventario</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="#"><i class="fa fa-money" aria-hidden="true"></i> Movimientos <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="cuentasXpagar.php">Proovedores (x pagar)</a>
                        </li>
                        <li>
                            <a href="cuentasXcobrar.php">Clientes (x cobrar)</a>
                        </li>
                        <li>
                            <a href="recibosEmitidos.php">Recibos emitidos</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <li>
                    <a href="#"><i class="fa fa-bar-chart" aria-hidden="true"></i> Reportes <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="reporteCompras.php">Compras General</a>
                        </li>
                        <li>
                            <a href="reporteExistencias.php">Existencias</a>
                        </li>
                        <li>
                            <a href="reporteInventario.php">Inventario</a>
                        </li>
                        <li>
                            <a href="reporteVentas.php">Ventas General</a>
                        </li>
                        <li>
                            <a href="notasDeSalida.php">Notas de salida</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i> M&aacute;s reportes<span class="fa arrow"></span></a>
                            <ul class="nav nav-third-level">
                                <li>
                                    <a href="reporteUtilidadXLPrecios.php"> Utilidad por lista de precios</a>
                                </li>
                                <li>
                                    <a href="reporteMasVendido.php"> Productos +/- vendidos</a>
                                </li>
                                <li>
                                    <a href="otrosReportes.php"> Cat&aacute;logos</a>
                                </li>
                            </ul>
                            <!-- /.nav-third-level -->
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
                <!-- <li>
                    <a href="#"><i class="fa fa-rocket" aria-hidden="true"></i> Facturas <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="facturasEmitidas.php">Facturas emitidas</a>
                        </li>
                        <li>
                            <a href="complementosEmitidos.php">Complementos emitidos</a>
                        </li>
                        <li>
                            <a href="facturarCierreMes.php">Al p&uacute;blico en general</a>
                        </li>
                    </ul>
                </li> -->
                <li>
                    <a href="#"><i class="fa fa-wrench" aria-hidden="true"></i> Configuracion <span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="usuarios.php">Usuarios</a>
                        </li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li>
                            <a href="departamentos.php">Departamentos</a>
                        </li>
                    </ul>
                    <!-- /.nav-second-level -->
                </li>
<?php
    }
?>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>
<?php
}
?>
