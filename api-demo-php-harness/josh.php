<!DOCTYPE html>
<html>
<?php

require_once __DIR__ . '/config/bootstrap.php';
function utc() {
	$curlUTC = curl_init();
	curl_setopt($curlUTC, CURLOPT_URL, 'https://checkout.forte.net/getUTC?callback=?');
	curl_setopt($curlUTC, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlUTC, CURLOPT_RETURNTRANSFER, true);	
	$curlData = (curl_exec($curlUTC));
	$stripOpenParen = stripos($curlData,"(");
	$stripClosedParen = stripos($curlData,")");		
	$utc = substr($curlData,$stripOpenParen+1,$stripClosedParen-2);
	return $utc;
	curl_close($curlUTC);
}
	
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$location_id       = forte_config('location_id');
$method = 'sale';  //can be token, auth, sale or schedule
$version = '2.0';
$utc = utc();
$data = "$api_access_id|$method|$version||$utc|||";
$hash = hash_hmac('sha256',$data,$api_secure_key);
?>

<head>
<!--script type="text/javascript" src="https://checkout.forte.net/v2/js"></script>     <!-- production -->
<script type="text/javascript" src="https://sandbox.forte.net/checkout/v2/js"></script>    <!-- sandbox -->

<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
	function oncallback(e) {
        var formatted_json = JSON.stringify(JSON.parse(e.data), null, 2);
        $('#message').html(formatted_json);
		var response = JSON.parse(e.data);
		switch (response.event) {
			case 'begin':
			break;
			case 'success':
				alert('Thanks for your payment.' + "\n\n" + 'The trace number is:' + "\n" + response.trace_number);
			break;
			case 'failure':
				alert('Sorry, the transaction failed.' + "\n\n" + 'The failed reason is ' + response.response_description);
		}
	}
</script>
</head>
<div style="height:130px">&nbsp;</div>
<div style="background-color:white; width:500px;"><pre style="margin-left:50px;" id="message"></pre></div>
<center>
<br><br><br><br>
<button class="bluebutton"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version;?>"
	callback="oncallback"
	method="<?php echo $method;?>"
	utc_time="<?php echo $utc;?>"
	hash_method="sha256"
	signature="<?php echo $hash;?>"
	>Pay Now
</button>
</body>
</html>