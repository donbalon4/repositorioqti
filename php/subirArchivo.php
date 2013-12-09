<?php
/*
Autor: Daniel Ojeda Sandoval
*/
include_once 'conexion.php';
include_once 'funciones.php';

$autor=mysql_real_escape_string($_REQUEST['autor']);
$autor=strip_tags($autor);
$autorv=str_replace(" ", "_", $autor);
$institucion=mysql_real_escape_string($_REQUEST['institucion']);
$institucion=strip_tags($institucion);
$nombre=mysql_real_escape_string($_REQUEST['nombre']);
$nombre=strip_tags($nombre);
$nombrev=str_replace(" ", "_", $nombre);
$nivel=mysql_real_escape_string($_REQUEST['nivel']);
$nivel=strip_tags($nivel);
$nivelv=str_replace(" ", "_", $nivel);
$materia=mysql_real_escape_string($_REQUEST['materia']);
$materia=strip_tags($materia);
$curso=mysql_real_escape_string($_REQUEST['curso']);
$curso=strip_tags($curso);
$tipo=mysql_real_escape_string($_REQUEST['tipo']);
$tipo=strip_tags($tipo);
$tipov=str_replace(" ", "_", $tipo);
$tags=mysql_real_escape_string($_REQUEST['tags']);
$tags=strip_tags($tags);
$tags=strtolower($tags);
//Validaciones
$ingreso=true;
if ($autor == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar un autor')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
if ($nombre == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar un nombre')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
if ($tipo == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar el tipo de pregunta')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
if ($nivel == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar un nivel')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
if ($tags == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar las palabras claves')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}


//Preguntamos si nuetro 'archivo' fue definido
if (isset ($_FILES["archivo"]) and $ingreso) {

  if ($_FILES['archivo']['type'] != 'application/zip' and $_FILES['archivo']['type'] != 'text/xml'){
    $ingreso=false;
    echo "<script>alert('Debe ingresar un archivo válido xml o zip')</script>";
    echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
  }
  if ($tipo == "hotspot" or $tipo == "graphic order") {
    if ($_FILES['archivo']['type'] != 'application/zip'){
      echo "<script>alert('Al seleccionar una pregunta del tipo hotspot o graphic order, debe subir un archivo zip que contenga el recurso de imagen asociado a la pregunta')</script>";
      echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
      die();
    }
  }
  if($_FILES['archivo']['type']=='text/xml'){
    $verifica_nombre=mysqli_query($conexion,"SELECT id FROM  `pregunta` where ruta_descarga = '../preguntas//$autorv/$nivelv/$tipov/$nombrev.xml' ORDER BY id ASC ") or die("Problemas en el select 1:".mysqli_error($conexion));
    $reg1=mysqli_fetch_array($verifica_nombre,MYSQLI_ASSOC);
    if (count($reg1) > 0){
      echo "<script>alert('Ya existe un archivo con esos mismos datos, sugerencia: cambie le nombre de la pregunta')</script>";
      echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
      die();
    }
    else{
      $pregunta= simplexml_load_file($_FILES['archivo']['tmp_name']);
      $analisis=validarXML($pregunta,$tipo); //invocamos a la funcion que valida las preguntas segun la estructura de aqurate
      $valido=$analisis['valido'];
      $mensaje=$analisis['mensaje'];
      if ($analisis['mobile']) {
        $compatible_Mobile='si';
      }
      else{
        $compatible_Mobile='no';
      }

      if ($valido==false) {
        echo "<script>alert('$analisis[mensaje]')</script>";
        echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
      }
      else{
        $nombretemporal = $_FILES['archivo']['tmp_name'];
        $ingresar_Bd=copiarArchivo($nombretemporal,$nombre,$nivel,$autor,$tipo);//invocamos la funcion q crea los directorios y copia el archivo a una caréta del repositorio
      }
    }
  }
  elseif ($_FILES['archivo']['type'] == 'application/zip') {
    $verifica_nombre=mysqli_query($conexion,"SELECT id FROM  `pregunta` where ruta_descarga = '../preguntas/$autorv/$nivelv/$tipov/$nombrev.zip' ORDER BY id ASC ") or die("Problemas en el select 1:".mysqli_error($conexion));
    $reg1=mysqli_fetch_array($verifica_nombre,MYSQLI_ASSOC);
    if (count($reg1) > 0){
      echo "<script>alert('Ya existe un archivo con esos mismos datos, sugerencia: cambie el nombre de la pregunta')</script>";
      echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
      die();
    }
    else{
      $nombretemporal = $_FILES['archivo']['tmp_name'];
      $zip=validarZip($nombretemporal,$tipo);
      if ($zip['mobile']) {
        $compatible_Mobile='si';
      }
      else{
        $compatible_Mobile='no';
      }

      if ($zip['valido']) {
        $ingresar_Bd=copiarArchivoZip($nombretemporal,$nombre,$nivel,$autor,$tipo);
      }
      else{
        echo "<script>alert('$zip[mensaje]')</script>";
        echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
      }
    }
  }
    if ($ingresar_Bd['exito']) {
      $tamano=($_FILES['archivo']['size'])/1000;//tamaño en KB
      $fecha=date('Y-m-d');
      
      /*ingreso a la base de datos*/

      mysqli_query($conexion,"INSERT INTO `pregunta`( `nombre`, `autor`, `institucion`,
       `compatible_mobile`, `nivel_educacion`, `materia`, `curso`, `tipo`,
        `ruta_descarga`, `tamano`, `fecha_ingreso`)
         VALUES ('$nombre','$autor','$institucion',
        '$compatible_Mobile','$nivel','$materia','$curso','$tipo',
        '$ingresar_Bd[ruta]','$tamano','$fecha')") or die("Problemas al ingresar 
        en la base de datos".mysqli_error($conexion));
  
  

        if ($tags != '') {
          $tags=explode(',', $tags);

          mysqli_close($conexion);

          $conexion=mysqli_connect("localhost","root","root") 
            or  die("Problemas en la conexion");

          mysqli_select_db($conexion,"repositorioqti") 
            or  die("Problemas en la selección de la base de datos");

          $id_preg = mysqli_query($conexion,"SELECT `id` FROM `pregunta` where  fecha_ingreso = '$fecha' ") 
          or die ("Problemas en la seleccion del id de pregunta ".mysqli_error($conexion));
          
          $id='';

          while($reg=mysqli_fetch_array($id_preg,MYSQLI_NUM)) {
            $id=$reg[0];
          }

          for ($i=0; $i <count($tags) ; $i++) {
            $tag=$tags[$i];
            $verifica_tag=mysqli_query($conexion,"SELECT `palabra` FROM `tag` where  palabra = '$tag' ") 
          or die ("Problemas en la seleccion del id de pregunta ".mysqli_error($conexion));
          $reg=mysqli_fetch_array($verifica_tag,MYSQLI_ASSOC);
          if (count($reg) != 0){ //si el tag ya existe
            mysqli_query($conexion,"INSERT INTO `relacion_tags`(`fk_id`, `fk_palabra`) 
              VALUES ('$id','$tag') ") or die ("Problemas al generar la relacion tag-pregunta "
              .mysqli_error($conexion)); 
            
            mysqli_free_result($verifica_tag);            
          }

          else{
            mysqli_query($conexion,"INSERT INTO `tag`(`palabra`) VALUES ('$tag')")
              or die("Problemas al ingresar tags en la base de datos ".mysqli_error($conexion));

            mysqli_query($conexion,"INSERT INTO `relacion_tags`(`fk_id`, `fk_palabra`) 
              VALUES ('$id','$tag') ") or die ("Problemas al generar la relacion tag-pregunta "
              .mysqli_error($conexion)); 
            }

          }
          mysqli_free_result($id_preg);
        }
      echo "<script>alert('La pregunta ha sido ingresa satisfactoriamente')</script>";
      echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
      mysqli_close($conexion);
    }
    
  
}

?>