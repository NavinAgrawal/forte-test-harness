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
	
$location_id    = forte_config('location_id');  //ABBA's is 357322
$api_access_id  = forte_config('api_access_id');  //log into Dex and go to Developers > API Credentials > Generate
$api_secure_key = forte_config('api_secure_key');  //same as above
$method         = 'token';  
$version        = '2.0';
$utc            = utc();
$data           = "$api_access_id|$method|$version||$utc|||";
$hash           = hash_hmac('md5',$data,$api_secure_key);
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
		var response = JSON.parse(e.data);
		switch (response.event) {
			case 'begin':
			break;
			case 'success':
				alert('Thank You! Your profile has been created successfully.');
			break;
			case 'failure':
				alert('Sorry, Something went wrong.' + "\n\n" + 'Please call our office at 555-555-5555');
		}
	}
</script>
</head>
<style>
body {
	font-family:calibri;
	font-size:20px;
}
.bluebutton {
	color: white;
	background-color: #1A7DC0 !important;
	height: 33px;
	width: 140px;
	font-size: 1rem;
	font-weight: bold;
	border: 1px solid #003366 !important;
	border-radius: 4px;
	box-shadow: 2px 2px #4d4d4d;
	text-align: center;
	cursor: default !important;
}
.bluebutton:hover {
	background-color: #125887 !important;
}
}
</style>
<body>
<center>
<br><br>
<img src="abba.png" style="width:300px">
<br><br>
Click the button below to<br>
create your account.<br><br><br>
<button class="bluebutton"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version;?>"
	callback="oncallback"
	allowed_methods="echeck"
	method="<?php echo $method;?>"
	utc_time="<?php echo $utc;?>"
	signature="<?php echo $hash;?>"
	>Create Account
</button>
</body>
</html>