#!/usr/bin/env python
# −*− coding:utf−8 −*−
import bottle
from bottle import route, run, install, template, get, post, request, static_file
import _mysql
import bottle_mysql
import re
import os
from stemming import Stemming

plugin = bottle_mysql.Plugin(dbuser='suma1', dbpass='784ui7i', dbname='suma1',dbhost='127.0.0.1')
install(plugin)

@route ( "/suma1",method='GET')
def startseite():
   # _mysql.connect(host="127.0.0.1",user="suma1",port=3306,passwd="784ui7i",db="suma2")
    return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext=None)


@route( "/suma1/search_fast", method='GET')
def searc_fast(db):
    searchtext = request.params.get('searchtext')
    if searchtext == None or searchtext=="":
        return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext="Bitte gib ein Suchwort ein!")
    #searchtext = request.forms.get('searchtext')   
    
    
    if request.params.get('Suchart') == None:
        return TrefferAnd(db,searchtext)
    if request.params.get('Suchart')=='tweedle':
        return TrefferAnd(db,searchtext)
    
    startpos=request.params.get('from')
    if startpos==None:
        startpos=0
    startpos=int(startpos)
    
    searchtext=re.sub("[Öö]","oe", searchtext)
    searchtext=re.sub("[Ää]","ae", searchtext)
    searchtext=re.sub("[Üü]","ue", searchtext)
    
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
         if row==None or len(row)==0:
             continue
         if len(stemmIds)>0:
             idString=idString+","
         stemmIds.append(row['id'])
         idString=idString+str(row['id'])

    idString=idString+")"
    if(idString=="()"):
         return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext="Diese Suche ergab keine Treffer!")
   
    c=db.execute("SELECT uf.url,SUM(wm.rank) as rank,concat(substring(content,1,138),'...') as teaser FROM twz_wordmap wm LEFT JOIN (twz_documents d LEFT JOIN twz_urls_final uf ON d.urls_final_id = uf.id) ON d.id=wm.documents_id WHERE wm.words_id IN "+idString+" GROUP BY documents_id ORDER BY rank DESC LIMIT "+str(startpos)+",50;");
    rows=db.fetchall()
    if len(rows)==0:
         return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext="Diese Suche ergab keine Treffer!")
    return template("/var/www/suma1/bottle/templates/ergebnis.tpl", data=rows,pos=startpos,search=searchtext,Suchart="regular")
    
    



####OR Suche
@route("/suma1/TrefferOR", method='post')
@route("/suma1/TrefferOR/", method='post')
def TrefferOr(db,suchstr=""):
    db.text_factory=str
    if suchstr=="":
        suchstringunprocessed=request.forms.get('suchwort1')
    else:
        suchstringunprocessed=suchstr
    suchstringprocessed1=re.sub("[Öö]","oe", suchstringunprocessed)
    suchstringprocessed2=re.sub("[Ää]","ae", suchstringprocessed1)
    suchstringprocessed3=re.sub("[Üü]","ue", suchstringprocessed2)
    suchterme=re.split("\W+",suchstringprocessed3)
    anzahl_suchworte=(len(suchterme))

