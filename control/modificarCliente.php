<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
require_once ('../conecta/bd.php');
require_once ("../conecta/sesion.class.php");
$sesion = new sesion();
$usuario = $sesion->get("nick");
if( $usuario == false )
{
    header("Location: /pventa_std/pages/login.php");
}
else
{
    $idCliente = $_POST['idCliente'];
    $sql = "SELECT *
            FROM clientes
            WHERE id = $idCliente
            AND activo = 1
            LIMIT 1";
    $result = $mysqli->query($sql);
    $arrayCliente = $result->fetch_assoc();

?>
<div class="col-lg-12">
        <div class="panel-body">
            <div class="row">
                <form role="form" method="POST" action="agregarCliente.php" id="formSubmit" onsubmit="submitForm();">
                    <div class="col-lg-12">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divRazon">
                                <label class="control-label">Raz&oacute;n Social</label>
                                <input id="inputRazon" name="inputRazon" autocomplete ="off" value="<?php echo $arrayCliente['rsocial'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divRepresentante">
                                <label class="control-label">Representante</label>
                                <input id="inputRepresentante" name="inputRepresentante" autocomplete ="off" value="<?php echo $arrayCliente['representante'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable">
                                <label class="control-label">Tipo de precio</label>
                                <select id="selectTipoPrecio" name="selectTipoPrecio" required="required" class="form-control">
                <?php
                    $tipoPrecioActual = $arrayCliente['tipoprecio'];
                    $sql = "SELECT * FROM tipoprecios ORDER BY id ASC";
                    if ($resultTipoPrecios = $mysqli->query($sql))
                    {
                        while ($arrayTipoPrecios = $resultTipoPrecios->fetch_assoc())
                        {
                            $idTipoPrecio = $arrayTipoPrecios['id'];
                            $nombreTipoPrecio = $arrayTipoPrecios['nombrelargo'];
                            if($tipoPrecioActual == $idTipoPrecio)
                                echo "<option selected value='$idTipoPrecio'>$nombreTipoPrecio</option>";
                            else
                                echo "<option value='$idTipoPrecio'>$nombreTipoPrecio</option>";
                        }
                    }
                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divCalle">
                                <label class="control-label">Calle</label>
                                <input id="inputCalle" name="inputCalle" autocomplete ="off"value="<?php echo $arrayCliente['calle'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divNumeroExt">
                                <label class="control-label">N&uacute;mero Ext.</label>
                                <input id="inputNumeroExt" name="inputNumeroExt" autocomplete ="off"value="<?php echo $arrayCliente['numeroext'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divNumeroInt">
                                <label class="control-label">N&uacute;mero Int.</label>
                                <input id="inputNumeroInt" name="inputNumeroInt" autocomplete ="off"value="<?php echo $arrayCliente['numeroint'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divPoblacion">
                                <label class="control-label">Poblaci&oacute;n</label>
                                <input id="inputPoblacion" name="inputPoblacion" autocomplete ="off"value="<?php echo $arrayCliente['poblacion'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divMunicipio">
                                <label class="control-label">Municipio</label>
                                <input id="inputMunicipio" name="inputMunicipio" autocomplete ="off"value="<?php echo $arrayCliente['municipio'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divCol">
                                <label class="control-label">Colonia</label>
                                <input id="inputCol" name="inputCol" autocomplete ="off"value="<?php echo $arrayCliente['colonia'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable">
                                <label class="control-label">Estado</label>
                                <select id="selectEstado" name="selectEstado" required="required" class="form-control">
                <?php
                    $idEstadoActual = $arrayCliente['estado'];
                    $sql = "SELECT * FROM estados ORDER BY id ASC";
                    if ($resultEstados = $mysqli->query($sql))
                    {
                        while ($arrayEstados = $resultEstados->fetch_assoc())
                        {
                            $idEstado = $arrayEstados['id'];
                            $nombreEstado = $arrayEstados['nombreLargo'];
                            if($idEstado == $idEstadoActual)
                                echo "<option selected value='$idEstado'>$nombreEstado</option>";
                            else
                                echo "<option value='$idEstado'>$nombreEstado</option>";
                        }
                    }
                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divCP">
                                <label class="control-label">C&oacute;digo Postal</label>
                                <input id="inputCP" name="inputCP" autocomplete ="off"value="<?php echo $arrayCliente['cp'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divTelefono1">
                                <label class="control-label">Tel&eacute;fono 1</label>
                                <input id="inputTelefono1" name="inputTelefono1" autocomplete ="off" value="<?php echo $arrayCliente['telefono1'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divTelefono2">
                                <label class="control-label">Tel&eacute;fono 2</label>
                                <input id="inputTelefono2" name="inputTelefono2" autocomplete ="off" value="<?php echo $arrayCliente['telefono2'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divCelular">
                                <label class="control-label">Celular</label>
                                <input id="inputCelular" name="inputCelular" autocomplete ="off" value="<?php echo $arrayCliente['celular'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divRfc">
                                <label class="control-label">RFC</label>
                                <input id="inputRfc" name="inputRfc" autocomplete ="false" value="<?php echo $arrayCliente['rfc'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divEmail">
                                <label class="control-label">E-mail</label>
                                <input id="inputEmail" name="inputEmail" autocomplete ="false" value="<?php echo $arrayCliente['email'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divDias">
                                <label class="control-label">D&iacute;as cr&eacute;dito</label>
                                <input type="number" id="inputDias" name="inputDias" autocomplete ="false" value="<?php echo $arrayCliente['diasCredito'];?>" class="form-control">
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.col-lg-6 (nested) -->
            </div>
            <!-- /.row (nested) -->
    </div>
</div>
<?php
}
?>
