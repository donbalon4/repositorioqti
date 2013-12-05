<?php
/*
Autor: Daniel Ojeda Sandoval
*/
function validarXML($pregunta,$tipo,$ruta='noruta'){
  $compatible_Mobile=False;
  $valido=False;
  $respuesta['mensaje']='';
  $valida_nodos=validarNodos($pregunta);
  if($valida_nodos['valido']){
    $valida_tipo=validarTipos($pregunta,$tipo,$ruta);
    if ($valida_tipo['valido']) {
      $valido=true;
      $compatible_Mobile=true;
    }
    else{
      $respuesta['mensaje']=$valida_tipo['mensaje'];
    }
  }
  else{
    $respuesta['mensaje']=$valida_nodos['mensaje'];
  }
  $respuesta['valido']=$valido;
  $respuesta['mobile']=$compatible_Mobile;
  return $respuesta;
}

function validarNodos($pregunta){
  $respuesta['valido']=false;
  $respuesta['mensaje']='';
  if((string)$pregunta['title'] == ''){
    $respuesta['mensaje']='La pregunta no tiene valor en el atributo title';
  }
  elseif (!$pregunta->responseDeclaration) {
    $respuesta['mensaje']='La pregunta no tiene el nodo responseDeclaration';  
  }
  elseif (!$pregunta->responseDeclaration->correctResponse) {
    $respuesta['mensaje']='La pregunta no tiene el nodo correctResponse';  
  }
  elseif ((string)$pregunta->responseDeclaration->correctResponse->value == '') {
    $respuesta['mensaje']='La pregunta no tiene el atributo valor para correctResponse';  
  }
  elseif (!$pregunta->outcomeDeclaration) {
    $respuesta['mensaje']='La pregunta no tiene el nodo outcomeDeclaration';  
  }
  elseif ((string)$pregunta->outcomeDeclaration['baseType'] == '') {
    $respuesta['mensaje']='La pregunta no tiene el valor baseType para el nodo outcomeDeclaration';  
  }
  elseif (!$pregunta->itemBody) {
            $respuesta['mensaje']='La pregunta no tiene el nodo itemBody';  
  }
  else{
    $respuesta['valido']=true;
  }
  return $respuesta;        
}

