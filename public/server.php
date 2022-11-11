<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbconnect.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_login.php');
	header("Access-Control-Allow-Origin: *");
	
	$actions = array();
	$actions['get_tables'] = array();
	$actions['get_tables']['description'] = 'List all tables in the database.';
	$actions['get_tables']['required'] = array();
	
	$actions['get_tables'] = array(
	    'description' => 'list all tables in the database', 
	    'required' => array(
	    ),
	    'example'=>'server.php?action=get_tables'
	);
	
	$actions['get_columns'] = array(
	    'description' => 'list all columns in the specified table', 
	    'required' => array(
		'tablename' => 'name of the requested table'
	    ),
	    'samplevalues'=>array(
		'tablename' => 'task'
	    ),
	    'example'=>'server.php?action=get_columns&tablename=team'
	);
	
	$actions['register_user'] = array(
	    'description' => 'register a new user account', 
	    'required' => array(
		'username' => 'unique username',
		'email'=>'unique (valid) email address',
		'password'=>'the user\'s password',
		'firstname'=>'first name',
		'lastname'=>'last name'
	    ),
	    'optional' => array(
		'phone'=>'user primary phone#',
		'address'=>'user primary address'
	    ),
	    'samplevalues'=>array(
		'username' => 'testuser1',
		'email'=>'testuser@domain.com',
		'password'=>'JELLYf15h!',
		'firstname'=>'Testy',
		'lastname'=>'McTesterson',
		'phone'=>'1-800-867-5309',
		'address'=>'1600 Pennsylvania Avenue '
	    ),
	    'example'=>'server.php?action=register_user&username=testuser&email=test@test.net&password=mypass123&firstname=test&lastname=user&phone=1-800-867-5309'
	);
	
	$actions['check_password'] = array(
	    'description' => 'check that a username and password match', 
	    'required' => array(
		'username' => 'username',
		'password' => 'user password'
	    ),
	    'samplevalues'=>array(
		'username' => 'testuser1',
		'password'=>'JELLYf15h!'
	    ),
	    'example'=>'server.php?action=check_password&username=wwood&password=nullpointers'
	);
 
	$colencode = 'server.php?action=get_rows&tablename=team&conditions='.urlencode('[{"col":"fname","op":"like","val":"%arre%"}]');
	$actions['get_rows'] = array(
	    'description' => 'get rows in the specified table', 
	    'required' => array(
		'tablename' => 'name of the requested table'
	    ),
	    'optional' => array(
		'page'=>'which page/set of records to retrieve (default:1)',
		'rowsperpage'=>'how many records to retrieve per page (default:10)',
		'conditions'=>'pass one or more [col], [op], and [val] as query conditions. Allowed operations (op) include "=",">","<","like"'
	    ),
	    'samplevalues'=>array(
		'tablename' => 'team',
		'page'=>'1',
		'rowsperpage'=>'5',
		'conditions'=>'[{"col":"fname","op":"=","val":"Paul"}]'
	    ),
	     'example 1'=>'server.php?action=get_rows&tablename=team',
	     'example 2'=>'server.php?action=get_rows&tablename=team&page=1&rowsperpage=2',
	      'example 3'=>$colencode
	);
	
	//tablename, updates (col, val), conditions (col, op, val)
	$uprowsencode1 = 'server.php?action=update_rows&tablename=team&updates='.urlencode('[{"col":"lname","val":"Word"}]').'&conditions='.urlencode('[{"col":"fname","op":"like","val":"%arre%"}]');
	$uprowsencode2 = 'server.php?action=update_rows&tablename=team&updates='.urlencode('[{"col":"lname","val":"Wood"}]').'&conditions='.urlencode('[{"col":"lname","op":"=","val":"Word"}]');
	$actions['update_rows'] = array(
	    'description' => 'update rows in the requested table', 
	    'required' => array(
		'tablename' => 'name of the requested table',
		'updates'=>'json encoded array of [col]umns to be updated with [val]ues',
		'conditions'=>'pass one or more [col], [op], and [val] as query conditions. Allowed operations (op) include "=",">","<","like"'
	    ),
	    'samplevalues'=>array(
		'tablename' => 'team',
		'updates'=>'[{"col":"fname","val":"Christopher"}]',
		'conditions'=>'[{"col":"fname","op":"=","val":"Chris"}]'
	    ),
		'example 1: set lname to "Word" where fname like \'%arre%\''=>$uprowsencode1,
		'example 2: set lname to "Wood" where fname equals \'Word\''=>$uprowsencode2
	);
	
	$insertencode1 = 'server.php?action=insert_row&tablename=team&row='.urlencode('[{"col":"fname","val":"Matt"},{"col":"lname","val":"Riley"}]');
	$insertencode2 = 'server.php?action=insert_row&tablename=team&row='.urlencode('[{"col":"fname","val":"Alphius"},{"col":"lname","val":"McConnell"}]');
	$insertencode3 = 'server.php?action=insert_row&tablename=user&row='.urlencode('[{"col":"phone","val":"1-900-867-5309"},{"col":"username","val":"junkuser"},{"col":"email","val":"junk@uswoods.net"},{"col":"password","val":"12345"}]');
	$actions['insert_row'] = array(
	    'description' => 'Insert a row in the requested table', 
	    'required' => array(
		'tablename' => 'name of the requested table',
		'row'=>'json encoded array of [col]umns to be inserted with [val]ues'
	    ),
	    'samplevalues'=>array(
		'tablename' => 'team',
		'row'=>'[{"col":"fname","val":"Matt"},{"col":"lname","val":"Riley"}]'
	    ),
		'example 1: insert Matt Riley'=>$insertencode1,
		'example 2: insert Alphius McConnell'=>$insertencode2,
		'example 3: insert into user table'=>$insertencode3
	);
	
	//tablename, updates (col, val), conditions (col, op, val)
	$deleteencode1 = 'server.php?action=delete_rows&tablename=team&conditions='.urlencode('[{"col":"lname","op":"=","val":"Riley"}]');
	$deleteencode2 = 'server.php?action=delete_rows&tablename=team&conditions='.urlencode('[{"col":"lname","op":"=","val":"McConnell"}]');
	$actions['delete_rows'] = array(
	    'description' => 'delete rows in the requested table', 
	    'required' => array(
		'tablename' => 'name of the requested table',
		'conditions'=>'Delete ALL ROWS matching the conditions: pass one or more [col], [op], and [val] as query conditions. Allowed operations (op) include "=",">","<","like"'
	    ),
	    'samplevalues'=>array(
		'tablename' => 'team',
		'conditions'=>'[{"col":"lname","op":"=","val":"Riley"}]'
	    ),
		'example 1: delete Riley'=>$deleteencode1,
		'example 2: delete McConnell'=>$deleteencode2
	);
	
	
	
	if(isset($_REQUEST['action'])){
	
		$reqaction = $_REQUEST['action'];
		
		if($reqaction=='get_tables'){
			try{
				$rows = array();
				
				$sql=<<<SQL
SELECT table_name FROM information_schema.tables
WHERE table_schema = 'npdb';
SQL;
		
				$res = dbQuery($sql,$link);
				while($row = mysqli_fetch_assoc($res)){
					array_push($rows,$row);
				}
				echo json_encode($rows);
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
		}elseif($reqaction=='get_columns'){
			try{
				$rows = array();
				$tablename = dbEscape($_REQUEST['tablename'],$link);
				
				$sql=<<<SQL
DESCRIBE $tablename
SQL;
				$res = dbQuery($sql,$link);
				while($row = mysqli_fetch_assoc($res)){
					array_push($rows,$row);
				}
				echo json_encode($rows);
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
		}elseif($reqaction=='get_rows'){
			try{
				$debugstr = '';
				$rows = array();
				$tablename = dbEscape($_REQUEST['tablename'],$link);
				$page = 1;
				$rowsperpage = 10;
				if(isset($_REQUEST['page'])){
					$page = intval($_REQUEST['page']);
				}
				if(isset($_REQUEST['rowsperpage'])){
					$rowsperpage =  intval($_REQUEST['rowsperpage']);
				}
				
				$record_start=($page-1)*$rowsperpage;
				
				$conditions = 'TRUE';
				if(isset($_REQUEST['conditions'])){
					$cc = json_decode($_REQUEST['conditions'],true);
					$debugstr = var_export($cc,true);
					$allowed_conditions = ['=','>','<','like'];
					$first = true;
					
					if(is_array($cc)){
						foreach($cc as $condition){
							if(in_array($condition['op'],$allowed_conditions)){
								if($first){
									$first=false;
									$conditions='';
									
								}else{
									$conditions.=' AND ';
								}
								$conditions.=$condition['col'].' '.$condition['op'].' "'.mysqli_real_escape_string($link,$condition['val']).'"';
							}
						}
					}
				}
				
				$orderby='';
				if(isset($_REQUEST['orderby'])){
					//allow alphanumeric, -, _, [space]
					if(preg_match('/^[\w\-,\s]+$/',$_REQUEST['orderby'])){
						$orderby=(' ORDER BY '.$_REQUEST['orderby']);
					}
				}
				
				
				$sql=<<<SQL
SELECT * FROM $tablename
WHERE $conditions
$orderby
LIMIT $record_start,$rowsperpage
SQL;
				$res = dbQuery($sql,$link);
				while($row = mysqli_fetch_assoc($res)){
					array_push($rows,$row);
				}
				echo json_encode($rows);
				//echo '<br />'.json_encode($sql);
				//echo '<br />'.$debugstr;
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
		}elseif($reqaction=='update_rows'){
			try{
				$tablename = dbEscape($_REQUEST['tablename'],$link);
				$changes = '';
				if(isset($_REQUEST['updates'])){
					$ch = json_decode($_REQUEST['updates'],true);
					$first = true;
					foreach($ch as $change){
						if($first){
							$first=false;
							$changes='';
						}else{
							$changes.=', ';
						}
						$changes.=$change['col'].'="'.mysqli_real_escape_string($link,$change['val']).'"';					
					}
				}
				if(isset($_REQUEST['conditions'])){
					$cc = json_decode($_REQUEST['conditions'],true);
					$allowed_conditions = ['=','>','<','like'];
					$first = true;
					foreach($cc as $condition){
						if(in_array($condition['op'],$allowed_conditions)){
							if($first){
								$first=false;
								$conditions='';
							}else{
								$conditions.=' AND ';
							}
							$conditions.=$condition['col'].' '.$condition['op'].' "'.mysqli_real_escape_string($link,$condition['val']).'"';
						}
						
					}
				}
				
				$sql=<<<SQL
UPDATE $tablename
SET $changes
WHERE $conditions
SQL;
				$res = dbQuery($sql,$link);
				if($res){
					echo '["success"]';
				}else{
					log_error("table update failure");
				}
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
		}elseif($reqaction=='insert_row'){
			try{
				$tablename = dbEscape($_REQUEST['tablename'],$link);
				$str_insert_columns = '';
				$str_insert_values = '';
				if(isset($_REQUEST['row'])){
					$insrow = json_decode($_REQUEST['row'],true);
					$first = true;
					foreach($insrow as $ins){
						if($first){
							$first=false;
						}else{
							$str_insert_columns .= ',';
							$str_insert_values .= ',';
						}
						$str_insert_columns .=mysqli_real_escape_string($link,$ins['col']);
						$str_insert_values .= '"'.mysqli_real_escape_string($link,$ins['val']).'"';			
					}
				}
				
				$sql=<<<SQL
INSERT INTO $tablename ($str_insert_columns)
VALUES ($str_insert_values)
SQL;
				//echo $sql;
				$res = dbQuery($sql,$link);
				if($res){
					echo '["success"]';
				}else{
					echo '["failure"]';
				}
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
			
		}elseif($reqaction=='delete_rows'){
			try{
				$tablename = dbEscape($_REQUEST['tablename'],$link);

				if(isset($_REQUEST['conditions'])){
					$cc = json_decode($_REQUEST['conditions'],true);
					$allowed_conditions = ['=','>','<','like'];
					$first = true;
					foreach($cc as $condition){
						if(in_array($condition['op'],$allowed_conditions)){
							if($first){
								$first=false;
								$conditions='';
							}else{
								$conditions.=' AND ';
							}
							$conditions.=$condition['col'].' '.$condition['op'].' "'.mysqli_real_escape_string($link,$condition['val']).'"';
						}
						
					}
				}
				
				$sql=<<<SQL
DELETE FROM $tablename
WHERE $conditions
SQL;
				//echo $sql;
				$res = dbQuery($sql,$link);
				if($res){
					echo '["success"]';
				}else{
					echo '["failure"]';
				}
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
		}elseif($reqaction=="register_user"){
			try{
				$username = $_REQUEST['username'];
				$email = $_REQUEST['email'];
				$password = $_REQUEST['password'];
				$firstname = $_REQUEST['firstname'];
				$lastname = $_REQUEST['lastname'];

				$phone=null;
				if(isset($_REQUEST['phone'])){
					$phone = $_REQUEST['phone'];
				}
				$address=null;
				if(isset($_REQUEST['address'])){
					$address = $_REQUEST['address'];
				}
				
				$usalt = strandom(10);
				$pwhash = md5($usalt.$password);
				
				//check that email is valid
				if(valid_email($email)){
					//check for unique values already present
					$stmt = mysqli_prepare($link,"SELECT username FROM user WHERE username=? OR email=?");
					mysqli_stmt_bind_param($stmt,'ss',$username, $email);
					mysqli_stmt_execute($stmt);
					$result = mysqli_stmt_get_result($stmt);
					if(mysqli_num_rows($result)==0){
						//insert
						$stmt = mysqli_prepare($link, "INSERT INTO user (username, email, firstname, lastname, pwsalt, pwhash, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
						mysqli_stmt_bind_param($stmt, 'ssssssss', $username, $email, $firstname, $lastname, $usalt, $pwhash, $phone, $address);
						$success = mysqli_stmt_execute($stmt);
						
						if($success){
							$GLOBALS["user_loggedin"] = true;
							$GLOBALS["user_firstname"] = $firstname;
							$GLOBALS["user_lastname"] = $lastname;
							$GLOBALS["user_username"] = $username;
							echo "[success]";
						}else{
							log_error("Failed to complete registration");
						}
					}else{
						log_error("Username and/or email address already exists.");
					}
				}else{
					log_error("Email address is not valid.");
				}
		
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
		
		}elseif($reqaction=="check_password"){
			try{
				$logun = $_REQUEST['username'];
				$logpw = $_REQUEST['password'];
				
				//fetch the user record
				$stmt = mysqli_prepare($link,"SELECT userid, username, firstname, lastname, password, pwsalt, pwhash FROM user WHERE username=? LIMIT 1");
				mysqli_stmt_bind_param($stmt, 's', $logun);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result)==1){
					$row = mysqli_fetch_assoc($result);
					$password = $row['password'];
					if($password!=null){
						if($password!=$logpw){
							log_error('explicit password mismatch');
						}else{
							echo '[success]';
						}
					}else{
						$pwsalt = $row['pwsalt'];
						$pwhash = $row['pwhash'];
						if(md5($row['pwsalt'].$logpw)==$row['pwhash']){
							echo '[success]';
						}else{
							log_error('password hash mismatch');
						}
					}
				}else{
					//echo "[failure]";
					log_error('user not found');
				}
				/*
				//check the password
				$stmt = mysqli_prepare($link,"SELECT userid, username, email, firstname, lastname FROM user WHERE username=? AND password=? LIMIT 1");
				mysqli_stmt_bind_param($stmt, 'ss', $logun, $logpw);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if(mysqli_num_rows($result)==1){
					echo "[success]";
				}else{

					log_error("no such user or password incorrect");
				}
				*/
			}catch(Exception $ex){
				log_error($ex->getMessage());
			}
			
		}elseif($reqaction=="upload"){
			$fileinfo = ''.count($_FILES).': '.var_export($_FILES,true);
			
			$target_dir = "/../upload/";
			echo ("request: <br />");
			echo var_export($_REQUEST, true);
			echo ("<br />files: <br />");
			echo $fileinfo;
			/*
			$target_file = $target_dir . basename($_FILES["upfile"]["name"]);
			if (move_uploaded_file($_FILES["upfile"]["tmp_name"], $target_file)) {
				echo '["success"]';
			} else {
				echo '["failure"]';
			}
			*/
		}
		
		if(count($GLOBALS['errors'])>0){
			$errorobj = array();
			$errorobj['errors'] = $GLOBALS['errors'];
			echo json_encode($errorobj);
		}
		
	}else{
		require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_genericBodyStart.php');
		echo '<h2>Services</h2>';
		foreach ($actions as $action => $info){
			echo '<div style="background-color:#f8f8f8; border:2px solid black; margin-bottom:40px; border-radius:10px; padding:6px; padding-left:12px;">';
			echo '<h2>'.$action.'</h2>';
			foreach ($info as $key => $value){
				if($key != "samplevalues"){
					echo '<b>'.$key.':</b> ';
					if(is_array($value)){
						if(count($value)>0){
							echo '<div style="border:1px solid #777777; margin:2px; padding:2px; display:inline-block;">';
							foreach ($value as $k => $v) {
								echo '<b>'.$k.':</b> '.$v.'<br />';
							}
							echo '</div><br />';
						}else{
							echo '<br />';
						}
					}else{
						if(str_starts_with($key,'example')){
							echo '<a href="'.$value.'">'.$value.'</a><br />';
						}else{
							echo $value.'<br />';
						}
					}
				
				}
			}
			
			if(isset($info["required"]) || isset($info["optional"])){
				echo '<div style="padding:10px">';
				echo '<span style="color:blue; text-decoration:underline; cursor:pointer;" onClick="javascript:toggleCSSClass($g(\'autoform_'.$action.'\'),\'hidden\');">Build Form</span>';
				echo '</div>';
				
				echo '<div class="hidden" id="autoform_'.$action.'">';
				echo '<form method="post" target="_blank" action="/server.php">';
					echo '<input type="hidden" name="action" value="'.$action.'" />';
					echo '<table><tbody>';
					echo '<tr><td><b>action:</b></td><td><input name="action" disabled value="'.$action.'" /></td></tr>';
					if(isset($info["required"])){
						foreach ($info["required"] as $field=>$description){
							echo '<tr><td><b>';
							echo $field;
							echo '</b></td><td>';
							echo '<input name="'.$field.'" type="text" ';
							if(isset($info["samplevalues"]) && $info["samplevalues"][$field]!=null){
								echo 'value="'.htmlspecialchars($info["samplevalues"][$field]).'"';
							}
							
							echo '/>';
							echo '</td></tr>';
						}
					}
					if(isset($info["optional"])){
						foreach ($info["optional"] as $field=>$description){
							echo '<tr><td>';
							echo $field;
							echo '</td><td>';
							echo '<input name="'.$field.'" type="text" ';
							if(isset($info["samplevalues"]) && $info["samplevalues"][$field]!=null){
								echo 'value="'.htmlspecialchars($info["samplevalues"][$field]).'"';
							}
							
							echo '/>';
							echo '</td></tr>';
						}
					}
					echo '</tbody></table>';
				echo '<input type="submit" value="submit">';
				echo '</form>';
				echo '</div>';
			
			}
			
			
			echo '</div>';
			

			
		}
		echo '<div style="background-color:#f8f8f8; border:2px solid black; margin-bottom:40px; border-radius:10px; padding:6px; padding-left:12px;">';
		echo '<h2>File Upload</h2>';
?>
		<form enctype="multipart/form-data" method="post" action="/upload.php">
			<input type="file" id="upfile" name="upfile"><br /><br />
			<input type="hidden" name="action" value="upload">
			<input type="submit" value="submit">
		</form>
<?
		echo '</div>';
		
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_genericBodyEnd.php');
	}
	
	dbDisconnect($link);
	
?>
