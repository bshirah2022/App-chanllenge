<?php
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	$link = dbConnect();
	
	$client_table = 'team'; 
	
	//NOTE: use relative path links, not direct links which were causing timeouts on AWS
	//$http_root_dir = 'https://'.$_SERVER['HTTP_HOST'].'/';
?>


<?php echo "<?xml version=\"1.1\" encoding=\"UTF-8\"?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="en">
	<head>
	
		<meta charset="UTF-8">
		<title>CPT App Challenge - Null Pointers</title>
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<script type="text/javascript" src="/common/js/common.js"></script>
		<link rel="stylesheet" href="/common/css/common.css">
		<script type='text/javascript'>
			var lastResponse;
			
			function bodyOnLoad(){
				var params = "action=get_tables";
				ajaxRequest('server.php',receiveServerResponse,params);
				
			}
			
			function receiveServerResponse(responseText){
				lastResponse = responseText;
				console.log(responseText);
			}
		</script>
	</head>
	<body onLoad="javascript:bodyOnLoad();">
		test client. <span class="hidden">hidden span</span>
	</body>
</html>

<?php
	dbDisconnect($link);
?>