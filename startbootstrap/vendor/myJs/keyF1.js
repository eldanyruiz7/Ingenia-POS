$(document).keydown(function(e)
{
    if(e.keyCode == 112)
    {
        e.preventDefault();
        $("#btnf1").click();
    }
    if( e.keyCode == 113
            || e.keyCode == 114
                || e.keyCode == 115
                    || e.keyCode == 116
                        || e.keyCode == 117
                            || e.keyCode == 118
                                || e.keyCode == 119
                                    || e.keyCode == 120
                                        || e.keyCode == 121
                                            || e.keyCode == 122
                                                || e.keyCode == 123)
        e.preventDefault();
});
$("#btnf1").click(function()
{
    window.location.assign("index.php");
});
