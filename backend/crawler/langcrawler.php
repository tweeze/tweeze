<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");

  if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
  else {
  	require_once 'detectlanguage.php';
  	$detectlanguage = new DetectLanguage("YOUR API KEY");
  	if ((isset($argv[1])) AND $argv[1]!="" ){
  		$zyklus=$argv[1];
  	} else $zyklus = 1;
  	if ((isset($argv[2])) AND $argv[2]!="" ){
  		$steps=$argv[2];
  	} else $steps = 1;  
  	$query = "SELECT * FROM";
  	//Abfrage: alle eingelesenen Texte mit Volltexten ohne language gesetzt
  	//$steps-viele Texte lesen und in Abfrage an detectlanguage senden
  	//erhaltene Sprachen in DB schreiben 	
  	
  }
?>