#kein Suchwort
    if suchstr=="":
        fehlermeldung="Bitte gib ein Suchwort ein!"
        return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext=None)
        pass
    

    #######
    #Gewichtung
    a=request.forms.get('followers')
    if a=="":
        a=2
    else:
        if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',a)==None:
            fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
            return template("fehler",fehlermeldung=fehlermeldung)
        else:
            a=re.sub("[\,]","[\.]", a)
    b=request.forms.get('retweets')
    if b=="":
        b=3
    else:
        if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',b)==None:
            fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
            return template("fehler",fehlermeldung=fehlermeldung)
        else:
            b=re.sub("[\,]","[\.]", b)
    c=request.forms.get('favourites')
    if c=="":
        c=3
    else:
        if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',c)==None:
            fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
            return template("fehler",fehlermeldung=fehlermeldung)
        else:
            c=re.sub("[\,]","[\.]", c)
    d=request.forms.get('inhashtag')
    if d=="":
        d=20
    else:
        if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',d)==None:
            fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
            return template("fehler",fehlermeldung=fehlermeldung)
        else:
            d=re.sub("[\,]","[\.]", d)
    e=request.forms.get('verified')
    if e=="":
        e=1
    else:
        if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',e)==None:
            fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
            return template("fehler",fehlermeldung=fehlermeldung)
        else:
            e=re.sub("[\,]","[\.]", e)
    f=request.forms.get('gewichtungf')
    if f=="":
        f=18
    else:
        if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',a)==None:
            fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
            return template("fehler",fehlermeldung=fehlermeldung)
        else:
            f=re.sub("[\,]","[\.]", f)


    #suchterme = ['SPD','CDU','Piraten']
    query_part = "'%s'"%(suchterme[0])
    for element in suchterme[1:]:
        query_part += " OR text = '%s'"%(element)
        #base_query muss noch angepasst werden

    base_query = "SELECT id, url, avg(wert) as rank2 from (select id, url,(((coalesce((followers_count/statuses_count),0)) * "+str(a)+")+((coalesce((retweet_count/followers_count),0)) * "+str(b)+") + ((coalesce((favorite_count/followers_count),0)) * "+str(c)+")+ ((coalesce((inhashtag_counter/hashtags_all),0)) * "+str(d)+")+ (verified* "+str(e)+")+((intweettext_counter/"+str(anzahl_suchworte)+") * "+str(f)+")+(tweetlink_counter/5)) AS wert from (SELECT tweets.id, (coalesce(hashtags_all,0)) as hashtags_all, retweet_count, favorite_count, followers_count, inhashtag_counter, intweettext_counter, statuses_count, verified, links.url, tweetlink_counter FROM tweets JOIN links ON (links.tweet_id=tweets.id) JOIN (SELECT teil2.tweet_id, teil1.inhashtag_counter, teil2.intweettext_counter FROM ((SELECT tweet_id, count( * ) AS inhashtag_counter FROM hashtags WHERE text = %s GROUP BY hashtags.tweet_id) AS teil1 RIGHT JOIN (SELECT tweet_id, count( * ) as intweettext_counter FROM tweettext WHERE tweetword = %s GROUP BY tweettext.tweet_id) AS teil2 ON (teil2.tweet_id=teil1.tweet_id))) AS counting ON (tweets.id=counting.tweet_id) LEFT JOIN hashtags_total ON(tweets.id=hashtags_total.id) LEFT JOIN links_counter ON (links_counter.url=links.url) GROUP BY tweets.id) AS part1) AS part2 GROUP BY url ORDER BY AVG(wert) DESC"%(query_part,query_part)

    print base_query
    #c1=db.execute("select (idf*wdf) as idfwdf from twz_wordmap join twz_words on twz_wordmap.words_id=twz_words.id where 
    c=db.execute(base_query)
    ergebnis=[]
    ergebnis=db.fetchall()

    return template("/var/www/suma1/bottle/templates/ergebnis.tpl", data=ergebnis,pos=0,search=suchstringunprocessed)
         #return template ("Treffer", ergebnis=ergebnis)


        ########das hier brauchen wir glaub ich nicht

    #Daten in DB zwischenspeichern
        #b.execute('INSERT INTO results (tweetID, urlID, url, value) VALUES (?,?,?,?)', (tweetid, urlid, url, wert,))

#Daten aus DB holen zum Anzeigen
        #c=db.execute('SELECT url, vSUM(value), COUNT(value) FROM tweetresults GROUP BY url')
        #endergebnis=c # geht so nicht weil ganz ganz viele ergebnisse
    #return template ("Treffer", filme=filme, nums=nums, benutzername=benutzername)

