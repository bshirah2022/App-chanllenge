<?php
	require_once('inc_globals.php');
	require_once('inc_dbfunctions.php');
	require_once('inc_dbconnect.php');
	
	$GLOBALS['user_loggedin'] = false;
	$GLOBALS['user_firstname'] = null;
	$GLOBALS['user_lastname'] = null;
	$GLOBALS['user_username'] = null;
	
	//return a semi-random alphanumeric string of a certain size
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
	
	//new user (auto login)
	if(isset($_REQUEST['regfirst'])){
		try{
		
			$regfirst = dbEscape($_REQUEST['regfirst'],$link);
			$reglast = dbEscape($_REQUEST['reglast'],$link);
			$regun = dbEscape($_REQUEST['regusername'],$link);
			$regemail = dbEscape($_REQUEST['regemail'],$link);
			$regpw = dbescape($_REQUEST['regpw'],$link);
			
			
			if(!valid_email($_REQUEST['regemail'])){
				log_error('Invalid email address');
			}
			
			$usalt = strandom(10);
			$pwhash = md5($usalt.$regpw);
			
			//check for unique values already present
			$stmt = mysqli_prepare($link,'SELECT username FROM user WHERE username=? OR email=?');
			mysqli_stmt_bind_param($stmt,'ss',$regun, $regemail);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result)==0){
				//insert
				$stmt = mysqli_prepare($link, 'INSERT INTO user (username, email, firstname, lastname, pwsalt, pwhash) VALUES (?, ?, ?, ?, ?, ?)');
				mysqli_stmt_bind_param($stmt, 'ssssss', $regun, $regemail, $regfirst, $reglast, $usalt, $pwhash);
				$success = mysqli_stmt_execute($stmt);
				
				if($success){
					$GLOBALS['user_loggedin'] = true;
					$GLOBALS['user_firstname'] = $regfirst;
					$GLOBALS['user_lastname'] = $reglast;
					$GLOBALS['user_username'] = $regun;
				}else{
					log_error('Account creation failed. : (');
				}
			}else{
				log_error('Username or email address already exists');
			}
		
		}catch(Exception $e){
			log_error('Failed to create account : (');
			
		}
		
	//explicit login by GET or POST
	}elseif(isset($_REQUEST['logun']) && isset($_REQUEST['logpw'])){
		$logun = $_REQUEST['logun'];
		$logpw = $_REQUEST['logpw'];
		
		
	}
	
?>