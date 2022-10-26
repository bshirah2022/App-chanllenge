<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	header("Access-Control-Allow-Origin: *");
	$link = dbConnect();
	
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
	    'example'=>'server.php?action=get_columns&tablename=team'
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
		'example 1: set lname to "Word" where fname like \'%arre%\''=>$uprowsencode1,
		'example 2: set lname to "Wood" where fname equals \'Word\''=>$uprowsencode2
	);
	
	$insertencode1 = 'server.php?action=insert_row&tablename=team&row='.urlencode('[{"col":"fname","val":"Matt"},{"col":"lname","val":"Riley"}]');
	$insertencode2 = 'server.php?action=insert_row&tablename=team&row='.urlencode('[{"col":"fname","val":"Alphius"},{"col":"lname","val":"McConnell"}]');
	$insertencode3 = 'server.php?action=insert_row&tablename=user&row='.urlencode('[{"col":"phone","val":"1-900-867-5309"},{"col":"email","val":"junk@uswoods.net"},{"password":"12345"}]');
	$actions['insert_row'] = array(
	    'description' => 'Insert a row in the requested table', 
	    'required' => array(
		'tablename' => 'name of the requested table',
		'row'=>'json encoded array of [col]umns to be inserted with [val]ues'
	    ),
		'example 1: insert Matt Riley'=>$insertencode1,
		'example 2: insert Alphius McConnell'=>$insertencode2
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
		'example 1: delete Riley'=>$deleteencode1,
		'example 2: delete McConnell'=>$deleteencode2
	);
	
	
	
	if(isset($_REQUEST['action'])){
	
		$reqaction = $_REQUEST['action'];
		
		if($reqaction=='get_tables'){
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
			
		}elseif($reqaction=='get_columns'){
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

		}elseif($reqaction=='get_rows'){
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
		}elseif($reqaction=='update_rows'){
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
				echo '["failure"]';
			}
		}elseif($reqaction=='insert_row'){
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
			
		}elseif($reqaction=='delete_rows'){
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
		
	}else{
		require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_genericBodyStart.php');
		echo '<h2>Services</h2>';
		foreach ($actions as $action => $info){
			echo '<div style="background-color:#f8f8f8; border:2px solid black; margin-bottom:40px; border-radius:10px; padding:6px; padding-left:12px;">';
			echo '<h2>'.$action.'</h2>';
			foreach ($info as $key => $value){
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
