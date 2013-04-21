Hi,

um größere Dateien (z.B. PDFs, Fotos, was-auch-immer) pushen zu können muss man noch folgendes unter Eclipse einstellen:

Im Menü unter "Window" -> "Preferences" -> "Team" -> "Git" -> "Configuration":

"Add Entry" klicken und folgende Werte eintragen:

Key: http.postBuffer
Value: 524288000

Dann mit "Ok" bestätigen.

Gruß