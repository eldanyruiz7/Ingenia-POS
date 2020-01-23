function reCalcularFactura()
{
    totalSumatoria  = 0;
    //totalDescuento  = 0;
    totalIva = 0;
    totalIeps = 0;
    total_ = 0;
    $(".trRowFactura").each(function()
    {
        if ($(this).hasClass('dialog-oculto') == false)
        {
            cantidad = parseFloat($(this).find(".spanCantidad").text());
            precioU = parseFloat($(this).find(".spanPrecioU").text());
            subTotal = parseFloat($(this).find(".spanSubTotal").text());
            iva = $(this).find(".inputIva").val();
            iva = (iva.length == 0) ? 0 : parseFloat($(this).find(".inputIva").val());
            ieps = $(this).find(".inputIeps").val();
            ieps = (ieps.length == 0) ? 0 : parseFloat($(this).find(".inputIeps").val());
            //descuento = $(this).find(".inputDescuento").val();
            //descuento = (descuento.length == 0) ? 0 : parseFloat($(this).find(".inputDescuento").val());
            //alert("descuento: "+descuento);
            factorIva = iva / 100;
            factorIva++;
            factorIeps = ieps / 100;
            factorIeps++;
            /*esteDescuento = (descuento * subTotal) / 100;
            esteDescuento = parseFloat(esteDescuento.toFixed(2));
            subTotalConDescuento = subTotal - esteDescuento;
            subTotalConDescuento = parseFloat(subTotalConDescuento.toFixed(2));*/
            //alert("conDesc: "+subTotalConDescuento);
            if (iva > 0 && ieps == 0 )
            {
                //subTotalSinImpuesto = subTotalConDescuento / factorIva;
                subTotalSinImpuesto = subTotal / factorIva;
                subTotalSinImpuesto = parseFloat(subTotalSinImpuesto);
                esteIva = subTotal - subTotalSinImpuesto;
                //esteIva = parseFloat(esteIva.toFixed(2));
                esteIeps = 0;
            }
            else if (iva == 0 && ieps > 0)
            {
    //                subTotalSinImpuesto = subTotalConDescuento / factorIeps;
                subTotalSinImpuesto = subTotal / factorIeps;
                subTotalSinImpuesto = parseFloat(subTotalSinImpuesto);
                esteIeps = subTotal - subTotalSinImpuesto;
                //esteIeps = parseFloat(esteIeps.toFixed(2));
                esteIva = 0;
            }
            else if (iva == 0 && ieps == 0)
            {
                subTotalSinImpuesto = subTotal
                subTotalSinImpuesto = parseFloat(subTotalSinImpuesto);
                esteIva = 0;
                esteIeps = 0;
            }
            totalIva += esteIva;
            totalIeps += esteIeps;
            totalSumatoria += subTotalSinImpuesto;// + esteIva + esteIeps;
            //totalDescuento += esteDescuento;
            //total_ += subTotalConDescuento;//Tel. Lorena 3131111438
        }
    });

    $("#spanSumatoria").text(totalSumatoria.toFixed(2));
    //$("#spanDescuento").text(totalDescuento.toFixed(2));
    subTot_ = totalSumatoria;// - totalDescuento
    $("#spanSubTotal").text(subTot_.toFixed(2));
    $("#spanIva").text(totalIva.toFixed(2));
    $("#spanIeps").text(totalIeps.toFixed(2));
    total_ = subTot_ + totalIva + totalIeps;
    total_ = total_.toFixed(2);
    $("#spanTotal").text(total_);
}
function revisarClaveSAT()
{
    error = 0;
    $(".inputSAT:enabled").each(function()
    {
        cadena = $(this).val();
        cadena = cadena.replace(" ", "");
        console.log(cadena);
        $(this).val(cadena);
        if(cadena.length != 8)
        {
            $(this).parent().addClass("danger");
            error = 1;
        }
        else
        {
            $(this).parent().removeClass("danger");
        }
    });
    return error;
}
function msgFactura(status, mensaje)
{
    if (status == 1)
    {
        mensajeHTML = '<i class="fa fa-2x fa-check-circle fa-pull-left" aria-hidden="true"></i> ';
        $("#tdMsg").css("color","green");
        $('body').find("#tdMsgInd").css("color","green");
        $('body').find("#tdMsgInd_g").css("color","green");
        $("#btnFacturarInd").css("display","none");
        $("#btnFacturarInd-g").css("display","none");
        $("#btnReenviarCfdiCompl").css("display","none");
        $(".trRowFactura").addClass("inactive");
        $(".trRowFacturaSubt").addClass("inactive");
        $(".inputSAT").attr("readonly",true);
        $(".inputIva").attr("readonly",true);
        $(".inputIeps").attr("readonly",true);
        $(".inputDescuento").attr("readonly",true);
    }
    else
    {
        $(".inputSAT").attr("readonly",false);
        $(".inputIva").attr("readonly",false);
        $(".inputIeps").attr("readonly",false);
        $(".inputDescuento").attr("readonly",false);
        mensajeHTML = '<i class="fa fa-2x fa-exclamation-triangle fa-pull-left" aria-hidden="true"></i> ';
        $("#tdMsg").css("color","red");
        $(document).find("#tdMsgInd").css("color","red");
        $(document).find("#tdMsgInd_g").css("color","red");
        $("#btnFacturarInd").attr("disabled",false);
        $("#btnFacturarInd-g").attr("disabled",false);
        $("#btnReenviarCfdiCompl").attr("disabled",false);
    }
    mensajeHTML += mensaje;
    $("#tdMsg").html(mensajeHTML);
    $('body').find("#tdMsgInd").html(mensajeHTML);
    $('body').find("#tdMsgInd_g").html(mensajeHTML);

    $("#btnFacturarInd").html('<i class="fa fa-rocket" aria-hidden="true"></i> factura!');
    $("#btnFacturarInd-g").html('<i class="fa fa-rocket" aria-hidden="true"></i> factura!');
    $("#btnReenviarCfdiCompl").html('<i class="fa fa-rocket" aria-hidden="true"></i> factura!');
    $("#btnCancelarFacturarInd").attr("disabled",false);
    $("#btnCancelarFacturarInd-g").attr("disabled",false);
    $("#btnCancelarReenviarCfdi").attr("disabled",false);
    $("#btnCancelarReenviarCfdi-comp").attr("disabled",false);


}
