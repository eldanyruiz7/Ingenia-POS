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
    $idProveedor = $_POST['idProveedor'];
    $sql = "SELECT *
            FROM proveedores
            WHERE id = $idProveedor
            AND activo = 1
            LIMIT 1";
    $result = $mysqli->query($sql);
    $arrayProveedor = $result->fetch_assoc();
?>
<div class="col-lg-12">
        <div class="panel-body">
            <div class="row">
                <form role="form" method="POST" action="agregarProveedor.php" id="formSubmit" onsubmit="submitForm();">
                    <div class="col-lg-12">
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group divControlable" id="divRSocial">
                                <label class="control-label">Nombre o Raz&oacute;n social</label>
                                <input id="inputRSocial" name="inputRSocial" autocomplete ="off" value="<?php echo $arrayProveedor['rsocial'];?>" required="required" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group divControlable" id="divRepresentante">
                                <label class="control-label">Representante</label>
                                <input id="inputRepresentante" name="inputRepresentante" value="<?php echo $arrayProveedor['representante'];?>" autocomplete ="off" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-12">
                            <div class="form-group divControlable" id="divDireccion">
                                <label class="control-label">Direcci&oacute;n</label>
                                <input id="inputDireccion" name="inputDireccion" autocomplete ="off" value="<?php echo $arrayProveedor['direccion'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divTelefono">
                                <label class="control-label">Tel&eacute;fono</label>
                                <input id="inputTelefono" name="inputTelefono" autocomplete ="off" value="<?php echo $arrayProveedor['telefono'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divRfc">
                                <label class="control-label">RFC</label>
                                <input id="inputRfc" name="inputRfc" autocomplete ="false" value="<?php echo $arrayProveedor['rfc'];?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group divControlable" id="divEmail">
                                <label class="control-label">E-mail</label>
                                <input id="inputEmail" name="inputEmail" autocomplete ="false" value="<?php echo $arrayProveedor['email'];?>" class="form-control">
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
