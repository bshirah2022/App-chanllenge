<?

require_once('inc_prodswitch.php');
$global_dbuser = 'cptnullpointers';
$global_dbpass = 'DBpass4pointers!';
$global_dbname = 'npdb';
	
	function dbConnect(){
		global $global_dbhost, $global_dbuser, $global_dbpass, $global_dbname;
		$link = mysqli_connect($global_dbhost,$global_dbuser,$global_dbpass,$global_dbname);
		return $link;
	}

	function dbDisconnect($link){
		try{
			mysqli_close($link);
		}catch(Exception $ex){
			//no action needed
		}
	}
	
	function dbQuery($sql,$link){
		$res = mysqli_query($link,$sql);
		if(!$res){
			$message = 'Invalid query: '.mysqli_error($link).'<br />sql: '.$sql.'<br />';
			die($message);
		}
		return $res;
	}
	
	function dbEscape($var,$link){
		return mysqli_real_escape_string($link,$var);
	}
	
?>