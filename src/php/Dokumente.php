<?php
 include ("connection.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
 else {	 
  if(isset($_GET['asso'])) {
   $query = "SELECT * FROM dokument WHERE id_dokument='$_GET[asso]';";  
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
	if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	 $asso=$row[assoziiert];
	 if ($asso==1) $query = "UPDATE dokument SET assoziiert=0 WHERE id_dokument='$_GET[asso]';";
	 if ($asso==0) $query = "UPDATE dokument SET assoziiert=1 WHERE id_dokument='$_GET[asso]';";
	 $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
       //hier könnte dem Erfolg der Aktion Ausdruck verliehen werden
     }
	} else print "ung&uuml;ltiger Aufruf<br/>";
   }
  }	 
  if ($_GET['sortierung']=="Titel") $query = "SELECT * FROM dokument ORDER BY bezeichner;";
  else if ($_GET['sortierung']=="Quelle") $query = "SELECT * FROM dokument ORDER BY url;";
  else $query = "SELECT * FROM dokument;";	 
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   $anzahl = mysql_num_rows($result);
   if ($anzahl==0) { print "Es wurden keine Dokumente eingelesen.";} 
   else{
	print "<table border='1'><tr><th><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."&sortierung=Titel'>Titel</a></th><th><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."&sortierung=Quelle'>Quelle</a></th><th><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."'/>Zeitstempel</a></th><th>eingelesen</th><th>assoziiert</th><th>W&ouml;rter</th></tr>";
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	 print "<tr><td><a href='".$_SERVER["PHP_SELF"]."?seite=Dokumentansicht&dok=".$row[id_dokument]."'>".$row[bezeichner]."</a></td><td>".$row[url]."</td><td>".$row[zeitstempel]."</td><td>".$row[eingelesen]."</td><td><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."&sortierung=".$_GET['sortierung']."&asso=".$row[id_dokument]."'>".$row[assoziiert]."</td>";	 
	 $query2 = "SELECT max(stelle) FROM text WHERE dokument_id='$row[id_dokument]';";
	 $result2 = mysql_query($query2,$connection);
     if(!$result2) {
      print "Fehler: " . mysql_error($connection);
     } else {
	  $max= mysql_result($result2,0);
	  $max++;
      print "<td>".$max."</td>";
     }  
	 "</tr>"; 
	}
	print "</table>";
   }
  }
 }
?>