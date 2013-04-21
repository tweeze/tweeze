<?php
 //Aufruf möglich mit: url, zyklus
 include ("../php/connection.php");
 include ("../php/Bibliothek.php");
 if (!$db) echo "Beim Zugriff auf die Datenbank ist ein Fehler aufgetreten. Bitte versuchen Sie es sp&auml;ter nochmal.<br/><br/>"; else {
  if ((isset($argv[1])) AND $argv[1]!="" ){
   $regex2 = "^http://(\d|\w)+.(\d|\w)+$";
   if (!(preg_match($regex2, $url))) {
    print "Das ist keine URL.";
   } else {
    $url=$argv[1];
    $query = "SELECT * FROM dokument WHERE url='$url';";
    $result = mysql_query($query,$connection);
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {
     if ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
      if ($row[eingelesen]==1) {
       print "Diese Seite kenne ich schon.";
       return;
      }
     } else {
      $query = "INSERT INTO dokument (bezeichner, url, eingelesen) VALUES ('', '$url',0);";
      $result = mysql_query($query,$connection);
      if(!$result) {
       print "Fehler: " . mysql_error($connection);
      } else {	  
       //Erfolg
      }
     }
    }
   }
  } else {
   $query = "SELECT * FROM dokument WHERE eingelesen=0 AND url!='';";
   $result = mysql_query($query,$connection);
   if(!$result) {
	  print "Fehler: " . mysql_error($connection);
	 } else {
    if ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
     $url=$row[url];
    } else {
     print "Alle Seiten, deren URLs in der Datenbank liegen, wurden eingelesen. Bitte einen Wert &uuml;bergeben. (textcrawler.php 'url' 'zyklus')";
     return;
    }
   }
  }
  if (isset ($url)){
   if (isset($argv[2])) $zyklus=$argv[2]; else $zyklus=1;
   $timestamp = time();
   for ($i=0;$i<$zyklus;$i++) {
    $html="";
    print "\n".$url;
    $remote = fopen($url, "r") or $remote=false;  //or die();
    if (!($remote)) {
     $query = "UPDATE dokument SET bezeichner='blacklist', eingelesen=1, assoziiert=1, zeitstempel=now() WHERE url LIKE '$url';";
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
     $regex = "/<\s*a\s+[^>]*href\s*=\s*[\"']?(http:\/\/[^\"' >]+)[\"' >]/isU";
     if (preg_match_all($regex, $html, $links)) {
      foreach($links[1] as $value) {
       //print_r ("\n".$value); //nach debug Kommentarzeichen gerne entfernen
       $query = "SELECT * FROM dokument WHERE url='$value';";
       $result = mysql_query($query,$connection);
       if(!$result) {
	    print "Fehler: " . mysql_error($connection);
	   } else {
        if ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
         //nix
        } else {
         $query = "INSERT INTO dokument (bezeichner, url, eingelesen) VALUES ('', '$value',0);";
         $result = mysql_query($query,$connection);
         if(!$result) {
          print "Fehler: " . mysql_error($connection);
         } else {	  
          /* $query = "SELECT * FROM dokument ORDER BY id_dokument DESC;";
          $result = mysql_query($query,$connection);
          if(!$result) {
           print "Fehler: " . mysql_error($connection);
          } else {
	       //Erfolg
          } */
         }
        }
       }
      }
     }
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
      Text($inhalt, $url);
     } else {
      $query = "UPDATE dokument SET bezeichner='empty', eingelesen=1, assoziiert=1, zeitstempel=now() WHERE url LIKE '$url';";
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
    //$query = "SELECT * FROM dokument WHERE eingelesen=0 AND url!='';";
    $query = "SELECT * FROM dokument WHERE eingelesen=0 AND (url LIKE 'http://de.%');"; //->Filter, um Testinhalte auf deutsche Wikipedia zu beschränken (oben kann beim ersten Aufruf was anderes gewählt werden
    $result = mysql_query($query,$connection);
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {
     if ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
      $url=$row[url];
     } else {
      print "Alle Seiten, deren URLs in der Datenbank liegen, wurden eingelesen. Bitte einen Wert &uuml;bergeben. (textcrawler.php 'url' 'zyklus')";
      return;
     }
    } 
   }
  } else print "Da ist irgendwo was falsch gelaufen. url=$url.";
 }
?>