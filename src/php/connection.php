<?php
 $server="localhost";
 $datenbank="assolier";
 $benutzername="assolier";
 $passwort="Asso_7z";
 $connection=mysql_connect($server, $benutzername, $passwort);
 $db=mysql_select_db($datenbank, $connection); 
?>