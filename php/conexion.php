<?php
/*
Autor: Daniel Ojeda Sandoval
*/
$conexion=mysqli_connect("localhost","root","root") 
  or  die("Problemas en la conexion");

mysqli_select_db($conexion,"repositorioqti") 
  or  die("Problemas en la selección de la base de datos");
?>