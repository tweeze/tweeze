<?php
 include ("connection.php"); 
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; 
 else {
 print "<form action=\"".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."\" method=\"post\"><br/><input type=\"submit\" name=\"reset\" value=\"Datenbank anlegen oder resetten\"/></form>"; 
  if(isset($_POST['reset'])) {
   if (Dbreset()) print "Datenbank wurde erfolgreich in den Ausgangszustand versetzt";
    else print "Zur&uuml;cksetzen war nicht erfolgreich.";
  } 	
 }
?>