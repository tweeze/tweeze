<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");
  include ("PorterStemmer_de.class.php");
  $stemmer = new PorterStemmer_de;
  
  if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
  else {
    if ((isset($argv[1])) AND $argv[1]!="" ){
  	    $zyklus=$argv[1];
  	} else $zyklus=1;
    if ((isset($argv[2])) AND $argv[2]!="" ){
  	    $begin=$argv[2];
  	} else $begin=0;
  	$query = "SELECT * FROM `$datenbank`.$dokument WHERE $id_dokument>=$begin AND $language='de';";
  	$result = mysql_query($query,$connection);
  	if(!$result) {
  		print "Fehler: " . mysql_error($connection);
  	} else {
  	   $timestamp = time();  	   
  	   for ($i=0; $i<$zyklus && ($row=mysql_fetch_array($result,MYSQL_ASSOC)); $i++) {
  	   	 $next = 0;
  		 $inhalt = $row[$full_text];
  		 $inhalt2 = $row[$bezeichner];
  		 $id_dok = $row[$id_dokument];
  		 $regex1 = "#[\w]+#i";
  	     print " ".$row[$id_dokument];
  		 $wortdok = preg_match_all($regex1, $inhalt, $para);
  		 $wortdoktit = preg_match_all($regex1, $inhalt2, $para2);
  		 print " Wörter: ".$wortdok;
  		 for($j=0; $j<count($para[0]);$j++) {
  			 for($k=0; $k<count($stopwords["de"]);$k++) {
  		 		if($stopwords["de"][$k]==$para[0][$j]) {
  		 			$para[0][$j]=="";
  		 			break;
  		 		}
  			 }
  			if ($para[0][$j]!="") $para[0][$j] = $stemmer->stem($para[0][$j]);
  		 }
  		 for($j=0; $j<count($para2[0]);$j++) {
  			 for($k=0; $k<count($stopwords["de"]);$k++) {
  		 		if($stopwords["de"][$k]==$para2[0][$j]) {
  		 			$para2[0][$j]=="";
  		 			break;
  		 		}
  			 }
  			if ($para2[0][$j]!="") $para2[0][$j] = $stemmer->stem($para2[0][$j]);
  		 }
  		 $wortliste = null;
  		 $titliste = null;
  		 for($j=0; $j<count($para[0]);$j++) {
  		 	if ($para[0][$j]!="") {
  		 		$checkwort = 0;
  		 		for ($k=0; $k<count($wortliste);$k++) {
  		 	  	if ($wortliste[$k][0]==$para[0][$j]) {
  		 	  		$wortliste[$k][1]++;
  		 	  		$checkwort = 1; 	
  		 	  		break;
  		 	 	 }
  		 		}
  		 		if(!$checkwort) {
  		 			$wortliste[][0] = $para[0][$j];
  		 			$wortliste[count($wortliste)-1][1] = 1;
  		 		}  		 		
  		 	}
  		 }
  		 for($j=0; $j<count($para2[0]);$j++) {
  		 	if ($para2[0][$j]!="") {
  		 		$checkwort = 0;
  		 		for ($k=0; $k<count($wortliste);$k++) {
  		 			if ($wortliste[$k][0]==$para2[0][$j]) {
  		 				$wortliste[$k][1]++;
  		 				$checkwort = 1;
  		 				$titliste[$k] = 1;
  		 				break;
  		 			}
  		 		}
  		 		if(!$checkwort) {
  		 			$wortliste[][0] = $para2[0][$j];
  		 			$wortliste[count($wortliste)-1][1] = 1;
  		 			$titliste[count($wortliste)-1] = 1;
  		 		}
  		 	}
  		 }
  		 $query4 = "";
  		 for ($j=0;$j<count($wortliste);$j++) {
  		 	 $worteintrag=$wortliste[$j][0];
  			 $query2 = "SELECT * FROM `$datenbank`.$twz_word WHERE $word='$worteintrag';";
  			 $result2 = mysql_query($query2,$connection);
  			 if(!$result2) {
  			 	print "Fehler: " . mysql_error($connection);
  			 } else {
				if ($row2 = mysql_fetch_array($result2,MYSQL_ASSOC)) {
					$idnew = $row2[$id_word];
				} else {
					$query3 = "INSERT INTO `$datenbank`.$twz_word ($word) VALUES ('$worteintrag');";
					$result3 = mysql_query($query3,$connection);
					if(!$result3) {
						print "Fehler: " . mysql_error($connection);
					} else {
						$idnew = mysql_insert_id();
					}
				}
				$wortzahl = $wortliste[$j][1];
				if ($query4=="")  $query4 .= "INSERT INTO `$datenbank`.$twz_text ($wort_id, $dok_id, $title, $countindoc) VALUES";
  			    if ($next) $query4 .= ","; else $next = 1;
  	        	if (isset($titliste[$j])) {
  	        		$query4 .= " ('$idnew','$id_dok','1', '$wortzahl')";
  	        	} else {
  	        		$query4 .= " ('$idnew','$id_dok','0', '$wortzahl')";
  	        	}
  			 }  		 	
  		 }
  		 $query4 .= ";";
  		 $result4 = mysql_query($query4,$connection);
  		 if(!$result4) {
  		 	print "Fehler: " . mysql_error($connection);
  		 } else {
  		 	//
  		 } 		 
  		
  		 $timestamp1 = time();
  		 print " ".($timestamp1-$timestamp)." Sekunden ";
  		 $timestamp = $timestamp1;
  	   } // */
  	 	
  	}
  	 
  }
?>