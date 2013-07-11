#!/usr/bin/env python
# −*− coding:utf−8 −*−
import bottle
from bottle import route, run, install, template, get, post, request, static_file
import _mysql
import bottle_mysql
import re
from stemming import Stemming

plugin = bottle_mysql.Plugin(dbuser='root', dbpass='1234', dbname='suma1',dbhost='127.0.0.1')
install(plugin)

@route ( "/",method='GET')
def startseite():
   # _mysql.connect(host="127.0.0.1",user="root",port=3306,passwd="1234",db="suma2")
    return template('templates/index.tpl')


@route( "/search_fast", method='GET')
def searc_fast(db):
    searchtext = request.params.get('searchtext')
    if searchtext == None :
        return template('src/index.tpl')
    #searchtext = request.forms.get('searchtext')   
    
    if not request.params.get('Suchart') == None:
        if request.params.get('Suchart')=='regular':
            return TrefferOr(db,searchtext)
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
         
    return template("templates/ergebnis.tpl", data=rows,pos=startpos,search=searchtext)
    
    



####OR Suche
@route("/TrefferOR", method='post')
@route("/TrefferOR/", method='post')
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
		return template("fehler",fehlermeldung=fehlermeldung)
        pass
    else:

        #######
		#Gewichtung
		a=request.forms.get('gewichtunga')
			if a=="":
				a=2
			else:
				if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',a)==None:
					fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
					return template("fehler",fehlermeldung=fehlermeldung)
				else:
					a=re.sub("[\,]","[\.]", a)
		b=request.forms.get('gewichtungb')
			if b=="":
				b=3
			else:
				if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',b)==None:
					fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
					return template("fehler",fehlermeldung=fehlermeldung)
				else:
					b=re.sub("[\,]","[\.]", b)
		c=request.forms.get('gewichtungc')
			if c=="":
				c=3
			else:
				if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',c)==None:
					fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
					return template("fehler",fehlermeldung=fehlermeldung)
				else:
					c=re.sub("[\,]","[\.]", c)
		d=request.forms.get('gewichtungd')
			if d=="":
				d=20
			else:
				if re.match('^(\d|\d\d|\d\d\d|\d[,]\d|\d[.]\d|\d\d[,]\d|\d\d[.]\d|\d\d\d[,]\d|\d\d\d[.]\d)$',d)==None:
					fehlermeldung="Bitte korrigiere deine Eingabe. Erlaubt sind Zahlen zwischen 0 und 999.9 mit bis zu einer Nachkommastelle!"
					return template("fehler",fehlermeldung=fehlermeldung)
				else:
					d=re.sub("[\,]","[\.]", d)
		e=request.forms.get('gewichtunge')
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

        return template("templates/ergebnis.tpl", data=ergebnis,pos=0,search=suchstringunprocessed)
         #return template ("Treffer", ergebnis=ergebnis)


        ########das hier brauchen wir glaub ich nicht

    #Daten in DB zwischenspeichern
        #b.execute('INSERT INTO results (tweetID, urlID, url, value) VALUES (?,?,?,?)', (tweetid, urlid, url, wert,))

#Daten aus DB holen zum Anzeigen
        #c=db.execute('SELECT url, vSUM(value), COUNT(value) FROM tweetresults GROUP BY url')
        #endergebnis=c # geht so nicht weil ganz ganz viele ergebnisse
    #return template ("Treffer", filme=filme, nums=nums, benutzername=benutzername)

