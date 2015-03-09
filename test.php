<style>
	/*
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
	*/
	
	.search_test_cont * {
		padding-left: 20px;
		background-color: inherit;
	}
	
	.search_test_cont *[class]:before {
		content: "[Class: " attr(class) " ]";
	}
	
	<?php if(isset($_REQUEST["userid"])) echo ".".$_REQUEST['userid']." {\nbackground-color: #99FF99;\n}"; ?>
	
	<?php if(isset($_REQUEST["comments"])) echo ".".$_REQUEST['comments']." {\nbackground-color: #6699FF;\n}"; ?>
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
	$userID = explode(" ", $_REQUEST["userid"]);
	$comments = explode(" ", $_REQUEST["comments"]);
	
	$query = array();
	$query[0] = ".";
	foreach($classes as $class){
		$query[0] .= "//*[contains(concat( ' ', normalize-space(@class), ' '), concat(' ', '$class', ' '))]";
	}
	
	$query[1] = ".";
	foreach($userID as $id){
		$query[1] .= "//*[contains(concat( ' ', normalize-space(@class), ' '), concat(' ', '$id', ' '))]";
	}
	
	$query[2] = ".";
	foreach($comments as $comment){
		$query[2] .= "//*[contains(concat( ' ', normalize-space(@class), ' '), concat(' ', '$comment', ' '))]";
	}

	libxml_use_internal_errors(true);
	
	$doc = DOMDocument::loadHTMLFile($url);
	$html = $doc->saveHTML();
	$doc = DOMDocument::loadHTML(mb_convert_encoding($html, "UTF-8"));
		
	libxml_use_internal_errors(false);

	$xpath = new DOMXpath($doc);
	
	//header('Content-Type: text/csv; charset=utf-8');
	//header('Content-Disposition: attachment; filename=data.csv');
				
	$file = fopen("php://output", "w");

	//if(!isset($_REQUEST["userid"]) || !isset($_REQUEST["comments"])){
	if(!isset($_REQUEST["save"])){
		$posts = $xpath->query($query[0]."[1]");
	}
	else{
		$posts = $xpath->query($query[0]);
	}

		foreach($posts as $post){
			
			$line = array();
			
			if(!isset($_REQUEST["save"])){
				$postTxt = $doc->saveHTML($post);
				echo "<div style='border: 1px solid #000;' id='search_test_cont'>$postTxt</div>";
			}
						
			if(isset($_REQUEST["userid"])){
		$users = $xpath->query($query[1], $post);
				
				if($users->length < 1) continue;
				
				//echo "Length: ".$users->length."<br/>".$users->item(1)->nodeValue;
				
				foreach($users as $user){
					//$userTxt = $doc->saveHTML($user);
					$userTxt = $user->nodeValue;
					//$line[] = $user->nodeValue;
				}
			}
			
			if(isset($_REQUEST["comments"])){
		$comments = $xpath->query($query[2], $post);
				
				if($comments->length < 1) continue;

				foreach($comments as $comment){
					//$commentTxt = $doc->saveHTML($comment);
					$commentTxt = $comment->nodeValue;
					//$commentTxt = str_replace(array('\n', '\r'), ' ', $comment->nodeValue);
					//$line[] = $comment->nodeValue;
				}
			}
			
			//echo "<div><h3>$userTxt</h3><p>$commentTxt</p></div><br/>";
			echo "<p>$commentTxt</p><br/><br/>";
			//echo "\n$userTxt, \"$commentTxt\"";
			//$list[] = "\n$userTxt, \'$commentTxt\'";
			
			//fputcsv($file, $line);
			//echo "<br/>";
		}
	//}
	
	fclose($file);
	
	if(!isset($_REQUEST["save"])){
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
					<input type="text" name="class" value="<?php echo (isset($_REQUEST["class"]) ? $_REQUEST["class"] : "entry"); ?>" />
				</li>
				<li>
					<label>UserID:</label>
				</li>
				<li>
					<input type="text" id="userclass" name="userid" value="<?php echo (isset($_REQUEST["userid"]) ? $_REQUEST["userid"] : "author"); ?>" />
					<a href="#" id="selectuser">Click n' Select</a>
				</li>
				<li>
					<label>Comment Container:</label>
				</li>
				<li>
					<input type="text" name="comments" id="commentsclass" value="<?php echo (isset($_REQUEST["comments"]) ? $_REQUEST["comments"] : "md"); ?>" />
					<a href="#" id="selectcomments">Click n' Select</a>
				</li>
				<li>
					<label>Save search:</label>
					<input type="checkbox" name="save" />
				</li>
				<li>
					<input type="submit" value="Pull again" />
				</li>
			</ul>
		</fieldset>
	</form>
<script>
	function findUserClass(){
		event.stopPropagation();
		event.preventDefault();
		var hook = document.getElementById("userclass");
		hook.value = this.classList[0];
		clearListeners();
	}
	
	function findCommentClass(){
		event.stopPropagation();
		event.preventDefault();
		var hook = document.getElementById("commentsclass");
		hook.value = this.classList[0];
		clearListeners();
	}
	
	function clearListeners(el){
		console.log(el);
		if(el === null || el === undefined){
			el = document.getElementById('search_test_cont');	
		}
		console.log(el);
		
		for(var i = 0; i < el.children.length; i++){
			clearListeners(el.children[i]);
		}
		
		if(el.className == "")
			return;
				
		el.removeEventListener('mouseover', highlight);
		el.removeEventListener('mouseout', restore);
		el.removeEventListener('click', inputSearched);
		searching = false;
	}
	
	function highlight(){
		event.stopPropagation();
		this.style.backgroundColor = "#ff8888";
	}
	
	function restore(){
		event.stopPropagation();
		this.style.backgroundColor = "";
	}
	
	function highlightChildren(el){
				
		for(var i = 0; i < el.children.length; i++){
			highlightChildren(el.children[i]);
		}
		
		if(el.className == "")
			return;
				
		el.addEventListener('mouseover', highlight);
		el.addEventListener('mouseout', restore);
		el.addEventListener('click', inputSearched);
	}
	
	var test = document.getElementById('search_test_cont');
	
	var inputSearched;
	var searching = false;
	document.getElementById('selectuser').addEventListener('click', function(){
		event.preventDefault();
		if(searching) return;
		
		searching = true;
		inputSearched = findUserClass;
		highlightChildren(test);
	});
	
	document.getElementById('selectuser').addEventListener('click', function(){
		event.preventDefault();
		if(searching) return;
		searching = true;
		
		inputSearched = findUserClass;
		highlightChildren(test);
	});
	
</script>
<?php }

}

?>
