<?php
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");

  if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>";
  else {
  	if (!(Tabzeilen($dokument)>0)) {
  		$query = "SELECT id from `$datenbank`.$twz_urls;";
  		$result = mysql_query($query,$connection);
  		if(!$result) {
  			print "Fehler: " . mysql_error($connection);
  		} else {
  			while ($row = mysql_fetch_array($result,MYSQL_ASSOC))	{
  				$query2 = "INSERT INTO `$datenbank`.$dokument ($url) VALUES ($row[id]);";
  				$result2 = mysql_query($query2,$connection);
  				if(!$result2) {
  					print "Fehler: " . mysql_error($connection);
  				} else {
  					//
  				}
  			}
  		}
  	} else {
  		//-> prüfen, ob Anzahl übereinstimmt, sonst neue Ids suchen und übertragen
  	}
  	if (!(Tabzeilen($dokument)>0)) {
  		print "Es konnten keine g&uuml;ltigen URLs aus $twz_urls geladen werden.";
  	} else {
  		if ((isset($argv[1])) AND $argv[1]!="" ){
  			$zyklus=$argv[1];
  		} else $zyklus=1;
  		$query = "SELECT $expanded_url, $dokument.$url, $dokument.$id_dokument FROM `$datenbank`.$dokument, $twz_urls WHERE $eingelesen=0 AND $dokument.$url=$twz_urls.id;";
  		$result = mysql_query($query,$connection);
  		if(!$result) {
  			print "Fehler: " . mysql_error($connection);
  		} else {
  			$timestamp = time();
  			for ($i=0; $i<$zyklus && $row=mysql_fetch_array($result,MYSQL_ASSOC) ;$i++) {
  				$url1=$row[$expanded_url];
  				$url2=$row[$url];
  				$dok=$row[$id_dokument];
  				print "\n".$url1. " " .$url2;
  				if (!isset($url1)) {
  					print "Alle Dokumente aus der Datenbank wurden eingelesen.";
  					break;
  				} else {
  					$inhalt2 = Getfullpage($url1);
  					$inhalt = Getcontent($inhalt2, false);
  					if (strlen($inhalt)>1) {
  						$query2 = "UPDATE `$datenbank`.$dokument SET $eingelesen=1, $full_text='$inhalt', $zeitstempel=now() WHERE $url=$url2;";
  						$result2 = mysql_query($query2,$connection);
  						if(!$result2) {
  							print "Fehler: " . mysql_error($connection). " SQL: ". $query2;
  						} else {
  							//Erfolg
  							$title = GetTitle($inhalt2);
  							if (isset($title) AND $title!="") {
  								$query2 = "UPDATE `$datenbank`.$dokument SET $bezeichner='$title' WHERE $dokument.$id_dokument=$dok;";
  								$result2 = mysql_query($query2,$connection);
  								if(!$result2) {
  									print "Fehler: " . mysql_error($connection). " SQL: ". $query;
  								} else {
  									//
  								}
  							}
  							$desc = GetMetaDesc($inhalt2);
  							if (isset($desc) AND $desc!="") {
  								$query2 = "UPDATE `$datenbank`.$dokument SET $meta_desc='$desc' WHERE $dokument.$id_dokument=$dok;";
  								$result2 = mysql_query($query2,$connection);
  								if(!$result2) {
  									print "Fehler: " . mysql_error($connection). " SQL: ". $query;
  								} else {
  									//
  								}
  							}
  							
  							$keyw = GetMetaKeyw($inhalt2);
  							if (isset($keyw) AND $keyw!="") {
  								$query2 = "UPDATE `$datenbank`.$dokument SET $meta_keyw='$keyw' WHERE $dokument.$id_dokument=$dok;";
  								$result2 = mysql_query($query2,$connection);
  								if(!$result2) {
  									print "Fehler: " . mysql_error($connection). " SQL: ". $query;
  								} else {
  									//
  								}
  							}
  						}  		  						
  					} else {
  					  if (strlen($inhalt)==1 || strlen($inhalt)==0) {
  						$query2 = "UPDATE `$datenbank`.$dokument SET $eingelesen=1, $zeitstempel=now() WHERE $url=$url2;";
  						$result2 = mysql_query($query2,$connection);
  						if(!$result2) {
  							print "Fehler: " . mysql_error($connection). " SQL: ". $query2;
  						} else {
  							//Erfolg
  						}  		
  						  	
  					  } 
  					}
  					$timestamp1 = time();
  					print " ".($timestamp1-$timestamp)." Sekunden";
  					$timestamp = $timestamp1;
  				}
  			}
  		}
  	}
  }
?>