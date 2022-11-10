<?php
//global variables and generic functions

$GLOBALS["errors"] = array();
$GLOBALS["notices"] = array();

function log_error($strerr){
	array_push($GLOBALS["errors"],$strerr);
}

function valid_email($email){
	if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
	return true;
}

?>