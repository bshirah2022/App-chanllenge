<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	
	$link = dbConnect();
	
	$actions = array();
	$actions['get_tables'] = array();
	$actions['get_tables']['description'] = 'List all tables in the database.';
	$actions['get_tables']['required'] = array();
	
	$actions['get_tables'] = array(
	    'description' => 'list all tables in the database', 
	    'required' => array(
	    )
	);
	
	$actions['get_columns'] = array(
	    'description' => 'list all columns in the specified table', 
	    'required' => array(
		'tablename' => 'name of the requested table'
	    )
	);
	
	$actions['get_rows'] = array(
	    'description' => 'get rows in the specified table', 
	    'required' => array(
		'tablename' => 'name of the requested table'
	    ),
	    'optional' => array(
		'page'=>'which page/set of records to retrieve (default:1)',
		'rowsperpage'=>'how many records to retrieve per page (default:10)'
	    )
	);
	
	$actions['update_row'] = array(
	    'description' => 'update a single row in the specified table', 
	    'required' => array(
		'tablename' => 'name of the requested table',
		'record'=>'json encoded keyed array representing the fields to be updated and their values'
	    )
	);
	
	$actions['update_rows'] = array(
	    'description' => 'update multiple rows in the requested table', 
	    'required' => array(
		'tablename' => 'name of the requested table',
		'records'=>'json encoded array of records, each of which is a keyed array representing the fields to be updated and their values'
	    )
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
			
			$sql=<<<SQL
SELECT * FROM $tablename
LIMIT $record_start,$rowsperpage
SQL;
			$res = dbQuery($sql,$link);
			while($row = mysqli_fetch_assoc($res)){
				array_push($rows,$row);
			}
			echo json_encode($rows);
		}
	
		
		
	}else{
		require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_genericBodyStart.php');
		echo '<h2>Services</h2>';
		foreach ($actions as $action => $info){
			echo '<h3>'.$action.'</h3>';
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
					echo $value.'<br />';
				}
			}
			
		}
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_genericBodyEnd.php');
		
	
	}
	
	dbDisconnect($link);
	
?>
