<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");
  $zyklus = Tabzeilen($dokument);
  for ($i = 1; $i<=266; $i++) {
  	SetTitle($i);
  }
?>