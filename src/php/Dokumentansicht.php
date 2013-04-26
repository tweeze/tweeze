<?php
 include ("connection.php");
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
 else {
  if(!isset($_GET['dok'])) { 
	print "Es wurde kein Dokument ausgew&auml;hlt.";
  } else {
   Dokvolltext($_GET['dok'], true);
  }
 }
?>