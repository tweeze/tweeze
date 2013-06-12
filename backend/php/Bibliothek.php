<?php
/*
Inhaltsverzeichnis: 
 Dbreset //setzt die Datenbank zurück auch die Auto-Inkrement-Werte
 Dokvolltext //gibt ein eingelesenes Dokument als Volltext aus, mit Quellenangabe
 GetContent  //gibt aus übergebenem HTML-Code den Inhalt in p-Tags zurück
 GetFullPage  //bekommt URL übergeben und gibt deren vollen Seiteninhalt zurück
 GetMetaDesc  //gibt aus übergebenem HTML-Code die Meta-Description zurück
 GetMetaKeyw  //gibt aus übergebenem HTML-Code die Meta-Keywords zurück
 GetTitle  //gibt aus übergebenem HTML-Code den Titel zurück
 logn //gibt den Logarithmis der $zahl zur $basis zurück
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
   $query = "SELECT $expanded_url, $full_text, $meta_desc, $meta_keyw, $bezeichner FROM `$datenbank`.$dokument, `$datenbank`.$twz_urls WHERE $dokument.$id_dokument=$dok AND $dokument.$url=$twz_urls.id;";
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
      print "<br/><br/><a href='".$row[$expanded_url]."'>Link zum Original</a>"; 
    }   
  }
 }
 //gibt aus übergebenem HTML-Code den Inhalt in p-Tags zurück
 function GetContent($html, $explode=true) {
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
 //bekommt URL übergeben und gibt deren vollen Seiteninhalt zurück
 function GetFullPage($url1) {
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
 //gibt aus übergebenem HTML-Code die Meta-Description zurück
 function GetMetaDesc($html) {	   
 	  $regex1 = "#<\s*meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*/?>|<\s*meta[^>]*content=[\"\'](.*?)[\"\'][^>]*name=[\"\']description[\"\'][^>]*/?>#is";
 	  preg_match_all($regex1, $html, $para);
 	  $desc = mysql_real_escape_string(implode(" ", $para[1]));
 	  return $desc;   	  
 } 
 //gibt aus übergebenem HTML-Code die Meta-Keywords zurück
 function GetMetaKeyw($html) {	   
 	  $regex1 = "#<\s*meta[^>]*name=[\"\']keywords[\"\'][^>]*content=[\"\'](.*?)[\"\'][^>]*/?>|<\s*meta[^>]*content=[\"\'](.*?)[\"\'][^>]*keywords=[\"\']description[\"\'][^>]*/?>#is";
 	  preg_match_all($regex1, $html, $para);
 	  $keyw = mysql_real_escape_string(implode(" ", $para[1]));
 	  return $keyw;   	  
 } 
 //gibt aus übergebenem HTML-Code den Titel zurück
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
 //Stoppwortliste von http://www.phpbar.de/w/Stoppwortliste_deutsch
 $stopwords["de"][] = "ab"; //hinzugefügt
 $stopwords["de"][] = "aber";
 $stopwords["de"][] = "alle";
 $stopwords["de"][] = "allem"; //hinzugefügt
 $stopwords["de"][] = "allen";
 $stopwords["de"][] = "alles";
 $stopwords["de"][] = "als";
 $stopwords["de"][] = "also";
 $stopwords["de"][] = "am"; //hinzugefügt
 $stopwords["de"][] = "an"; //hinzugefügt
 $stopwords["de"][] = "andere";
 $stopwords["de"][] = "anderem";
 $stopwords["de"][] = "anderer";
 $stopwords["de"][] = "anderes";
 $stopwords["de"][] = "anders";
 $stopwords["de"][] = "angesichts"; //hinzugefügt
 $stopwords["de"][] = "auch";
 $stopwords["de"][] = "auf";
 $stopwords["de"][] = "aus";
 $stopwords["de"][] = "ausser";
 $stopwords["de"][] = "ausserdem";
 $stopwords["de"][] = "bei";
 $stopwords["de"][] = "beide";
 $stopwords["de"][] = "beiden";
 $stopwords["de"][] = "beides";
 $stopwords["de"][] = "beim";
 $stopwords["de"][] = "bereits";
 $stopwords["de"][] = "bestehen";
 $stopwords["de"][] = "besteht";
 $stopwords["de"][] = "bevor";
 $stopwords["de"][] = "bin";
 $stopwords["de"][] = "bis";
 $stopwords["de"][] = "bloss";
 $stopwords["de"][] = "brauchen";
 $stopwords["de"][] = "braucht";
 $stopwords["de"][] = "dabei";
 $stopwords["de"][] = "dadurch";
 $stopwords["de"][] = "dagegen";
 $stopwords["de"][] = "daher";
 $stopwords["de"][] = "damit";
 $stopwords["de"][] = "danach";
 $stopwords["de"][] = "dann";
 $stopwords["de"][] = "da"; //hinzugefügt
 $stopwords["de"][] = "dar"; //hinzugefügt
 $stopwords["de"][] = "daran"; //hinzugefügt
 $stopwords["de"][] = "darauf"; //hinzugefügt
 $stopwords["de"][] = "daraus"; //hinzugefügt
 $stopwords["de"][] = "darin"; //hinzugefügt
 $stopwords["de"][] = "darf";
 $stopwords["de"][] = "darueber";
 $stopwords["de"][] = "darüber"; //hinzugefügt
 $stopwords["de"][] = "darum";
 $stopwords["de"][] = "darunter";
 $stopwords["de"][] = "das";
 $stopwords["de"][] = "dass";
 $stopwords["de"][] = "davon";
 $stopwords["de"][] = "dazu";
 $stopwords["de"][] = "dem";
 $stopwords["de"][] = "den";
 $stopwords["de"][] = "denen"; //hinzugefügt
 $stopwords["de"][] = "denn";
 $stopwords["de"][] = "denoch"; //hinzugefügt
 $stopwords["de"][] = "der";
 $stopwords["de"][] = "deren"; //hinzugefügt
 $stopwords["de"][] = "derer"; //hinzugefügt
 $stopwords["de"][] = "des";
 $stopwords["de"][] = "deshalb";
 $stopwords["de"][] = "dessen";
 $stopwords["de"][] = "desto"; //hinzugefügt
 $stopwords["de"][] = "deswegen"; //hinzugefügt
 $stopwords["de"][] = "die";
 $stopwords["de"][] = "dies";
 $stopwords["de"][] = "diese";
 $stopwords["de"][] = "diesem";
 $stopwords["de"][] = "diesen";
 $stopwords["de"][] = "dieser";
 $stopwords["de"][] = "dieses";
 $stopwords["de"][] = "doch";
 $stopwords["de"][] = "dort";
 $stopwords["de"][] = "dortigen"; //hinzugefügt
 $stopwords["de"][] = "duerfen";
 $stopwords["de"][] = "durch";
 $stopwords["de"][] = "durfte";
 $stopwords["de"][] = "durften";
 $stopwords["de"][] = "ebenfalls";
 $stopwords["de"][] = "ebenso";
 $stopwords["de"][] = "ein";
 $stopwords["de"][] = "eine";
 $stopwords["de"][] = "einem";
 $stopwords["de"][] = "einen";
 $stopwords["de"][] = "einer";
 $stopwords["de"][] = "eines";
 $stopwords["de"][] = "einige";
 $stopwords["de"][] = "einiges";
 $stopwords["de"][] = "einzig";
 $stopwords["de"][] = "entsprechend"; //hinzugefügt
 $stopwords["de"][] = "entweder";
 $stopwords["de"][] = "er"; //hinzugefügt
 $stopwords["de"][] = "erst";
 $stopwords["de"][] = "erste";
 $stopwords["de"][] = "ersten";
 $stopwords["de"][] = "es"; //hinzugefügt
 $stopwords["de"][] = "etwa";
 $stopwords["de"][] = "etwas";
 $stopwords["de"][] = "euch"; //hinzugefügt
 $stopwords["de"][] = "falls";
 $stopwords["de"][] = "fast";
 $stopwords["de"][] = "ferner";
 $stopwords["de"][] = "folgender";
 $stopwords["de"][] = "folglich";
 $stopwords["de"][] = "fuer";
 $stopwords["de"][] = "gab"; //hinzugefügt
 $stopwords["de"][] = "ganz";
 $stopwords["de"][] = "gar"; //hinzugefügt
 $stopwords["de"][] = "gebe"; //hinzugefügt
 $stopwords["de"][] = "geben";
 $stopwords["de"][] = "gegen";
 $stopwords["de"][] = "gehabt";
 $stopwords["de"][] = "gekonnt";
 $stopwords["de"][] = "gemaess";
 $stopwords["de"][] = "getan";
 $stopwords["de"][] = "gewesen";
 $stopwords["de"][] = "gewollt";
 $stopwords["de"][] = "geworden";
 $stopwords["de"][] = "gibt";
 $stopwords["de"][] = "habe";
 $stopwords["de"][] = "haben";
 $stopwords["de"][] = "haette";
 $stopwords["de"][] = "haetten";
 $stopwords["de"][] = "hallo";
 $stopwords["de"][] = "hat";
 $stopwords["de"][] = "hatte";
 $stopwords["de"][] = "hatten";
 $stopwords["de"][] = "heraus";
 $stopwords["de"][] = "herein";
 $stopwords["de"][] = "hier";
 $stopwords["de"][] = "hin";
 $stopwords["de"][] = "hinein";
 $stopwords["de"][] = "hinter";
 $stopwords["de"][] = "ich";
 $stopwords["de"][] = "ihm";
 $stopwords["de"][] = "ihn";
 $stopwords["de"][] = "ihnen";
 $stopwords["de"][] = "ihr";
 $stopwords["de"][] = "ihre";
 $stopwords["de"][] = "ihrem";
 $stopwords["de"][] = "ihren";
 $stopwords["de"][] = "ihres";
 $stopwords["de"][] = "im"; //hinzugefügt
 $stopwords["de"][] = "immer";
 $stopwords["de"][] = "in"; //hinzugefügt
 $stopwords["de"][] = "indem";
 $stopwords["de"][] = "infolge";
 $stopwords["de"][] = "innen";
 $stopwords["de"][] = "innerhalb";
 $stopwords["de"][] = "ins";
 $stopwords["de"][] = "inzwischen";
 $stopwords["de"][] = "irgend";
 $stopwords["de"][] = "irgendwas";
 $stopwords["de"][] = "irgendwen";
 $stopwords["de"][] = "irgendwer";
 $stopwords["de"][] = "irgendwie";
 $stopwords["de"][] = "irgendwo";
 $stopwords["de"][] = "ist";
 $stopwords["de"][] = "jede";
 $stopwords["de"][] = "jedem";
 $stopwords["de"][] = "jeden";
 $stopwords["de"][] = "jeder";
 $stopwords["de"][] = "jedes";
 $stopwords["de"][] = "jedoch";
 $stopwords["de"][] = "jene";
 $stopwords["de"][] = "jenem";
 $stopwords["de"][] = "jenen";
 $stopwords["de"][] = "jener";
 $stopwords["de"][] = "jenes";
 $stopwords["de"][] = "kann";
 $stopwords["de"][] = "kaum"; //hinzugefügt
 $stopwords["de"][] = "kein";
 $stopwords["de"][] = "keine";
 $stopwords["de"][] = "keinem";
 $stopwords["de"][] = "keinen";
 $stopwords["de"][] = "keiner";
 $stopwords["de"][] = "keines";
 $stopwords["de"][] = "koennen";
 $stopwords["de"][] = "koennte";
 $stopwords["de"][] = "koennten";
 $stopwords["de"][] = "konnte";
 $stopwords["de"][] = "konnten";
 $stopwords["de"][] = "kuenftig";
 $stopwords["de"][] = "leer";
 $stopwords["de"][] = "machen";
 $stopwords["de"][] = "macht";
 $stopwords["de"][] = "machte";
 $stopwords["de"][] = "machten";
 $stopwords["de"][] = "mal"; //hinzugefügt
 $stopwords["de"][] = "man";
 $stopwords["de"][] = "mehr";
 $stopwords["de"][] = "mein";
 $stopwords["de"][] = "meine";
 $stopwords["de"][] = "meinen";
 $stopwords["de"][] = "meinem";
 $stopwords["de"][] = "meiner";
 $stopwords["de"][] = "meist";
 $stopwords["de"][] = "meiste";
 $stopwords["de"][] = "meisten";
 $stopwords["de"][] = "mich";
 $stopwords["de"][] = "mit";
 $stopwords["de"][] = "moechte";
 $stopwords["de"][] = "moechten";
 $stopwords["de"][] = "muessen";
 $stopwords["de"][] = "muessten";
 $stopwords["de"][] = "muss";
 $stopwords["de"][] = "musste";
 $stopwords["de"][] = "mussten";
 $stopwords["de"][] = "nach";
 $stopwords["de"][] = "nachdem";
 $stopwords["de"][] = "nacher";
 $stopwords["de"][] = "naemlich";
 $stopwords["de"][] = "nämlich"; //hinzugefügt
 $stopwords["de"][] = "neben";
 $stopwords["de"][] = "nein";
 $stopwords["de"][] = "nicht";
 $stopwords["de"][] = "nichts";
 $stopwords["de"][] = "noch";
 $stopwords["de"][] = "nuetzt";
 $stopwords["de"][] = "nun"; //hinzugefügt
 $stopwords["de"][] = "nur";
 $stopwords["de"][] = "nutzt";
 $stopwords["de"][] = "ob"; //hinzugefügt
 $stopwords["de"][] = "obgleich";
 $stopwords["de"][] = "obwohl";
 $stopwords["de"][] = "oder";
 $stopwords["de"][] = "ohne";
 $stopwords["de"][] = "per";
 $stopwords["de"][] = "pro";
 $stopwords["de"][] = "rund";
 $stopwords["de"][] = "schon";
 $stopwords["de"][] = "sehr";
 $stopwords["de"][] = "sei"; //hinzugefügt
 $stopwords["de"][] = "seid";
 $stopwords["de"][] = "sein";
 $stopwords["de"][] = "seine";
 $stopwords["de"][] = "seinem";
 $stopwords["de"][] = "seinen";
 $stopwords["de"][] = "seiner";
 $stopwords["de"][] = "seit";
 $stopwords["de"][] = "seitdem";
 $stopwords["de"][] = "seither";
 $stopwords["de"][] = "selber";
 $stopwords["de"][] = "sich";
 $stopwords["de"][] = "sie";
 $stopwords["de"][] = "siehe";
 $stopwords["de"][] = "sind";
 $stopwords["de"][] = "so"; //hinzugefügt
 $stopwords["de"][] = "sobald";
 $stopwords["de"][] = "sodann"; //hinzugefügt
 $stopwords["de"][] = "solange";
 $stopwords["de"][] = "solch";
 $stopwords["de"][] = "solche";
 $stopwords["de"][] = "solchem";
 $stopwords["de"][] = "solchen";
 $stopwords["de"][] = "solcher";
 $stopwords["de"][] = "solches";
 $stopwords["de"][] = "soll";
 $stopwords["de"][] = "sollen";
 $stopwords["de"][] = "sollte";
 $stopwords["de"][] = "sollten";
 $stopwords["de"][] = "somit";
 $stopwords["de"][] = "sondern";
 $stopwords["de"][] = "soweit";
 $stopwords["de"][] = "sowie";
 $stopwords["de"][] = "spaeter";
 $stopwords["de"][] = "statt"; //hinzugefügt
 $stopwords["de"][] = "stets";
 $stopwords["de"][] = "such";
 $stopwords["de"][] = "ueber";
 $stopwords["de"][] = "über"; //hinzugefügt
 $stopwords["de"][] = "überhaupt"; //hinzugefügt
 $stopwords["de"][] = "um"; //hinzugefügt
 $stopwords["de"][] = "ums";
 $stopwords["de"][] = "umso"; //hinzugefügt
 $stopwords["de"][] = "und";
 $stopwords["de"][] = "uns";
 $stopwords["de"][] = "unser";
 $stopwords["de"][] = "unsere";
 $stopwords["de"][] = "unserem";
 $stopwords["de"][] = "unseren";
 $stopwords["de"][] = "viel";
 $stopwords["de"][] = "viele";
 $stopwords["de"][] = "vollstaendig";
 $stopwords["de"][] = "vom";
 $stopwords["de"][] = "von";
 $stopwords["de"][] = "vor";
 $stopwords["de"][] = "vorbei";
 $stopwords["de"][] = "vorher";
 $stopwords["de"][] = "vorueber";
 $stopwords["de"][] = "waehrend";
 $stopwords["de"][] = "waere";
 $stopwords["de"][] = "waeren";
 $stopwords["de"][] = "wann";
 $stopwords["de"][] = "war";
 $stopwords["de"][] = "wäre"; //hinzugefügt
 $stopwords["de"][] = "waren";
 $stopwords["de"][] = "warum";
 $stopwords["de"][] = "was";
 $stopwords["de"][] = "wegen";
 $stopwords["de"][] = "weil";
 $stopwords["de"][] = "weiter";
 $stopwords["de"][] = "weitere";
 $stopwords["de"][] = "weiterem";
 $stopwords["de"][] = "weiteren";
 $stopwords["de"][] = "weiterer";
 $stopwords["de"][] = "weiteres";
 $stopwords["de"][] = "weiterhin";
 $stopwords["de"][] = "welche";
 $stopwords["de"][] = "welchem";
 $stopwords["de"][] = "welchen";
 $stopwords["de"][] = "welcher";
 $stopwords["de"][] = "welches";
 $stopwords["de"][] = "wem";
 $stopwords["de"][] = "wen";
 $stopwords["de"][] = "wenigstens";
 $stopwords["de"][] = "wenn";
 $stopwords["de"][] = "wenngleich";
 $stopwords["de"][] = "wer";
 $stopwords["de"][] = "werde";
 $stopwords["de"][] = "werden";
 $stopwords["de"][] = "weshalb";
 $stopwords["de"][] = "wessen";
 $stopwords["de"][] = "wie";
 $stopwords["de"][] = "wieder";
 $stopwords["de"][] = "wieviel"; //hinzugefügt
 $stopwords["de"][] = "will";
 $stopwords["de"][] = "wir";
 $stopwords["de"][] = "wird";
 $stopwords["de"][] = "wo"; //hinzugefügt
 $stopwords["de"][] = "wodurch";
 $stopwords["de"][] = "wohin";
 $stopwords["de"][] = "wollen";
 $stopwords["de"][] = "wollte";
 $stopwords["de"][] = "wollten";
 $stopwords["de"][] = "worin";
 $stopwords["de"][] = "wuerde";
 $stopwords["de"][] = "wuerden";
 $stopwords["de"][] = "wurde";
 $stopwords["de"][] = "wurden";
 $stopwords["de"][] = "zu"; //hinzugefügt
 $stopwords["de"][] = "zufolge";
 $stopwords["de"][] = "zum";
 $stopwords["de"][] = "zusammen";
 $stopwords["de"][] = "zur";
 $stopwords["de"][] = "zwar";
 $stopwords["de"][] = "zwischen";
?>
