<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
    $pdf->Cell(208,10,utf8_decode('REPORTE DE INVENTARIO'),0,1,'C');
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
            $campoOrden = 'unidadesventa.nombre';
            break;
        case 3:
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
    if (isset($_POST['chkAgruparDepto']))
    {
        if(isset($_POST['selectDepartamentos']))
        {
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
                $pdf->Cell(12,3,utf8_decode('ID.'),0,0,'C',1);
                $pdf->Cell(20,3,utf8_decode('CODIGO'),0,0,'C',1);
                $pdf->Cell(10,3,utf8_decode('UNIDAD'),0,0,'C',1);
                $pdf->Cell(11,3,utf8_decode('EXIST'),0,0,'C',1);
                $pdf->Cell(76,3,utf8_decode('NOMBRE'),0,0,'C',1);
                //$pdf->Cell(65,2.5,utf8_decode('DESCRIPCION'),1,0,'C',1);
                //$pdf->Cell(15,2.5,utf8_decode('SHCP'),0,0,'C',1);
                if (isset($_POST['mUltimo']))
                    $pdf->Cell(14,2.5,utf8_decode('$ LISTA'),0,0,'C',1);
                else
                    $pdf->Cell(14,2.5,utf8_decode(''),0,0,'C',1);
                if (isset($_POST['mPublico']))
                    $pdf->Cell(16,2.5,utf8_decode('$ PÚBLICO'),0,0,'C',1);
                else
                    $pdf->Cell(16,2.5,utf8_decode(''),0,0,'C',1);
                if (isset($_POST['mPromedio']))
                    $pdf->Cell(17,2.5,utf8_decode('$ LISTA PROM'),0,0,'C',1);
                else
                    $pdf->Cell(17,2.5,utf8_decode(''),0,0,'C',1);
                $pdf->Cell(10,2.5,utf8_decode('IVA'),0,0,'C',1);
                $pdf->Cell(10,2.5,utf8_decode('IEPS'),0,1,'C',1);
                $sql = "SELECT
                            productos.id                    AS      idProducto,
                            productos.codigo                AS      codigo,
                            productos.codigo2               AS      codigo2,
                            productos.nombrecorto           AS      nombreCorto,
                            productos.nombrelargo           AS      nombreLargo,
                            productos.existencia            AS      existencia,
                            productos.claveSHCP             AS      claveSHCP,
                            productos.IVA                   AS      IVA,
                            productos.IEPS                  AS      IEPS,
                            departamentos.nombre            AS      departamento,
                            unidadesventa.nombre            AS      unidadVenta,
                            precios.preciolista             AS      precioLista,
                            detalleprecios.precioXunidad    AS      precioPublico,
                            IF((SELECT AVG(preciolista)
                                FROM detallecompra
                                WHERE producto = idProducto
                                ORDER BY detallecompra.id DESC LIMIT 10 ) IS NULL,
                                precioLista,   (SELECT AVG(preciolista)
                                                FROM detallecompra
                                                WHERE producto = idProducto
                                                ORDER BY detallecompra.id DESC LIMIT 10)) AS precioProm
                            FROM        productos
                            INNER JOIN  departamentos
                            ON          productos.departamento = departamentos.id
                            INNER JOIN  unidadesventa
                            ON          productos.unidadventa = unidadesventa.id
                            INNER JOIN  precios
                            ON          productos.id = precios.producto
                            INNER JOIN  detalleprecios
                            ON          productos.id = detalleprecios.producto
                            WHERE       productos.activo = 1 AND departamento = $depto
                            GROUP BY    productos.id
                            ORDER BY    $campoOrden $ascOrden";
                //$pdf->Cell(8,6,utf8_decode('T'),1,0,'C',1);
                //$pdf->Cell(25,6,utf8_decode('MONTO'),1,1,'C',1);
                if($resultProd = $mysqli->query($sql))
                {
                    $stripper = 0;
                    $exist = 0;
                    $pdf->SetTextColor(0);
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
                        $stripper++;
                        if($stripper%2==0)
                            $pdf->SetFillColor(220);
                        else
                            $pdf->SetFillColor(255);
                        $pdf->SetFont('Times','',8.3);

                        $pdf->Cell(12,3,utf8_decode(str_pad($rowProd['idProducto'], 6, "0", STR_PAD_LEFT)),0,0,'R',1);
                        $cod = (strlen($rowProd['codigo']) > 0) ? $rowProd['codigo'] : $rowProd['codigo2'];
                        $pdf->Cell(20,3,utf8_decode($cod),0,0,'C',1);
                        //$pdf->Cell(41,2.5,utf8_decode($rowProd['nombreCorto']),0,0,'L',1);
                        $pdf->Cell(10,3,utf8_decode($rowProd['unidadVenta']),0,0,'L',1);
                        $pdf->Cell(11,3,utf8_decode($rowProd['existencia']),0,0,'R',1);
                        $exist+= $rowProd['existencia'];
                        $pdf->Cell(76,3,utf8_decode($rowProd['nombreLargo']),0,0,'L',1);
                        //$pdf->Cell(65,2.5,utf8_decode($sql),0,0,'L',1);
                        //$pdf->Cell(15,2.5,utf8_decode($rowProd['claveSHCP']),0,0,'L',1);
                        if (isset($_POST['mUltimo']))
                            $pdf->Cell(14,3,utf8_decode(number_format($rowProd['precioLista'],2,".",",")),0,0,'R',1);
                        else
                            $pdf->Cell(14,3,utf8_decode(''),0,0,'R',1);
                        if (isset($_POST['mPublico']))
                            $pdf->Cell(16,3,utf8_decode(number_format($rowProd['precioPublico'],2,".",",")),0,0,'R',1);
                        else
                            $pdf->Cell(16,3,utf8_decode(''),0,0,'R',1);
                        if (isset($_POST['mPromedio']))
                            $pdf->Cell(17,3,utf8_decode(number_format($rowProd['precioProm'],2,".",",")),0,0,'R',1);
                        else
                            $pdf->Cell(17,3,utf8_decode(''),0,0,'R',1);
                        $iva    = ($rowProd['IVA'] > 0) ? $rowProd['IVA'].'%' : '';
                        $pdf->Cell(10,3,utf8_decode($iva),0,0,'C',1);
                        $ieps   = ($rowProd['IEPS'] > 0) ? $rowProd['IEPS'].'%' : '';
                        $pdf->Cell(10,3,utf8_decode($ieps),0,1,'C',1);
                    }
                    $pdf->SetDrawColor(0);
                    $pdf->SetFillColor(255);
                    $pdf->SetFont('Times','B',8);
                    $pdf->Cell(13,2.5,utf8_decode(''),0,1,'C',0);
                    $pdf->Cell(196,0,utf8_decode(''),1,1,'C',1);
                    $pdf->Cell(43,2.5,utf8_decode('TOTAL EXIST:'),0,0,'C',0);
                    $pdf->Cell(11,2.5,utf8_decode($exist),1,0,'R',0);
                    $pdf->Cell(54,2.5,utf8_decode('TOTAL ARTÍCULOS:'),0,0,'C',0);
                    $pdf->Cell(11,2.5,utf8_decode($stripper),1,1,'R',0);
                    $pdf->Cell(13,2.5,utf8_decode(''),0,1,'C',0);
                }
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
    }
    else
    {
        $pdf->SetFont('Arial','',8);
        $pdf->SetDrawColor(64);
        $pdf->SetTextColor(255);
        // $sql = "SELECT nombre FROM departamentos WHERE id = $depto LIMIT 1";
        // $resultNDep = $mysqli->query($sql);
        // $nombDep = $resultNDep->fetch_assoc();
        $pdf->SetFillColor(64, 64, 64);
        $pdf->Cell(21,3,utf8_decode("TODOS"),0,1,'C',1);
        //$pdf->Cell(21,3,utf8_decode($nombDep['nombre']),0,1,'C',1);
        $pdf->SetFont('Courier','B',7);
        $pdf->SetDrawColor(255);
        $pdf->SetTextColor(255);
        $pdf->Cell(12,3,utf8_decode('ID.'),0,0,'C',1);
        $pdf->Cell(20,3,utf8_decode('CODIGO'),0,0,'C',1);
        $pdf->Cell(10,3,utf8_decode('UNIDAD'),0,0,'C',1);
        $pdf->Cell(11,3,utf8_decode('EXIST'),0,0,'C',1);
        $pdf->Cell(76,3,utf8_decode('NOMBRE'),0,0,'C',1);
        //$pdf->Cell(65,2.5,utf8_decode('DESCRIPCION'),1,0,'C',1);
        //$pdf->Cell(15,2.5,utf8_decode('SHCP'),0,0,'C',1);
        if (isset($_POST['mUltimo']))
            $pdf->Cell(14,2.5,utf8_decode('$ LISTA'),0,0,'C',1);
        else
            $pdf->Cell(14,2.5,utf8_decode(''),0,0,'C',1);
        if (isset($_POST['mPublico']))
            $pdf->Cell(16,2.5,utf8_decode('$ PÚBLICO'),0,0,'C',1);
        else
            $pdf->Cell(16,2.5,utf8_decode(''),0,0,'C',1);
        if (isset($_POST['mPromedio']))
            $pdf->Cell(17,2.5,utf8_decode('$ LISTA PROM'),0,0,'C',1);
        else
            $pdf->Cell(17,2.5,utf8_decode(''),0,0,'C',1);
        $pdf->Cell(10,2.5,utf8_decode('IVA'),0,0,'C',1);
        $pdf->Cell(10,2.5,utf8_decode('IEPS'),0,1,'C',1);
        $sql = "SELECT
                    productos.id                    AS      idProducto,
                    productos.codigo                AS      codigo,
                    productos.codigo2               AS      codigo2,
                    productos.nombrecorto           AS      nombreCorto,
                    productos.nombrelargo           AS      nombreLargo,
                    productos.existencia            AS      existencia,
                    productos.claveSHCP             AS      claveSHCP,
                    productos.IVA                   AS      IVA,
                    productos.IEPS                  AS      IEPS,
                    departamentos.nombre            AS      departamento,
                    unidadesventa.nombre            AS      unidadVenta,
                    precios.preciolista             AS      precioLista,
                    detalleprecios.precioXunidad    AS      precioPublico,
                    IF((SELECT AVG(preciolista)
                        FROM detallecompra
                        WHERE producto = idProducto
                        ORDER BY detallecompra.id DESC LIMIT 10 ) IS NULL,
                        precioLista,   (SELECT AVG(preciolista)
                                        FROM detallecompra
                                        WHERE producto = idProducto
                                        ORDER BY detallecompra.id DESC LIMIT 10)) AS precioProm
                    FROM        productos
                    INNER JOIN  departamentos
                    ON          productos.departamento = departamentos.id
                    INNER JOIN  unidadesventa
                    ON          productos.unidadventa = unidadesventa.id
                    INNER JOIN  precios
                    ON          productos.id = precios.producto
                    INNER JOIN  detalleprecios
                    ON          productos.id = detalleprecios.producto
                    WHERE       productos.activo = 1
                    GROUP BY    productos.id
                    ORDER BY    $campoOrden $ascOrden";
        //$pdf->Cell(8,6,utf8_decode('T'),1,0,'C',1);
        //$pdf->Cell(25,6,utf8_decode('MONTO'),1,1,'C',1);
        if($resultProd = $mysqli->query($sql))
        {
            $stripper = 0;
            $exist = 0;
            $pdf->SetTextColor(0);
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
                $stripper++;
                if($stripper%2==0)
                    $pdf->SetFillColor(220);
                else
                    $pdf->SetFillColor(255);
                $pdf->SetFont('Times','',8.3);

                $pdf->Cell(12,3,utf8_decode(str_pad($rowProd['idProducto'], 6, "0", STR_PAD_LEFT)),0,0,'R',1);
                $cod = (strlen($rowProd['codigo']) > 0) ? $rowProd['codigo'] : $rowProd['codigo2'];
                $pdf->Cell(20,3,utf8_decode($cod),0,0,'C',1);
                //$pdf->Cell(41,2.5,utf8_decode($rowProd['nombreCorto']),0,0,'L',1);
                $pdf->Cell(10,3,utf8_decode($rowProd['unidadVenta']),0,0,'L',1);
                $pdf->Cell(11,3,utf8_decode($rowProd['existencia']),0,0,'R',1);
                $exist+= $rowProd['existencia'];
                $pdf->Cell(76,3,utf8_decode($rowProd['nombreLargo']),0,0,'L',1);
                //$pdf->Cell(65,2.5,utf8_decode($sql),0,0,'L',1);
                //$pdf->Cell(15,2.5,utf8_decode($rowProd['claveSHCP']),0,0,'L',1);
                if (isset($_POST['mUltimo']))
                    $pdf->Cell(14,3,utf8_decode(number_format($rowProd['precioLista'],2,".",",")),0,0,'R',1);
                else
                    $pdf->Cell(14,3,utf8_decode(''),0,0,'R',1);
                if (isset($_POST['mPublico']))
                    $pdf->Cell(16,3,utf8_decode(number_format($rowProd['precioPublico'],2,".",",")),0,0,'R',1);
                else
                    $pdf->Cell(16,3,utf8_decode(''),0,0,'R',1);
                if (isset($_POST['mPromedio']))
                    $pdf->Cell(17,3,utf8_decode(number_format($rowProd['precioProm'],2,".",",")),0,0,'R',1);
                else
                    $pdf->Cell(17,3,utf8_decode(''),0,0,'R',1);
                $iva    = ($rowProd['IVA'] > 0) ? $rowProd['IVA'].'%' : '';
                $pdf->Cell(10,3,utf8_decode($iva),0,0,'C',1);
                $ieps   = ($rowProd['IEPS'] > 0) ? $rowProd['IEPS'].'%' : '';
                $pdf->Cell(10,3,utf8_decode($ieps),0,1,'C',1);
            }
            $pdf->SetDrawColor(0);
            $pdf->SetFillColor(255);
            $pdf->SetFont('Times','B',8);
            $pdf->Cell(13,2.5,utf8_decode(''),0,1,'C',0);
            $pdf->Cell(196,0,utf8_decode(''),1,1,'C',1);
            $pdf->Cell(43,2.5,utf8_decode('TOTAL EXIST:'),0,0,'C',0);
            $pdf->Cell(11,2.5,utf8_decode($exist),1,0,'R',0);
            $pdf->Cell(54,2.5,utf8_decode('TOTAL ARTÍCULOS:'),0,0,'C',0);
            $pdf->Cell(11,2.5,utf8_decode($stripper),1,1,'R',0);
            $pdf->Cell(13,2.5,utf8_decode(''),0,1,'C',0);
        }
    }
    $pdf->Output();
}
     ?>
