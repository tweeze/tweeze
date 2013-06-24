#!/usr/bin/env python
# −*− coding:utf−8 −*−
from bottle import route, run, install, template, get, post, request
import bottle_mysql
import re

app = bottle.Bottle()
plugin = bottle_mysql.Plugin(dbuser='root', dbpass='1234', dbname='suma1')
app.install(plugin)

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
    
    stemmIds=[]
    stemmIDFs=[]
    docDictionary={}      # doc_id -> IDF*TF
    docUrlDictionary={}      # doc_id -> IDF*TF
    docDictionarySorted={}
    for keyword in keywords:
        # Stamm ermitteln durch http://snowball.tartarus.org/algorithms/german/stemmer.html
        # stemm = stemm(keyword)
         stemm = keyword
         
        # IDF und w_word id aus datenbank holen 
         c=db.query("SELECT word_id,idf,word FROM twz_documents WHERE word LIKE '"+stemm+"';");
         row=c.fetchone()
         word_id=row[0]
         idf=row[1]
         
         # Dokument, TF und URL aus Datenbank holen
         c2=db.query("SELECT wm.doc_id,wm.word_count,uf.url FROM twz_wordmap wm LEFT JOIN twz_documents d ON d.id=wm.doc_id LEFT JOIN twz_urls_final uf ON d.urls_final_id = uf.id WHERE wm.word_id="+word_id+";")
         docRows=c2.fetchall()
         
         #Dokumente in Dictionary speichern und
         for row in docRows:
             #tf=logn(row[1]+1,2)/logn(row[2],2);
          
             tf=row[1]
             if docDictionary.has_key(row[0]):
                 docDictionary[row[0]]=docDictionary[row[0]]+(tf*idf)
             else:
                 docDictionary[row[0]]=tf*idf
                 docUrlDictionary[row[0]]=row[2]
    # Dokumente nach IDF*TF Wert sortieren
    docDictionarySorted = sorted(docDictionary.iteritems(), key=lambda (k,v): (v,k))
        
    return template("src/ergebnisse.tpl", urlDict=docUrlDictionary,docDictSorted=docDictionarySorted)
        
        
@route ( "/search_old",method='GET')
@route ( "/search_old",method='POST')
def search_old(db):
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
    
    return template("src/ergebnisse.tpl", daten=daten)
