<?php echo "<?xml version=\"1.1\" encoding=\"UTF-8\"?>"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>CPT App Challenge - Null Pointers - entity client v1.1</title>
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<script type="text/javascript" src="/common/js/common.js"></script>
		<link rel="stylesheet" href="/common/css/common.css">
		<script type='text/javascript'>
			var lastResponse;
			var lastparams;
			
			
			function loadInjuryData(page,rowsperpage,conditions){
				var params = "action=get_injury";
				params+="&page="+page;
				params+="&rowsperpage="+rowsperpage;
				if(conditions!=null){
					params+="&conditions="+JSON.stringify(conditions);
					lastparams = params;
					
					/* Conditions is an array of obj data, example:
					[{
						"col":"userid",
						"op":"=",
						"val":userid
					}]
					*/
					ajaxRequest('server.php',receiveServerResponse.bind(obj),params);
				}
				
				var obj={
					"request":"list_tasks"
				};
				
				lastparams=params;
				//ajaxRequest('entityserver.php',receiveServerResponse.bind(obj),params);
				ajaxRequest('entityserver.php',receiveServerResponse,params);
			}
			function receiveServerResponse(responseText){
				console.log(this);
				console.log(responseText);
			}
			function bodyOnLoad(){
				loadInjuryData(1,20,null);
				
			}
		</script>
	<body onLoad="javascript:bodyOnLoad();">
		<div class="master">
			<div class="main">
			</div>
		</div>
	</body>
</html>