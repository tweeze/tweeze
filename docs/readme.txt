Hi,

um größere Dateien (z.B. PDFs, Fotos, was-auch-immer) pushen zu können muss man noch folgendes unter Eclipse einstellen:

Im Menü unter "Window" -> "Preferences" -> "Team" -> "Git" -> "Configuration":

"Add Entry" klicken und folgende Werte eintragen:

Key: http.postBuffer
Value: 524288000

Dann mit "Ok" bestätigen.

Gruß

# Test

# Denkt dran das ist ein ÖFFENTLICHES Repository d.h. keine Passwörter/etc. veröffentlichen.

# Anleitung_Eclipse_Git_python_SQL.pdf enthält alle wichtigen Informationen. 
Den SQL-Teil habe ich reingenommen damit jeder schonmal die Möglichkeit hat evtl. mit dem 
ER-Modell zu arbeiten. Später sollte das ein öffentlicher MySQL-Server sein auf den wir alle
zugreifen könnnen.