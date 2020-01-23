
<?php
/*require('../startbootstrap/vendor/fpdf/pdf_js.php');

class PDF_AutoPrint extends PDF_JavaScript
{
    function AutoPrint($printer='')
    {
        // Open the print dialog
        if($printer)
        {
            $printer = str_replace('\\', '\\\\', $printer);
            $script = "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        }
        else
            $script = 'print(false);';
        $this->IncludeJS($script);
    }
}

$pdf = new PDF_AutoPrint();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 20);
$pdf->Text(1, 11, 'Print meeeeeeeeeeeeeeeeeeeeeee!');
$pdf->AutoPrint();
$pdf->Output();*/
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Imprimir una página con jQuery (printThis)</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="../startbootstrap/vendor/jquery/jquery.min.js"></script>
    <script src="../startbootstrap/vendor/printArea/jquery.printArea.js"></script>
  </head>
  <body>
    <div id="pagina" style="display:none">
      <h2>Título de página</h2>
      <table >
        <tr>
          <td>dato1</td>
          <td>dato2</td>
          <td>dato3</td>
        </tr>
        <tr>
          <td>dato4</td>
          <td>dato5</td>
          <td>dato6</td>
        </tr>
      </table>
    </div>
    <button id="imprime" type="button">Imprimir</button>
    <script>
      $(document).ready(function () {
        $('#imprime').click(function () {
          $('#pagina').printArea();
        });
      });
    </script>
  </body>
</html>
