%#!/usr/bin/env python
%# −*− coding:utf−8 −*−
<html>
<head><title>test</title></head>
<body>
<table border="0">
  <tr>
   <th>URL</th>
   <th>Rang</th>
 </tr>
%for row in data:
  <tr>
   <td style="vertical-align: top;padding-bottom: 20px;border-bottom: 1px solid #ccc;">
   <a href="{{row['url']}}"  style="color:#333;text-decoration:none;">
	<p style="width:600px">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea tak</p>
	</a>
	<a href="{{row['url']}}" >{{row['url']}}</a></td>
   <td style="vertical-align: top;padding-bottom: 20px;border-bottom: 1px solid #ccc;">{{row['rank2']}}</td>
 </tr>
%end
  <tr>
   <td>
%if pos>0:
%posDown=str(pos-50)
   <form method="GET" action="/search_fast">
      <input value="{{search}}" type="hidden" name="searchtext"/>
      <input value="{{posDown}}" type="hidden" name="from"/>
      <input type="submit", value="zurueck"/><br/>
    </form>
%end    
%posUp=str(pos+50)
   </td>
   <td>   
   	<form method="GET" action="/search_fast">
      <input value="{{search}}" type="hidden" name="searchtext"/>
      <input value="{{posUp}}" type="hidden" name="from"/>
      <input type="submit", value="weiter"/><br/>
    </form>
</td>
 </tr>
</table>

</body>
</html>