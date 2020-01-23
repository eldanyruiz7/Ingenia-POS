<?php
date_default_timezone_set('America/Mexico_City');

require "../conecta/bd.php";
require "corteCajaRecibo.php";
?>

<div id="recibo">
    <?php echo genReciboCorteCaja(14,$mysqli); ?>
</div>
