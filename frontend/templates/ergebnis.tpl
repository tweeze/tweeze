%#!/usr/bin/env python
%# −*− coding:utf−8 −*−
<html>
    	<title>tweedle - Durchsuche Tweets!</title>
  	<link rel="stylesheet" type="text/css" href="/bottle/suma1/css/formate.css" />
<body>
<a class="header-link" href="/bottle/suma1" alt="Twitter Search Engine">Twitter Search Engine</a>
<div id="Banner" style="overflow-y:auto;bottom:50px;padding: 10px 0;" >
<table border="0">
 %for row in data:
  <tr>
   <td style="vertical-align: top;padding-bottom: 20px;border-bottom: 1px solid #ccc;">
   <a href="{{row['url']}}"  style="color:#333;text-decoration:none;">
%if not row['teaser']==None:	
%try:
<div style="width:600px">{{row['teaser'].decode("iso-8859-1").encode("utf-8")}}</div>
%except Exception:
Fehler
%end
%else:
	<div style="width:600px">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea tak</p>
%end	
	</a>
	<a href="{{row['url']}}">{{row['url']}}</a></td>
   	<td style="vertical-align: top;padding-bottom: 20px;border-bottom: 1px solid #ccc;">{{row['rank']}}</td>
 </tr>
%end
%if Suchart=='regular':
  <tr>
   <td>
%if pos>0 and len(data)==50:
%posDown=str(pos-50)
   <form method="GET" action="/bottle/suma1/search_fast"
      <input value="{{search}}" type="hidden" name="searchtext"/>
      <input value="{{posDown}}" type="hidden" name="from"/>
      <input type="submit", value="zurueck"/><br/>
    </form>
%end    
%posUp=str(pos+50)
   </td>
   <td>   
   	<form method="GET" action="/bottle/suma1/search_fast">
      <input value="{{search}}" type="hidden" name="searchtext"/>
      <input value="{{posUp}}" type="hidden" name="from"/>
      <input value="{{Suchart}}" type="hidden" name="Suchart" /> 
      <input type="submit", value="weiter"/><br/>
    </form>
</td>
 </tr>
%end
</table>
</div>
<div style="position:absolute;right:0;left:0;bottom:10px;text-align:center;font-family: verdana;">tweedle twitter search engine &copy;2013 HHU</div>
</body>
</html>