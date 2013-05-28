#!/usr/bin/env python
# −*− coding:utf−8 −*−
from bottle import route, run, install, template, get, post, request
import bottle_mysql
import re

#TODO find correct solution for mysql
from bottle_sqlite import SQLitePlugin
install (SQLitePlugin (dbfile="suma.db"))

@route ( "/",method='GET')
def startseite():
    return template('src/index.tpl')

@route ( "/searchBase",method='GET')
@route ( "/searchBase",method='POST')
def searchbase(db):
    idfilm = request.forms.get('idfilm')
    query= "SELECT * FROM Film WHERE 1=1;"
    c=db.execute(query)
    daten=c
    row=c.fetchone() 
    idfilm=row[0]
    return template("src/search_base", daten=daten)


@route ( "/search",method='GET')
@route ( "/search",method='POST')
def search(db):
    searchtext = request.forms.get('searchtext')   
    
    keywords=[]
    keywords=searchtext.split(" ")
    
    # Anzahl aller Dokumente in der Datenbank ermitteln (N)
    documentCountQuery="SELECT count(*) FROM `suma1`.twz_documents;"
    documentCount=0
    c=db.query(documentCountQuery)
    row=c.fetchone()
    documentCount=row[0]
    
    keywordDocumentMatchCounts=[]
    keywordIDF=[]
    index=-1;
    for keyword in keywords:
        index=index+1
        
        #Ermitteln wie viele Dokumente die Keywords enthalten
        documentCountQueryFilteredByKeyword="SELECT COUNT(*) FROM `suma1`.twz_documents` WHERE full_text LIKE '%"+keyword+"%';"
        c=db.query(documentCountQueryFilteredByKeyword)
        row=c.fetchone()
        keywordDocumentMatchCounts.append(row[0])
    
        #Regulären Ausdruck zu Keyword kompilieren
        keywordRegularExpression=re.compile(keyword,Re.I|Re.U)
        
        #Ermitteln wie oft das Keyword in der Datenbank auftaucht
        documentQueryFilteredByKeyword="SELECT * FROM `suma1`.twz_documents` WHERE full_text LIKE '%"+keyword+"%';"
        c=db.query(documentQueryFilteredByKeyword)
        rows=c.fetchall()
        for row in rows:
            documentContent=row['full_text']
            matches=keywordRegularExpression.findall(documentContent)
            # Die id des Dokuments und die Anzahl der Treffer im Dokument zwischenspeichern
            # Die Anzahl der Wörter des Dokuments ermitteln
            # Die WDF (TF) nach Harman berecchnen und zwischenspeichern
            # logn($t[$key][$idw]+1,2)/logn($wortges[$key],2);
        # -IDF für das keyword berechnen
        keywordIDF[index]=math.log(1+(documentCount/keywordDocumentMatchCounts[index]),2)


        # Alle Dokuemnte die eines der Keywords enthalten aus der DB holen
        # Alle vorhandenen IDF * TF Werte für jedes Dokument aufaddieren
        # Die Dokumente absteigend nach den Werten sortieren
        # Die Dokumente ans Ergebnis-Template senden (ausgeben)
    
    return template("src/search", daten=daten)
