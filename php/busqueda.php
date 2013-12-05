<?php 
/*
Autor: Daniel Ojeda Sandoval
*/
include_once "conexion.php";

$autor = mysqli_real_escape_string($conexion,$_REQUEST['autor']);
$autor=strip_tags($autor);
$tags = mysqli_real_escape_string($conexion,$_REQUEST['tags']);
$tags=strip_tags($tags);
$tipo = mysqli_real_escape_string($conexion,$_REQUEST['tipo']);
$tipo=strip_tags($tipo);
$pagina=mysqli_real_escape_string($conexion,$_REQUEST['pagina']);
$pagina=strip_tags($pagina);
$busqueda=true;

$n_paginas=1;
if ($busqueda) {
	if ($tipo=='' and $autor =='' and $tags == '') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM  `pregunta` ORDER BY id ASC ") or die("Problemas en el select 1:".mysqli_error($conexion));
	}
	elseif ($tipo=='' and $autor !='' and $tags == '') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where autor ='$autor' ORDER BY id ASC ") or die("Problemas en el select 2:".mysqli_error($conexion));
	}
	elseif ($tipo !='' and $autor =='' and $tags == '') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where tipo = '$tipo' ORDER BY id ASC ") or die("Problemas en el select 3:".mysqli_error($conexion));
	}
	elseif ($tipo !='' and $autor !='' and $tags == '') {
		$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where autor ='$autor' and tipo = '$tipo' and curso = '$curso' ORDER BY id ASC ") or die("Problemas en el select 4:".mysqli_error($conexion));
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
		$preguntas[$c]['tipo']=$reg['tipo'];
		$preguntas[$c]['curso']=$reg['curso'];
		$preguntas[$c]['autor']=$reg['autor'];
		$preguntas[$c]['ruta']=$reg['ruta_descarga'];
		//$preguntas[$c]['ruta']=str_replace('../', '', $preguntas[$c]['ruta']);
		$preguntas[$c]['descargas']=$reg['numero_descargas'];
		$preguntas[$c]['tamano']=$reg['tamano'];
		$preguntas[$c]['fecha']=$reg['fecha_ingreso'];
		
		$c=$c+1;
	}

	if ($c==0 and $tags != '') {
		$pregunta=mysqli_query($conexion,"SELECT fk_id FROM relacion_tags where fk_palabra LIKE '%$tags%' ORDER BY fk_id ASC ") or die("Problemas en el select 5:".mysqli_error($conexion));

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
			$d=0;
			for ($i=0; $i < count($preguntas); $i++) {
				 $id=$preguntas[$i]['id'];
				 if ($tipo=='' and $autor =='' and $tags != '') {
				 	$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where id = '$id' ORDER BY id ASC ") or die("Problemas en el select 6:".mysqli_error($conexion));
				 }
				 elseif ($tipo=='' and $autor !='' and $tags != '') {
				 	$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where id = '$id' and autor = '$autor' ORDER BY id ASC ") or die("Problemas en el select 6:".mysqli_error($conexion));
				 }
				 elseif ($tipo !='' and $autor !='' and $tags != '') {
				 	$pregunta=mysqli_query($conexion,"SELECT id, nombre,autor,compatible_mobile,nivel_educacion,tipo,curso,autor,ruta_descarga,numero_descargas,tamano,fecha_ingreso FROM pregunta where id = '$id' and autor = '$autor' and tipo = '$tipo'  ORDER BY id ASC ") or die("Problemas en el select 6:".mysqli_error($conexion));
				 }
				
				
				 
				while($reg=mysqli_fetch_array($pregunta,MYSQLI_ASSOC)){
					$preguntas2[$i]['id']=$reg['id'];
					$preguntas2[$i]['nombre']=$reg['nombre'];
					$preguntas2[$i]['autor']=$reg['autor'];
					$preguntas2[$i]['compatible_mobile']=$reg['compatible_mobile'];
					$preguntas2[$i]['nivel']=$reg['nivel_educacion'];
					$preguntas2[$i]['tipo']=$reg['tipo'];
					$preguntas2[$i]['curso']=$reg['curso'];
					$preguntas2[$i]['autor']=$reg['autor'];
					$preguntas2[$i]['ruta']=$reg['ruta_descarga'];
					//$preguntas2[$i]['ruta']=str_replace('../', '', $preguntas2[$i]['ruta']);
					$preguntas2[$i]['descargas']=$reg['numero_descargas'];
					$preguntas2[$i]['tamano']=$reg['tamano'];
					$preguntas2[$i]['fecha']=$reg['fecha_ingreso'];
					$d=d+1;
					
				}

			}
			if ($d==0) {
				$respuesta='No hay resultados para su búsqueda';
			}
			else{
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
						$tipo=$preguntas2[$i]['tipo'];
						$curso=$preguntas2[$i]['curso'];
						$autor=$preguntas2[$i]['autor'];
						$ruta=$preguntas2[$i]['ruta'];
						$descargas=$preguntas2[$i]['descargas'];
						$tamano=$preguntas2[$i]['tamano'];
						$fecha=$preguntas2[$i]['fecha'];

						$respuesta=$respuesta."<div class='pregunta'>
							<h4><span>Nombre: </span>$nombre</h4>
							<p>
								<div>Autor:</div> $autor<br>
								<div>Nivel:</div> $nivel<br>
								<div>Tipo:</div> $tipo<br>
								<div>Curso:</div> $curso<br>
								<div>Compatible_mobile:</div><span mobi='$compatible_mobile'> $compatible_mobile</span><br>
								<div>Descargas:</div> $descargas<br>
								<div>Tamaño:</div> $tamano KB<br>
								<div>Fecha:</div> $fecha<br>
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

	}
	elseif ($c==0 and $tags =='') {
		$respuesta='No hay resultados para su búsqueda';
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
					$tipo=$preguntas[$i]['tipo'];
					$curso=$preguntas[$i]['curso'];
					$autor=$preguntas[$i]['autor'];
					$ruta=$preguntas[$i]['ruta'];
					$descargas=$preguntas[$i]['descargas'];
					$tamano=$preguntas[$i]['tamano'];
					$fecha=$preguntas[$i]['fecha'];

					$respuesta=$respuesta."<div class='pregunta'>
						<h4><span>Nombre: </span>$nombre</h4>
						<p>
							<div>Autor:</div> $autor<br>
							<div>Nivel:</div> $nivel<br>
							<div>Tipo:</div> $tipo<br>
							<div>Curso:</div> $curso<br>
							<div>Compatible_mobile:</div><span mobi='$compatible_mobile'> $compatible_mobile</span><br>
							<div>Descargas:</div> $descargas<br>
							<div>Tamaño:</div> $tamano KB<br>
							<div>Fecha:</div> $fecha<br>
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
		$respuesta = $respuesta.$nav.$nav1.$nav2."<br><a href='busqueda.html'> <input type = 'button' value='Volver a la búsqueda'></a>";
	}
	else{
		$respuesta=$respuesta."<a href='busqueda.html'> <input type = 'button' value='Volver a la búsqueda'></a>";
	}
	echo $respuesta;
	mysqli_free_result($pregunta);
	mysqli_close($conexion);
}
?>