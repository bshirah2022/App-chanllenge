<?php
	//turn off php behavior of printing errors/warnings - activate in prod
	//error_reporting(0);
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	
	function valid_email($email){
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
		return true;
	}
	
	function strandom($size){
		$chars = '0123456789bcdghjkmnpqrtvwxz';
		$len=1;
		$randstr='';
		if(intval($size)>1) $len = intval($size);
		if($len>50) $len=50; //max
		
		for ($i=0; $i<$len; $i++) {
			$randstr.=substr($chars,rand(0,strlen($chars)-1),1);
		}
		return $randstr;
	}
	
	$link = dbConnect();
	
	$client_table = 'team'; 
	
	$GLOBALS["user_loggedin"] = false;
	$GLOBALS["user_firstname"] = null;
	$GLOBALS["user_lastname"] = null;
	$GLOBALS["user_username"] = null;
	
	$v_green_darkest = '#37451F'; //logo background
	$v_green_darker = '#586A36'; //tab footer
	$v_green_dark = '#6B7D47'; //tab background
	$v_green_mid = '#6B7D47';
	$v_green_light = '#a4b585';
	$v_green_lightest = '#e6eed8';
	
	$v_pagename = "Search";
	$v_notice=null;
	$v_errors = [];
	
	
	//user registration variables
	if(isset($_REQUEST['regfirst'])){
		$regfirst = dbEscape($_REQUEST['regfirst'],$link);
		$reglast = dbEscape($_REQUEST['reglast'],$link);
		$regun = dbEscape($_REQUEST['regusername'],$link);
		$regemail = dbEscape($_REQUEST['regemail'],$link);
		$regpw = dbescape($_REQUEST['regpw'],$link);
		
		$usalt = strandom(10);
		$pwhash = md5($usalt.$regpw);
		
		//check for unique values already present
		$stmt = mysqli_prepare($link,"SELECT username FROM user WHERE username=? OR email=?");
		mysqli_stmt_bind_param($stmt,'ss',$regun, $regemail);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if(mysqli_num_rows($result)==0){
			//insert
			$stmt = mysqli_prepare($link, "INSERT INTO user (username, email, firstname, lastname, pwsalt, pwhash) VALUES (?, ?, ?, ?, ?, ?)");
			mysqli_stmt_bind_param($stmt, 'ssssss', $regun, $regemail, $regfirst, $reglast, $usalt, $pwhash);
			$success = mysqli_stmt_execute($stmt);
			
			if($success){
				$GLOBALS["user_loggedin"] = true;
				$GLOBALS["user_firstname"] = $regfirst;
				$GLOBALS["user_lastname"] = $reglast;
				$GLOBALS["user_username"] = $regun;
			}
		}else{
			array_push($v_errors,"Username and/or email address already exists.");
		}

	}elseif(isset($_REQUEST['logun'])){
		//explicit login
		$logun = $_REQUEST['logun'];
		$logpw = $_REQUEST['logpw'];
		
		//fetch the password salt
		$stmt = mysqli_prepare($link,"SELECT pwsalt FROM user WHERE username=?");
		mysqli_stmt_bind_param($stmt,'s',$logun);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		$row = mysqli_fetch_assoc($result);
		$salt = $row["pwsalt"];
		
		//check the password
		$pwhash = md5($salt.$logpw);
		$stmt = mysqli_prepare($link,"SELECT userid, username, email, firstname, lastname FROM user WHERE username=? AND pwhash=? LIMIT 1");
		mysqli_stmt_bind_param($stmt, 'ss', $logun, $pwhash);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if(mysqli_num_rows($result)==1){
			$row = mysqli_fetch_assoc($result);
			
			$GLOBALS["user_loggedin"] = true;
			$GLOBALS["user_firstname"] = $row["firstname"];
			$GLOBALS["user_lastname"] = $row["lastname"];
			$GLOBALS["user_username"] = $row["username"];
			$GLOBALS["user_userid"] = $row["userid"];
		}else{
			array_push($v_errors,"Incorrect username or password.");
		}
		
	}
	

	
	
	//NOTE: use relative path links, not direct links which were causing timeouts on AWS
	//$http_root_dir = 'https://'.$_SERVER['HTTP_HOST'].'/';
?>


