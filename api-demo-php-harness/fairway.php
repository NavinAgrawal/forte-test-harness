<!DOCTYPE html>
<html>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
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
	
$location_id_01       = '191620';
$api_access_id_01    = forte_config('api_access_id');
$api_secure_key_01    = forte_config('api_secure_key');
$method_01 = 'sale';  //can be token, auth, sale or schedule
$version_01 = '2.0';
$utc_01 = utc();
$utc_01 = $utc_01;
$data_01 = "$api_access_id_01|$method_01|$version_01||$utc_01|||";
$hash_01 = hash_hmac('sha256',$data_01,$api_secure_key_01 );

$location_id_02       = '185064';
$api_access_id_02     = forte_config('api_access_id');
$api_secure_key_02     = forte_config('api_secure_key');
$method_02  = 'sale';  //can be token, auth, sale or schedule
$version_02  = '2.0';
$utc_02  = utc();
$utc_02  = $utc_02;
$data_02  = "$api_access_id_02|$method_02|$version_02||$utc_02|||";
$hash_02  = hash_hmac('sha256',$data_02,$api_secure_key_02);
?>

<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
<style>
body {
	background-image: url('fairway01.png');
	background-repeat:repeat;
	background-size:cover;
}
.bluebutton {
	color: white;
	background-color: #001196 !important;
	background-repeat:no-repeat;
	background-size:cover;
	height: 40px;
	width: auto;
	font-size: 1rem;
	border: 1px solid #003366 !important;
	border-radius: 4px;
	box-shadow: 2px 2px #4d4d4d;
	text-align: center;
	cursor: default !important;
}
.bluebutton:hover {
	background-color: #0080d5 !important;
}
.bluebutton:focus {
	outline: 1px solid #000000;
}
.bluebutton:active {
	box-shadow: 0 0px #666;
	transform: translateY(2px);
}.bluebutton_02 {
	color: white;
	background-color: #001196 !important;
	background-repeat:no-repeat;
	background-size:cover;
	height: 40px;
	width: auto;
	font-size: 1rem;
	border: 1px solid #003366 !important;
	border-radius: 4px;
	box-shadow: 2px 2px #4d4d4d;
	text-align: center;
	cursor: default !important;
}
.bluebutton_02:hover {
	background-color: #0080d5 !important;
}
.bluebutton_02:focus {
	outline: 1px solid #000000;
}
.bluebutton_02:active {
	box-shadow: 0 0px #666;
	transform: translateY(2px);
}
</style>
</head>
<div style="height:260px">&nbsp;</div>
<div style="background-color:white; width:500px;"><pre style="margin-left:50px;" id="message"></pre></div>
<center>
<br><br><br><br>
<button class="bluebutton"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version_01;?>"
	save_token="true"
	method="<?php echo $method_01;?>"
	utc_time="<?php echo $utc_01;?>"
	callback="oncallback"	
	hash_method="sha256"
	signature="<?php echo $hash_01;?>"
	>Police Account #1
</button>

<div style="height:50px">&nbsp;</div>

<button class="bluebutton_02"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version_02;?>"
	callback="oncallback"	
	method="<?php echo $method_02;?>"
	utc_time="<?php echo $utc_02;?>"
	hash_method="sha256"
	signature="<?php echo $hash_02;?>"
	>Police Account #2
</button>

</body>
</html>