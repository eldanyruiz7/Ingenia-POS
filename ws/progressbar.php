<?php
header( 'Content-type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Documento sin título</title>
<link href="../startbootstrap/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../startbootstrap/dist/css/sb-admin-2.css" rel="stylesheet">
<link href="../startbootstrap/vendor/morrisjs/morris.css" rel="stylesheet">
<link href="../startbootstrap/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="../startbootstrap/vendor/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css">
<link href="../startbootstrap/vendor/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet" type="text/css">
<link href="../startbootstrap/vendor/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css">
<script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
<script src="../startbootstrap/vendor/jquery-ui/jquery-ui.min.js"></script>
<script src="../startbootstrap/vendor/bootstrap/js/bootstrap.min.js"></script>

</head>
<body>
    <iframe id="ifram">

    </iframe>
    <button id="btn">aa</button>
</body>
<script>
    function ocultariframe()
    {
        //$("#ifram").hide();
        //$("#ifram").css("visibility","hidden");
        //alert("completo");
        console.log("completo");
    }
    $(document).ready(function()
    {
        $("#btn").click(function()
        {
            $("#ifram").css("visibility","visible");
            $("#ifram").attr('src','gen_factura.php?id=4');
            //$("#iframe").contentWindow.location.reload(true);
            //$('#ifram')[0].contentWindow.location.reload(true);
            $('iframe').attr('src', $('iframe').attr('src'));

            //$('#ifram').contentWindow.location.reload(true);
            //$("#iframe").reload();
        });
    });
</script>
</html>
