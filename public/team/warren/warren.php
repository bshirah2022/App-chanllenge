<?php
	//$global_pagename = 'Team | Warren';
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	
	$link = dbConnect();
	
	$team = array();
	$sql=<<<SQL
SELECT rowid, fname, lname
	FROM team
	ORDER BY lname, fname
SQL;
	
	$res = dbQuery($sql,$link);
	while($row = mysqli_fetch_assoc($res)){
		array_push($team,$row);
	}
	$json_team = json_encode($team);
	
	dbDisconnect($link);
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>CPT App Challenge - Null Pointers: Team | Warren</title>
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="warren.css">
		<script src="warren.js" ></script>
		<script type='text/javascript'>
			//getElementById shortcut
			function $(elid){
				return document.getElementById(elid);
			}

			//createElement shortcut
			function $n(type,pnode,nclass,nid){
				var node = document.createElement(type);
				if(typeof nclass=="string") node.className = nclass;
				if(typeof nid=="string") node.id = nid;
				if(pnode!=null){
					if(typeof pnode == "string") pnode = $(pnode);
					if(typeof pnode == "object") pnode.appendChild(node);
				}
				return node;
			}

			//cloneElement shortcut
			function $c(cnode,pnode,nclass,nid){
				if(typeof cnode=="string") cnode = $(cnode);
				if(typeof cnode=="object"){
					var node = cnode.cloneNode(true);
					node.id=null;
					if(typeof nclass=="string") node.className = nclass;
					if(typeof nid=="string") node.id = nid;
					if(typeof pnode=="string") pnode = $(pnode);
					if(typeof pnode=="object") pnode.appendChild(node);
					return node;
				}
				return false;
			}

			//createTextNode shortcut
			function $t(text,pnode){
				var node = document.createTextNode(text);
				if(typeof pnode == "string") pnode = $(pnode);
				if(typeof pnode == "object") pnode.appendChild(node);
				
				return node;
			}
			//empty node
			function $empty(node){
				if(typeof(node)!=="undefined"){
					while(node && node.firstChild) node.removeChild(node.firstChild);
				}
				return node;
			}
			
			var team = <?php echo $json_team ?>;
			
			window.onload=function(){
				init();
			}
			
			function init(){
				var divteam = $("div_team");
				for(var i=0; i<team.length; i++){
					var name = team[i]["fname"]+" "+team[i]["lname"];
					$t(name,($n("div",divteam)));
				}
			}
		</script>
	</head>
	<body>
		<div class="main">
			 <h1>H1 Heading</h1>
			 <h2>H2 Heading</h2>
			 <h3>H3 Heading</h3>
			 <p>Paragraph.</p>
			 
			 <h3>Null Pointers Team Members:</h3>
			 <div id="div_team"></div>
		</div>

	</body>
</html>