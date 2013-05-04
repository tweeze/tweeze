<?php
 $server="localhost";
 $datenbank="assolier";
 $benutzername="assolier";
 $passwort="Asso_7z";
 $connection=mysql_connect($server, $benutzername, $passwort);
 $db=mysql_select_db($datenbank, $connection);
 //Tabelle Dokument
 $dokument = "twz_documents";
 $id_dokument = "id";
 $bezeichner = "identifer";
 $url = "url_id";
 $eingelesen = "parsed";
 $zeitstempel = "parse_date";
 //Tabelle Wort
 $wort = "wort";
 $id_wort = "id_wort";
 $wort = "wort";
 $anzahl = "anzahl";
 $idf = "idf";
 //Tabelle Text
 $text = "text";
 $wort_id = "wort_id";
 $dokument_id = "dokument_id";
 $stelle = "stelle";
?>