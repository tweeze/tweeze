<?php
 include ("connection.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
 else {	 
  if (isset($_GET['sortierung']) && $_GET['sortierung']=="Titel") $query = "SELECT * FROM `$datenbank`.twz_urls, `$datenbank`.$dokument WHERE twz_urls.id=$dokument.$url ORDER BY $bezeichner;";
  else if (isset($_GET['sortierung']) && $_GET['sortierung']=="Quelle") $query = "SELECT * FROM `$datenbank`.twz_urls, `$datenbank`.$dokument WHERE twz_urls.id=$dokument.$url ORDER BY expanded_url;";
  else $query = "SELECT * FROM `$datenbank`.twz_urls, `$datenbank`.$dokument WHERE twz_urls.id=$dokument.$url;";	 
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   $anzahl = mysql_num_rows($result);
   if ($anzahl==0) { print "Es wurden keine Dokumente eingelesen.";} 
   else{
	print "<table border='1'><tr><th><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."&sortierung=Titel'>Titel</a></th><th><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."&sortierung=Quelle'>Quelle</a></th><th><a href='".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."'/>Zeitstempel</a></th><th>eingelesen</th><th>W&ouml;rter</th></tr>";
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	 print "<tr><td><a href='".$_SERVER["PHP_SELF"]."?seite=Dokumentansicht&dok=".$row[$id_dokument]."'>".$row[$bezeichner]."</a></td><td>".$row['expanded_url']."</td><td>".$row[$zeitstempel]."</td><td>".$row[$eingelesen]."</td>";	 
	 $max = count(explode(" ",$row[$full_text]));
      print "<td>".$max."</td>";
      
	 "</tr>"; 
	}
	print "</table>";
   }
  }
 }
?>