@route("/TrefferAND", method='post')
@route("/TrefferAND/", method='post')
def TrefferAnd(db,suchstr=""):
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
			return template("fehler",fehlermeldung=fehlermeldung)
			pass
		else:
		db.text_factory=str
		suchstringunprocessed=request.forms.get('suchwort1')
		suchstringprocessed1=re.sub("[Öö]","oe", suchstringunprocessed)
		suchstringprocessed2=re.sub("[Ää]","ae", suchstringprocessed1)
		suchstringprocessed3=re.sub("[Üü]","ue", suchstringprocessed2)
		suchterme=re.split("\W+",suchstringprocessed3)
		anzahl_suchworte=(len(suchterme))

	#kein Suchwort
		if suchterme[0]=="":
			fehlermeldung="Bitte gib ein Suchwort ein!"
			return template("fehler",fehlermeldung=fehlermeldung)
		else:
			#######
			#Gewichtung
			a=request.forms.get('gewichtunga')
				if a=="":
					a=2
				else:
					a=re.sub("[\,]","[\.]", a)
			b=request.forms.get('gewichtungb')
				if b=="":
					b=3
				else:
					b=re.sub("[\,]","[\.]", b)
			c=request.forms.get('gewichtungc')
				if c=="":
					c=3
				else:
					c=re.sub("[\,]","[\.]", c)
			d=request.forms.get('gewichtungd')
				if d=="":
					d=20
				else:
					d=re.sub("[\,]","[\.]", d)
			e=request.forms.get('gewichtunge')
				if e=="":
					e=1
				else:
					e=re.sub("[\,]","[\.]", e)
			f=request.forms.get('gewichtungf')
				if f=="":
					f=18
				else:
					f=re.sub("[\,]","[\.]", f)
			x=1
			y=50

			#suchterme = ['SPD','CDU','Piraten']
			query_part1 = "(SELECT tweet_id, count( * ) AS inhashtag_counter1 FROM hashtags WHERE text = '%s' GROUP BY hashtags.tweet_id)as one"%(suchterme[0])
			query_part2 = "(SELECT tweet_id, count( * ) AS intweettext_counter1 FROM tweettext WHERE tweetword = '%s' GROUP BY tweettext.tweet_id)as eins "%(suchterme[0])
			hcounter = "(coalesce(inhashtag_counter1,0))"
			tcounter = "intweettext_counter1"
			print query_part1
			print query_part2
			print hcounter
			print tcounter
			for element in suchterme[1:]:	
				x=x+1
				y=y+1
				hcounter += "+(coalesce(inhashtag_counter"+str(x)+",0))"
				tcounter += "+intweettext_counter"+str(x)+" "
				query_part1 +=" join(SELECT tweet_id, count( * ) AS inhashtag_counter"+str(x)+" FROM hashtags WHERE text = '%s' GROUP BY hashtags.tweet_id)as d%s ON (one.tweet_id=d%s.tweet_id)"%(element,str(x),str(x))			
				query_part2 +=" join (SELECT tweet_id, count( * ) AS intweettext_counter"+str(x)+" FROM tweettext WHERE tweetword = '%s' GROUP BY tweettext.tweet_id)as p%s ON (eins.tweet_id=p%s.tweet_id)"%(element,str(y),str(y))
			base_query = "SELECT id, url, text, avg(wert) from (select id, url, text,(((coalesce((followers_count/statuses_count),0)) * "+str(a)+")+((coalesce((retweet_count/followers_count),0)) * "+str(b)+") + ((coalesce((favorite_count/followers_count),0)) * "+str(c)+")+ ((coalesce((inhashtag_counter/hashtags_all),0)) * "+str(d)+")+ (verified* "+str(e)+")+((intweettext_counter/"+str(a)+") * "+str(f)+")+(tweetlink_counter/5)) AS wert from (SELECT tweets.id, tweets.text,(coalesce(hashtags_all,0)) as hashtags_all,retweet_count, favorite_count, followers_count, inhashtag_counter, intweettext_counter, statuses_count, verified, links.url, (coalesce(tweetlink_counter,0)) as tweetlink_counter FROM tweets JOIN links ON (links.tweet_id=tweets.id) JOIN(SELECT teil2.tweet_id, (coalesce(teil1.inhashtag_counter,0)) as inhashtag_counter, teil2.intweettext_counter FROM ((select one.tweet_id,(%s ) as inhashtag_counter from (%s )) AS teil1 RIGHT JOIN (select eins.tweet_id, (%s) as intweettext_counter from ( %s )) AS teil2 ON (teil2.tweet_id=teil1.tweet_id))) AS counting ON(tweets.id=counting.tweet_id) LEFT JOIN hashtags_total ON(tweets.id=hashtags_total.id) LEFT JOIN links_counter ON (links_counter.url=links.url) GROUP BY tweets.id) AS part1) AS part2 GROUP BY url ORDER BY AVG(wert) DESC"%(hcounter,query_part1,tcounter,query_part2)
			print base_query
			c=db.execute(base_query)
			ergebnis=[]
			ergebnis=c.fetchall()

			return template ("Treffer", ergebnis=ergebnis)


        ########

    #Daten in DB zwischenspeichern
        #db.execute('INSERT INTO results (tweetID, urlID, url, value) VALUES (?,?,?,?)', (tweetid, urlid, url, wert,))

#Daten aus DB holen zum Anzeigen
       # c=db.execute('SELECT url, SUM(value), COUNT(value) FROM tweetresults GROUP BY url')
        #endergebnis=c # geht so nicht weil ganz ganz viele ergebnisse
    #return template ("Treffer", filme=filme, nums=nums, benutzername=benutzername)

@route("/calc",method="GET")
def calc(db):
    db.execute("UPDATE twz_words w,twz_wordmap wm SET wm.rank=w.idf*wm.wdf WHERE wm.words_id=w.id;")

@route('/css/<filename>')
def css_static(filename):
    return static_file(filename, root='./css/')

@route('/images/<filename>')
def css_static(filename):
    return static_file(filename, root='./images')


run()
