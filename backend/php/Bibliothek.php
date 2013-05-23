<?php
/*
Inhaltsverzeichnis: 
 Dbreset //setzt die Datenbank zurück auch die Auto-Inkrement-Werte
 Dokvolltext //gibt ein eingelesenes Dokument als Volltext aus, mit Quellenangabe
 Getpage //Bekommt URL übergeben und gibt deren Seiteninhalt zurück
 logn //gibt den Logarithmis der $zahl zur $basis zurück
 SetTitle //Setzt den Bezeichner eines Dokuments gleich dem Titel der Quelle
 Tabtest //testet, ob es eine Tabelle mit dem angegebenen Namen in der Datenbank gibt
 Tabzeilen //gibt Anzahl der Zeilen einer Tabelle zurück
*/
 //setzt die Datenbank zurück, auch die Auto-Inkrement-Werte
 function Dbreset(){
  include ("connection.php");
  //$query = "DROP DATABASE IF EXISTS `$datenbank`;";
   //$query = "CREATE DATABASE IF NOT EXISTS `$datenbank`;";  
    $query = "DROP TABLE IF EXISTS `$datenbank`.`$dokument`;";
    $result = mysql_query($query,$connection);
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {  
      $query = "CREATE TABLE IF NOT EXISTS `$datenbank`.`$dokument` (
      `$id_dokument` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `$bezeichner` varchar(255) COLLATE utf8_bin NOT NULL,
      `$url` int(11) unsigned NOT NULL,
      `$eingelesen` tinyint(1) NOT NULL DEFAULT '0',
      `$zeitstempel` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `$full_text` mediumtext NOT NULL,
      `$meta_desc` text NOT NULL,
      `$meta_keyw` text NOT NULL,
      `$language` varchar(3) COLLATE utf8_bin NOT NULL,      
      PRIMARY KEY (`$id_dokument`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
      $result = mysql_query($query,$connection);
      if(!$result) {
       print "Fehler: " . mysql_error($connection);
      } else {
       return 1;
      }
     }    
   
 
 }  
 //gibt ein eingelesenes Dokument als Volltext aus, mit Quellenangabe
 function Dokvolltext($dok, $link=false) {
  include ("connection.php");   
   $query = "SELECT expanded_url, $full_text, $meta_desc, $meta_keyw, $bezeichner FROM `$datenbank`.$dokument, `$datenbank`.twz_urls WHERE $dokument.$id_dokument=$dok AND $dokument.$url=twz_urls.id;";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
    if($link==true)
     if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      print "<h1>$row[$bezeichner]</h1>";
      print $row[$full_text];
      print "<h2>Description</h2>";
      print $row[$meta_desc];
      print "<h2>Keywords</h2>";
      print $row[$meta_keyw];
      print "<br/><br/><a href='".$row['expanded_url']."'>Link zum Original</a>"; 
    }   
  }
 }
 //gibt aus übergebenem HTML-Code den Inhalt in p-Tags zurück
 function Getcontent($html, $explode=true) {
 	$regex1 = "#<\s*p\s*[^>]*>(.*?)</\s*p\s*>#is";
 	if (preg_match_all($regex1, $html, $para)) {
 		$html = implode(" ", $para[1]);
 		$html = preg_replace("#<\s*br\s*/?>#i", " ", $html);
 		$html = preg_replace("#&nbsp;#i", " ", $html);
 		$html = preg_replace("#\n#i", " ", $html);
 		$html = preg_replace("#</?[^>]*/?>#i", "", $html);
 		$html = preg_replace("#\s{2,}#i", " ", $html);
 		$html = trim(html_entity_decode($html));
 		//print $html."\n\n";
 		//print_r($para[1]);
 		if ($explode) {
 			$inhalt = explode(" ",mysql_real_escape_string($html));
 		} else {
 			$inhalt = mysql_real_escape_string($html);
 		}
 		return $inhalt;
 	}else {
 		return 0;
 	}
 }
 //Bekommt URL übergeben und gibt deren vollen Seiteninhalt zurück
 function Getfullpage($url1) {
 	$remote = fopen($url1, "r") or $remote=false;  //or die();
 	if (!($remote)) {
 		return -1;
 	} else {
 		$html = "";
 		while (!feof($remote)) {
 			$html .= fread($remote, 8192);
 		}
 		fclose($remote);
 			return $html;
 	}
 }
 function GetMetaDesc($html) {	   
 	  $regex1 = "#<\s*meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*/?>|<\s*meta[^>]*content=[\"\'](.*?)[\"\'][^>]*name=[\"\']description[\"\'][^>]*/?>#is";
 	  preg_match_all($regex1, $html, $para);
 	  $desc = mysql_real_escape_string(implode(" ", $para[1]));
 	  return $desc;   	  
 } 
 function GetMetaKeyw($html) {	   
 	  $regex1 = "#<\s*meta[^>]*name=[\"\']keywords[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*/?>|<\s*meta[^>]*content=[\"\'](.*?)[\"\'][^>]*keywords=[\"\']description[\"\'][^>]*/?>#is";
 	  preg_match_all($regex1, $html, $para);
 	  $keyw = mysql_real_escape_string(implode(" ", $para[1]));
 	  return $keyw;   	  
 } 
 //Bekommt URL übergeben und gibt deren Seiteninhalt zurück
 function Getpage($url1, $explode=true) {
   $remote = fopen($url1, "r") or $remote=false;  //or die();
   if (!($remote)) {
   	 return -1;
   } else {
   	 $html = "";
 	 while (!feof($remote)) {
 	   $html .= fread($remote, 8192);
 	 }
 	 fclose($remote);
 	 $regex1 = "#<\s*p\s*[^>]*>(.*?)</\s*p\s*>#is";
 	 if (preg_match_all($regex1, $html, $para)) {
 	   $html = implode(" ", $para[1]);
 	   $html = preg_replace("#<\s*br\s*/?>#i", " ", $html);
 	   $html = preg_replace("#&nbsp;#i", " ", $html);
 	   $html = preg_replace("#\n#i", " ", $html);
 	   $html = preg_replace("#</?[^>]*/?>#i", "", $html);
 	   $html = preg_replace("#\s{2,}#i", " ", $html);
 	   $html = trim(html_entity_decode($html));
 	   //print $html."\n\n";
 	   //print_r($para[1]);
 	   if ($explode) {
 	     $inhalt = explode(" ",mysql_real_escape_string($html)); 	   	
 	   } else {
 	   	 $inhalt = mysql_real_escape_string($html);
 	   }
 	   return $inhalt;
 	 }else {
 	   return 0;	
 	 }	
   }
 } 
 function GetTitle($html) {	   
 	  $regex1 = "#<\s*title\s*[^>]*>(.*?)</\s*title\s*>#is";
 	  preg_match_all($regex1, $html, $para);
 	  $title = mysql_real_escape_string(implode(" ", $para[1]));
 	  return $title;   	
  
 } 
 //gibt den Logarithmis der $zahl zur $basis zurück
 function logn($zahl, $basis) {
  $erg=log10($zahl)/log10($basis);
  return $erg;
 }
 //Setzt den Bezeichner eines Dokuments gleich dem Titel der Quelle
 function SetTitle($dok) {
  include ("connection.php");
   $query = "SELECT expanded_url FROM `$datenbank`.twz_urls, `$datenbank`.$dokument WHERE twz_urls.id=$dokument.$url AND $dokument.$id_dokument=$dok AND $dokument.$eingelesen=1;";	
   $result = mysql_query($query,$connection);
   if(!$result) {
   	print "Fehler: " . mysql_error($connection). " SQL: ". $query;
   } else {
   	if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
   	  $url1 = $row['expanded_url'];	
      $remote = fopen($url1, "r") or $remote=false;  //or die();
      if (!($remote)) {
   	   return -1;
      } else {
   	   $html = "";
 	   while (!feof($remote)) {
 	    $html .= fread($remote, 8192);
 	   }
 	  fclose($remote);
 	  $regex1 = "#<\s*title\s*[^>]*>(.*?)</\s*title\s*>#is";
 	  preg_match_all($regex1, $html, $para);
 	  $title = mysql_real_escape_string(implode(" ", $para[1]));
 	  if (isset($title) AND $title!="") {
 	   $query = "UPDATE `$datenbank`.$dokument SET $bezeichner='$title' WHERE $dokument.$id_dokument=$dok;";
 	   $result = mysql_query($query,$connection);
       if(!$result) {
        print "Fehler: " . mysql_error($connection). " SQL: ". $query;
       } else { 	   
 	     //
       }   	  	
 	  } 	  
   	 }
    } 	
   }
 } 
//testet, ob es eine Tabelle mit dem angegebenen Namen in der Datenbank gibt
 function Tabtest($tab) {
  include ("connection.php");
  $query = "SHOW TABLES LIKE '$tab';";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   $anzahl = mysql_num_rows($result);
   if ($anzahl==0) return 0;
   else return 1;
  }
 } 
 //gibt Anzahl der Zeilen einer Tabelle zurück
 function Tabzeilen($tab) {
  include ("connection.php");
  $query = "SELECT * FROM `$datenbank`.$tab;";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
    $anzahl = mysql_num_rows($result);
    return $anzahl;
  }
 }
?>
