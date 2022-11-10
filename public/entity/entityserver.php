<?php
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbconnect.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_login.php');
	header('Access-Control-Allow-Origin: *');
	
	$require_login = true;
	


	$entitytable = "task";
	$entitycols = [];
	$entitykeys=[];
	
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
			array_push($entitykeys,$row['Field']);
		}
	}
	
	
	dbDisconnect($GLOBALS["dblink"]);
	
	echo json_encode($entitycols);
	echo '<br /><br />';
	echo json_encode($entitykeys);
	echo '<br /><br />';

	echo '<br /><br />';
	
?>

