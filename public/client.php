<?php
	
	require_once($_SERVER['DOCUMENT_ROOT'].'/../include/inc_dbfunctions.php');
	$link = dbConnect();
?>


<?php echo "<?xml version=\"1.1\" encoding=\"UTF-8\"?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>CPT App Challenge - Null Pointers: Team | Warren</title>
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="warren.css">
		<script src="warren.js" ></script>
		<script type='text/javascript'>



<?php
	dbDisconnect($link);
?>