@route("/suma1/TrefferAND", method='post')
@route("/suma1/TrefferAND/", method='post')
def TrefferAnd(db,suchstr=""):
    db.text_factory=str
    if suchstr=="":
        suchstringunprocessed=request.forms.get('suchwort1')
        if suchstringunprocessed==None or suchstringunprocessed=="":
            fehlermeldung="Bitte gib ein Suchwort ein!"
            return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext=fehlermeldung)
    else:
        suchstringunprocessed=suchstr
        
    suchstringprocessed1=re.sub("[Öö]","oe", suchstringunprocessed)
    suchstringprocessed2=re.sub("[Ää]","ae", suchstringprocessed1)
    suchstringprocessed3=re.sub("[Üü]","ue", suchstringprocessed2)
    suchterme2=re.split("\W+",suchstringprocessed3)
    anzahl_suchworte=(len(suchterme2))
    
    suchterme=[]
    stemmer=Stemming()
    for suchterm in suchterme2:
        
        # Stamm ermitteln durch http://snowball.tartarus.org/algorithms/german/stemmer.html
         suchterme.append(stemmer.stem(suchterm))
   

    #kein Suchwort
    if suchterme[0]=="":
        fehlermeldung="Bitte gib ein Suchwort ein!"
        return template("fehler",fehlermeldung=fehlermeldung)
    else:
        #######
        #Gewichtung
        a=request.params.get('followers')
        if a==None or a=="" :
            a=2
        else:
            a=float(re.sub("[\,]","[\.]", a))
        b=request.params.get('retweets')
        if b==None or b=="":
            b=3
        else:
            b=float(re.sub("[\,]","[\.]", b))
        c=request.params.get('favourites')
        if c==None or c=="":
            c=3
        else:
            c=float(re.sub("[\,]","[\.]", c))
        d=request.params.get('inhashtag')
        if d==None or d=="":
            d=20
        else:
            d=float(re.sub("[\,]","[\.]", d))
        e=request.params.get('verified')
        if e==None or e=="":
            e=1
        else:
            e=float(re.sub("[\,]","[\.]", e))
        
        f=request.forms.get('gewichtungf')
        if f==None or f=="":
           f=18
        else:
            f=re.sub("[\,]","[\.]", f)
        x=1
        y=50

        #suchterme = ['SPD','CDU','Piraten']
        query_part1 = "(SELECT tweet_id, count( * ) AS inhashtag_counter1 FROM hashtags WHERE text = '%s' GROUP BY hashtags.tweet_id)as one"%(suchterme[0])
        query_part2 = "(SELECT tweet_id FROM tweettext WHERE tweetword = '%s' GROUP BY tweettext.tweet_id)as eins "%(suchterme[0])
        hcounter = "(coalesce(inhashtag_counter1,0))"
        for element in suchterme[1:]:    
            x=x+1
            y=y+1
            hcounter += "+(coalesce(inhashtag_counter"+str(x)+",0))"
            query_part1 +=" join(SELECT tweet_id, count( * ) AS inhashtag_counter"+str(x)+" FROM hashtags WHERE text = '%s' GROUP BY hashtags.tweet_id)as d%s ON (one.tweet_id=d%s.tweet_id)"%(element,str(x),str(x))            
            query_part2 +=" join (SELECT tweet_id FROM tweettext WHERE tweetword = '%s' GROUP BY tweettext.tweet_id)as p%s ON (eins.tweet_id=p%s.tweet_id)"%(element,str(y),str(y))
        base_query = "SELECT id, url, text as teaser, avg(wert) as rank from (select id, url, text,(((coalesce((followers_count/statuses_count),0)) * "+str(a)+")+((coalesce((retweet_count/followers_count),0)) * "+str(b)+") + ((coalesce((favorite_count/followers_count),0)) * "+str(c)+")+ ((coalesce((inhashtag_counter/hashtags_all),0)) * "+str(d)+")+ (verified* "+str(e)+")+(log(tweetlink_counter))) AS wert from (SELECT tweets.id, tweets.text,(coalesce(hashtags_all,0)) as hashtags_all,retweet_count, favorite_count, followers_count, inhashtag_counter, statuses_count, verified, links.url, (coalesce(tweetlink_counter,0)) as tweetlink_counter FROM tweets JOIN links ON (links.tweet_id=tweets.id) JOIN(SELECT teil2.tweet_id, (coalesce(teil1.inhashtag_counter,0)) as inhashtag_counter FROM ((select one.tweet_id,(%s ) as inhashtag_counter from (%s )) AS teil1 RIGHT JOIN (select eins.tweet_id from ( %s )) AS teil2 ON (teil2.tweet_id=teil1.tweet_id))) AS counting ON(tweets.id=counting.tweet_id) LEFT JOIN hashtags_total ON(tweets.id=hashtags_total.id) LEFT JOIN links_counter ON (links_counter.url=links.url) GROUP BY tweets.id) AS part1) AS part2 GROUP BY url ORDER BY rank DESC"%(hcounter,query_part1,query_part2)
        print base_query
        c=db.execute(base_query)
        ergebnis=[]
        ergebnis=db.fetchall()
        if len(ergebnis)==0:
            return template('/var/www/suma1/bottle/templates/index.tpl',fehlertext="Diese Suche ergab keine Treffer!")
        return template("/var/www/suma1/bottle/templates/ergebnis.tpl", data=ergebnis,pos=0,search=suchstringunprocessed,Suchart="tweedle")
   
        ########

    #Daten in DB zwischenspeichern
        #db.execute('INSERT INTO results (tweetID, urlID, url, value) VALUES (?,?,?,?)', (tweetid, urlid, url, wert,))

#Daten aus DB holen zum Anzeigen
       # c=db.execute('SELECT url, SUM(value), COUNT(value) FROM tweetresults GROUP BY url')
        #endergebnis=c # geht so nicht weil ganz ganz viele ergebnisse
    #return template ("Treffer", filme=filme, nums=nums, benutzername=benutzername)

@route("/suma1/calc",method="GET")
def calc(db):
    db.execute("UPDATE twz_words w,twz_wordmap wm SET wm.rank=w.idf*wm.wdf WHERE wm.words_id=w.id;")



#------------------------------------CHECK WITH JENS---------------->



@route('/suma1/css/<filename>')
def css_static(filename):
#Hier muss evtl die absolute url zu dem css Verzeichniss rein
    return static_file(filename, root='/var/www/suma1/bottle/css/')

#Ist das richtig mit dem suma1???
@route('/suma1/images/<filename>')
def css_static(filename):
#Hier muss evtl die relative url zu dem images Verzeichniss rein
    return static_file(filename, root='/var/www/suma1/bottle/images/')

