<?php  //Aufruf mit $zyklus $steps -> wie oft das Skript die Sprache anfragen soll und wie viele Dokumente in einer Anfrage verarbeitet werden sollen. Es werden also bei $zyklus*$steps Dokumenten die Sprache eingetragen.
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");

  if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
  else {
  	require_once 'detectlanguage.php';
  	$detectlanguage = new DetectLanguage("YOUR API KEY"); //Absicht
  	if ((isset($argv[1])) AND $argv[1]!="" ){
  		$zyklus=$argv[1];
  	} else $zyklus = 1;
  	if ((isset($argv[2])) AND $argv[2]!="" ){
  		$steps=$argv[2];
  	} else $steps = 1;  
  	$query = "SELECT * FROM `$datenbank`.$dokument WHERE $full_text !='' AND $full_text is not null AND ($language ='' OR $language is null);";
  	$result = mysql_query($query,$connection);
  	if(!$result) {
  		print "Fehler: " . mysql_error($connection);
  	} else {
  	  for ($i=0; $i<$zyklus; $i++) {
  	  	$texts = array($steps);
  	  	for ($j=0; $j<$steps ; $j++) {
  	  		if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
  	  		  $fulltext = $row[$full_text];
  	  		  $dids[$j] = $row[$id_dokument];
  	  		  $fulltext1 = explode(" ",$fulltext); 
  	  		  $fulltext = "";
  	  		  for ($k=0; $k<20; $k++) {
                if ($k>0) $fulltext .= " ";
  	  			$fulltext .= $fulltext1[$k];
  	  		  }
              $texts[$j] = $fulltext;  	  			
  	  		}  else {
  	  			print "Zu allen eingelesenen Dokumenten wurde die Sprache erfasst.";
  	  			break;
  	  		}	  		
  	  	}
  	  	$results = $detectlanguage->detect($texts);
  	    for ($j=0; $j<$steps; $j++) {
            $lang = $results[$j][0]->language;
  	    	$query2 = "UPDATE `$datenbank`.$dokument SET $language='$lang' WHERE $id_dokument=$dids[$j];";
  	    	$result2 = mysql_query($query2,$connection);
  	    	if(!$result2) {
  	    		print "Fehler: " . mysql_error($connection);
  	    	} else {
  	    		//Erfolg
  	    	}
  	    }
  	  }	
  	}	  	
  }
?>