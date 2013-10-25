function getHeader(){
	$('header').append("<h1>REPOSITORIO WEB QTI</h1>");
    getFooter();
}

function getFooter(){
	var fecha = new Date();
    var anio = fecha.getFullYear();
	$('footer').append("Repositorio Web QTI "+ anio);
}