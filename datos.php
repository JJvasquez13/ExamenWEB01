<?php
//Crea el comjunto de datos que seran presentados en la grafica
$genero = array("Masculino", "Femenino");
$provincia1 = array("San José");
$provincia2 = array("Alajuela");
$provincia3 = array("Heredia");
$provincia4 = array("Puntarenas");
$provincia5 = array("Guanacaste");
$provincia6 = array("Cartago");
$provincia7 = array("Limón");

echo json_encode(array(
  $genero,
  $provincia1,
  $provincia2,
  $provincia3,
  $provincia4,
  $provincia5,
  $provincia6,
  $provincia7
));
