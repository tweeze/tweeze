<?php
/*
Inhaltsverzeichnis: 
 Arrayaus //Ausgabe für "normale" Arrays
 Arrayidaus //Ausgabe für ID-Arrays 
 Arraytoarray //Wandelt ID-Array in "normales" Array um, wobei nach minimalem Gewicht gefiltert werden kann
 Dbreset //setzt die Datenbank zurück auch die Auto-Inkrement-Werte
 *Dokument //legt neues Dokument an und vergibt die ersten drei Wörter von $inhalt als Titel (wenn es keinen findet, noch nicht umgesetzt)
 Dokvolltext //gibt ein eingelesenes Dokument als Volltext aus, mit Quellenangabe
 Einzelwort //trägt neues Wort in die Datenbank ein oder erhöht Zähler des vorhandenen um 1
 Idzuwort //gibt zu übergebener ID gehöriges Wort als Zeichenkette zurück
 logn //gibt den Logarithmis der $zahl zur $basis zurück
 Punkttester //testet, ob Zeichenkette mit Satzzeichen endet und gibt dann 1 zurück, sonst 0
 Tabtest //testet, ob es eine Tabelle mit dem angegebenen Namen in der Datenbank gibt
 Tabzeilen //gibt Anzahl der Zeilen einer Tabelle zurück
 Text //trägt Array von Wörtern als Text ein, indem es zu jedem Wort die Dokument-ID und die Stelle im Dokument speichert.
 Wortzuid //findet ID zu einem Wort und gibt dieses zurück. Wenn es nicht in der Datenbank ist, 0
*/
 //Ausgabe für "normale" Arrays
 function Arrayaus($aushalt4) {
  include ("connection.php");
  if(!$aushalt4==NULL) {
   for ($i=0;$i<count($aushalt4);$i++) {
    $query = "SELECT * FROM wort WHERE id_wort='$aushalt4[$i]';";    
    $result = mysql_query($query,$connection);
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {
     $row = mysql_fetch_array($result,MYSQL_ASSOC);
     print $row['wort']." "; 
    }
   }
  } else print "Dar&uuml;ber wei&szlig; ich nichts.";
 } 
 //Ausgabe für ID-Arrays
 function Arrayidaus($aushalt) {
  include ("connection.php");
  if(!$aushalt==NULL) {
   arsort($aushalt);
   foreach($aushalt as $key => $value) { 
    $query = "SELECT * FROM wort WHERE id_wort='$key';"; 
    $result = mysql_query($query,$connection);
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {
     $row = mysql_fetch_array($result,MYSQL_ASSOC);
     print $row['wort']." ".$value."<br/>"; //Gewicht nur zum Test angezeigt, später nur Werte ab einer bestimmten Größe zeigen oder die ersten 50 etc.
    }
   }
  } else print "Dar&uuml;ber wei&szlig; ich nichts.";
 }
  //Wandelt ID-Array in "normales" Array um, wobei nach minimalem Gewicht gefiltert werden kann
 function Arraytoarray($aushalt, $gew = 1) {
  include ("connection.php");
  if(!$aushalt==NULL) {
   arsort($aushalt);
   $count=0;
   foreach($aushalt as $key => $value) { 
    if ($value>=$gew) {
     $query = "SELECT * FROM wort WHERE id_wort='$key';"; 
     $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
      $row = mysql_fetch_array($result,MYSQL_ASSOC);
      $neuaushalt[$count] = $row['wort'];
      $count++;
     }
    }
   }
   return $neuaushalt;
  } else return false;
 }
 //setzt die Datenbank zurück, auch die Auto-Inkrement-Werte
 function Dbreset(){
  include ("connection.php");
  $query = "DROP DATABASE IF EXISTS `$datenbank`;";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   $query = "CREATE DATABASE IF NOT EXISTS `$datenbank`;";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
    
    if(!$result) {
     print "Fehler: " . mysql_error($connection);
    } else {
     $query = "CREATE TABLE IF NOT EXISTS `$datenbank`.`dokument` (
     `id_dokument` int(10) unsigned NOT NULL AUTO_INCREMENT,
     `bezeichner` varchar(255) COLLATE utf8_bin NOT NULL,
     `url` text COLLATE utf8_bin DEFAULT NULL,
     `eingelesen` tinyint(1) NOT NULL DEFAULT '0',
     `zeitstempel` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `anzahl` int(10) NOT NULL DEFAULT '0',
     PRIMARY KEY (`id_dokument`)
     ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
     $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
      $query = "CREATE TABLE IF NOT EXISTS `$datenbank`.`text` (
      `wort_id` int(11) unsigned NOT NULL,
      `dokument_id` int(11) unsigned NOT NULL,
      `stelle` int(11) signed NOT NULL,
      PRIMARY KEY (`wort_id`,`dokument_id`,`stelle`),
      KEY `wort_id` (`wort_id`,`dokument_id`,`stelle`),
      KEY `dokument_id` (`dokument_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
      $result = mysql_query($query,$connection);
      if(!$result) {
       print "Fehler: " . mysql_error($connection);
      } else {
       $query = "CREATE TABLE IF NOT EXISTS `$datenbank`.`wort` (
       `id_wort` int(10) unsigned NOT NULL AUTO_INCREMENT,
       `wort` varchar(255) COLLATE utf8_bin NOT NULL,
       `anzahl` int(10) unsigned NOT NULL DEFAULT '1',
       `idf` float(14) unsigned NOT NULL DEFAULT '0',
       PRIMARY KEY (`id_wort`)
       ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
       $result = mysql_query($query,$connection);
       if(!$result) {
        print "Fehler: " . mysql_error($connection);
       } else {
        if(!$result) {
         print "Fehler: " . mysql_error($connection);
        } else {
         $query = "ALTER TABLE `$datenbank`.`text`
         ADD CONSTRAINT `text_ibfk_1` FOREIGN KEY (`wort_id`) REFERENCES `wort` (`id_wort`),
         ADD CONSTRAINT `text_ibfk_2` FOREIGN KEY (`dokument_id`) REFERENCES `dokument` (`id_dokument`);";
         $result = mysql_query($query,$connection);
         if(!$result) {
          print "Fehler: " . mysql_error($connection);
         } else {
          if(!$result) {
           print "Fehler: " . mysql_error($connection);
          } else {
           if(!$result) {
            print "Fehler: " . mysql_error($connection);
           } else {
            return 1;          
           }
          }
         }
        }
       }
      }
     }
    }
   }
  }
 }  
 //legt neues Dokument an und vergibt die ersten drei Wörter von $inhalt als Titel (wenn es keinen findet, noch nicht umgesetzt)
//wenn $url übergeben wurde, dann wird geschaut, ob schon vorhanden und wenn ja, ob schon eingelesen, besser erst nach Titel schauen?
 function Dokument($inhalt,$url = NULL, $titel = NULL) {
  include ("connection.php");
  if(isset($url) AND $url!="") {
   $query = "SELECT * FROM dokument WHERE eingelesen=0 AND url LIKE '$url';";
   $result = mysql_query($query,$connection);
   if(!$result) {
     print "Fehler: " . mysql_error($connection);
   } else {
    if ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
     if ($row['eingelesen']==0) {    
      $dokid=$row['id_dokument'];
      if ($row['bezeichner']=="") {
       if (isset($titel) AND $titel!="") $query = "UPDATE dokument SET bezeichner='$titel', eingelesen=1, zeitstempel=now() WHERE id_dokument LIKE '$dokid';";
       else $query = "UPDATE dokument SET bezeichner='$inhalt[0] $inhalt[1] $inhalt[2]', eingelesen=1, zeitstempel=now() WHERE id_dokument LIKE '$dokid';";
       $result = mysql_query($query,$connection);
       if(!$result) {
        print "Fehler: " . mysql_error($connection);
       } else {
         //
       }
      } else {
       $query = "UPDATE dokument SET eingelesen=1, zeitstempel=now() WHERE id_dokument LIKE '$dokid';";
       $result = mysql_query($query,$connection);
       if(!$result) {
        print "Fehler: " . mysql_error($connection);
       } else {
         //
       }
      }
       return $dokid;      
     } else return 0;
    }
   }
  } else {
   if (isset($titel) AND $titel!="") $query = "INSERT INTO dokument (bezeichner, url, eingelesen) VALUES ('$titel', '$url',1);";
   else if(count($inhalt)>=3) $query = "INSERT INTO dokument (bezeichner, url, eingelesen) VALUES ('$inhalt[0] $inhalt[1] $inhalt[2]', '$url',1);";
   else $query = "INSERT INTO dokument (bezeichner, url, eingelesen) VALUES ('$inhalt[0]', '$url',1);";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
    return mysql_insert_id($connection);
   }            
  }
 }
 //gibt ein eingelesenes Dokument als Volltext aus, mit Quellenangabe
 function Dokvolltext($dok, $link=false) {
  include ("connection.php");
  $query = "SELECT * FROM text, wort WHERE dokument_id='$dok' AND id_wort=wort_id ORDER BY stelle ASC;"; 
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    print $row['wort']." ";
   }
   $query = "SELECT url FROM dokument WHERE id_dokument='$dok';";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
    if($link==true)
     if (($row = mysql_fetch_array($result,MYSQL_ASSOC)) && !($row['url']==""))
      print "<br/><br/><a href='".$row['url']."'>Link zum Original</a>"; else print "<br/><br/>Dokument wurde manuell eingegeben.";
   }
  }
 }
 //trägt neues Wort in die Datenbank ein oder erhöht Zähler des vorhandenen um 1
 function Einzelwort($wort){
  include ("connection.php");
  $query ="SELECT * FROM wort WHERE wort LIKE '$wort';";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   if($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $idwort = $row['id_wort'];
    $query = "UPDATE wort SET anzahl=anzahl+1 WHERE id_wort=".$row['id_wort'].";";
    $result = mysql_query($query,$connection);
    if($result) {
     return $idwort;
    } else return 0;  
   } else {
    $query = "INSERT INTO wort (wort) VALUES ('$wort');"; 
    $result = mysql_query($query,$connection);
    if($result) {
     $query ="SELECT * FROM wort WHERE wort LIKE '$wort';";
     $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
      if($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
       return $row['id_wort'];
      }     
     }
    } else return 0;
   }
  }
 }
 //gibt zu übergebener ID gehöriges Wort als Zeichenkette zurück
 function Idzuwort($id) {
  include ("connection.php");
  $query = "SELECT wort FROM wort WHERE id_wort='$id';";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   $row = mysql_fetch_array($result,MYSQL_ASSOC);
   return $row['wort'];
  }
 }
 //gibt den Logarithmis der $zahl zur $basis zurück
 function logn($zahl, $basis) {
  $erg=log10($zahl)/log10($basis);
  return $erg;
 }
 //testet, ob Zeichenkette mit Satzzeichen endet und gibt dann 1 zurück, sonst 0
 function Punkttester($id){
  include ("connection.php");
  $query = "SELECT * FROM wort WHERE id_wort='$id';";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   if ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    if (substr($row['wort'], -1, 1)=="." || substr($row['wort'], -1, 1)=="?" || substr($row['wort'], -1, 1)=="!") return 1;
    else return 0;
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
  $query = "SELECT * FROM $tab;";
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
    $anzahl = mysql_num_rows($result);
    return $anzahl;
  }
 }
 //trägt Array von Wörtern als Text ein, indem es zu jedem Wort die Dokument-ID und die Stelle im Dokument speichert.
 function Text($inhalt, $url = NULL, $titel = NULL) {
  include ("connection.php");
  $dok = Dokument($inhalt, $url, $titel);
  for($i=0;$i<count($inhalt);$i++){
   $query = "SELECT * FROM wort WHERE wort LIKE '$inhalt[$i]';";
   $result = mysql_query($query,$connection);
   if(!$result) {
    print "Fehler: " . mysql_error($connection);
   } else {
    if ($row = mysql_fetch_array($result,MYSQL_ASSOC) ) {  
     $query = "INSERT INTO text VALUES ('$row[id_wort]','$dok','$i');";   
     $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
        //nix
     }
    } else {
     $idwort = Einzelwort($inhalt[$i]);
     $query = "INSERT INTO text VALUES ('$idwort','$dok','$i');";   
     $result = mysql_query($query,$connection);
     if(!$result) {
      print "Fehler: " . mysql_error($connection);
     } else {
        //nix
     }
    }
   }
  }
  return $dok;
 }
 //findet ID zu einem Wort und gibt diese zurück. Wenn es nicht in der Datenbank ist, 0
 function Wortzuid($wort) {
  include ("connection.php");
  $query = "SELECT * FROM wort WHERE wort='$wort';"; 
  $result = mysql_query($query,$connection);
  if(!$result) {
   print "Fehler: " . mysql_error($connection);
  } else {
   if($row = mysql_fetch_array($result,MYSQL_ASSOC))
   return $row['id_wort'];
   else return 0;
  }
 }
?>
