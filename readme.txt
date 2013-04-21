Hi,

um größere Dateien (z.B. PDFs, Fotos, was-auch-immer) pushen zu können muss man noch folgendes unter Eclipse einstellen:

Im Menü unter "Window" -> "Preferences" -> "Team" -> "Git" -> "Configuration":

"Add Entry" klicken und folgende Werte eintragen:

Key: http.postBuffer
Value: 524288000

Dann mit "Ok" bestätigen.

Gruß

# Denkt dran das ist ein ÖFFENTLICHES Repository d.h. keine Passwörter/etc. veröffentlichen.

# Anleitung_Eclipse_Git_python.pdf steht auch was zu Python drinne...

# Eclipse SQL Explorer http://eclipsesql.sourceforge.net/
