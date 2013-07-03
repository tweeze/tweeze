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
   <td><a href="{{row['url']}}" >{{row['url']}}</a></td>
   <td>{{row['rank2']}}</td>
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