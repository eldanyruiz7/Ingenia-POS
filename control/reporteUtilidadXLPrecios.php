<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
date_default_timezone_set('America/Mexico_City');
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
    $sql            = "SELECT * FROM configuracion WHERE id = 1 LIMIT 1";
    $resultConfig   = $mysqli->query($sql);
    $rowConfig      = $resultConfig->fetch_assoc();
    require "../startbootstrap/vendor/fpdf/fpdf.php";

    $pdf = new FPDF('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetMargins(10,11);
    $pdf->SetFillColor(225);
    $pdf->SetXY(4,7);
    $pdf->SetFont('Courier','I',15);
    //Celda en la posición 40,10, con borde, sin salto de línea, a la derecha
    //$pdf->Cell(40,10,utf8_decode('¡Hola, Mundo!'),1,0,'R');
    $pdf->Cell(100,7,utf8_decode($rowConfig['nombreComercio']),0,1,'L');
    $pdf->SetFont('Courier','',7);
    $pdf->Cell(100,4,utf8_decode($rowConfig['nombreRepresentante']),0,1,'L');
    $pdf->Cell(100,4,utf8_decode($rowConfig['rfcComercio']),0,1,'L');
    $pdf->Cell(100,4,utf8_decode($rowConfig['direccionComercio']),0,1,'L');
    $pdf->SetFont('Courier','BU',16);
    $pdf->Cell(208,10,utf8_decode('REPORTE DE UTILIDADES POR LISTA DE PRECIOS'),0,1,'C');
    $existencia         = $_POST['radioArts'];
    $orden              = $_POST['selectOrden'];
    $campoOrden         = '';
    $ascdesc            = $_POST['selectAsc'];
    $ascOrden           = '';
    switch ($orden)
    {
        case 1:
            $campoOrden = 'productos.id';
            break;
        case 2:
            $campoOrden = 'productos.existencia';
            break;
        default:
            $campoOrden = 'productos.nombrelargo';
            break;
    }
    switch ($ascdesc)
    {
        case 1:
            $ascOrden   = 'ASC';
            break;

        default:
            $ascOrden   = 'DESC';
            break;
    }
    if(isset($_POST['selectDepartamentos']))
    foreach ($_POST['selectDepartamentos'] as $depto)
    {
        //echo $selectedOption."\n";
        $pdf->SetFont('Arial','',8);
        $pdf->SetDrawColor(64);
        $pdf->SetTextColor(255);
        $sql = "SELECT nombre FROM departamentos WHERE id = $depto LIMIT 1";
        $resultNDep = $mysqli->query($sql);
        $nombDep = $resultNDep->fetch_assoc();
        $pdf->SetFillColor(64, 64, 64);
        $pdf->Cell(21,3,utf8_decode($nombDep['nombre']),0,1,'C',1);
        $pdf->SetFont('Courier','B',7);
        $pdf->SetDrawColor(255);
        $pdf->SetTextColor(255);
        $pdf->Cell(15,3.9,utf8_decode('ID'),0,0,'C',1);
        $pdf->Cell(24,3.9,utf8_decode('CODIGO'),0,0,'C',1);
        $pdf->Cell(15,3.9,utf8_decode('UNIDAD'),0,0,'C',1);
        $pdf->Cell(90,3.9,utf8_decode('NOMBRE'),1,0,'C',1);
        $pdf->Cell(15,3.9,utf8_decode('$ LISTA'),0,0,'C',1);
        $pdf->Cell(15,3.9,utf8_decode('F. CONV.'),0,0,'C',1);
        $pdf->Cell(22,3.9,utf8_decode(''),0,1,'C',1);

        $sql = "SELECT
                    productos.id                    AS      idProducto,
                    productos.codigo                AS      codigo,
                    productos.codigo2               AS      codigo2,
                    productos.nombreLargo           AS      nombreLargo,
                    productos.existencia            AS      existencia,
                    productos.factorconversion      AS      factorConversion,
                    precios.preciolista             AS      precioLista,

                    departamentos.nombre            AS      departamento,
                    unidadesventa.nombre            AS      unidadVenta
                    FROM        productos
                    INNER JOIN  departamentos
                    ON          productos.departamento = departamentos.id
                    INNER JOIN  unidadesventa
                    ON          productos.unidadventa = unidadesventa.id
                    INNER JOIN  precios
                    ON          productos.id = precios.producto
                    WHERE       productos.activo = 1 AND departamento = $depto
                    GROUP BY    productos.id
                    ORDER BY    $campoOrden $ascOrden";
        if($resultProd = $mysqli->query($sql))
        {
            $pdf->SetDrawColor(0);
            $stripper = 0;
            $exist = 0;
            $pdf->SetTextColor(0);
            $pdf->SetFillColor(220);
            while ($rowProd = $resultProd->fetch_assoc())
            {
                if($existencia == 2)
                {
                    if($rowProd['existencia'] == 0)
                        continue;
                }
                elseif ($existencia == 3)
                {
                    if($rowProd['existencia'] > 0)
                        continue;
                }
                $idProducto = $rowProd['idProducto'];
                $sql_p  = " SELECT *
                            FROM detalleprecios
                            INNER JOIN tipoprecios
                            ON detalleprecios.tipoprecio = tipoprecios.id
                            WHERE producto = $idProducto
                            ORDER BY tipoprecio ASC";
                $res_p = $mysqli->query($sql_p);
                $stripper++;
                // if($stripper%2==0)
                //     $pdf->SetFillColor(220);
                // else
                //     $pdf->SetFillColor(255);
                $pdf->SetFont('Times','',9.4);
                $pdf->Cell(15,3.4,utf8_decode(str_pad($rowProd['idProducto'], 6, "0", STR_PAD_LEFT)),0,0,'R',1);
                $cod = (strlen($rowProd['codigo']) > 0) ? $rowProd['codigo'] : $rowProd['codigo2'];
                 $pdf->Cell(24,3.4,utf8_decode($cod),0,0,'C',1);
                //$pdf->Cell(41,2.5,utf8_decode($rowProd['nombreCorto']),0,0,'L',1);
                $pdf->Cell(15,3.4,utf8_decode($rowProd['unidadVenta']),0,0,'C',1);
                $pdf->Cell(90,3.4,utf8_decode($rowProd['nombreLargo']),0,0,'L',1);
                $pdf->Cell(15,3.4,utf8_decode(number_format($rowProd['precioLista'],2,".",",")),0,0,'R',1);
                $pdf->Cell(15,3.4,utf8_decode($rowProd['factorConversion']),0,0,'C',1);
                $pdf->Cell(22,3.9,utf8_decode(''),0,1,'C',1);
                $pdf->SetFont('Times','',8);
                while ($row_p = $res_p->fetch_assoc())
                {
                    $pdf->Cell(20,3,$row_p['nombre_2'],0,0,'L',0);
                    $pdf->Cell(14,3,'Paquete:',0,0,'C',0);
                    $pdf->Cell(14,3,"$".number_format($row_p['precioXpaquete'],2,".",""),0,0,'R',1);
                    $pdf->Cell(9,3,'Utilidad:',0,0,'C',0);
                    $pdf->Cell(14,3,number_format($row_p['utilidadXpaquete'],3,".","")."%",0,0,'R',1);
                    $pdf->Cell(14,3,'Unidad:',0,0,'C',0);
                    $pdf->Cell(14,3,"$".number_format($row_p['precioXunidad'],2,".",""),0,0,'R',1);
                    $pdf->Cell(9,3,'Utilidad:',0,0,'C',0);
                    $pdf->Cell(14,3,number_format($row_p['utilidadXunidad'],3,".","")."%",0,1,'R',1);

                }
                $exist+= $rowProd['existencia'];
            }
            $pdf->SetDrawColor(0);
            $pdf->SetFillColor(255);
            $pdf->SetFont('Times','B',8);
            $pdf->Cell(13,2.5,utf8_decode(''),0,1,'C',0);
            $pdf->Cell(187,0,utf8_decode(''),1,1,'C',1);
            $pdf->Cell(43,2.5,utf8_decode('TOTAL EXIST:'),0,0,'C',0);
            $pdf->Cell(11,2.5,utf8_decode($exist),1,0,'R',0);
            $pdf->Cell(54,2.5,utf8_decode('TOTAL ARTÍCULOS:'),0,0,'C',0);
            $pdf->Cell(11,2.5,utf8_decode($stripper),1,1,'R',0);
            $pdf->Cell(13,2.5,utf8_decode(''),0,1,'C',0);
        }
    }
    else
    {
        //$pdf->SetFont('Courier','',10);
        //$pdf->Cell(239,6,utf8_decode(preg_replace('/\&(.)[^;]*;/', '\\1', $sql)),0,0,'R',0);
        //$sumaTotal = number_format('', 2, ".", ",");
        $pdf->SetFont('Courier','I',10);
        $pdf->Cell(120,6,utf8_decode('(SIN INFORMACIÓN)'),0,1,'R',0);
    }
    //$pdf->Output('D');
    $pdf->Output();
}
     ?>
