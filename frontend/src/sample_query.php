<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>test query</title>
<meta charset="utf-8" />

<style>
	body{font-family:Verdana, Geneva, sans-serif; color:#666;}
	#center{width:350px;margin:5px auto 0 auto;}
	form { float:left; width:380px; }
	input, label { width:70%; clear:left; float:left; margin-top:5px; }
	input{color:#999;}
	input[type="submit"] { width:30%; clear:left; float:left; margin-top:20px; color:#666; }
	.small{ font-size:0.7em; color:#999999}
	h3{font-size:1.4em;}
	#kwdz{ width:400px; position:relative; top:5px;}
	#domainz{width:400px; margin-top:10px;}
	#result{margin-top:20px;}
</style>

</head>
<body>

<div id="center">

<form action="/query" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
	<div id="Query">
	<label>Search terms:</label>
	<input name="keywords" value="e.g. Bundestagswahl 2013" onclick="this.value=''"/>
	</div>
	<input type="submit" name="check" value="Query" />
</form>

<br><br><br><br><br><br>

<table align="center" border="1" style="border-collapse:collapse" width="100%" cellpadding="5px">
	<tr>
        <td>Content</td>
	</tr>
        %for row in afilme:
        	<tr>
                %for col in row:
                <td>{{col}}</td>
                %end
                </tr>
                %end
</table>


</body>
</html>