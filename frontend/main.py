#!/usr/bin/env python
# −*− coding:utf−8 −*−
from bottle import route, run, install, template, get, post, request
from bottle_sqlite import SQLitePlugin
install (SQLitePlugin (dbfile="film.db")) #//->Zugang zur DB richtig definieren

#Zeichenerkl�rung:
#//-> an dieser Stelle muss noch Folgendes programmiert werden
#//<- Zeile zum Debuggen, sp�ter l�schen

@route ( "/" )
@route ( "/<place>", method='POST')
@route ( "/<place>", method='GET')

def film ( db, place="home" ): #Standard
  daten=[] #//<- Beispiel-Array nach Bedarf l�schen
#Baseline-Suche
  if place=="search_base":
    idfilm = request.forms.get('idfilm') #//<-Beispiel Daten aus einem Formular ziehen, nach Bedarf l�schen
    query= "SELECT * FROM Film WHERE 1=1;" #//<- Beispiel query ausf�hren und Daten abrufen, nach Bedarf l�schen
    c=db.execute(query)
    daten=c
    row=c.fetchone() #//<- Beispiel Daten abrufen
    idfilm=row[0]
  if place=="searchb_result":     
    searchtext = request.forms.get('searchtext')   
    """//-> Folgendes Schritt f�r Schritt programmieren zur TF*IDF-Suche
    $inhalt = explode(" ",$_POST['Suchfeld']);   

"Suchfeld" ist bei mir der Name des Textfelds. In Python kommst du an den Inhalt mit inhalt = request.forms.get('Suchfeld') heran (bzw. wie auch immer es bei dir hei�t). Den anderen Befehl kenne ich in Python nicht, aber exlode trennt den Inhalt von $_POST['Suchfeld'] an den Leerzeichen auf und macht ein Array draus, das in $inhalt gespeichert wird.

Als n�chstes wollen wir N ermitteln, die Anzahl der Dokumente in der Datenbank, mit der Abfrage:

   $query = "SELECT count(id) FROM `suma1`.twz_documents;";

Und das Ergebnis wird in N oder bei mir $N gespeichert.
Ich habe dann das Array $inhalt mit den Suchworten. Ich m�chte f�r jedes Wort wissen, in wie vielen Dokumenten es auftaucht, das ist hier n oder $n. (Ich habe die Formel aus meinem Kurs Wissensorganisation entnommen. Es kann sein, dass die Variablen bei euch anders hei�en.)

Mit der foreach-Schleife durchlaufe ich das Array, da ich nicht wei�, wie viele Suchbegriffe der Benutzer eingegeben hat. $idw m�sste der Index im Array sein und $wort1 das konkrete Suchwort. (Achtung, ich ber�cksichtige kein Stemming und auch keine boolschen Operatoren. Stemming m�sste hier erfolgen.)

   foreach ($inhalt AS $idw => $wort1) {
   $query = "SELECT COUNT(*) FROM `suma1`.twz_documents` WHERE full_text LIKE '%$wort1%';";
...}

Und das Ergebnis speichere ich im Array n an der gleichen Stelle, wo das Suchwort in inhalt steht, indem ich �ber $idw auf den Index zugreife.
Ich hab dann also zwei Arrays, die so aussehen:

$inhalt
0 Hallo
1 wie
2 gehts

$n
0 245
1 2345
2 567

Okay, jetzt brauchen wir auch die Anzahl, wie oft ein Suchwort insgesamt auftaucht, auch mehrmals in einem Dokument. Ich gehe mit einer Schleife wieder alle Suchw�rter durch und frage f�r jedes erstmal ab, in welchem Dokumenten es vorkommt. (Ich hab sowas �hnliches oben schon gemacht. Vielleicht l�sst sich das ja noch optimieren und k�rzen.) (Wichtig: Die Datenbank achtet auf Gro�- und Kleinschreibung.)

   $query = "SELECT * FROM `suma1`.twz_documents` WHERE full_text LIKE '%$wort1%';";

Dieses Suchergebnis f�r ein Wort laufe ich mit einer Schleife durch

    while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {

Und ich speichere den Text des Dokuments zur weiteren Verarbeitung in einer Variablen.

      $inhalt2 = $row[full_text];

Aus dem Suchwort mache ich einen regul�ren Ausdruck. (Den man durch Anh�ngen von i auch dazu bringen k�nnte, nicht mehr auf Gro�- und Kleinschreibung zu achten.)

      $regex1 = "#$wort1#s";

In PHP gibt preg_match_all die Anzahl zur�ck, wie oft ein Ausdruck im Suchtext gefunden wurde. Fr�her wurden die Arrays mit 0 initialisiert, sodass ich das einfach hinzuaddiert habe. Jetzt meckert PHP, wenn man zu einem nichtgesetzten Wert etwas addieren m�chte, aber eigentlich ist es halt das: Voriger Z�hler f�r das Wort + neue Fundstellen in diesem Dokument = neuer Z�hlerstand

  $anzahl[$idw] = $anzahl[$idw] + preg_match_all($regex1, $inhalt2, $para);

Und da ich hiermit auch die Anzahl habe, wie oft das Wort nur in diesem Dokument auftaucht, speichere ich das auch gleich mit. (Man k�nnte das auch mit dem vorigen Schritt vertauschen und die Variablen addieren, damit der Regex nicht zweimal ausgewertet werden muss.) Das sieht jetzt etwas kompliziert aus, aber das ist ein zweidimensionales Array, wo der erste Index die Id des Dokuments ist und der zweite die Id des Suchworts.

      $t[$row['id']][$idw] = preg_match_all($regex1, $inhalt2, $para);

Und die Anzahl aller W�rter dieses Dokuments brauchen wir sp�ter auch noch. Hierzu wird der Volltext an den Leerzeichen aufgetrennt (explode gibt ein Array zur�ck), mit Count die Anzahl der Werte des Arrays gez�hlt und das Ergebnis wird in einem neuen Array gespeichert, dass an der Stelle der Dokument-Id jetzt die Anzahl der W�rter dieses Dokuments enth�lt.

      $wortges[$row['id']] = count(explode(" ",$inhalt2));

Jetzt k�nnen wir f�r jedes Suchwort (wird wieder mit einer Schleife durchlaufen) den IDF berechnen und das Ergebnis in einem Array speichern, auch wieder am gleichen Index, wie das Suchwort in $inhalt steht.

   $IDF[$idw]=(logn($N,2))/($n[$idw]+1);

Zur Ausgabe des Suchergebnisses will ich erstmal alle Dokumente haben, in denen irgendeins der Suchworte vorkommt. Dazu baue ich die Suchabfrage mit einer Schleife zusammen. Am Ende kommt dann heraus "WHERE fulltext LIKE '%Hallo%' OR fulltext LIKE '%Wie%' OR fulltext LIKE '%gehts%' ..." in beliebiger L�nge.

   $query = "SELECT DISTINCT id FROM `suma1`.twz_documents WHERE ";   
   for ($i=0;$i<count($inhalt);$i++) {
if ($i>0) $query.=" OR ";
$query.="`full_text` LIKE '%$inhalt[$i]%'";
   }

Ich speichere die Anzahl der Dokumente. Wenn die 0 ist, will ich ja sowas wie "Es konnte nichts gefunden werden" ausgeben. M�glicherweise ging das in Python nicht? Ich glaube, ich musste damals das Ergebnis kopieren, damit ich es bei einem Durchlauf z�hlen und beim anderen ausgeben kann.

$anzahl2 = mysql_num_rows($result);

Die n�chsten Zeilen bei mir sind vermutlich sinnlos, weil ich z�hle, wie oft das Dokument gefunden wurde, aber oben steht ja DISTINCT. Wer programmiert nur solchen Bl�dsinn. :))) Auf jeden Fall will ich wissen, welche Dokumente das sind und setze dann in einem Array an dem gleichen Index wie die Id des Dokuments lautet, den Wert auf 1, wenn es gefunden wurde.

 else $erglis[$row['id']] = 1;  

Als n�chstes muss ich zu jedem Dokument die Termfrequenz berechnen, also wieder eine Schleife

foreach($erglis as $key => $value) {

Und zu jedem Dokument muss ich alle Suchworte ber�cksichtigen, also wird die Schleife nochmal verschachtelt.

 foreach ($inhalt as $idw => $wortid1) {

Hm, ich frage mich gerade, was ich mir dabei gedacht habe. Anscheinend wollte ich die IDFs aller Suchworte, die in einem Dokument vorkommen, zu einem IDF-Wert f�r das Dokument addieren und das speichere ich in einem neuen Array am selben Index wie die Id des Dokuments lautet. Aber eigentlich macht das wegen der Schleife keinen Sinn.

  $IDF2[$key]+=$IDF[$idw];

Jetzt soll die Termfrequenz berechnet werden. Das geht nicht, wenn das Dokument aus nur einem Wort besteht, daher eine Sicherheitsabfrage.

  if ($wortges[$key]==1) {/*Logarithmus von 1 ist nicht definiert */}

PHP kennt keine Funktion, um beliebige Logarithmen zu berechnen. logn ist eine Funktion in der Bibliothek, die das kann. Im Matheunterricht haben wir gelernt, dass der Logarithmus einer Zahl zu einer beliebigen Basis genauso gro� ist, wie ein Logarithmus einer Zahl geteilt durch den Logarithmus der Basis. Also habe ich in der Funktion den Dekadischen Logarithmus genommen, aber es ginge auch der nat�rliche oder irgendein anderer, der verf�gbar ist. Wei� nicht, wie das mit Python ist.
function logn($zahl, $basis) {
  $erg=log10($zahl)/log10($basis);
  return $erg;
 }
Also wird die Termfrequenz so berechnet:

  else $TF=logn($t[$key][$idw]+1,2)/logn($wortges[$key],2); //Termfrequenz

Und multipliziert mit dem IDF - wie k�nnte es anders sein - in einem neuen Array aufaddiert gespeichert, wieder an der Stelle der Dokument-Id. Dieses Array soll das fertige Suchergebnis enthalten.

  $ergis2[$key]+=$TF*$IDF2[$key];

Wenn dieses Array am Ende mehr als 1 Element enth�lt, wird es nach den Werten (also den TF*IDF-Werten) absteigend sortiert.

if(count($ergis2)>1) arsort($ergis2);

Dann durchlaufe ich ergis2 mit einer Schleife,

 foreach($ergis2 as $key => $value) {

stelle f�r jedes Dokument (mit TF*IDF>0)

  if ($value>0) {   

eine Anfrage an die Datenbank, um die vollen Daten zu erhalten

   $query = "SELECT twz_documents.id  AS idd, $bezeichner, url_id, expanded_url FROM `suma1`.twz_urls, `suma1`.twz_documents WHERE twz_documents.id='$key' AND twz_urls.id=twz_documents.id;";

 und gebe alles der Reihe nach aus.   

          print "<br/>".$row['identifier']." <a href='".$_SERVER["PHP_SELF"]."?seite=Dokumentansicht&dok=".$row['idd']."'>Im Cache</a>"; 
   if (!($row['url_id']==NULL)) print " <a href='".$row['expanded_url']."'>Internetdokument</a>";

Im Repo sehen die Abfragen etwas anders aus, weil ich die Namen der Datenbank, Tabellen und Attribute durch Variablen eintragen lasse.
    """

  return template("src/search_base", place=place, daten=daten) #gib Ort und sämtliche Daten an die Seite weiter
  
run (host="localhost" , port=8080)

