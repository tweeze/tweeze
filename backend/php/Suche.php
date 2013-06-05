<?php
 include ("connection.php"); 
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
 else {
  if (!isset($_POST['Suchfeld'])) {
   print "<form action=\"".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."\" method=\"post\" ><input name=\"Suchfeld\" type=\"text\" value=\"\"/><input type=\"submit\" name=\"\" value=\"Suche\"/></form>";
  }
  else{
   print "<form action=\"".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."\" method=\"post\" ><input name=\"Suchfeld\" type=\"text\" value=\"".$_POST['Suchfeld']."\"/><input type=\"submit\" name=\"\" value=\"Suche\"/></form>";
   $inhalt = explode(" ",$_POST['Suchfeld']);   
   //N= wie viele Dokumente gibt es insgesamt
   $query = "SELECT count($id_dokument) FROM `$datenbank`.$dokument;";
   $result = mysql_query($query,$connection);
   if(!$result) {
   	print "Fehler: " . mysql_error($connection);
   } else {
   	$N= mysql_result($result,0);
   }
   //n=In wie vielen Dokumenten taucht das Wort überhaupt auf
   foreach ($inhalt AS $idw => $wort1) {
   	$query = "SELECT COUNT(*) FROM `$datenbank`.$dokument` WHERE $full_text LIKE '%$wort1%';";
    $result = mysql_query($query,$connection);
    if(!$result) {
   	 print "Fehler: " . mysql_error($connection);
    } else {
   	 $n[$idw]= mysql_result($result,0);
    }
   }
   //anzahl = wie oft taucht das Wort insgesamt auf, inklusive mehrmals in einem Dokument
   $anzahl;	
   foreach ($inhalt AS $idw => $wort1) {
   	$query = "SELECT * FROM `$datenbank`.$dokument` WHERE $full_text LIKE '%$wort1%';";
    $result = mysql_query($query,$connection);
    if(!$result) {
   	 print "Fehler: " . mysql_error($connection);
    } else {
   	 while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
   	   $inhalt2 = $row[$full_text];	
   	   $regex1 = "#$wort1#s"; //achtet auf Groß-Kleinschreibung! mit i case-insensitive
   	   if (isset($anzahl[$idw])) $anzahl[$idw] = $anzahl[$idw] + preg_match_all($regex1, $inhalt2, $para);
   	   else $anzahl[$idw] = preg_match_all($regex1, $inhalt2, $para);
   	   //Anzahl des Auftretends dieses Worts im Dokument auch gleich mitnehmen
   	   $t[$row[$id_dokument]][$idw] = preg_match_all($regex1, $inhalt2, $para);
   	   //Und Anzahl der Wörter dieses Dokuments
   	   $wortges[$row[$id_dokument]] = count(explode(" ",$inhalt2));
   	 }
    }
   }
   //$IDF=(logn($N,2))/($n+1);
   foreach ($inhalt AS $idw => $wort1) {
   	$IDF[$idw]=(logn($N,2))/($n[$idw]+1);
   }  
   //Liste aller Dokumente, in denen eins der Suchworte vorkommt und die im Ergebnis erscheinen
   $query = "SELECT DISTINCT $id_dokument FROM `$datenbank`.$dokument WHERE ";   
   for ($i=0;$i<count($inhalt);$i++) {
	if ($i>0) $query.=" OR ";
	$query.="`$full_text` LIKE '%$inhalt[$i]%'";
   }
   $query.=";";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
	$anzahl2 = mysql_num_rows($result);
	if ($anzahl2>0) {
	 while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	  if (isset($erglis[$row[$id_dokument]])) $erglis[$row[$id_dokument]]+=1;
	  else $erglis[$row[$id_dokument]] = 1;	  
	 }
	 //zu jedem Ergebnisdokument: 
	 foreach($erglis as $key => $value) {
	  foreach ($inhalt as $idw => $wortid1) {
	   if ($n[$idw]!=0) if (isset($IDF2[$key]))$IDF2[$key]+=$IDF[$idw]; else $IDF2[$key]=$IDF[$idw]; //inverse Dokumenthäufigkeit

	   if ($wortges[$key]==1) {/*Logarithmus von 1 ist nicht definiert */}
	   else $TF=logn($t[$key][$idw]+1,2)/logn($wortges[$key],2); //Termfrequenz
	   if (isset($ergis2[$key])) $ergis2[$key]+=$TF*$IDF2[$key]; else $ergis2[$key]=$TF*$IDF2[$key];
	  }  
	 }
	 print "<br/><br/>";
	 if(count($ergis2)>1) arsort($ergis2);
	  foreach($ergis2 as $key => $value) {
	   if ($value>0) {   
	    $query = "SELECT $dokument.$id_dokument  AS idd, $bezeichner, $url, $expanded_url FROM `$datenbank`.$twz_urls, `$datenbank`.$dokument WHERE $dokument.$id_dokument='$key' AND $twz_urls.id=$dokument.$id_dokument;";
	    $result = mysql_query($query,$connection);
        if(!$result) {
         print "Fehler: " . mysql_error($connection);
        } else {
		 if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		  print "<br/>".$row[$bezeichner]." <a href='".$_SERVER["PHP_SELF"]."?seite=Dokumentansicht&dok=".$row['idd']."'>Im Cache</a>"; 
	  	  if (!($row[$url]==NULL)) print " <a href='".$row[$expanded_url]."'>Internetdokument</a>";	
		 }
	    }
	   }
	  }
	} else print "Kein Ergebnis.";
   }
  }
 }
 	//$query2 = "SELECT * FROM wort, text WHERE id_wort=wort_id AND dokument_id='$row[dokument_id]'"; //stelle ='$row[stelle]'
	//suche, ob Eingabe in DB vorhanden und in einem Dokument, wenn ja gib Textstelle mit Link zum Volltext an (wie google)
	 //dann suche in asso2 nach Ersetzungen und suche damit weitere Dokumente, gewichte diese nach Ersetzungsgrad
	//wenn ein oder mehrere Wörter nicht vorhanden, bilde Variationen, die du in Asso2 speicherst und frage Benutzer, ob er dieses oder jenes meinte. Setze Zähler, welche wie oft richtig waren und frage ab einem gewissen Grad nicht mehr nach, sondern ersetze automatisch 
?>