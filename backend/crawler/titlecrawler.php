<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php"); /*
  $zyklus = Tabzeilen($dokument);
  for ($i = 1; $i<=266; $i++) {
  	SetTitle($i);
  } //*/
  if ((isset($argv[1])) AND $argv[1]!="" ){
  	SetTitle($argv[1]);
  }
?>