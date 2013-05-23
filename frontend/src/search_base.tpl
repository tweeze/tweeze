<!DOCTYPE HTML>
<html>
  <head>
    <title>FilmDb {{place}}</title>
  </head>
  <body>

  Menü: <a href="/home">Home</a>, <a href="/search_base">Baseline-Suche</a>, <a href="/filmdb/schwarzes_loch">Schwarzes Loch</a>

<!--Home, ev. mit Statistiken der Seite -->
%if place=="home": 
    <h1>Willkommen</h1> 
%end
<!-- Baseline-Suche -->
%if place=="search_base":
  <h2>Hier kannst du nach Filmen suchen.</h2>
    <form method="post" action="/searchb_result">
      Suchbegriffe: <input type="text" name="searchtext"/><br/>
      <input type="submit", value="Suchen"/><br/>
    </form>
%end
<!-- Das Suchergebnis anzeigen. -->
%if place=="searchb_result":
  <br/>{{daten2}}
    <table>
      <tr><td><b>Titel</b></td><td><b>Uploadzeit</b></td></tr>
<!--Für den Komfort wurden wieder die Filmbeschreibungen des Benutzers mitverlinkt -->
  %for row in daten.fetchall():
    <tr><td><a href="/filmdb/film?idfilm={{row['IdFilm']}}">{{row['Titel']}}</a></td><td>{{row['Uploadzeit']}}</td></tr>

  %end
    </table>
%end

  </body>
</html>
