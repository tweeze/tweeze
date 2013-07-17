<!DOCTYPE HTML>
<html>
<head>
    <title>tweedle - Durchsuche Tweets!</title>
  	<link rel="stylesheet" type="text/css" href="/bottle/suma1/css/formate.css" />
</head>
<body>
%if not fehlertext==None: 
  <div id="error">{{fehlertext}}</div>
%end
  <a class="header-link" href="/bottle/suma1" alt="Twitter Search Engine">Twitter Search Engine</a>
	<div id="Banner"> 
	    <form method="GET" action="/bottle/suma1/search_fast">
			<div class="form1">
				<input id="eingabefeld" type="text" name="searchtext" title="Gib hier Suchbegriffe ein." />
				<input class="submit-button" value="" type="submit" />
				<input type="radio" value="tweedle" id="tweedle_search" name="Suchart" class="tweedle-button" checked="checked" />
				<label for="tweedle_search" class="tweedle-image"></label>
				<div class="selected"> 
					<div class="labe_block"><label for="followers" class="tweedle_weight_label">Qualit&auml;t des Tweeters</label></div>
					<input title="Ver&auml;ndere die Werte um die Gewichtung zu ver&auml;ndern" type="text" id="followers" name="followers" value="2" />
					<div class="labe_block"><label for="retweets" class="tweedle_weight_label">Retweets</label></div>
					<input title="Ver&auml;ndere die Werte um die Gewichtung zu ver&auml;ndern" type="text" id="retweets" name="retweets" value="3"/>
					<div class="labe_block"><label for="favourites" class="tweedle_weight_label">Favorites</label></div>
					<input title="Ver&auml;ndere die Werte um die Gewichtung zu ver&auml;ndern" type="text" id="favourites" name="favourites" value="3"/>
					<div class="labe_block"><label for="inhashtag" class="tweedle_weight_label">Hashtags</label></div>
					<input title="Ver&auml;ndere die Werte um die Gewichtung zu ver&auml;ndern" type="text" id="inhashtag" name="inhashtag" value="20"/>
					<div class="labe_block"><label for="verified" class="tweedle_weight_label">Offizieller Account</label></div>
					<input title="Ver&auml;ndere die Werte um die Gewichtung zu ver&auml;ndern" type="text" id="verified" name="verified" value="1"/>
				</div>
				<input type="radio" value="regular" name="Suchart" class="regular-button" id="regular_search"  />
				<label for="regular_search" class="regular-image"></label>
			</div>
		</form>
	</div>
<div style="position:absolute;right:0;left:0;bottom:10px;text-align:center;font-family: verdana;">tweedle twitter search engine &copy;2013 HHU</div>
</body>
</html>