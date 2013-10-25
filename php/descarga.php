<?php
include_once 'conexion.php';

$nombre = mysql_real_escape_string($_REQUEST['nombre']);
$enlace = mysql_real_escape_string($_REQUEST['ruta']);
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
?>