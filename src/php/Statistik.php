<?php
 include ("connection.php");
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; 
 else {
  print "W&ouml;rter unique: ";
  print Tabzeilen("wort");
  print "<br/>W&ouml;rter verwendet: ";
  print Tabzeilen("text");
  print "<br/>Dokumente: ";
  print Tabzeilen("dokument");
  print "<br/><li/>davon Blacklist oder empty (Zugang verweigert oder Problem mit Zeichensatz): ";
  $query = "SELECT * FROM dokument WHERE bezeichner LIKE 'blacklist' OR bezeichner LIKE 'empty';";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
	$anzahl = mysql_num_rows($result);
	print $anzahl;
  }
  print "<br/>Die h&auml;ufigsten W&ouml;rter:<br/>";
  $query = "SELECT * FROM wort ORDER BY anzahl DESC;";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   for ($i=0;$i<100 && ($row = mysql_fetch_array($result,MYSQL_ASSOC));$i++) {
	print "<li/>".$row[wort]." Anzahl: ".$row[anzahl]." IDF: ".$row[idf];   
   }
  }
 }
?>