<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
require_once ("../conecta/cerrarOtrasSesiones.php");
require_once ("../conecta/usuarioLogeado.php");
$usuario= $sesion->get("id");
$idSes  = $sesion->get("idsesion");
if( logueado($idSes,$usuario,$mysqli) == FALSE)
{
    header("Location: /pventa_std/pages/login.php");
}
else
{
    $idProducto = $_POST['idProducto'];
    // Todas las consultas
    $sql = "SELECT *
            FROM productos
            WHERE id = $idProducto
            AND activo = 1
            LIMIT 1";
    $result = $mysqli->query($sql);
    $arrayProducto = $result->fetch_assoc();

    $sql = "SELECT preciolista
            FROM precios
            WHERE producto = $idProducto
            LIMIT 1";
    $result = $mysqli->query($sql);
    $arrayPrecio = $result->fetch_assoc();

    $sql = "SELECT *
            FROM detalleprecios
            WHERE producto = $idProducto
            AND tipoprecio = 1";
    $result = $mysqli->query($sql);
    $arrayPrecioMenudeo = $result->fetch_assoc();

    $sql = "SELECT *
            FROM detalleprecios
            WHERE producto = $idProducto
            AND tipoprecio = 2";
    $result = $mysqli->query($sql);
    $arrayPrecioMediomay = $result->fetch_assoc();

    $sql = "SELECT *
            FROM detalleprecios
            WHERE producto = $idProducto
            AND tipoprecio = 3";
    $result = $mysqli->query($sql);
    $arrayPrecioMayoreo = $result->fetch_assoc();

    $sql = "SELECT *
            FROM detalleprecios
            WHERE producto = $idProducto
            AND tipoprecio = 4";
    $result = $mysqli->query($sql);
    $arrayPrecioEspecial = $result->fetch_assoc();

?>
<div class="col-lg-12">
    <div class="row">
        <div class="col-lg-6 col-md-6 col-xs-6 col-sm-6">
            <h3 class="page-header"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Producto encontrado </h3>
        </div>
        <!-- <div class="col-lg-3 col-md-3 col-xs-3 col-sm-3" id="divRespuestaModal">

        </div> -->
        <div class="col-lg-6 col-md-6 col-xs-6" ><!--style="padding:10px;text-align:center;cursor:pointer;color:#708090;margin-top:25px;margin-bottom:15px">-->
            <div class="col-lg-5 col-md-7 col-lg-offset-7 col-md-offset-5" style="padding-right: 0px;margin-bottom:20px; margin-top: 10px;box-shadow: 19px -3px 25px -8px #999;">
                <div class="col-lg-12" id="divstatus" style="height:150px;text-align:center;color:#337ab7; padding-right:0px" ><!--<style="margin-bottom5px;height:180px;width:250px">-->
            <?php
                if (strlen($arrayProducto['img']) > 0)
                {
            ?>
            <!-- <div id="divstatus" style="width:100%;height:100%" >
            </div> -->
                    <img id="vistaPrevia" class="img-thumbnail" style="width:100%;height:100%" src='../images/_productos_/<?php echo $arrayProducto['img'].".jpg"; ?>' />
            <?php
                }
                else
                {
            ?>
                    <img id="vistaPrevia" class="img-thumbnail" style="width:100%;height:100%"/>

            <?php
                }
             ?>

                </div>
                <div class="col-lg-12" style="margin-bottom:-1px;">
                    <div class="progress progress-striped active" id="progressUpload" style="display:none">
                        <div id="divfileupload" class="progress-bar progress-bar-info" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                            <span class="sr-only">0% Completado</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12" style="padding-right:0px">
                <?php
                    if (strlen($arrayProducto['img']) > 0)
                    {
                ?>
                    <button type="button" id="btnEliminarImg" class="btn btn-sm btn-block btn-success" style="border-radius: 0px 0px 5px 5px;"><i class="fa fa-times fa-2x" aria-hidden="true"></i> <br>Eliminar</button>
                <?php
                    }
                    else
                    {
                ?>
                    <button type="button" id="btnAnadirImg" class="btn btn-sm btn-outline btn-info btn-block" style="border-radius: 0px 0px 5px 5px;"><i class="fa fa-plus-circle fa-2x" aria-hidden="true"></i> </br>Imagen</button>
                <?php
                    }
                ?>
                </div>
            </div>

        </div>
        <!-- <div class="col-lg-3 col-md-3 col-xs-6 col-lg-offset-9 col-md-offset-9">

        </div> -->
    <!-- /.col-lg-6 .col-md-6 .col-xs-6 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Datos del producto "<?php echo $arrayProducto['nombrelargo'];?>"
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form role="form" method="POST" action="agregarProducto.php" id="formSubmit" onsubmit="submitForm();">
                            <div class="col-lg-12">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divNombreCorto">
                                        <label class="control-label">Nombre corto</label>
                                        <input id="inputNombreCorto" name="inputNombreCorto" autocomplete ="off" required="required" class="form-control" value="<?php echo $arrayProducto['nombrecorto'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divNombreLargo">
                                        <label class="control-label">Presentaci&oacute;n</label>
                                        <input id="inputNombreLargo" name="inputNombreLargo" autocomplete ="off" required="required" class="form-control" value="<?php echo $arrayProducto['nombrelargo'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable">
                                        <label class="control-label">Departamento</label>
                                        <select id="selectCategoria" name="selectCategoria" required="required" class="form-control">
                        <?php
                            $sql = "SELECT * FROM departamentos WHERE activo = 1 ORDER BY id ASC";
                            $resultCategorias = $mysqli->query($sql);
                            while ($arrayCategorias = $resultCategorias->fetch_assoc())
                            {
                                $idCategoria = $arrayCategorias['id'];
                                $nombreCategoria = $arrayCategorias['nombre'];
                                if ($idCategoria == $arrayProducto['departamento'])
                                    echo "<option selected value='$idCategoria'>$nombreCategoria</option>";
                                else
                                    echo "<option value='$idCategoria'>$nombreCategoria</option>";
                            }

                        ?>
                                        </select>
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divCodigoBarras">
                                        <label class="control-label">Codigo de barras</label>
                                        <input id="inputCodigoBarras" name="inputCodigoBarras" autocomplete ="off" class="form-control" value="<?php echo $arrayProducto['codigo'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divCodigo2">
                                        <label class="control-label">Clave corta</label>
                                        <input id="inputCodigo2" name="inputCodigo2" autocomplete ="off" class="form-control" value="<?php echo $arrayProducto['codigo2'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divSHCP">
                                        <label class="control-label">Clave SAT</label>
                                        <input id="inputSHCP" name="inputSHCP" autocomplete ="off" class="form-control" value="<?php echo $arrayProducto['claveSHCP'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divPrecioLista">
                                        <label class="control-label">Precio de lista</label>
                                        <input type="number" id="inputPrecioLista" name="inputPrecioLista" min="0" autocomplete ="off" class="form-control" value="<?php echo $arrayPrecio['preciolista'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable divShowHide" id="divFactor">
                                        <label class="control-label">Factor de conversi&oacute;n</label>
                                        <input id="inputFactor" type="number" min="1" name="inputFactor" autocomplete ="off" class="form-control" value="<?php echo $arrayProducto['factorconversion'];?>">
                                        <p class="help-block">Unidades por caja o paquete</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6" style="display:none">
                                    <div class="form-group divControlable divShowHide" id="divUnidades">
                                        <label class="control-label">Unidad de venta</label>
                                        <select id="selectUnidad" name="selectUnidad" required="required" class="form-control">
                        <?php
                            $sql = "SELECT * FROM unidadesventa ORDER BY id ASC";
                            $resultUnidades = $mysqli->query($sql);
                            while ($arrayUnidades = $resultUnidades->fetch_assoc())
                            {
                                $idUnidad = $arrayUnidades['id'];
                                $nombreUnidad = $arrayUnidades['nombre'];

                                if ($idUnidad == $arrayProducto['unidadventa'])
                                    echo "<option selected value='$idUnidad'>$nombreUnidad</option>";
                                else
                                    echo "<option value='$idUnidad'>$nombreUnidad</option>";
                            }
                        ?>
                                        </select>
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divIVA">
                                        <label class="control-label">I.V.A.</label>
                                        <input type="number" id="inputIVA" name="inputIVA" min="0" autocomplete ="off" class="form-control" value="<?php echo $arrayProducto['IVA'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group divControlable" id="divIEPS">
                                        <label class="control-label">I.E.P.S.</label>
                                        <input type="number" id="inputIEPS" name="inputIEPS" min="0" autocomplete ="off" class="form-control" value="<?php echo $arrayProducto['IEPS'];?>">
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6" style="display:none">
                                    <div class="form-group divControlable" id="divBalanza">
                                        <label>¿Su venta requiere b&aacute;scula?:</label>
                                        <select id="selectBascula" name="selectBascula" required="required" class="form-control">
                                            <option value="0" <?php echo ($arrayProducto['balanza'] == 0) ? 'selected' : '';?>>No</option>
                                            <option value="1" <?php echo ($arrayProducto['balanza'] == 1) ? 'selected' : '';?>>Si</option>
                                        </select>
                                        <p class="help-block">&nbsp;</p>
                                    </div>
                                </div>


                            </div>

                        </form>
                        <!-- /.col-lg-6 (nested) -->
                    </div>
                    <!-- /.row (nested) -->
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
    </div>
    <div class="row">
        <div class="<?php echo ($arrayProducto['balanza'] == 0) ? 'col-lg-12' : 'col-lg-6 col-lg-offset-3';?>" id="dvCol" style="transition-duration:0.31s">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Lista de Precios "<?php echo $arrayProducto['nombrelargo'];?>"
                </div>
                <div class="panel-body">
                    <div class="table-responsive" id="divListaPrecios">
                        <table class="table table-hover" id="tablaPrecios" >
                <?php       if ($arrayProducto['balanza'] == 1)
                            {
                ?>
                            <thead>
                                <tr>
                                    <th><b>PRECIOS</b></th>
                                    <th>Precio x Kg</th>
                                    <th>Desc x Kg</th>
                                    <th>Utilidad x Kg</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <b>Menudeo</b>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputPrimeroFila" id="inputMenPxU" value="<?php echo number_format($arrayPrecioMenudeo['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenDxU" value="<?php echo number_format($arrayPrecioMenudeo['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputUltimoFila" id="inputMenUxU" value="<?php echo number_format($arrayPrecioMenudeo['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Medio Mayoreo</b></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMedPxU" value="<?php echo number_format($arrayPrecioMediomay['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMedDxU" value="<?php echo number_format($arrayPrecioMediomay['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMedUxU" value="<?php echo number_format($arrayPrecioMediomay['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Mayoreo</b></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMayPxU" value="<?php echo number_format($arrayPrecioMayoreo['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMayDxU" value="<?php echo number_format($arrayPrecioMayoreo['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMayUxU" value="<?php echo number_format($arrayPrecioMayoreo['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Especial</b></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroFila inputUltimoColumna" id="inputEspPxU" value="<?php echo number_format($arrayPrecioEspecial['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspDxU" value="<?php echo number_format($arrayPrecioEspecial['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna inputUltimoFila" id="inputEspUxU" value="<?php echo number_format($arrayPrecioEspecial['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                <?php
                        }
                        else
                        {
                ?>
                        <table class="table table-hover" id="tablaPrecios">
                            <thead>
                                <tr>
                                    <th><b>PRECIOS</b></th>
                                    <th>Precio x Paquete</th>
                                    <th>Desc x Paquete</th>
                                    <th>Utilidad x Paquete</th>
                                    <th>Precio x Unidad</th>
                                    <th>Desc x Unidad</th>
                                    <th>Utilidad x Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <b>Menudeo</b>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputPrimeroFila" id="inputMenPxP" value="<?php echo number_format($arrayPrecioMenudeo['precioXpaquete'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenDxP" value="<?php echo number_format($arrayPrecioMenudeo['descuentoXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenUxP" value="<?php echo number_format($arrayPrecioMenudeo['utilidadXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenPxU" value="<?php echo number_format($arrayPrecioMenudeo['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna" id="inputMenDxU" value="<?php echo number_format($arrayPrecioMenudeo['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroColumna inputUltimoFila" id="inputMenUxU" value="<?php echo number_format($arrayPrecioMenudeo['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Medio Mayoreo</b></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMedPxP" value="<?php echo number_format($arrayPrecioMediomay['precioXpaquete'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMedDxP" value="<?php echo number_format($arrayPrecioMediomay['descuentoXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMedUxP" value="<?php echo number_format($arrayPrecioMediomay['utilidadXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable" id="inputMedPxU" value="<?php echo number_format($arrayPrecioMediomay['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMedDxU" value="<?php echo number_format($arrayPrecioMediomay['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMedUxU" value="<?php echo number_format($arrayPrecioMediomay['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Mayoreo</b></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroFila" id="inputMayPxP" value="<?php echo number_format($arrayPrecioMayoreo['precioXpaquete'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMayDxP" value="<?php echo number_format($arrayPrecioMayoreo['descuentoXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMayUxP" value="<?php echo number_format($arrayPrecioMayoreo['utilidadXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable" id="inputMayPxU" value="<?php echo number_format($arrayPrecioMayoreo['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable" id="inputMayDxU" value="<?php echo number_format($arrayPrecioMayoreo['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoFila" id="inputMayUxU" value="<?php echo number_format($arrayPrecioMayoreo['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>Especial</b></td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputPrimeroFila inputUltimoColumna" id="inputEspPxP" value="<?php echo number_format($arrayPrecioEspecial['precioXpaquete'],3);?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspDxP" value="<?php echo number_format($arrayPrecioEspecial['descuentoXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspUxP" value="<?php echo number_format($arrayPrecioEspecial['utilidadXpaquete'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <span class="input-group-addon">$</span>
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspPxU" value="<?php echo number_format($arrayPrecioEspecial['precioXunidad'],3);?>">
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna" id="inputEspDxU" value="<?php echo number_format($arrayPrecioEspecial['descuentoXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                    <td class="sinBascula">
                                        <div class="input-group">
                                            <input type="number" min="0" class="form-control inputTable inputUltimoColumna inputUltimoFila" id="inputEspUxU" value="<?php echo number_format($arrayPrecioEspecial['utilidadXunidad'],3);?>">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                <?php
                        }
                ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</div>
<script src="../startbootstrap/vendor/myJs/listaProductos.js"></script>
<script>
$(document).ready(function()
{
    //actualizarPrecioLista($("#divPrecioLista > #inputPrecioLista"));
});
// (function() {
//
//     bar                 = $('#divfileupload');
//     percent             = $('.sr-only');
//     divstatus           = $('#divstatus');
//     progressUpload      = $("#progressUpload");
//     var btnImg          = $("#btnAnadirImg");
//     var hidden          = $("#hiddenImgBinario");
//     var hiddenTipo		= $("#hiddenImgTipo");
//     $('#formfileupload').ajaxForm({
//         beforeSend: function() {
//             //divstatus.empty();
//             var percentVal = '0%';
//             bar.width(percentVal)
//             percent.html(percentVal);
//             progressUpload.show();
//
//         },
//         uploadProgress: function(event, position, total, percentComplete) {
//             var percentVal = percentComplete + '%';
//             bar.width(percentVal)
//             percent.html(percentVal);
//         },
//         success: function(xhr) {
//             var percentVal = '100%';
//             bar.width(percentVal)
//             percent.html(percentVal);
//             if(xhr.exito == 1)
//             {
//                 btnImg.removeClass('btn-outline');
//                 btnImg.addClass('btn-success');
//                 btnImg.removeClass('btn-info');
//                 btnImg.html('<i class="fa fa-times fa-2x" aria-hidden="true"></i></br>Eliminar');
//                 btnImg.prop('id','btnEliminarImg');
//                 $("#imgSrc").attr('src',xhr.src);
//                 divstatus.html(xhr.respuesta);
//                 hidden.val(xhr.binario);
//                 hiddenTipo.val(xhr.tipo);
//                 $("#imgToggle").val('1');
//                 $("#imgCtrl").val('1');
//                 $("#inputNombreCorto").focus();
//             }
//             else
//             {
//                 //$("#divRespuesta").html(xhr.respuesta);
//                 $("#inputReset").click();
//                 $("#inputNombreCorto").focus();
//             }
//         },
//         complete: function(xhr) {
//             //divstatus.html(xhr.responseText);
//             console.log(xhr);
//             progressUpload.hide();
//         }
//     });
//
// })();
</script>
<?php
}
?>
