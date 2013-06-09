<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");
  
  if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
  else {
    if ((isset($argv[1])) AND $argv[1]!="" ){
  	    $zyklus=$argv[1];
  	} else $zyklus=1;
    if ((isset($argv[2])) AND $argv[2]!="" ){
  	    $begin=$argv[1];
  	} else $begin=0;
  	$query = "SELECT * FROM `$datenbank`.$dokument WHERE $id_dokument>=$begin;";
  	$result = mysql_query($query,$connection);
  	if(!$result) {
  		print "Fehler: " . mysql_error($connection);
  	} else {
  	   $timestamp = time();
  	   for ($i=0; $i<$zyklus && $row = mysql_fetch_array($result,MYSQL_ASSOC) && $i<1; $i++) { //<-debug
  		 $inhalt = $row[$full_text];
  		 print " Row: "; //<-debug
  		 print_r($row);	//<-debug
  		 print $row[0]; //<-debug
  		 print " Inhalt: ".$inhalt; //<- debug
  		 $regex1 = "#(\w)*#i";
  	     print " ".$row[$id_dokument];
  		 $wortdok = preg_match_all($regex1, $inhalt, $para);
  		 print " Wörter: ".$wortdok;
  		 for($j=0; $j<count($para[0] && $j<1);$j++) { //<-debug
  		 	print_r($para[0][$j]); //<-debug
  		 	$query2 = "SELECT * FROM `$datenbank`.$twz_word WHERE $word='$para[1][$j]';";
  		 	print_r($para[0][$j]);
  		 	print $query2; //<-debug
  		 	print $para[$j]; //<->debug
            $result2 = mysql_query($query2,$connection);
            if(!$result2) {
  		      print "Fehler: " . mysql_error($connection);
  	        } else {
  	        	if (!($row2 = mysql_fetch_array($result,MYSQL_ASSOC))) {
  	        		//$N = Gesamtanzahl Dokumente in DB
  	        		$query2 = "SELECT COUNT($id_dokument) FROM `$datenbank`.$dokument;";
  	        		$result2 = mysql_query($query2,$connection);
  	        		if(!$result2) {
  	        			print "Fehler: " . mysql_error($connection);
  	        		} else {
  	        			$N = mysql_result($result2,0);
  	        		}	
  	        		//neues Wort eintragen + neue Id zurückgeben lassen	
  	        		$query2 = "INSERT INTO `$datenbank`.$twz_word ($word) VALUES ('$para[0][$j]');";
  	        		$result2 = mysql_query($query2,$connection);
  	        		if(!$result2) {
  	        			print "Fehler: " . mysql_error($connection);
  	        		} else {
  	        			$idnew = mysql_insert_id();
  	        		}	  	   
  	        		//Dokumente, in denen das Wort vorkommt. Stellen ermitteln und eintragen   	
  	        		//-> ev. mit Kriterium keyword, title, description oder fulltext?
  	        		$countallnow = 0;
  	        		$query2 = "SELECT * from `$datenbank`.$dokument WHERE $full_text LIKE '%$para[0][$j]%';";
  	        		$n = 0;
  	        		$result2 = mysql_query($query2,$connection);
  	        		if(!$result2) {
  	        			print "Fehler: " . mysql_error($connection);
  	        		} else {
  	        			while ($row2 = mysql_fetch_array($result,MYSQL_ASSOC)) {
  	        				$inhalt2 = $row[$fulltext];
  	        				$c = preg_match_all($regex1, $inhalt2, $para2);
  	        				if ($c>0) $n++;
  	        				for($k=0; $k<count($para2[0]);$k++) {
  	        					if($para2[0][$k]==$para[0][$j]) {
  	        						$query3 = "INSERT INTO `$datenbank`.$text ($wort_id, $dok_id, $position) VALUES ($idnew,$row[$id_dokument],$k);";
  	        						$result3 = mysql_query($query3,$connection);
  	        						if(!$result3) {
  	        							print "Fehler: " . mysql_error($connection);
  	        						} else {
  	        			               $countallnow++;
  	        						}
  	        					}
  	        				}
  	        			}
  	        		}		
  	        		$idfnow = (logn($N,2))/($n+1);
  	        		$query2 = "UPDATE `$datenbank`.$word SET $countall=$countallnow, $countdok=$n, $idf=$idfnow WHERE $id_word=$idnew";
  	        		$result2 = mysql_query($query2,$connection);
  	        		if(!$result2) {
  	        			print "Fehler: " . mysql_error($connection);
  	        		} else {
  	        			//Erfolg
  	        		}  	        		
  	        	}
  	        }
  		 }
  		 $timestamp1 = time();
  		 print " ".($timestamp1-$timestamp)." Sekunden";
  		 $timestamp = $timestamp1;
  	   }
  	 	
  	}
  	 
  }
?>