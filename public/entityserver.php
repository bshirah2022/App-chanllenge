<?php
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbconnect.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_login.php');
	header('Access-Control-Allow-Origin: *');
	
	$require_login = true;
	


	$entitytable = "task";
	$GLOBALS['entitytable'] = $entitytable;
	$entitiesClientNickname="tasks";
	$entities=[];
	if(isset($_REQUEST[$entitiesClientNickname])){
		try{
			$entities = json_decode($_REQUEST[$entitiesClientNickname],true);
			if(!is_array($entities)){
				throw new Exception('bad data');
			}
		}catch(Exception $ex){
			log_error('Could not interpret '.$entitiesClientNickname);
		}
	}
	
	$entitycols = [];
	$entitykeys=[];
	$entitykeynames=[];
	
	$sql = 'DESCRIBE '.$entitytable;
	$res = dbQuery($sql,$link);
	while($row = mysqli_fetch_assoc($res)){
		/* response fields with examples:
		Field		taskid
		Type		int
		Null		NO
		Key		PRI
		Default	NULL
		Extra		auto_increment
		*/
		array_push($entitycols,$row);
		if($row["Key"]=='PRI'){
			array_push($entitykeynames,$row['Field']);
			array_push($entitykeys,$row);
		}
	}
	
	
	function selectEntities(){
		$sql = 'SELECT * FROM '.$GLOBALS['entitytable'];
		$stmt = mysqli_prepare($GLOBALS['dblink'], $sql);
		//mysqli_stmt_bind_param($stmt,'s',$logun);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		$rows = array();
		while($row = mysqli_fetch_assoc($result)){
			array_push($rows,$row);
		}
		return $rows;
	}
	
	function updateEntities(){
		$returnobj = null;
		
		return $returnobj;
	}
	
	function deleteEntities($ents){
		for($i=0; $i<count($ents); $i++){
			$ent = $ents[i];
			
			$keymatch = true;
			for($k=0; $k<count($entitykeys); $i++){
				$kn = $entitykeynames[$k];
				if(!$ent[$kn]){
					$keymatch=false;
					log_error('Insufficient key data to delete entity');
				}
			}
			if($keymatch){
				$sql = 'DELETE FROM $entitytable WHERE ';
				
				/*
				$stmt = mysqli_prepare($link, $sql);
				mysqli_stmt_bind_param($stmt, 'ssssss', $regun, $regemail, $regfirst, $reglast, $usalt, $pwhash);
				$success = mysqli_stmt_execute($stmt);
				*/
				
				echo $sql;
			}
		}
	}
	
	$outputobj = null;
	if(isset($_REQUEST['action'])){
		$action = $_REQUEST['action'];
		switch ($action){
			case 'select':
				$outputobj = selectEntities();
				break;
			case 'insert':
				//$outputobj = deleteEntities();
				break;
			case 'update':
				$outputobj = updateEntities();
				break;
			case 'delete':
				break;
			
		}
	}
	
	dbDisconnect($GLOBALS["dblink"]);
	
	/*
	echo json_encode($entitycols);
	echo '<br /><br />';
	echo json_encode($entitykeys);
	echo '<br /><br />';

	echo '<br /><br />';
	*/
	
	$output = '';
	try{
		$output = json_encode($outputobj);
	}catch(Exception $ex){
		log_error('Failed to encode result');
	}
	
	if(count($GLOBALS['errors'])>0){
		$errorobj = array();
		$errorobj['errors'] = $GLOBALS['errors'];
		echo json_encode($errorobj);
	}else{
		echo $output;
	}
	
	
?>

