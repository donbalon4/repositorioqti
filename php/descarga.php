<?php

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

exit;
?>