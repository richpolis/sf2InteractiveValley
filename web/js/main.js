function setLenguajePagina(lenguaje){
    var pagina=document.location.href + "?lang="+lenguaje;
    window.location=pagina;
}