//Programación grid tipo hoja de cálculo
function actualizarPxP(thisInput)
{
    precioLista         =   parseFloat($("#inputPrecioLista").val());
    factorConversion    =   parseFloat($("#inputFactor").val());
    precioPaquete       =   parseFloat(thisInput.val());
    thisInput.val(precioPaquete.toFixed(3));
    diferenciaPaq       =   precioPaquete - (precioLista * factorConversion);
    utilPaquete         =   (diferenciaPaq * 100) / (precioLista * factorConversion);
    if(utilPaquete < 3)
        thisInput.parent().parent().next().next().find(".input-group").addClass("has-error");
    else
        thisInput.parent().parent().next().next().find(".input-group").removeClass("has-error");
    thisInput.parent().parent().next().next().find(".inputTable").val(utilPaquete.toFixed(3));
    precioUnidad        =   precioPaquete / factorConversion;
    thisInput.parent().parent().next().next().next().find(".inputTable").val(precioUnidad.toFixed(3));

    //$("#inputMenPxU")   .val(precioUnidad.toFixed(3));
    precioUnidadLista   =   precioLista;// / factorConversion;
    diferenciaUni       =   precioUnidad - precioUnidadLista;
    utilUnidad          =   (diferenciaUni * 100) / precioUnidadLista;
    if(utilUnidad < 3)
    thisInput.parent().parent().next().next().next().next().next().find(".input-group").addClass("has-error");

    //    $("#inputMenUxU").parent().addClass("has-error");
    else
        thisInput.parent().parent().next().next().next().next().next().find(".input-group").removeClass("has-error");
        //$("#inputMenUxU").parent().removeClass("has-error");
    thisInput.parent().parent().next().next().next().next().next().find(".inputTable").val(utilUnidad.toFixed(3));
    //$("#inputMenUxU")   .val(utilUnidad.toFixed(3));
}
function actualizarUxP(thisInput)
{
    if(thisInput.val() < 3)
        thisInput.parent().addClass("has-error");
    else
        thisInput.parent().removeClass("has-error");
    precioLista         =   parseFloat($("#inputPrecioLista").val());
    factorConv         =   parseFloat($("#inputFactor").val());
    porcentaje = parseFloat(thisInput.val());
    thisInput.val(porcentaje.toFixed(3));
    f = porcentaje * 0.01 + 1;
    precioxPaquete = precioLista * f * factorConv;
    thisInput.parent().parent().prev().prev().find(".inputTable").val(precioxPaquete.toFixed(3));
}
function actualizarPxU(thisInput)
{
    precioLista         =   parseFloat($("#inputPrecioLista").val());
    factorConv         =   parseFloat($("#inputFactor").val());
    precioUnidad = parseFloat(thisInput.val());
    thisInput.val(precioUnidad.toFixed(3));
    precioListaUnidad = precioLista; // / factorConv;
    dif = precioUnidad - precioListaUnidad;
    porcentajeUtilidad = (dif * 100) / precioListaUnidad;
    //precioxUnidad = precioxPaquete / parseFloat($("#inputFactor").val());
    if(porcentajeUtilidad < 3)
        thisInput.parent().parent().next().next().find(".input-group").addClass("has-error");
    else
        thisInput.parent().parent().next().next().find(".input-group").removeClass("has-error");
    thisInput.parent().parent().next().next().find(".inputTable").val(porcentajeUtilidad.toFixed(3));
}
function actualizarUxU(thisInput)
{
    if(thisInput.val() < 3)
        thisInput.parent().addClass("has-error");
    else
        thisInput.parent().removeClass("has-error");
    precioLista         =   parseFloat($("#inputPrecioLista").val());
    factorConv         =   parseFloat($("#inputFactor").val());
    porcentaje = parseFloat(thisInput.val());
    thisInput.val(porcentaje.toFixed(3));
    f = porcentaje * 0.01 + 1;
    precioxUnidad = (precioLista * f); // / factorConv;
    //precioxUnidad = precioxPaquete / parseFloat($("#inputFactor").val());
    thisInput.parent().parent().prev().prev().find(".inputTable").val(precioxUnidad.toFixed(3));
    //thisInput.parent().parent().next().find(".inputTable").val(precioxUnidad.toFixed(3));
}
function actualizarPrecioLista(thisInput)
{
    if (isNaN(thisInput.val()))
    {
        thisInput.parent().addClass("has-error");
    }
    else
    {
        thisInput.parent().removeClass("has-error");
        if ($("#inputMenPxP").length > 0 )
        {
            actualizarPxP($("#inputMenPxP"));
            actualizarPxP($("#inputMedPxP"));
            actualizarPxP($("#inputMayPxP"));
            actualizarPxP($("#inputEspPxP"));
        }
        else
        {
            actualizarPxP($("#inputMenPxU"));
            actualizarPxP($("#inputMedPxU"));
            actualizarPxP($("#inputMayPxU"));
            actualizarPxP($("#inputEspPxU"));
        }
    }
}
$("#divListaPrecios").on("keydown",".inputTable",function(e)
{
    if(e.keyCode == 39 || e.keyCode == 13) //right arrow / return
    {
        e.preventDefault();
        if($(this).hasClass("inputUltimoFila") == false)
        {
            $(this).parent().parent().next().find(".inputTable").focus();
        }
        else if($(this).hasClass("inputUltimoFila") == true && $(this).hasClass("inputUltimoColumna") == false)
        {

            $(this).parent().parent().parent().next().find(".inputTable.inputPrimeroFila").focus();
        }
    }
    if(e.keyCode == 37) // left arrow
    {
        e.preventDefault();
        if($(this).hasClass("inputPrimeroFila") == false)
        {
            $(this).parent().parent().prev().find(".inputTable").focus();
        }
        else if($(this).hasClass("inputPrimeroFila") == true && $(this).hasClass("inputPrimeroColumna") == false)
        {

            $(this).parent().parent().parent().prev().find(".inputTable.inputUltimoFila").focus();
        }
    }
    if(e.keyCode == 38) // up arrow
    {
        e.preventDefault();
        if($(this).hasClass("inputPrimeroColumna") == false)
        {
            index = $(this).parent().parent().index();
            $(this).parent().parent().parent().prev().find("td").eq(index).find(".inputTable").focus();
        }
    }
    if(e.keyCode == 40) // down arrow
    {
        e.preventDefault();
        if($(this).hasClass("inputUltimoColumna") == false)
        {
            index = $(this).parent().parent().index();
            $(this).parent().parent().parent().next().find("td").eq(index).find(".inputTable").focus();
        }
    }
});
$("#divListaPrecios").on("focus",".inputTable",function()
{
    this.select();
});
$(document).on("focus","#inputPrecioLista",function()
{
    this.select();
})
// Cálculo inputs lista de precios//

