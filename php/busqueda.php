<?php 
/*
Autor: Daniel Ojeda Sandoval
*/
include_once "conexion.php";

$concepto = mysqli_real_escape_string($conexion,$_REQUEST['concepto']);
$concepto=strip_tags($concepto);
$nivel = mysqli_real_escape_string($conexion,$_REQUEST['nivel']);
$nivel=strip_tags($nivel);
$materia = mysqli_real_escape_string($conexion,$_REQUEST['materia']);
$materia=strip_tags($materia);
$curso = mysqli_real_escape_string($conexion,$_REQUEST['curso']);
$curso=strip_tags($curso);
$pagina=mysqli_real_escape_string($conexion,$_REQUEST['pagina']);
$pagina=strip_tags($pagina);
$busqueda=true;
if ($nivel == '') {
  $busqueda=false;
  echo "<script>alert('Debe ingresar un nivel')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
if ($concepto == '') {
  $busqueda=false;
  echo "<script>alert('Debe ingresar un concepto')</script>";
  echo "<SCRIPT>window.location='../subida.html';</SCRIPT>";
}
$n_paginas=1;
if ($busqueda) {
	if ($materia=='' and $curso =='') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,materia,curso,concepto,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM  `pregunta` where concepto ='$concepto' and nivel_educacion = '$nivel' ORDER BY id ASC ") or die("Problemas en el select 1:".mysqli_error($conexion));
	}
	elseif ($materia=='' and $curso !='') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,materia,curso,concepto,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where concepto ='$concepto' and nivel_educacion = '$nivel' and curso= '$curso' ORDER BY id ASC ") or die("Problemas en el select 2:".mysqli_error($conexion));
	}
	elseif ($materia !='' and $curso =='') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,materia,curso,concepto,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where concepto ='$concepto' and nivel_educacion = '$nivel' and materia = '$materia' ORDER BY id ASC ") or die("Problemas en el select 3:".mysqli_error($conexion));
	}
	elseif ($materia !='' and $curso !='') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,materia,curso,concepto,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where concepto ='$concepto' and nivel_educacion = '$nivel' and materia = '$materia' and curso = '$curso' ORDER BY id ASC ") or die("Problemas en el select 4:".mysqli_error($conexion));
	}

	$preguntas=array();
	$respuesta='<h3>Resultados de la búsqueda: </h3>';
	$c=0;
	while($reg=mysqli_fetch_array($pregunta,MYSQLI_ASSOC)){
		$preguntas[$c]['id']=$reg['id'];
		$preguntas[$c]['nombre']=$reg['nombre'];
		$preguntas[$c]['autor']=$reg['autor'];
		$preguntas[$c]['compatible_mobile']=$reg['compatible_mobile'];
		$preguntas[$c]['nivel']=$reg['nivel_educacion'];
		$preguntas[$c]['materia']=$reg['materia'];
		$preguntas[$c]['curso']=$reg['curso'];
		$preguntas[$c]['concepto']=$reg['concepto'];
		$preguntas[$c]['ruta']=$reg['ruta_descarga'];
		//$preguntas[$c]['ruta']=str_replace('../', '', $preguntas[$c]['ruta']);
		$preguntas[$c]['descargas']=$reg['numero_descargas'];
		$preguntas[$c]['tamano']=$reg['tamano'];
		$preguntas[$c]['fecha']=$reg['fecha_ingreso'];
		
		$c=$c+1;
	}

	if ($c==0) {
		$pregunta=mysqli_query($conexion,"SELECT fk_id FROM relacion_tags where fk_palabra LIKE '%$concepto%' ORDER BY fk_id ASC ") or die("Problemas en el select 5:".mysqli_error($conexion));

		$c=0;
		while($reg=mysqli_fetch_array($pregunta,MYSQLI_ASSOC)){
			$preguntas[$c]['id']=$reg['fk_id'];
			$c=$c+1;
		}

		if ($c==0) {
			$respuesta='No hay resultados para su búsqueda';
		}
		else{
			/*por cada pregunta sacamos los datos*/
			$preguntas2=array();
			for ($i=0; $i < count($preguntas); $i++) {
				 $id=$preguntas[$i]['id'];
				$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,materia,curso,concepto,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where id = '$id' ORDER BY id ASC ") or die("Problemas en el select 6:".mysqli_error($conexion));
				

				while($reg=mysqli_fetch_array($pregunta,MYSQLI_ASSOC)){
					$preguntas2[$i]['id']=$reg['id'];
					$preguntas2[$i]['nombre']=$reg['nombre'];
					$preguntas2[$i]['autor']=$reg['autor'];
					$preguntas2[$i]['compatible_mobile']=$reg['compatible_mobile'];
					$preguntas2[$i]['nivel']=$reg['nivel_educacion'];
					$preguntas2[$i]['materia']=$reg['materia'];
					$preguntas2[$i]['curso']=$reg['curso'];
					$preguntas2[$i]['concepto']=$reg['concepto'];
					$preguntas2[$i]['ruta']=$reg['ruta_descarga'];
					//$preguntas2[$i]['ruta']=str_replace('../', '', $preguntas2[$i]['ruta']);
					$preguntas2[$i]['descargas']=$reg['numero_descargas'];
					$preguntas2[$i]['tamano']=$reg['tamano'];
					$preguntas2[$i]['fecha']=$reg['fecha_ingreso'];
					
					
				}

			}
			$n_paginas=ceil($i/10);
			if ( $i < ($pagina*10) ) {
				$limite=($i-1);
			}
			else{
				$limite = ($pagina*10)-1;
			}

			if ($i>0) {
		
				for ($i=(($pagina*10)-10); $i <= $limite ; $i++) { 
					$nombre=$preguntas2[$i]['nombre'];
					$autor=$preguntas2[$i]['autor'];
					$compatible_mobile=$preguntas2[$i]['compatible_mobile'];
					$nivel=$preguntas2[$i]['nivel'];
					$materia=$preguntas2[$i]['materia'];
					$curso=$preguntas2[$i]['curso'];
					$concepto=$preguntas2[$i]['concepto'];
					$ruta=$preguntas2[$i]['ruta'];
					$descargas=$preguntas2[$i]['descargas'];
					$tamano=$preguntas2[$i]['tamano'];
					$fecha=$preguntas2[$i]['fecha'];

					$respuesta=$respuesta."<div class='pregunta'>
						<h4>Concepto: $concepto</h4>
						<p>
							<div>Nombre:</div> $nombre<br>
							<div>Autor:</div> $autor<br>
							<div>nivel:</div> $nivel<br>
							<div>materia:</div> $materia<br>
							<div>curso:</div> $curso<br>
							<div>compatible_mobile:</div><span mobi='$compatible_mobile'> $compatible_mobile</span><br>
							<div>descargas:</div> $descargas<br>
							<div>tamaño:</div> $tamano KB<br>
							<div>fecha:</div> $fecha<br>
						</p>
						<div class='descarga' ruta='$ruta' nombre='$nombre'>
							<a href='#'' ><img src='imagenes/download.png'><div>Descargar</div></a>
						</div>
						<div class=separador></div>
					</div>";
				}
			}
		}

	}
	else{
		$n_paginas=ceil($c/10);
			if ( $c < ($pagina*10) ) {
				$limite=($c-1);
			}
			else{
				$limite = ($pagina*10)-1;
			}

			if ($c>0) {
		
				for ($i=(($pagina*10)-10); $i <= $limite ; $i++) { 
					$nombre=$preguntas[$i]['nombre'];
					$autor=$preguntas[$i]['autor'];
					$compatible_mobile=$preguntas[$i]['compatible_mobile'];
					$nivel=$preguntas[$i]['nivel'];
					$materia=$preguntas[$i]['materia'];
					$curso=$preguntas[$i]['curso'];
					$concepto=$preguntas[$i]['concepto'];
					$ruta=$preguntas[$i]['ruta'];
					$descargas=$preguntas[$i]['descargas'];
					$tamano=$preguntas[$i]['tamano'];
					$fecha=$preguntas[$i]['fecha'];

					$respuesta=$respuesta."<div class='pregunta'>
						<h4>Concepto: $concepto</h4>
						<p>
							<div>Nombre:</div> $nombre<br>
							<div>Autor:</div> $autor<br>
							<div>nivel:</div> $nivel<br>
							<div>materia:</div> $materia<br>
							<div>curso:</div> $curso<br>
							<div>compatible_mobile:</div><span mobi='$compatible_mobile'> $compatible_mobile</span><br>
							<div>descargas:</div> $descargas<br>
							<div>tamaño:</div> $tamano KB<br>
							<div>fecha:</div> $fecha<br>
						</p>
						<div class='descarga' ruta='$ruta' nombre='$nombre'>
							<a href='#' ><img src='imagenes/download.png'><div>Descargar</div></a>
						</div>
						<div class=separador></div>
					</div>";
				}
			}

	}
	if ($respuesta != 'No hay resultados para su búsqueda') {
		
		$nav="<div class='nav-down'>
				<a class= 'left-arrow' id='anterior' href='#'><img src='imagenes/btn-anterior.png'> Anterior </a>
				<div class='info'>
					<label>P&aacute;gina: </label><select name='pagina' id='selector'>";
		$nav2="</select> <label>de <span id='n_resultados'>$n_paginas</span></label>
				</div>
				<a class= 'right-arrow' id='siguiente' num='$n_paginas' href='#'><img src='imagenes/btn-siguiente.png'> Siguiente </a>
			</div>	";
		$nav1='';
		for ($i=0; $i < $n_paginas ; $i++) {
							$j=$i+1; 
							if($j==$pagina){
								$nav1 = $nav1."<option selected value = '".$j."'>".$j."</option>";
							}
							else{
								$nav1 = $nav1."<option value = '".$j."'>".$j."</option>";	
							}
							
						}
		$respuesta = $respuesta.$nav.$nav1.$nav2;
	}
	echo $respuesta;
	mysqli_free_result($pregunta);
	mysqli_close($conexion);
}
?>