<style>
	.search_test_cont *:nth-child(2n) {
		background-color: #CCC;
	}

	.search_test_cont * *:nth-child(2n+1) {
		background-color: #DDD;
	}
	
	.search_test_cont * * *:nth-child(2n) {
		background-color: #EEE;
	}

	.search_test_cont *,
	.search_test_cont * *,
	.search_test_cont * * * {
		display: block;
	}
	
	.search_test_cont * { padding-left: 20px; }
	/*
	.search_test_cont * * { padding-left: 20px; }
	.search_test_cont * * * { padding-left: 30px; }
	.search_test_cont * * * * { padding-left: 40px; }
	*/
	
	.search_test_cont *[class]:before {
		content: "[Class: " attr(class) " ] [" url(<?php );
	}
</style>
<?php

if(!isset($_REQUEST["url"])){ ?>

<form name="findData" id="form0" method="get" action="test.php">
	<fieldset>
		<legend>Data Location</legend>
		<ul>
			<li>
				<label>URL:</label>
			</li>
			<li>
				<input type="text" name="url" value="http://reddit.com" />
			</li>
			<li>
				<label>Class:</label>
			</li>
			<li>
				<input type="text" name="class" placeholder="Classnames separated with spaces" />
			</li>
			<li>
				<input type="submit" value="Pull" />
			</li>
		</ul>
	</fieldset>
</form>
	
<?php }

else {

	$url = $_REQUEST["url"];
	$classes = explode(" ", $_REQUEST["class"]);
	
	//if(isset($_REQUEST["query"]))
		//$query = $_REQUEST["query"];
	//else{
		$query = "";
		foreach($classes as $class){
			$query .= "//div[contains(concat( ' ', normalize-space(@class), ' '), concat(' ', '$class', ' '))]";
		}
	//}

	libxml_use_internal_errors(true);
	
	$doc = DOMDocument::loadHTMLFile($url);
		
	libxml_use_internal_errors(false);

	$xpath = new DOMXpath($doc);

	$comments = $xpath->query("(".$query.")[1]");
	
	echo "(".$query.")[1]";
		
	foreach($comments as $comment){
		//$txt = htmlspecialchars($doc->saveHTML($comment));
		$txt = $doc->saveHTML($comment);
		/*
		$txt = str_replace("<", "&lt;", $txt);
		$txt = str_replace(">", "&gt;", $txt);
		$txt = str_replace("&lt;/", "<br/>&lt;/", $txt);
		$txt = str_replace("&gt;", "&gt;<br/>", $txt);
		*/
		echo "<br/><br/><div style='border: 1px solid #000;' class='search_test_cont'>$txt</div>";
	}
	
	?>
	<form name="narrow" id="form1" action="test">
		<fieldset>
			<legend>Narrow Down Results</legend>
			<ul>
				<li>
					<label>URL:</label>
				</li>
				<li>
					<input type="text" name="url" value="<?php echo $url; ?>" />
				</li>
				<li>
					<label>Class:</label>
				</li>
				<li>
					<input type="text" name="class" value="<?php echo $_REQUEST["class"]; ?>" />
				</li>
				<li>
					<input type="submit" value="Pull again" />
				</li>
			</ul>
		</fieldset>
	</form>
<?php }

?>
