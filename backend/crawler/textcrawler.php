<?php
 //Aufruf möglich mit: zyklus
 include ("../php/connection.php");
 include ("../php/Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; 
 else {
   if (!(Tabzeilen($dokument)>0)) {
   	 $query = "SELECT id from suma1.twz_urls where valid = true;";
     $result = mysql_query($query,$connection);
     if(!$result) {
       print "Fehler: " . mysql_error($connection);
     } else {
       while ($row = mysql_fetch_array($result,MYSQL_ASSOC))	{
      	 $query = "INSERT INTO $dokument ($url) VALUES $row[id];";
       }
     }
   }	
   if (!(Tabzeilen($dokument)>0)) {
  	 print "Es konnten keine g&uuml;ltigen URLs aus twz_urls geladen werden.";
   } else {
     if ((isset($argv[0])) AND $argv[0]!="" ){
  	   $zyklus=$argv[0];
     } else $zyklus=1;
     $query = "SELECT expanded_url FROM `$datenbank`.$dokument, suma1.twz_urls WHERE $eingelesen=0 AND $dokument.$url=twz_urls.id;";
     $result = mysql_query($query,$connection);
     if(!$result) {
	   print "Fehler: " . mysql_error($connection);
	 } else {
       $timestamp = time();
       for ($i=0;$i<$zyklus && $row=mysql_fetch_array($result,MYSQL_ASSOC);$i++) {
         $url1=$row['expanded_url'];
         $html="";
         print "\n".$url;
         if (!isset($url)) {
           print "Alle Dokumente aus der Datenbank wurden eingelesen.";
           break;
         } else {
           $remote = fopen($url, "r") or $remote=false;  //or die();
           if (!($remote)) {
             $query = "UPDATE `$datenbank`.$dokument SET $bezeichner='blacklist', $eingelesen=1, $zeitstempel=now() WHERE $url LIKE '$url1';";
             $result = mysql_query($query,$connection);
             if(!$result) {
     	       print "Fehler: " . mysql_error($connection);
             } else {
     			//
           }
         } else {
           while (!feof($remote)) {
     	     $html .= fread($remote, 8192);
           }
           fclose($remote);
           $regex1 = "§<\s*p\s*[^>]*>(.*?)</\s*p\s*>§is";
     	   if (preg_match_all($regex1, $html, $para)) {
     		 $html = implode(" ", $para[1]);
     		 $html = preg_replace("§<\s*br\s*/?>§i", " ", $html);
     		 $html = preg_replace("§&nbsp;§i", " ", $html);
     		 $html = preg_replace("§</?[^>]*/?>§i", "", $html);
     		 $html = preg_replace("§\s{2,}§i", " ", $html);
     		 $html = trim(html_entity_decode($html));
     		 //print $html."\n\n";
     		 //print_r($para[1]);
     		 $inhalt = explode(" ",mysql_real_escape_string($html));
     		 Text($inhalt, $url1);
     	   } else {
     		 $query = "UPDATE `$datenbank`.$dokument SET $bezeichner='empty', $eingelesen=1, $zeitstempel=now() WHERE $url LIKE '$url1';";
     		 $result = mysql_query($query,$connection);
     		 if(!$result) {
     		   print "Fehler: " . mysql_error($connection);
     		 } else {
     				//Erfolg
     		}
     	  }
     	  $timestamp1 = time();
     	  print " ".($timestamp1-$timestamp)." Sekunden";
     	  $timestamp = $timestamp1;
     	}
     	$query = "SELECT * FROM `$datenbank`.$dokument WHERE $eingelesen=0 AND $url!='';";
     	//$query = "SELECT * FROM dokument WHERE eingelesen=0 AND (url LIKE 'http://de.%');"; //->Filter, um Testinhalte auf deutsche Wikipedia zu beschränken (oben kann beim ersten Aufruf was anderes gewählt werden
     	$result = mysql_query($query,$connection);
     	if(!$result) {
     	  print "Fehler: " . mysql_error($connection);
     	} else {
     	  if ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
     		$url=$row[$url];
     	  } else {
     		print "Alle Seiten, deren URLs in der Datenbank liegen, wurden eingelesen. Bitte einen Wert &uuml;bergeben. (textcrawler.php 'url' 'zyklus')";
     		return;
     	  }
     	}     
      }     
    }		
	}
  }
  
 }
?>
