<?php
// error_reporting(E_ALL); //nettes Spielzeug
 include ("connection.php");
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; else {
  print "<form action=\"".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."\" method=\"post\"><textarea name=\"einfeld\" cols=\"40\" rows=\"10\">$_POST[einfeld]</textarea><br/><input type=\"submit\" name=\"wortein\" value=\"Einlesen\"/></form>";
  if(isset($_POST['wortein'])) {
   $inhalt = explode(" ",mysql_real_escape_string($_POST['einfeld']));
   $textgrenze=1000;
   if (count($inhalt)<$textgrenze){
   Text($inhalt);  
   echo "Eingabe ok";
   } else print "Der Text ist zu lang f&uuml;r meine Rechenkapazit&auml;t. Bitte k&uuml;rzen oder mir in mehreren Teilen geben, maximal ".$textgrenze." W&ouml;rter pro.";
  }
 }
?>