<?php echo "<?xml version=\"1.1\" encoding=\"UTF-8\"?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>CPT App Challenge - Null Pointers - entity client v1.1</title>
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<script type="text/javascript" src="/common/js/common.js"></script>
		<link rel="stylesheet" href="/common/css/common.css">
		<script type='text/javascript'>
			var lastResponse;
			
			var loggedin = <?=$GLOBALS["user_loggedin"] ? 'true' : 'false';?>;
			var username = <?=$GLOBALS["user_loggedin"] ? "'".$GLOBALS["user_username"]."'" : 'null';?>;
			var userid = <?=$GLOBALS["user_loggedin"] ? $GLOBALS["user_userid"] : 'null';?>;
			
			function bodyOnLoad(){
				//var params = "action=get_tables";
				
				//action=get_rows&tablename=team&conditions=[{"col":"fname","op":"like","val":"%arre%"}]
				var params = "action=get_injury";
				params+="&page=1";
				params+="&rowsperpage=10";
				/*
				params+="&tablename=task";
				params+="&conditions="+JSON.stringify([{
					"col":"userid",
					"op":"=",
					"val":userid
				}]);
				*/
				var obj={
					"request":"list_tasks"
				};
				
				ajaxRequest('server.php',receiveServerResponse.bind(obj),params);
				
			}
			
			var injuries=[];
			var injOrder = [
				"ID",
				"EventDate",
				"Employer",
				"Address1",
				"Address2",
				"City",
				"State",
				"Zip",
				"Inspection",
				"Industry"
			];
			
			var injFilters = [
			];
			
			function receiveServerResponse(responseText){
				lastResponse = responseText;
				//console.log(responseText);
				//console.log(this);
				
				
				var obj = this;
				
				
				
				if(this["request"]=="list_tasks"){
					var tasks = JSON.parse(responseText);
					console.log("tasks:");
					console.log(tasks);
					
					var divSearch = $g("searchlist");
					injuries = JSON.parse(responseText);
					
					$empty(divSearch);
					var tbl = $n("table",divSearch,"taskTable");
					var thead=$n("thead",tbl);
					var tr = $n("tr",thead);
					for (var j=0; j<injOrder.length; j++){
						var th = $n("th",tr);
						$t(injOrder[j],th);
						$n("br",th);
						var mag = $n("span",th,"magnify");

					}
					
					var tbody=$n("tbody",tbl);
					for(var i=0; i<injuries.length; i++){
						var injury = injuries[i];
						var tr = $n("tr",tbody);
						for (var j=0; j<injOrder.length; j++){
							var col = injOrder[j];
							var val = injury[col];
							var td = $n("td",tr);
							
							if(col=="ID"){
								var link = $n("a");
								link.href="?id="+val;
								td.appendChild(link);
								$t(val,link);
							}else{
								$t(val,td);
							}
						}
					}

					
					
					var divTaskList = $g("tasklist");
					if(tasks.length==0){
						$t("You have no tasks",divTaskList);
					}else{
						var taskTable = $n("table",divTaskList,"taskTable");
						var thead = $n("thead",taskTable);
						var tr = $n("tr",thead);
						
						var th = $n("th",tr); $t("",th);
						var th = $n("th",tr); $t("Task",th);
						var th = $n("th",tr); $t("Status",th);
						
						var tbody = $n("tbody",taskTable);
						
						for(var i=0; i<tasks.length; i++){
							var task = tasks[i];
							
							var tr = $n("tr",tbody);
							var td = $n("td",tr); var chk = $n("input"); chk.type="checkbox"; td.appendChild(chk);
							var td = $n("td",tr); $t(task["tasktitle"],td);
							var td = $n("td",tr); $t(task["taskstatus"],td);

						}
					}
					
				}
			}
		</script>
		<script type="text/javascript">
			function inputGainedFocus(obj){
				if(obj.value==obj.dataset.default){
					obj.value="";
					removeCSSClass(obj,"inPreview");
					if(obj.dataset.secure){
						obj.type="password";
					}
				}
			}
			function inputLostFocus(obj){
				if(obj.value==""){
					obj.value=obj.dataset.default;
					addCSSClass(obj,"inPreview");
					if(obj.dataset.secure){
						obj.type="text";
					}
				}
			}
		</script>
		<style type='text/css'>
			html{
				height:100%;
			}
			body{
				width: 100%;
				height:100%;
				margin: 0;
				background-color: #37451f;
				background-image: linear-gradient(#37451f, #a4b585);
				background-image: linear-gradient(#000000, #a4b585);
			}
			input{
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				border-radius: 5px;
			}
			input.inPreview{
				color:#999999;
			}
			form{
				padding:0px;
				margin:0px;
			}
			ul,ol{
				margin-top:0;
				margin-bottom:0;
				padding-top:0;
				padding-bottom:0;
			}
			.hidden{
				display:none;
			}
			div.master{
			    position: absolute;
			    left: 0px;
			    top: 0px;
			    z-index: 2;
			    text-align: center;
			    width: 100%;
			    margin: 0;
			}
			div.main{
			    min-width: 900px;
			    max-width:1500px;
			    margin: 0 auto;
			    position: relative;
			    padding-top: 10px;
			}
			div.headerBar{
				height:50px;
				width:100%; /* 100% of main div (900px) */
				margin-bottom:8px;
			}
			div.headerLogo{
				float:left;
				height:50px;
				/*width:160px;*/
				
				color:white;
				font-size:26px;
			}
			img.headerLogoImg{
				border:0;
			}
			div.headerLogin{
				position:relative;
				top:30px;
				left:-6px;
				display:inline-block;
				float:right;
				height:30px;
				white-space:nowrap;
			}
			div.headerLoginImg{
				background-image:url('img/keys26.jpg');
				background-repeat:no-repeat;
				float:left;
				display:inline-block;
				width:26px;
				height:26px;
			}
			span.magnify{
				background-image:url('img/magnify.svg');
				display:inline-block;
				width:25px;
				height:25px;
				background-size:cover;
				cursor:pointer;

			}
			span.headerLoginTxt{
				position:relative;
				white-space:nowrap;
				top:3px;
				color:white;
				text-align:left;
				text-shadow:black 1px 1px 2px;
				font-family:arial;
				font-size:14px;
			}
			input.inpLoginUser{
				position:relative;
				top:2px;
				width:150px;
				margin-right:3px;
				float:left;
			}
			input.inpLoginPass{
				position:relative;
				top:2px;
				width:100px;
				float:left;
			}
			input.inpLogSubmit{
				float:left;
			}
			a.lightLink{
				color:white;
				text-decoration:none;
			}
			a.lightLink:hover{
				text-decoration:underline;
			}
			a.darkLink{
				color:<?php echo $v_green_darker; ?>;
			}
			.darkText{
				color:<?php echo $v_green_darker; ?>;
			}
			div.headerTabsBG{
				position:relative;
				background-image:url('img/htab/tabbg.gif');
				background-repeat:repeat-x;
				height:47px;
				width:100%;
			}
			div.htabLeft{
				background-image:url('img/htab/tableft.gif');
				width:7px;
				height:47px;
				float:left;
			}
			div.htabRight{
				background-image:url('img/htab/tabright.gif');
				width:6px;
				height:47px;
				float:right;
			}
			div.htabs{
				position:absolute;
				left:0px;
				top:0px;
				width:900px;
				height:47px;
			}
			div.outerhtab{
				display:inline-block;
				height:47px;
				float:left;
				margin:auto;
			}
			div.tabsep{
				float:left;
				width:2px;
				height:47px;
				background-image:url('img/htab/tabsep.gif');
				background-repeat:no-repeat;
			}
			div.innerhtab{
				color:white;
				text-align:center;
				padding:12px;
				cursor:pointer;
				text-shadow:black 1px 1px 2px;
				font-family:arial;
				font-size:14px;
			}
			div.divider{
				height:6px;
				background-color:#586A36;
			}
			div.contentheader{
				position:relative;
				height:34px;
				background-color:#DFD6AF;
				background-image:url('img/htab/contentheaderbg.gif');
				text-align:left;
				font-style:italic;
				
			}
			span.chs{
				font-family:"Lucida Console", Monaco, monospace;
				font-size:18px;
				font-weight:bold;
				color:#A3996E;
				text-shadow:white 1px 1px 3px;
				letter-spacing:3px;
				position:relative;
				top:7px;
				left:18px;
			}
			div.divoptions{
				display:inline-block;
				position:absolute;
				right:4px;
				top:3px;
				width:32px;
				height:28px;
				background-position:center;
				background-color:<?php echo $v_green_dark ?>;
				background-image:url('img/greenoptions.gif');
				background-repeat:no-repeat;
				cursor:pointer;
			}
			div.content{
				background-color:white;
				color:#223311;
				padding:6px;
				border-bottom-left-radius: 8px;
				border-bottom-right-radius: 8px;
				text-align:left;
			}

			div.imgbanner{
				background-color:white;
				text-align:left;
				position:relative;
				padding:10px;
				min-height:250px;
				<?php
					echo "background-image:url('img/photo/injury2.jpg');";
					
				?>
				background-size:cover;
			}
			div.registerbox{
				position:relative;
				left:40px;
				top:5px;
				padding:5px;
				background-image:url('img/blacktrans30.png');
				border-radius:9px;
				display:inline-block;
				border:3px solid #37451f;
				width:auto;
			}
			div.notifybox{
				position:relative;
				left:40px;
				top:5px;
				/*
				position:relative;
				right:40px;
				top:5px;
				*/
				padding:10px;
				margin-left:20px;
				background-image:url('img/whitetrans50.png');
				border-radius:9px;
				display:inline-block;
				/*border:3px solid #6a7c46;*/
				border:3px solid red;
				width:auto;
				font-family:arial;
				font-size:14px;
				font-weight:bold;
			}
			div.registernew{
				position:relative;
				white-space:nowrap;
				text-align:center;
				margin:4px;
				padding:10px;
				border:1px solid #ffff99;
				background-image:url('img/blacktrans70.png');
				border-radius:4px;
				width:auto;
			}
			span.registernewtxt{
				color:white;
				letter-spacing:2px;
				font-family:arial;
				font-size:16px;
				font-style:italic;
				font-weight:bold;
			}
			div.registertip{
				float:left;
				width:225px;
				padding:5px;
				text-align:justify;
				
			}
			span.registertiptxt{
				color:white;
				font-family:arial;
				font-size:16px;
			}
			div.registerform{
				float:right;
				padding:5px;
				padding-left:15px;
			}
			div.relativereg{
				position:relative;
			}
			input.reginput{
				position:relative;
				width:134px;
				height:17px;
			}

			input.inpRegSubmit:hover{
				background-image:url('img/casubmit_darker.png');
			}

			div.bottombar{
				background-image:url('img/htab/bbtabbg.gif');
				background-repeat:repeat-x;
				height:8px;
				float:left;
				width:888px;
			}
			div.bbleft{
				float:left;
				width:6px;
				height:8px;
				background-image:url('img/htab/bbtableft.gif');
			}
			div.bbright{
				float:left;
				width:6px;
				height:8px;
				background-image:url('img/htab/bbtabright.gif');
			}
			div.botspacer{
				height:50px;
			}
			.emph{
				font-weight:bold;
			}
		</style>
		<style type='text/css'>

			.taskTable{
				border:1px solid black;
				border-collapse:collapse;
			}
			.taskTable th{
				background-color:#696969;
				color:white;
				font-weight:bold;
			}
			.taskTable td, .taskTable th{
				padding:6px;
				border:2px solid black;
			}
		</style>
	</head>
	<body onLoad="javascript:bodyOnLoad();">
		<div class="master">
			<div class="main">
				<div class="headerBar">
					<div class="headerLogo">
						Severe Injury Search
					</div>
					<div class="headerLogin">
					</div>
				</div>
				<div class="headerTabsBG">
					<div class="htabLeft"><!-- --></div>
					<div class="htabRight"><!-- --></div>
					<div class="htabs">

					</div>
				</div>
				<div class="imgbanner" id="inbody_imgbanner"><!-- -->

					<?php if(count($v_errors)>0 || $v_notice!=null){ ?>
						<div class="notifybox">
							<?php
							if(count($v_errors)>0){
								echo "<div style='color:#990000'>Please correct these errors and try again:\r\n<ul>\r\n";
								for($i=0;$i<count($v_errors);$i++){
									echo "<li>".$v_errors[$i]."\r\n";
								}
								echo "</ul>\r\n</div>\r\n";
							}else{
								echo  "<div style='color:#000099'>".$v_notice."</div>\r\n";
							}
							?>
						</div>
					<?php } ?>
						
				</div>
				<div class="divider"><!-- --></div>
				<div class="contentheader">
					<span class="chs"><?php if(isset($v_pagename)) echo $v_pagename; ?></span>
					<?php if(isset($v_display_options)){?>
						<div class="divoptions" onclick="showOptions()"><!-- --></div>
					<?php } ?>
				</div>
				<div class="content">
					<div style="padding:20px">
						<div id="searchlist" class="searchlist">
						</div>
					</div>
				</div>
			</div>
		
		
		
		
	</body>
</html>

<?php
	dbDisconnect($link);
?>