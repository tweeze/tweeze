<?php
 include ("connection.php"); 
 include ("Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
 else {
  print "<form action=\"".$_SERVER["PHP_SELF"]."?seite=".$_GET['seite']."\" method=\"post\" ><input name=\"Suchfeld\" type=\"text\" value=\"$_POST[Suchfeld]\"/><input type=\"submit\" name=\"\" value=\"Suche\"/></form>"; 
  if (isset($_POST['Suchfeld'])) {
   $inhalt = explode(" ",$_POST['Suchfeld']);      
   foreach ($inhalt AS $wort) {
	$wortlist[]=Wortzuid($wort);
	$query = "SELECT count(id_dokument) FROM dokument, wort, text WHERE id_wort=wort_id AND id_dokument=dokument_id AND wort LIKE '$wort';";
	$result = mysql_query($query,$connection);
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {
	 $n[Wortzuid($wort)]= mysql_result($result,0);
    }
   }
   $query = "SELECT DISTINCT dokument_id FROM text WHERE ";
   for ($i=0;$i<count($wortlist);$i++) {
	if ($i>0) $query.=" OR ";
	$query.="wort_id='$wortlist[$i]'";
   }
   $query.=";";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
	$anzahl = mysql_num_rows($result);
	if ($anzahl>0) {
	 while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	  if (isset($erglis[$row[dokument_id]])) $erglis[dokument_id]+=1;
	  else $erglis[$row[dokument_id]] = 1;	  
	 }
	 $query = "SELECT count(id_dokument) FROM dokument;";
	 $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
	   $N= mysql_result($result,0);
     }	 
	 foreach($erglis as $key => $value) {
	  foreach ($wortlist as $wortid) {
	   if ($n[$wortid]!=0) $IDF[$key]+=(logn($N,2))/$n[$wortid]+1; //inverse Dokumenthäufigkeit
	  }
	  $query = "SELECT count(stelle) FROM text WHERE dokument_id='$key' AND wort_id='$wortid';";
	  $result = mysql_query($query,$connection);
      if(!$result) {
       print "Fehler: " . mysql_error($connection);
      } else {
	   $t= mysql_result($result,0);
      }
	  $query2 = "SELECT max(stelle) FROM text WHERE dokument_id='$key';";
	  $result2 = mysql_query($query2,$connection);
      if(!$result2) {
       print "Fehler: " . mysql_error($connection);
      } else {
	   $wortanzahl= mysql_result($result2,0);
	   $wortanzahl++;
      }
	  if ($wortanzahl==1) {}
	  else $TF=logn($t+1,2)/logn($wortanzahl,2); //Termfrequenz
	  $ergis2[$key]+=$TF*$IDF[$key];
	 }
	 print "<br/><br/>";
	 if(count($ergis2)>1) arsort($ergis2);
	  foreach($ergis2 as $key => $value) {
	   if ($value>0) {   
	    $query = "SELECT * FROM dokument WHERE id_dokument='$key';";
	    $result = mysql_query($query,$connection);
        if(!$result) {
         print "Fehler: " . mysql_error($connection);
        } else {
		 if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
		  print "<br/>".$row[bezeichner]." <a href='".$_SERVER["PHP_SELF"]."?seite=Dokumentansicht&dok=".$row[id_dokument]."'>Im Cache</a>"; 
	  	  if (!($row[url]==NULL)) print " <a href='".$row[url]."'>Internetdokument</a>";	
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