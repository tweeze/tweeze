<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");
  if ((isset($argv[1])) AND $argv[1]!="" ){
  	DokAkt($argv[1]);
  }
?>