function validarTipos($pregunta,$tipo,$ruta='noruta'){
  $respuesta['mensaje']='';
  $respuesta['valido']=false;
  switch ($tipo) {
    case 'inline choice':
      if (!$pregunta->itemBody->p) {
        $respuesta['mensaje']='No se encuentra el nodo p para la pregunta tipo inline choice';
      }
      elseif (!$pregunta->itemBody->p->inlineChoiceInteraction){
        $respuesta['mensaje']='No se encuentra el nodo p para la pregunta tipo inline choice';
      }
      else{
        $respuesta['valido']=true;
      }
      break;

    case 'choice':
      if(!$pregunta->itemBody->choiceInteraction){
       $respuesta['mensaje']='No se encuentra el nodo choiceInteraction para la pregunta tipo choice'; 
      }
      elseif (!$pregunta->itemBody->choiceInteraction->prompt) {
        $respuesta['mensaje']='No se encuentra el nodo prompt para la pregunta tipo choice'; 
      }
      else{
        $respuesta['valido']=true;
      }
      break;

      case 'order':
      if(!$pregunta->itemBody->orderInteraction){
       $respuesta['mensaje']='No se encuentra el nodo orderInteraction para la pregunta tipo order'; 
      }
      elseif (!$pregunta->itemBody->orderInteraction->prompt) {
        $respuesta['mensaje']='No se encuentra el nodo prompt para la pregunta tipo order'; 
      }
      else{
        $respuesta['valido']=true;
      }
      break;

      case 'associate':
      if(!$pregunta->itemBody->associateInteraction){
       $respuesta['mensaje']='No se encuentra el nodo associateInteraction para la pregunta tipo associate'; 
      }
      elseif (!$pregunta->itemBody->associateInteraction->prompt) {
        $respuesta['mensaje']='No se encuentra el nodo prompt para la pregunta tipo associate'; 
      }
      else{
        $respuesta['valido']=true;
      }
      break;

      case 'slider':
      if(!$pregunta->itemBody->sliderInteraction){
       $respuesta['mensaje']='No se encuentra el nodo sliderInteraction para la pregunta tipo slider'; 
      }
      elseif (!$pregunta->itemBody->sliderInteraction->prompt) {
        $respuesta['mensaje']='No se encuentra el nodo prompt para la pregunta tipo slider'; 
      }
      else{
        $respuesta['valido']=true;
      }
      break;

      case 'hotspot':
      $imagen=$pregunta->itemBody->hotspotInteraction->object['data'];
      if(!$pregunta->itemBody->hotspotInteraction){
       $respuesta['mensaje']='No se encuentra el nodo hotspotInteraction para la pregunta tipo hotspot'; 
      }
      elseif (!$pregunta->itemBody->hotspotInteraction->prompt) {
        $respuesta['mensaje']='No se encuentra el nodo prompt para la pregunta tipo hotspot'; 
      }
      elseif ((string)$pregunta->itemBody->hotspotInteraction->object['data'] == '') {
       $respuesta['mensaje']='No se encuentra el valor de data para el nodo object para la pregunta tipo hotspot';  
      }
      elseif (!file_exists($ruta.$imagen)) {
        $respuesta['mensaje']="No se encuentra el recurso imagen para la pregunta tipo hot spot";
      }
      else{
        $respuesta['valido']=true;
      }
      break;

      case 'graphic order':
      $imagen=$pregunta->itemBody->graphicOrderInteraction->object['data'];
      if(!$pregunta->itemBody->graphicOrderInteraction){
       $respuesta['mensaje']='No se encuentra el nodo graphicOrderInteraction para la pregunta tipo graphic order'; 
      }
      elseif (!$pregunta->itemBody->graphicOrderInteraction->prompt) {
        $respuesta['mensaje']='No se encuentra el nodo prompt para la pregunta tipo graphic order'; 
      }
      elseif ((string)$pregunta->itemBody->graphicOrderInteraction->object['data'] == '') {
       $respuesta['mensaje']='No se encuentra el valor de data para el nodo object para la pregunta tipo graphic order';  
      }
      
      elseif (!file_exists($ruta.$imagen)) {
       $respuesta['mensaje']='No se encuentra el recurso imagen para la pregunta tipo graphic order';
      }
      else{
        $respuesta['valido']=true;
      }
      break;
 }
  return $respuesta;
}

function copiarArchivo($nombreTemporal,$nombre,$nivel,$autor,$tipo){
  $nivel=str_replace(' ','_',$nivel);
  $nombreTemporal=str_replace(' ','_',$nombreTemporal);
  $nombre=str_replace(' ','_',$nombre);
  $autor=str_replace(' ','_',$autor);
  $tipo=str_replace(' ','_',$tipo);
  $respuesta['exito']=false;
	$ruta="../preguntas/$autor/$nivel/$tipo";
    
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

function validarZip($nombreTemporal,$tipo){
    $respuesta['valido']=false;
    $respuesta['mobile']=false;
    $respuesta['mensaje']='';
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
                $xml=validarXML($pregunta,$tipo,$dir);
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

function copiarArchivoZip($nombreTemporal,$nombre,$nivel,$autor,$tipo){
  $nivel=str_replace(' ','_',$nivel);
  $nombreTemporal=str_replace(' ','_',$nombreTemporal);
  $nombre=str_replace(' ','_',$nombre);
  $autor=str_replace(' ','_',$autor);
  $tipo=str_replace(' ','_',$tipo);
  $respuesta['exito']=false;
  $ruta="../preguntas/$autor/$nivel/$tipo";
  $carpeta=mkdir($ruta,0775,true);
  if(move_uploaded_file ($nombreTemporal, "$ruta/$nombre.zip")){
      $respuesta['exito']=true;
      $respuesta['ruta']=$ruta.'/'.$nombre.'.zip' ;
  }
  return $respuesta;


}
?>














