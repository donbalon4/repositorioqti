<?php
include_once 'conexion.php';
include_once 'funciones.php';

$autor=mysql_real_escape_string($_REQUEST['autor']);
$institucion=mysql_real_escape_string($_REQUEST['institucion']);
$nombre=mysql_real_escape_string($_REQUEST['nombre']);
$nivel=mysql_real_escape_string($_REQUEST['nivel']);
$materia=mysql_real_escape_string($_REQUEST['materia']);
$curso=mysql_real_escape_string($_REQUEST['curso']);
$concepto=mysql_real_escape_string($_REQUEST['concepto']);
$herramienta_autor='';
$herramienta_autor=mysql_real_escape_string($_REQUEST['herramienta_autor']);
$tags=mysql_real_escape_string($_REQUEST['tags']);
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
if ($nivel == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar un nivel')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
elseif ($nivel == 'basico' or $nivel == 'medio') {
  if ($materia == ''){
    $ingreso=false;
    echo "<script>alert('Debe ingresar una materia')</script>";
    echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
  }
  if ($curso == ''){
    $ingreso=false;
    echo "<script>alert('Debe ingresar un curso')</script>";
    echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
  }
}
if ($concepto == '') {
  $ingreso=false;
  echo "<script>alert('Debe ingresar un concepto')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
//Preguntamos si nuetro arreglo 'archivos' fue definido
if (isset ($_FILES["archivo"]) and $ingreso) {

  if ($_FILES['archivo']['type'] != 'application/zip' and $_FILES['archivo']['type'] != 'text/xml'){
    $ingreso=false;
    echo "<script>alert('Debe ingresar un archivo válido xml o zip')</script>";
    echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
  }
  elseif($_FILES['archivo']['type']=='text/xml'){
   
    $pregunta= simplexml_load_file($_FILES['archivo']['tmp_name']);
    $analisis=validarXML($pregunta); //invocamos a la funcion que valida las preguntas segun la estructura de aqurate
    $valido=$analisis['valido'];
    if ($analisis['mobile']) {
      $compatible_Mobile='si';
    }
    else{
      $compatible_Mobile='no';
    }

    if ($valido==false) {
      echo "<script>alert('El archivo xml no es válido')</script>";
      echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
    }
    else{
      $nombretemporal = $_FILES['archivo']['tmp_name'];
      $ingresar_Bd=copiarArchivo($nivel,$nombretemporal,$nombre,$materia,$curso,$concepto);//invocamos la funcion q crea los directorios y copia el archivo a una caréta del repositorio
    }
  }
  elseif ($_FILES['archivo']['type'] == 'application/zip') {
    $nombretemporal = $_FILES['archivo']['tmp_name'];
    $zip=validarZip($nombretemporal);
    if ($zip['mobile']) {
      $compatible_Mobile='si';
    }
    else{
      $compatible_Mobile='no';
    }

    if ($zip['valido']) {
      $ingresar_Bd=copiarArchivoZip($nivel,$nombretemporal,$nombre,$materia,$curso,$concepto);
    }
    else{
      echo "<script>alert('$zip[mensaje]')</script>";
      echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
    }
  }
    if ($ingresar_Bd['exito']) {
      $tamano=($_FILES['archivo']['size'])/1000;//tamaño en KB
      $fecha=date('Y-m-d');
      
      /*ingreso a la base de datos*/
      $c=0;
      while($c<44){
      mysqli_query($conexion,"INSERT INTO `pregunta`( `nombre`, `autor`, `institucion`,
       `herramienta_autor`, `compatible_mobile`, `nivel_educacion`, `materia`, `curso`, `concepto`,
        `ruta_descarga`, `tamano`, `fecha_ingreso`)
         VALUES ('$nombre','$autor','$institucion',
        '$herramienta_autor','$compatible_Mobile','$nivel','$materia','$curso','$concepto',
        '$ingresar_Bd[ruta]','$tamano','$fecha')") or die("Problemas al ingresar 
        en la base de datos".mysqli_error($conexion));
        $c=$c+1;
        }

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