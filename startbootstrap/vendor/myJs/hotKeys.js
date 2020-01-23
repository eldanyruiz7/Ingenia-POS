$(document).bind('keydown', 'ctrl+d', function(e)
{
    e.preventDefault();
    ocultarMenu();

});
$("#btnCtrlD").click(function()
{

    ocultarMenu();
});
function ocultarMenu()
{
    if (typeof dialogoAbierto != "undefined")
        if (dialogoAbierto == 1)
            return false;
    if (typeof dialogoAbierto != "undefined")
        dialogoAbierto = 1
    pageWrapper = $("#page-wrapper");
    sideBar = $( ".sidebar" );
    if (sideBar.is(":visible"))
    {
        pageWrapper.animate({marginLeft: "0px"}, 300);
        sideBar.hide("drop",{ direction: "left" }, 300, function()
        {
            if (typeof dialogoAbierto != "undefined")
                dialogoAbierto = 0;
            $("#inputBuscar").focus();
        });
    }
    else
    {
        pageWrapper.animate({marginLeft: "250px"}, 300);
        sideBar.show("drop",{ direction: "left" }, 300, function()
        {
            if (typeof dialogoAbierto != "undefined")
                dialogoAbierto = 0;
            $("#inputBuscar").focus();
        });
    }
}
