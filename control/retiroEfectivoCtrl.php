<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
$ipserver = $_SERVER['SERVER_ADDR'];
require ('../conecta/bd.php');
require ("../conecta/sesion.class.php");
$sesion = new sesion();
require ("../conecta/cerrarOtrasSesiones.php");
require ("../conecta/usuarioLogeado.php");
require ('../conecta/validarUsuario.php');
if( logueado($idSesion,$idUsuario,$mysqli) == false || $idSesion == false)
{
    header("Location: /pventa_std/pages/salir.php");
}
else
{
    function responder($response, $mysqli)
    {
        header('Content-Type: application/json');
        echo json_encode($response, JSON_FORCE_OBJECT);
        $mysqli->close();
        exit;
    }
    $usuario                = $_POST["usuario"];
    $password               = $_POST["password"];
    $validar                = validarUsuario($usuario, $password, $mysqli);
    if($validar['tipousuario'] == 1)
    {
?>
        <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 ">
            <div class="panel panel-info">
                <div class="panel-heading">
                </div>
                <div class="panel-body">
                    <div id="recibo" style="display:none"></div>
                    <form id="formRetiro" role="form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                        <div class="form-group">
                            <label>Monto a retirar</label>
                            <div class="form-group input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" class="form-control" id="inputMontoRetirar" name="inputMontoRetirar">
                            </div>
                            <p>Observaciones</p>
                            <input type="text" class="form-control" id="inputObsRetirar" name="inputObsRetirar">
                        </div>
                        <input type="button" name="btnRetirar" value="Retirar" id="btnRetirar" class="btn btn-lg btn-info btn-block">
                        <input type="hidden" name="xArs" value="<?php echo $validar['id']; ?>">
                        <input type="submit" name="submitRetirar" id="submitRetirar" style="display:none">
                    </form>
                </div>
            </div>
        </div>
    <script>
    $("#formRetiro").submit(function(e){
            //alert('submit intercepted');
            e.preventDefault(e);
        });
        $( "#dialog-confirm-retirar" ).dialog(
        {
            resizable: true,
            height: "auto",
            width: "auto",
            modal: true,
            autoOpen: false,
            position: { my: 'top', at: 'top+1' },
            show:
            {
                effect: "slide",
                duration: 300,
                direction: 'up'
            },
            open: function()
            {
                $("#inputMontoRetirar").parent().removeClass('has-error');
                $("#divRespuesta").empty();
            },
            close: function()
            {
            },
            buttons:
            [
                {
                    text: "Aceptar",
                    id:   "btnRealizarRetiro",
                    click: function()
                    {

                        $("#btnRealizarRetiro").html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>Espera...');
                        dialogo = $( this );
                        $.ajax(
                        {
                           type: "POST",
                           url: "../control/retiroEfectivoBd.php",
                           data: $("#formRetiro").serialize() // Adjuntar los campos del formulario enviado.
                       }).done(function(p)
                       {
                           console.log(p);

                           if(p.status == 0)
                           {
                               $("#divRespuesta").html(p.respuesta);
                               if (p.numerico == 0)
                               {
                                   $("#btnRealizarRetiro").html('Aceptar');
                                    dialogo.dialog( "close" );
                                    input = $("#inputMontoRetirar");
                                    input.parent().addClass("has-error");
                                    input.focus();
                               }
                           }
                           else
                           {
                               $('#recibo').html(p.recibo).promise().done(function()
                               {
                                   //your callback logic / code here
                                   $('#recibo').printThis();
                                   setTimeout(function()
                                   {
                                       window.location.assign(p.url);
                                   }, 2350);
                               });

                           }
                       }).fail(function(p)
                       {
                            console.log(p);
                            dialogo.dialog( "close" );
                           alert("Servidor no disponible, ponte en contacto con el administrador del sistema");
                       });
                    }
                },
                {
                    text: "Cancelar",
                    click: function()
                    {
                        $( this ).dialog( "close" );
                    }
                }
            ]
        });
        $("#btnRetirar").click(function()
        {
            $( "#dialog-confirm-retirar" ).dialog("open");
        });
        $("#inputObsRetirar,#inputMontoRetirar").keydown(function(e)
        {
            //e.preventDefault;
            if(e.keyCode == 13)
                $("#btnRetirar").click();
        });
        $("#inputMontoRetirar").focus();

        </script>
        <div id="dialog-confirm-retirar" title="Retirar efectivo">
            <p>
                <i class="fa fa-exclamation-circle" aria-hidden="true"></i> ¿Seguro que deseas continuar? <br> Se imprimir&aacute; un ticket como comprobante
            </p>
        </div>
<?php
    }
    else
    {
        $response['status'] = 0;
        $response['respuesta'] =   "<i class='fa fa-key fa-pull-left' aria-hidden='true'></i> Nombre de usuario o contraseña incorrectos";
        responder($response, $mysqli);
    }
}
?>
