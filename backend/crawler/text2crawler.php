<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");
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
  		 $inhalt = $row[$full_text];
  		 $regex1 = "#[\wäÄöÖüÜß]+#i";
  	     print " ".$row[$id_dokument];
  		 $wortdok = preg_match_all($regex1, $inhalt, $para);
  		 print " Wörter: ".$wortdok;
  		 for($j=0; $j<count($para[0]);$j++) { 
  		 	$parastr = $para[0][$j];
  		 	$check = 1;
  		 	$parastr = $stemmer->stem($parastr);
  		 	for($k=0; $k<count($stopwords["de"]);$k++) {
  		 		if($stopwords["de"][$k]==$parastr) {
  		 			$check = 0;
  		 			break;
  		 		}
  		 	}
  		 	if ($check) {
  		 		$query2 = "SELECT * FROM `$datenbank`.$twz_word WHERE $word='$parastr';";
            	$result2 = mysql_query($query2,$connection);
            	if(!$result2) {
  		      	print "Fehler: " . mysql_error($connection);
  	        	} else {
  	        		if (!($row2 = mysql_fetch_array($result2,MYSQL_ASSOC))) {
  	        			//$N = Gesamtanzahl Dokumente in DB
  	        			$query3 = "SELECT COUNT($id_dokument) FROM `$datenbank`.$dokument WHERE $language='de';";
  	        			$result3 = mysql_query($query3,$connection);
  	        			if(!$result3) {
  	        				print "Fehler: " . mysql_error($connection);
  	        			} else {
  	        				$N = mysql_result($result3,0);
  	        			}	
  	        			//neues Wort eintragen + neue Id zurückgeben lassen	
  	        			$query3 = "INSERT INTO `$datenbank`.$twz_word ($word) VALUES ('$parastr');";
  	        			$result3 = mysql_query($query3,$connection);
  	        			if(!$result3) {
  	        				print "Fehler: " . mysql_error($connection);
  	        			} else {
  	        				$idnew = mysql_insert_id();
  	        			}	  	   
  	        			//Dokumente, in denen das Wort vorkommt. Stellen ermitteln und eintragen   	
  	        			//-> ev. mit Kriterium keyword, title, description oder fulltext?
  	        			$countallnow = 0;
  	        			$query3 = "SELECT * from `$datenbank`.$dokument WHERE $full_text LIKE '%$parastr%' AND $language='de';";
  	        			$n = 0;
  	        			$result3 = mysql_query($query3,$connection);
  	        			if(!$result3) {
  	        				print "Fehler: " . mysql_error($connection);
  	        			} else {
  	        				while ($row3 = mysql_fetch_array($result3,MYSQL_ASSOC)) {
  	        					$inhalt2 = $row3[$full_text];
  	        					$c = preg_match_all($regex1, $inhalt2, $para2);
  	        					$first = 0;
  	        					for($k=0; $k<count($para2[0]);$k++) {
  	        						$parastr2 = $stemmer->stem($parastr2);
  	        						if($parastr==$parastr2) {
  	        							if (!$first) {
  	        								$n++;
  	        								$first=1;  	        								
  	        							}
  	        							$query4 = "INSERT INTO `$datenbank`.$twz_text ($wort_id, $dok_id, $position) VALUES ('$idnew','$row3[$id_dokument]','$k');";
  	        							$result4 = mysql_query($query4,$connection);
  	        							if(!$result4) {
  	        								print "Fehler: " . mysql_error($connection);
  	        							} else {
  	        				               $countallnow++;
  	        							}
  	        						}
  	        					}
  	        				}
  	        			}		
  	        			$idfnow = (logn($N,2))/($n+1);
  	        			$query3 = "UPDATE `$datenbank`.$twz_word SET $countall=$countallnow, $countdok=$n, $idf=$idfnow WHERE $id_word=$idnew";
  	        			$result3 = mysql_query($query3,$connection);
  	        			if(!$result3) {
  	        				print "Fehler: " . mysql_error($connection);
  	        			} else {
  	        				//Erfolg
  	        			}  	        		
  	        		}
  	        	}
  		 	}
  		 }
  		 $timestamp1 = time();
  		 print " ".($timestamp1-$timestamp)." Sekunden ";
  		 $timestamp = $timestamp1;
  	   } // */
  	 	
  	}
  	 
  }
?>