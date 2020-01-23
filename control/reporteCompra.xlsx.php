<?php
require "../startbootstrap/vendor/PHPExcel/Classes/PHPExcel.php";

function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(
        array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                                'rgb' => $color
        )
    ));
}
$parts = explode('-',$_POST['fix']);
$fi = $parts[2]."-".$parts[1]."-".$parts[0];
$parts = explode('-',$_POST['ffx']);
$ff = $parts[2]."-".$parts[1]."-".$parts[0];
$objPHPExcel = new PHPExcel();
$coleccionTabla = json_decode($_POST['rx']);
$cuantos = sizeof($coleccionTabla);
$num = $numFormula = 6;
$objPHPExcel->
getProperties()
    ->setCreator("Ingenia System")
    ->setLastModifiedBy("Ingenia System")
    ->setTitle("Reporte de ventas creado por Ingenia System")
    ->setSubject("Reporte de ventas")
    ->setDescription("Documento generado dinamicamente")
    ->setKeywords("usuarios compras ")
    ->setCategory("reportes");
$objPHPExcel->getActiveSheet()->mergeCells('D1:I1');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D1', 'REPORTE' );
$objPHPExcel->getActiveSheet()->mergeCells('D2:I2');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D2', 'REPORTE DE COMPRAS' );
$objPHPExcel->getActiveSheet()->mergeCells('B4:C4');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B4', 'TOTAL REGISTROS:' );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D4', $cuantos );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('G4', $fi );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('H4', 'al:' );
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I4', $ff );
$objPHPExcel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D2')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);

$objPHPExcel->getActiveSheet()->getStyle('H4:J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
cellColor('B5:J5', '2271B3');
$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF'),
        'size'  => 10,
        'name'  => 'Verdana'
    ));
$objPHPExcel->getActiveSheet()->getStyle('B5:J5')->applyFromArray($styleArray);
$objPHPExcel->setActiveSheetIndex(0)
->setCellValue('B5', 'No.' )
->setCellValue('C5', 'Fecha')
->setCellValue('D5', 'Hora')
->setCellValue('E5', 'proveedor')
->setCellValue('F5', 'Fecha Expira')
->setCellValue('G5', 'Cajero')
->setCellValue('H5', 'Método')
->setCellValue('I5', 'Total');


foreach ($coleccionTabla as $esteRow)
{
/*    if($cont%2==0)
        //$pdf->SetFillColor(207,252,255);
        $pdf->SetFillColor(228, 238, 255);
    else
        $pdf->SetFillColor(255);*/
    $numero     = $esteRow  ->numero;
    $fecha      = $esteRow  ->fecha;
    $hora       = $esteRow  ->hora;
    $proveedor  = $esteRow  ->proveedor;
    $fechaexpira= $esteRow  ->fechaexpira;
    //$descuento_f= str_replace(',','',substr($descuento,1));
    $cajero     = $esteRow  ->cajero;
    $metodo     = $esteRow  ->metodo;
    $total      = $esteRow  ->total;
    $total_f    = str_replace(',','',substr($total,1));
    $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('B'.$num, $numero )
    ->setCellValue('C'.$num, $fecha)
    ->setCellValue('D'.$num, $hora)
    ->setCellValue('E'.$num, $proveedor)
    ->setCellValue('F'.$num, $fechaexpira)
    ->setCellValue('G'.$num, $cajero)
    ->setCellValue('H'.$num, $metodo)
    ->setCellValue('I'.$num, $total_f);
    $objPHPExcel->getActiveSheet()
    ->getStyle('I'.$num)
    ->getNumberFormat()
    ->setFormatCode(
        '_-$* #,##0.00_-;-$* #,##0.00_-;_-$* "-"??_-;_-@_-'
    );
    $objPHPExcel->getActiveSheet()
    ->getStyle('F'.$num)
    ->getNumberFormat()
    ->setFormatCode(
        '_-$* #,##0.00_-;-$* #,##0.00_-;_-$* "-"??_-;_-@_-'
    );
    $cont++;
    $num++;
    $sumaDesc += floatval(str_replace(',','',substr($descuento,1)));
    $subTotal += floatval(str_replace(',','',substr($total,1)));
}
$objPHPExcel->getActiveSheet()
->getStyle('I'.$num)
->getNumberFormat()
->setFormatCode(
    '_-$* #,##0.00_-;-$* #,##0.00_-;_-$* "-"??_-;_-@_-'
);
//$objPHPExcel->getActiveSheet()->mergeCells('H'.$num.':I'.$num);
$objPHPExcel->setActiveSheetIndex(0)

->setCellValue('H'.$num, 'SUMATORIA:' )
->setCellValue('I'.$num, '=SUM(I'.$numFormula.':I'.--$num.')');
$styleArray = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 10,
        'name'  => 'Verdana'
    ));
$objPHPExcel->getActiveSheet()->getStyle('B'.++$num.':J'.$num)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize('true');
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize('true');


$objPHPExcel->getActiveSheet()->setTitle('Usuarios');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="01simple.xls"');
header('Cache-Control: max-age=0');

$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
$objWriter->save('php://output');
exit;
 ?>
