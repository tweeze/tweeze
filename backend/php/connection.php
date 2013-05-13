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
 $bezeichner = "identifier";
 $url = "url_id";
 $eingelesen = "parsed";
 $zeitstempel = "parse_date";
?>