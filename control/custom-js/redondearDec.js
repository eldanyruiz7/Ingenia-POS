function redondearDec(dec)
{
    dec         = dec.toString();
    partes      = dec.split(".");
    parteEntera = partes[0];
    parteDecimal= partes[1];
    if(parteDecimal == undefined)
        parteDecimal = "00";
    if(parteDecimal.length == 1)
        parteDecimal = parteDecimal + "0";
    parteDecimal= parteDecimal.slice(0, 2);
    dec         = parteEntera + "." + parteDecimal;
    str         = dec.toString();
    str         = str.split(".");
    p  = str[1];
    segundoDec  = parseInt(parteDecimal.charAt(1));
    if(segundoDec  == 0)
        return parseFloat(dec).toFixed(2);
    else if (segundoDec > 0)
    {
        dec     = parseFloat(dec).toFixed(1);
        if(segundoDec < 5)
            dec = parseFloat(dec) + parseFloat("0.1");
        return parseFloat(dec).toFixed(2);
    }
}
