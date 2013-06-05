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
 $url = "urls_final_id";
 $eingelesen = "parsed";
 $zeitstempel = "parse_date";
 $full_text = "content";
 $meta_desc = "meta_description";
 $meta_keyw = "meta_keywords";
 $language= "language_description";
 //Tabelle Urls
 $twz_urls = "twz_urls_final";
 $expanded_url = "url";
 //id ist hard-coded
?>