<html>
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    </head>
	<body>
		<ul class="inhalt">
			<li class="titel"><a href="index.php">Inhaltsverzeichnis</a></li>
			<li><a href="index.php?seite=Einlesen">Einlesen</a></li>
			<li><a href="index.php?seite=Dokumente">Dokumente</a></li>
			<li><a href="index.php?seite=Suche">Suche</a></li>
			<li><a href="index.php?seite=Statistik">Statistik</a></li>
			<li><a href="index.php?seite=Chat">Chat</a></li>
		</ul>
	</body>
</html>

<?php
  if ($_GET[seite] != "") include "php/".$_GET[seite].".php";
?>