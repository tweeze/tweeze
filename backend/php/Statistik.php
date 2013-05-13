<?php
 include ("connection.php");
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; 
 else {
  print "<br/>Dokumente: ";
  print Tabzeilen($dokument);
  print "<br/><li/>davon eingelesen: ";
  $query = "SELECT * FROM `$datenbank`.$dokument WHERE $eingelesen=1;";
  $result = mysql_query($query,$connection);
  if(!$result) {
  	print "Fehler: " . mysql_error($connection);
  } else {
  	$anzahl2 = mysql_num_rows($result);
  	print $anzahl2;
  }
  print "<br/><li/>davon Blacklist oder empty (Zugang verweigert oder Problem mit Zeichensatz): ";
  $query = "SELECT * FROM `$datenbank`.$dokument WHERE $bezeichner LIKE 'blacklist' OR $bezeichner LIKE 'empty';";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
	$anzahl1 = mysql_num_rows($result);
	print $anzahl1;
  }
 }
?>