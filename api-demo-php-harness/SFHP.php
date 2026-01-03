<!DOCTYPE html>
<html>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<?php

require_once __DIR__ . '/config/bootstrap.php';
// https://sandbox.forte.net/checkout/getUTC?callback=?  //sandbox (for UTC)
// https://checkout.forte.net/getUTC?callback=?          //production (for UTC)

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
$total_amount = "10.00";
$method = 'sale';  //can be token, auth, sale or schedule
$version = '2.0';
$ordernumber = 'SM22111900102047';
$reference_id = "71691";
$utc = utc();
$data = "$api_access_id|$method|$version|$total_amount|$utc|$ordernumber||";
$hash = hash_hmac('sha256',$data,$api_secure_key);
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
	background-image: url('csgforte2x.png');
}
.bluebutton {
	color: white;
	background-color: #1A7DC0 !important;
	height: 34px;
	width: 100px;
	font-size: 1rem;
	border: 1px solid #003366 !important;
	border-radius: 4px;
	box-shadow: 2px 2px #4d4d4d;
	text-align: center;
	cursor: default !important;
}

.bluebutton:hover {
	background-color: #125887 !important;
}

.bluebutton:focus {
	outline: 1px solid #000000;
}

.bluebutton:active {
	box-shadow: 0 0px #666;
	transform: translateY(2px);
}
</style>
</head>
<div style="height:160px">&nbsp;</div>
<div style="background-color:white; width:500px;"><pre style="margin-left:50px;" id="message"></pre></div>
<center>
<br><br><br><br>
<button class="bluebutton"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version;?>"
	order_number="<?php echo $ordernumber;?>"
	reference_id="<?php echo $reference_id;?>"
	billing_company_name="Account Holder"
	billing_company_name_attr="hide"
	callback="oncallback"
	total_amount="<?php echo $total_amount;?>"
	method="<?php echo $method;?>"
	utc_time="<?php echo $utc;?>"
	hash_method="sha256"
	signature="<?php echo $hash;?>"
	>Pay Now
</button>
</body>
</html>