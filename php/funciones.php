<?php
/*
Autor: Daniel Ojeda Sandoval
*/
function validarXML($pregunta,$ruta='noruta')
{
    $compatible_Mobile=False;
    $valido=False;
    $respuesta['mensaje']='';
    if((string)$pregunta['title'] != ''){
      if ((string)$pregunta->responseDeclaration->correctResponse->value != '') {
        if ((string)$pregunta->outcomeDeclaration['baseType'] != '') {
            if ($pregunta->itemBody->p->inlineChoiceInteraction) {
              $compatible_Mobile=True;
              $valido=true;
            }
            elseif ($pregunta->itemBody->choiceInteraction->prompt) {
              $compatible_Mobile=True;
              $valido=true;
            }
            elseif ($pregunta->itemBody->orderInteraction->prompt) {
              $compatible_Mobile=True;
              $valido=true;
            }
            elseif ($pregunta->itemBody->hotspotInteraction->prompt) {
              $imagen=$pregunta->itemBody->hotspotInteraction->object['data'];
              if (file_exists($ruta.$imagen)) {
                $compatible_Mobile=True;
                $valido=true;
              }
              else{
                $respuesta['mensaje']='No se encuentra el recurso imagen para la pregunta';
              }
              
            }
            elseif ($pregunta->itemBody->associateInteraction->prompt) {
              $compatible_Mobile=True;
              $valido=true;
            }
            elseif ($pregunta->itemBody->graphicOrderInteraction->prompt) {
              $imagen=$pregunta->itemBody->graphicOrderInteraction->object['data'];
              if (file_exists($ruta.$imagen)) {
                $compatible_Mobile=True;
                $valido=true;
              }
              else{
                $respuesta['mensaje']='No se encuentra el recurso imagen para la pregunta en $imagen';
              }
            }
            elseif ($pregunta->itemBody->sliderInteraction->prompt) {
              $compatible_Mobile=True;
              $valido=true;
            }
            else{
            	$valido=true;
            }
          }
    	}
   	  
	}
	$respuesta['valido']=$valido;
	$respuesta['mobile']=$compatible_Mobile;
  return $respuesta;
}

function copiarArchivo($nivel,$nombreTemporal,$nombre,$materia,$curso,$concepto){
  $respuesta['exito']=false;
	if ($nivel == 'basico' or $nivel == 'medio') {
        $ruta="../preguntas/$nivel/$materia/$curso/$concepto";
    }
    elseif ($nivel == 'superior') {
        $ruta="../preguntas/$nivel/$materia/$concepto";
    } 
    $carpeta=mkdir($ruta,0775,true);
    if(move_uploaded_file ($nombreTemporal, "$ruta/$nombre.xml")){
        $respuesta['exito']=true;
        $respuesta['ruta']=$ruta.'/'.$nombre.'.xml';
      }
    return $respuesta;


}

function eliminarDir($carpeta){
  foreach(glob($carpeta . "/*") as $archivos_carpeta){
    //echo $archivos_carpeta;
    if (is_dir($archivos_carpeta)){
      eliminarDir($archivos_carpeta);
    }
    else
    {
    unlink($archivos_carpeta);
    }
  }
  rmdir($carpeta);
}

function validarZip($nombreTemporal){
    $respuesta['valido']=false;
    $respuesta['mobile']=false;
    $zip = new ZipArchive;
    if ($zip->open($nombreTemporal) === TRUE) {
      $dir='temporal/';
      $carpeta_temporal=mkdir($dir,0755,true);
      if (is_dir($dir)) {
        $zip->extractTo($dir);
        $zip->close();
        $c=0;
      
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
              $separador=explode('.', $file);
              $extension=end($separador);
              if ($extension == 'xml') {
                $c=1;
                $pregunta = simplexml_load_file($dir.$file);
                $xml=validarXML($pregunta,$dir);
                $respuesta['valido']=$xml['valido'];
                $respuesta['mobile']=$xml['mobile'];
                if ($xml['mensaje'] != '') {
                  $respuesta['mensaje']=$xml['mensaje'];
                }
              }
            }
            closedir($dh);
            if ($c==0) {
              $respuesta['mensaje']="No se encontro el archivo xml en la carpeta zip";
            }
        }
      }
      eliminarDir('temporal');

        } 
    else {
      $respuesta['mensaje']='error al abrir el archivo .zip, es posible que esté corrupto';
    }
    return $respuesta;
}

function copiarArchivoZip($nivel,$nombreTemporal,$nombre,$materia,$curso,$concepto){
  $respuesta['exito']=false;
  if ($nivel == 'basico' or $nivel == 'medio') {
        $ruta="../preguntas/$nivel/$materia/$curso/$concepto";
    }
    elseif ($nivel == 'superior') {
        $ruta="../preguntas/$nivel/$materia/$concepto";
    } 
    $carpeta=mkdir($ruta,0775,true);
    if(move_uploaded_file ($nombreTemporal, "$ruta/$nombre.zip")){
        $respuesta['exito']=true;
        $respuesta['ruta']=$ruta.'/'.$nombre.'.zip' ;
    }
    return $respuesta;


}
?>














