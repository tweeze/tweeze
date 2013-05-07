<?php
  //Aufruf möglich mit: zyklus
  include ("../php/connection.php");
  include ("../php/Bibliothek.php");
  if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; 
  else {
    if (!(Tabzeilen($dokument)>0)) {
   	  $query = "SELECT id from `$datenbank`.twz_urls where valid = true;";
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
  	  print "Es konnten keine g&uuml;ltigen URLs aus twz_urls geladen werden.";
    } else {
      if ((isset($argv[1])) AND $argv[1]!="" ){
  	    $zyklus=$argv[1];
      } else $zyklus=1;
      $query = "SELECT expanded_url, $url FROM `$datenbank`.$dokument, twz_urls WHERE $eingelesen=0 AND $dokument.$url=twz_urls.id AND expanded_url LIKE 'http://%';";
      $result = mysql_query($query,$connection);
      if(!$result) {
	    print "Fehler: " . mysql_error($connection);
	  } else {
        $timestamp = time();
        for ($i=0; $i<$zyklus && $row=mysql_fetch_array($result,MYSQL_ASSOC) ;$i++) {	
          $url1=$row['expanded_url'];
          $url2=$row[$url];
          print "\n".$url1. " " .$url2;
          if (!isset($url1)) {
            print "Alle Dokumente aus der Datenbank wurden eingelesen.";
            break;
          } else {
          	//->Einlese-Teil in eigene Funktion in Bibliothek          	
            $inhalt = Getpage($url1);
            if ($inhalt == -1) {
              $query = "UPDATE `$datenbank`.$dokument SET $bezeichner='blacklist', $eingelesen=1, $zeitstempel=now() WHERE $url LIKE '$url2';";
              $result = mysql_query($query,$connection);
              if(!$result) {
                print "Fehler: " . mysql_error($connection);
              } else {
            		//
              }
            } else if ($inhalt == 0) {
              $query = "UPDATE `$datenbank`.$dokument SET $bezeichner='empty', $eingelesen=1, $zeitstempel=now() WHERE $url LIKE '$url2';";
              $result = mysql_query($query,$connection);
              if(!$result) {
                print "Fehler: " . mysql_error($connection);
              } else {
            		//Erfolg
              }
            } else {
     		  Text($inhalt, $url2);            	
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
