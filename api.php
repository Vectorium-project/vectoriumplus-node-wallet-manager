<?php
//	Enable the wallet
	require_once ("daemon.php");
	
//	A check for no request
	if (!isset ($_REQUEST["request"]) || $_REQUEST["request"] == "")
	{
		bcapi_error (0, "No Request");
	}

//	URL formatting is stripped from the request
	$request = urldecode ($_REQUEST["request"]);

//	The request is split in case anyone tries to send a multi-parameter
//	request to the API, any parameters after method will be ignored
	$request = explode (" ", $request);
	foreach($request as $key => $value)
	{
	   if (in_array($value, array('true', 'false'))) $request[$key] = filter_var($request[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	}
	
//	These are security checks to ensure that no one uses the API
//	to request balance data or mess up the wallet.
	
	$request[0] = str_replace("gettransactios", "gettransaction ", $request[0]);
		
//	Check to stop remote users from killing the daemon via API
	if ($request[0] == "stop")
	{
		bcapi_error (6, "You can't stop me!");
	}	

/*	
//	The first word of the request is passed to the daemon as a
//	JSON-RPC method
	$query["method"] = $request[0];
	
//	The data is fetched from the wallet
	$result = wallet_fetch ($query);
*/

// added by Miodrag
$full_request['method'] = $request[0];
for ($i = 1; $i  < count($request) ; $i++ )
{
    $full_request['params'][] = $request[$i];
}
$result = wallet_fetch ($full_request);
// end of added by Miodrag

//	The wallet fetch routine has removed the JSON formatting for 
//	internal use. The JSON format is re-applied for the the feed
	print_r (json_encode ($result));

//	That's it.
	exit;

//	this function is here to generate repetitive error messages
	function bcapi_error ($code, $message)
	{
		$error["code"] = $code;
		$error["message"] = $message;
		
		print_r (json_encode($error));
		exit;
	}
?>