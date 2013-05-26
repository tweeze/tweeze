<?php
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
  	$query = "SELECT * FROM `$database`.$dokument WHERE $full_text !='' AND $full_text is not null AND $language !='' AND $language is not null;";
  	$result = mysql_query($query,$connection);
  	if(!$result) {
  		print "Fehler: " . mysql_error($connection);
  	} else {
  	  for ($i=0; $i<$zyklus; $i++) {
  	  	$texts = null;
  	  	for ($j=0; $j<$steps && $row = mysql_fetch_array($result,MYSQL_ASSOC); $j++) {
  	  		$fulltext = $row[$full_text];
  	  		$dids[$j] = $row[$id_dokument];
  	  		$fulltext1 = explode(" ",$fulltext); 
  	  		$fulltext = "";
  	  		for ($k=0; $k<10; $k++) {
  	  			$fulltext += $fulltext1[$k];
  	  		}
            $texts[$j] = $fulltext;  	  		
  	  	}
  	  	$results = $detectlanguage->detect($texts);
  	    for ($j=0; $j<$steps; $j++) {
  	    	$query2 = "UPDATE `$database`.$dokument SET $language='$results[$j][0]' WHERE $id_dokument=$dids[$j];";
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