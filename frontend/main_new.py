#!/usr/bin/env python
# −*− coding:utf−8 −*−
import bottle
from bottle import route, run, install, template, get, post, request
import _mysql
import bottle_mysql
import re
from stemming import Stemming

plugin = bottle_mysql.Plugin(dbuser='root', dbpass='1234', dbname='suma2',dbhost='127.0.0.1')
install(plugin)

@route ( "/",method='GET')
def startseite():
   # _mysql.connect(host="127.0.0.1",user="root",port=3306,passwd="1234",db="suma2")
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




@route( "/search_fast", method='GET')
def searc_fast(db):
    searchtext = request.params.get('searchtext')
    if searchtext == None :
        return template('src/index.tpl')
    #searchtext = request.forms.get('searchtext')   
    startpos=request.params.get('from')
    if startpos==None:
        startpos=0
    startpos=int(startpos)
    
    keywords=[]
    keywords=searchtext.split(" ")
    stemmIds=[]
    listSorted={}
    idString="("
    stemmer=Stemming()
    for keyword in keywords:
        # Stamm ermitteln durch http://snowball.tartarus.org/algorithms/german/stemmer.html
         stemm = stemmer.stem(keyword)
         
        # IDF und w_word id aus datenbank holen 
         c=db.execute("SELECT id FROM twz_words WHERE word LIKE '"+stemm+"';");
         row=db.fetchone()
         if len(stemmIds)>0:
             idString=idString+","
         stemmIds.append(row['id'])
         idString=idString+str(row['id'])

    idString=idString+")"
    c=db.execute("SELECT uf.url,SUM(wm.rank) as rank2 FROM twz_wordmap wm LEFT JOIN (twz_documents d LEFT JOIN twz_urls_final uf ON d.urls_final_id = uf.id) ON d.id=wm.documents_id WHERE wm.words_id IN "+idString+" GROUP BY documents_id ORDER BY rank2 DESC LIMIT "+str(startpos)+",50;");
    rows=db.fetchall()
         
    return template("templates/ergebniss.tpl", data=rows,pos=startpos,search=searchtext)
    
    
@route ( "/search",method='GET')
@route ( "/search",method='POST')
def search(db):
    searchtext = request.params.get('searchtext');
    if searchtext == "" :
        return template('src/index.tpl')
    #searchtext = request.forms.get('searchtext')   
    
    keywords=[]
    keywords=searchtext.split(" ")
    
    stemmIds=[]
    stemmIDFs=[]
    docDictionary={}      # doc_id -> IDF*TF
    docUrlDictionary={}      # doc_id -> IDF*TF
    listSorted={}
    for keyword in keywords:
        # Stamm ermitteln durch http://snowball.tartarus.org/algorithms/german/stemmer.html
        # stemm = stemm(keyword)
         stemm = keyword
         
        # IDF und w_word id aus datenbank holen 
         c=db.execute("SELECT id,idf,word FROM twz_words WHERE word LIKE '"+stemm+"';");
         row=db.fetchone()
         word_id=row['id']
         idf=row['idf']
         
         # Dokument, TF und URL aus Datenbank holen
         c2=db.execute("SELECT wm.documents_id,wm.word_count,uf.url FROM twz_wordmap wm LEFT JOIN twz_documents d ON d.id=wm.documents_id LEFT JOIN twz_urls_final uf ON d.urls_final_id = uf.id WHERE wm.words_id="+str(word_id)+";")
         docRows=db.fetchall()
         
         #Dokumente in Dictionary speichern und
         for row in docRows:
             #tf=logn(row[1]+1,2)/logn(row[2],2);
          
             tf=row['word_count']
             if docDictionary.has_key(row['documents_id']):
                 docDictionary[row['documents_id']]=docDictionary[row['documents_id']]+(tf*idf)
             else:
                 docDictionary[row['documents_id']]=tf*idf
                 docUrlDictionary[row['documents_id']]=row['url']
    # Dokumente nach IDF*TF Wert sortieren
    listSorted = [(k, docDictionary[k],docUrlDictionary[k]) for k in sorted(docDictionary, key=docDictionary.get, reverse=True)]
    return template("templates/ergebniss.tpl", data=listSorted)
        
        
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

@route("calc")
def calc(db):
    db.execute("UPDATE twz_words w,twz_wordmap wm SET wm.rank=w.idf*wm.word_count WHERE wm.words_id=w.id;")

run()