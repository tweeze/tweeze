#!/ us r / b in /env python
# -*- coding : utf-8 -*-

import re, MySQLdb 

mysql_opts = {
    'host': "localhost",
    'user': "suma1",
    'pass': "784ui7i",
    'db':   "suma1"
    }
mysql = MySQLdb.connect(mysql_opts['host'], mysql_opts['user'], mysql_opts['pass'], mysql_opts['db']) 

mysql.apilevel = "2.0"
mysql.threadsafety = 2
mysql.paramstyle = "format" 

c=mysql.cursor()

####OR Suche
@route("/TrefferOR", method='post')
@route("/TrefferOR/", method='post')
def Treffer(db):
  db.text_factory=str
	suchstringunprocessed=request.forms.get('suchwort1')
	suchstringprocessed1=re.sub("[Öö]","oe", suchstringunprocessed)
	suchstringprocessed2=re.sub("[Ää]","ae", suchstringprocessed1)
	suchstringprocessed3=re.sub("[Üü]","ue", suchstringprocessed2)
	suchterme=re.split("\W+",suchstringprocessed3)
	anzahl_suchworte=(len(suchworte))

#kein Suchwort
	if argument0=="":
		pass
	else:

		#######
		a=2
		b=3
		c=3
		d=20
		e=1
		f=18
		
		#suchterme = ['SPD','CDU','Piraten']
		query_part = "'%s'"%(suchterme[0])
		for element in suchterme[1:]:
			query_part += " OR text = '%s'"%(element)
			#base_query muss noch angepasst werden
			
		base_query = "SELECT id, url, avg(wert) from (select id, url,(((coalesce((followers_count/statuses_count),0)) * a)+((coalesce((retweet_count/followers_count),0)) * b) + ((coalesce((favorite_count/followers_count),0)) * c)+ ((coalesce((inhashtag_counter/hashtags_all),0)) * d)+ (verified* e)+((intweettext_counter/anzahl_suchworte) * f)+(tweetlink_counter/5)) AS wert from (SELECT tweets.id, (coalesce(hashtags_all,0)) as hashtags_all, retweet_count, favorite_count, followers_count, inhashtag_counter, intweettext_counter, statuses_count, verified, links.url, tweetlink_counter FROM tweets JOIN links ON (links.tweet_id=tweets.id) JOIN (SELECT teil2.tweet_id, teil1.inhashtag_counter, teil2.intweettext_counter FROM ((SELECT tweet_id, count( * ) AS inhashtag_counter FROM hashtags WHERE text = %s GROUP BY hashtags.tweet_id) AS teil1 RIGHT JOIN (SELECT tweet_id, count( * ) as intweettext_counter FROM tweettext WHERE tweetword = %s GROUP BY tweettext.tweet_id) AS teil2 ON (teil2.tweet_id=teil1.tweet_id))) AS counting ON (tweets.id=counting.tweet_id) LEFT JOIN hashtags_total ON(tweets.id=hashtags_total.id) LEFT JOIN links_counter ON (links_counter.url=links.url) GROUP BY tweets.id) AS part1) AS part2 GROUP BY url ORDER BY AVG(wert) DESC"%(query_part,query_part)

		print base_query
		c=db.execute(base_query)
		ergebnis=[]
		ergebnis=c.fetchall()
		
		return template ("Treffer", ergebnis=ergebnis)
		
		
		########das hier brauchen wir glaub ich nicht
	
	#Daten in DB zwischenspeichern
        #b.execute('INSERT INTO results (tweetID, urlID, url, value) VALUES (?,?,?,?)', (tweetid, urlid, url, wert,))

#Daten aus DB holen zum Anzeigen
        #c=db.execute('SELECT url, SUM(value), COUNT(value) FROM tweetresults GROUP BY url')
        #endergebnis=c # geht so nicht weil ganz ganz viele ergebnisse
	#return template ("Treffer", filme=filme, nums=nums, benutzername=benutzername)
	
@route("/TrefferAND", method='post')
@route("/TrefferAND/", method='post')
def Treffer(db):
	db.text_factory=str
	suchstringunprocessed=request.forms.get('suchwort1')
	suchstringprocessed1=re.sub("[Öö]","oe", suchstringunprocessed)
	suchstringprocessed2=re.sub("[Ää]","ae", suchstringprocessed1)
	suchstringprocessed3=re.sub("[Üü]","ue", suchstringprocessed2)
	suchterme=re.split("\W+",suchstringprocessed3)
	anzahl_suchworte=(len(suchworte))

#kein Suchwort
	if argument0=="":
		pass
	else:

		#######
		a=2
		b=3
		c=3
		d=20
		e=1
		f=18
		x=1
		y=50
		
		#suchterme = ['SPD','CDU','Piraten']
		query_part = "'%s'"%(suchterme[0])
		for element in suchterme[1:]:
			#query_part += " OR text = '%s'"%(element)
			query_part1 +=" join(SELECT tweet_id, count( * ) AS inhashtag_counter FROM hashtags WHERE text = '%s' GROUP BY hashtags.tweet_id)as ? ON (one.tweet_id=?.tweet_id)"%(element,x,x)
			#base_query muss noch angepasst werden
			query_part2 +=" join (SELECT tweet_id, count( * ) AS intweettext_counter FROM tweettext WHERE tweetword = '%s' GROUP BY tweettext.tweet_id)as ? ON (eins.tweet_id=?.tweet_id)"%(element,y,y)
			x=x+1
			y=y+1
			
		base_query = "SELECT id, url, avg(wert) from (select id, url,(((coalesce((followers_count/statuses_count),0)) * a)+((coalesce((retweet_count/followers_count),0)) * b) + ((coalesce((favorite_count/followers_count),0)) * c)+ ((coalesce((inhashtag_counter/hashtags_all),0)) * d)+ (verified* e)+((intweettext_counter/anzahl_suchworte) * f)+(tweetlink_counter/5)) AS wert from (SELECT tweets.id, (coalesce(hashtags_all,0)) as hashtags_all,retweet_count, favorite_count, followers_count, inhashtag_counter, intweettext_counter, statuses_count, verified, links.url, (coalesce(tweetlink_counter,0)) as tweetlink_counter FROM tweets JOIN links ON (links.tweet_id=tweets.id) JOIN (SELECT teil2.tweet_id, (coalesce(teil1.inhashtag_counter,0)) as inhashtag_counter, teil2.intweettext_counter FROM ((select one.tweet_id,((coalesce(inhashtag_counter1,0))+(coalesce(inhashtag_counter,0))) as inhashtag_counter from ((SELECT tweet_id, count( * ) AS inhashtag_counter1 FROM hashtags WHERE text = %s GROUP BY hashtags.tweet_id)as one %s )) AS teil1 RIGHT JOIN (select eins.tweet_id,(intweettext_counter1+intweettext_counter) as intweettext_counter from ((SELECT tweet_id, count( * ) AS intweettext_counter1 FROM tweettext WHERE tweetword = %s GROUP BY tweettext.tweet_id)as eins %s )) AS teil2 ON (teil2.tweet_id=teil1.tweet_id))) AS counting ON (tweets.id=counting.tweet_id) LEFT JOIN hashtags_total ON(tweets.id=hashtags_total.id) LEFT JOIN links_counter ON (links_counter.url=links.url) GROUP BY tweets.id) AS part1) AS part2 GROUP BY url ORDER BY AVG(wert) DESC"%(element,query_part1,element,query_part2)

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
