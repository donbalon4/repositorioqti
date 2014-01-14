<?php
/*
Autor: Daniel Ojeda Sandoval
*/
include_once 'conexion.php';

$nombre = mysqli_real_escape_string($conexion,$_REQUEST['nombre']);
$nombre=strip_tags($nombre);
$enlace = mysqli_real_escape_string($conexion,$_REQUEST['ruta']);
$enlace=strip_tags($enlace);
if ($nombre=='' or $enlace=='') {
	echo "Error :P";
}
if (file_exists($enlace)) {
	$separador=explode('.', $enlace);
	$extension=end($separador);

	if ($extension=='xml') {
	        $tipo='text/xml';
	}
	else{
	        $tipo='application/zip';
	}
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/force-download");
	header("Content-Type: $tipo");
	header("Content-Type: $tipo");
	header("Content-Disposition: attachment;filename=$nombre.$extension ");
	header("Content-Transfer-Encoding: binary ");
	readfile($enlace);
	flush();

	$descarga = mysqli_query($conexion,"SELECT `numero_descargas` FROM `pregunta` where  ruta_descarga = '$enlace' ") 
	          or die ("Problemas en la seleccion del numero de descargas ".mysqli_error($conexion));
	          
	$descargas='';

	while($reg=mysqli_fetch_array($descarga,MYSQLI_NUM)) {
		$descargas=$reg[0];
	}
	$descargas=$descargas+1;

	$actualizacion=mysqli_query($conexion,"UPDATE pregunta SET numero_descargas = '$descargas' 
	where ruta_descarga = '$enlace' ")
	 or die("Problemas al actualizar los datos de descarga ".mysqli_error($conexion));

	exit;
}
else{
	echo "<script>alert('No se encuentra el archivo')</script>";
  	echo "<SCRIPT>window.location='../busqueda.html';</SCRIPT>";
}
?>