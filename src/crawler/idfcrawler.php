<?php
 //vorher ans Einlesen denken
 include ("../php/connection.php");
 include ("../php/Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
 else {
  $query = "SELECT * FROM wort";
  $result = mysql_query($query,$connection);

  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   $query2 = "SELECT count(id_dokument) FROM dokument;";
	 $result2 = mysql_query($query2,$connection);
   if(!$result2) {
    print "Fehler: " . mysql_error($connection);
   } else {
	  $N= mysql_result($result2,0);
   }	 
   while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {

   	$query1 = "SELECT count(id_dokument) FROM dokument, text WHERE wort_id='$row[id_wort]' AND id_dokument=dokument_id;";
	$result1 = mysql_query($query1,$connection);
    if(!$result1) {
     print "Fehler: " . mysql_error($connection);
    } else {
	   $n= mysql_result($result1,0);
    }
   	$IDF=(logn($N,2))/($n+1);

   	$query3 = "UPDATE wort SET idf='$IDF' WHERE id_wort='$row[id_wort]'";

   	$result3 = mysql_query($query3,$connection);
    if($result3) {
    //nix
    }

   }

   print "Es wurden alle inversen Dokumenth&auml;ufigkeiten berechnet und in die Datenbank eingetragen.";

  }
 }
?>