$("#divListaPrecios").on("change","#inputMenPxP,#inputMedPxP,#inputMayPxP,#inputEspPxP",function()
{
    actualizarPxP($(this));
});
$("#divListaPrecios").on("keydown","#inputMenPxP,#inputMedPxP,#inputMayPxP,#inputEspPxP",function(e)
{
    if(e.keyCode == 13)
    {
        //e.preventDefault();
        actualizarPxP($(this));
    }
});
$("#divListaPrecios").on("change","#inputMenUxP,#inputMedUxP,#inputMayUxP,#inputEspUxP",function()
{
    actualizarUxP($(this));
});
$("#divListaPrecios").on("keydown","#inputMenUxP,#inputMedUxP,#inputMayUxP,#inputEspUxP",function(e)
{
    if(e.keyCode == 13)
    {
        //e.preventDefault();
        actualizarUxP($(this));
    }
});
$("#divListaPrecios").on("change","#inputMenPxU,#inputMedPxU,#inputMayPxU,#inputEspPxU",function()
{
    actualizarPxU($(this));
});
$("#divListaPrecios").on("keydown","#inputMenPxU,#inputMedPxU,#inputMayPxU,#inputEspPxU",function(e)
{
    if(e.keyCode == 13)
    {
        //e.preventDefault();
        actualizarPxU($(this));
    }
});
$("#divListaPrecios").on("change","#inputMenUxU,#inputMedUxU,#inputMayUxU,#inputEspUxU",function()
{
    actualizarUxU($(this));
});
$("#divListaPrecios").on("keydown","#inputMenUxU,#inputMedUxU,#inputMayUxU,#inputEspUxU",function(e)
{
    if(e.keyCode == 13)
    {
        //e.preventDefault();
        actualizarUxU($(this));
    }
});
$(document).on("change","#divPrecioLista > #inputPrecioLista",function()
{
    actualizarPrecioLista($(this));
});
$(document).on("keydown","#divPrecioLista > #inputPrecioLista",function(e)
{
    if(e.keyCode == 13)
    {
        actualizarPrecioLista($(this));
    }

});
$(document).on("change","#inputFactor",function()
{
    actualizarPrecioLista($(this));
});
$(document).on("keydown","#inputFactor",function(e)
{
    if(e.keyCode == 13)
    {
        actualizarPrecioLista($(this));
    }

});
